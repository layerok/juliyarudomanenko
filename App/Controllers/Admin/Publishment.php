<?php

namespace App\Controllers\Admin;

use App\Support\File;
use \Core\View;
use \App\Models\Post;
use \App\Flash;
use \App\Paginator;
use \App\Request;
use Verot\Upload\Upload;

class Publishment extends Authenticated
{
    public function indexAction()
    {
        $records = Post::orderBy('id', 'desc')->get()->toArray();

        $paginator = new Paginator();

        $paginator->setCurrentPage($this->route_params['page'] ?? 1)
            ->setRecordsCount(count($records))
            ->setPerPageLimit(10)
            ->setMaxPageCount(10);

        View::renderTemplate('/Admin/Publishment/index.html', [
            'records' => array_slice($records, ($paginator->getCurrentPage() - 1) * $paginator->getPerPageLimit(), $paginator->getPerPageLimit()),
            'pages' => $paginator->getPages(),
        ]);
    }

    public function addAction()
    {
        View::renderTemplate('/Admin/Publishment/add.html');
    }

    public function editAction()
    {
        View::renderTemplate('/Admin/Publishment/edit.html', [
            'record' => Post::findOrFail($this->route_params['id'] ?? null)
        ]);
    }

    public function saveAction()
    {
        if (!Request::isPost()) {
            throw new \Exception("Method [${$_SERVER['REQUEST_METHOD']}] is not allowed");
        }

        $errors = $this->validate($_POST);

        $record = new Post($_POST);

        try {
            $record->image = $this->uploadImage($_FILES['image']);
        } catch (\Exception $exception) {
            $errors[] = $exception->getMessage();
        }

        if (count($errors) > 0) {
            File::delete($record->image);
            foreach ($errors as $value) {
                Flash::addMessage($value, Flash::DANGER);
            }

            View::renderTemplate('/Admin/Publishment/add.html', [
                'post' => $_POST,
                'files' => $_FILES,
                'record' => $record
            ]);
            return;

        }

        $record->save();
        Flash::addMessage("Запись добавлена", Flash::SUCCESS);
        $this->redirect("/admin/publishment/index");
    }


    public function saveChangesAction()
    {
        if (!Request::isPost()) {
            throw new \Exception("Method [${$_SERVER['REQUEST_METHOD']}] is not allowed");
        }

        $id = $this->route_params['id'] ?? null;

        /** @var Post $record */
        $record = Post::findOrFail($id);

        $errors = $this->validate($_POST, $id);

        if ($_FILES['image']["name"]) {
            try {
                $image_path = $this->uploadImage($_FILES['image']);

                File::delete($record->image);
                $record->image = $image_path;

            } catch (\Exception $exception) {
                $errors[] = $exception->getMessage();
            }
        }


        if (count($errors) > 0) {
            File::delete($record->image);
            foreach ($errors as $value) {
                Flash::addMessage($value, Flash::DANGER);
            }
            View::renderTemplate('/Admin/Publishment/edit.html', [
                'post' => $_POST,
                'files' => $_FILES,
                'record' => $record
            ]);
            return;
        }

        $record->content = htmlspecialchars($record->content);
        $record->update($_POST);

        Flash::addMessage("Изменения сохранены", Flash::SUCCESS);
        $this->redirect("/admin/publishment/index");

    }


    public function validate($data = [], $id = null): array
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Введите заголовок поста';
        }

        if (empty($data['slug'])) {
            $errors[] = 'Введите slug';
        }

        if (empty($data['content'])) {
            $errors[] = 'Введите содержимое поста';
        }

        if (Post::slugExists($data['slug'], $id)) {
            $errors[] = 'Пост с таким слагом существует';
        }


        return $errors;
    }

    public function uploadImage($image): string
    {
        $handle = new Upload($image, 'ru_RU');
        $handle->allowed = array('image/*');

        if ($handle->uploaded) {
            $handle->image_resize = true;
            $handle->image_ratio_y = true;
            $handle->image_x = 360;

            $pathToImages = '/img/posts/';

            $handle->process($_SERVER['DOCUMENT_ROOT'] . $pathToImages);

            if (!$handle->processed) {
                throw new \Exception($handle->error);
            }


            return $pathToImages . $handle->file_dst_name;
        }
        throw new \Exception($handle->error);
    }

    public function deleteAction()
    {
        /** @var Post $record */
        $record = Post::findOrFail($this->route_params['id'] ?? null);

        if ($record->delete()) {
            Flash::addMessage("Запись удалена", Flash::SUCCESS);
        } else {
            Flash::addMessage("Возникла ошибка удаления записи. Обратитесь к разработчику", Flash::DANGER);
        }

        $this->redirect("/admin/publishment/index");
    }

    public function checkSlugAction()
    {
        if (!Post::slugExists($_POST['slug'], $_POST['id'] ?? null)) {
            return json_encode(['response' => 'success']);
        }
        return json_encode(['response' => 'error']);
    }

}