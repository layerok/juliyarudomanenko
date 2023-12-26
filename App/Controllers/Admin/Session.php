<?php

namespace App\Controllers\Admin;

use App\Support\File;
use \Core\View;
use \App\Models\Service;
use \App\Request;
use \App\Flash;
use \App\Paginator;
use Verot\Upload\Upload;

class Session extends Authenticated
{
    public function indexAction()
    {
        $records = Service::all()->toArray();
        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($this->route_params['page'] ?? 1)
            ->setRecordsCount(count($records))
            ->setPerPageLimit(10)
            ->setMaxPageCount(10)
            ->getPages();

        View::renderTemplate('/Admin/Session/index.html', [
            'records' => array_slice($records, ($paginator->getCurrentPage() - 1) * $paginator->getPerPageLimit(), $paginator->getPerPageLimit()),
            'pages' => $pages,
        ]);
    }

    public function addAction()
    {
        View::renderTemplate('/Admin/Session/add.html');
    }

    public function editAction()
    {
        View::renderTemplate('/Admin/Session/edit.html', [
            'record' => Service::findOrFail($this->route_params['id'] ?? null)
        ]);
    }

    public function saveAction()
    {
        if (!Request::isPost()) {
            throw new \Exception("Method [${$_SERVER['REQUEST_METHOD']}] is not allowed");
        }

        $errors = $this->validate();

        $record = new Service($_POST);

        try {
            $record->image = $this->uploadImage($_FILES['image']);
        } catch (\Exception $exception) {
            $errors[] = $exception->getMessage();
        }

        if (count($errors) > 0) {
            foreach ($errors as $value) {
                Flash::addMessage($value, Flash::DANGER);
            }

            View::renderTemplate('/Admin/Session/add.html', [
                'post' => $_POST,
                'files' => $_FILES
            ]);
            return;
        }

        try {
            $record->save();
        } catch (\Exception $exception) {
            File::delete($record->image);
            throw $exception;
        }

        Flash::addMessage("Запись добавлена", Flash::SUCCESS);
        $this->redirect("/admin/session/index");
    }


    public function saveChangesAction()
    {
        if (!Request::isPost()) {
            throw new \Exception("Method [${$_SERVER['REQUEST_METHOD']}] is not allowed");
        }

        $id = $this->route_params['id'] ?? null;

        /** @var Service $record */
        $record = Service::findOrFail($id);

        $errors = $this->validate($id);

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

            foreach ($errors as $value) {
                Flash::addMessage($value, Flash::DANGER);
            }
            View::renderTemplate('/Admin/Session/edit.html', [
                'post' => $_POST,
                'files' => $_FILES,
                'record' => $record
            ]);
            return;
        }
        $record->description = htmlspecialchars($record->description);
        $record->update($_POST);

        Flash::addMessage("Изменения сохранены", Flash::SUCCESS);
        $this->redirect("/admin/session/index");

    }


    public function validate($id = null): array
    {
        $data = $_POST;
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = "Введите 'Название' услуги";
        }

        if (empty($data['price'])) {
            $errors[] =  "Введите 'Стоимость' услуги";
        }

        if (empty($data['duration'])) {
            $errors[] = "Введите 'Продолжительность' услуги";
        }

        if (empty($data['description'])) {
            $errors[] = "Введите 'Описание' услуги";
        }

        if (!is_numeric($data['price'])) {
            $errors[] = "Цена должна быть числом";
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

            $pathToImages = "/img/services/";

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
        /** @var Service $record */
        $record = Service::findOrFail($this->route_params['id'] ?? null);
        if ($record->delete()) {
            Flash::addMessage("Запись удалена", Flash::SUCCESS);
        } else {
            Flash::addMessage("Возникла ошибка удаления записи. Обратитесь к разработчику", Flash::DANGER);
        }

        $this->redirect("/admin/session/index");
    }

}