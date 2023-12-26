<?php

namespace App\Controllers\Admin;

use App\Support\File;
use \Core\View;
use \App\Models\Certificate;
use \App\Request;
use \App\Flash;
use \App\Paginator;
use Verot\Upload\Upload;

class Certification extends Authenticated
{
    public function indexAction()
    {
        $records = Certificate::all()->toArray();

        $paginator = new Paginator();

        $paginator->setCurrentPage($this->route_params['page'] ?? 1)
            ->setRecordsCount(count($records))
            ->setPerPageLimit(10)
            ->setMaxPageCount(10);

        View::renderTemplate('/Admin/Certificate/index.html', [
            'records' => array_slice($records, ($paginator->getCurrentPage() - 1) * $paginator->getPerPageLimit(), $paginator->getPerPageLimit()),
            'pages' => $paginator->getPages(),
        ]);
    }

    public function addAction()
    {
        View::renderTemplate('/Admin/Certificate/add.html');
    }

    public function editAction()
    {
        View::renderTemplate('/Admin/Certificate/edit.html', [
            'record' => Certificate::findOrFail($this->route_params['id'] ?? null)
        ]);
    }

    public function saveAction()
    {
        if (!Request::isPost()) {
            throw new \Exception("Method [${$_SERVER['REQUEST_METHOD']}] is not allowed");
        }

        $errors = $this->validate($_POST);


        $record = new Certificate($_POST);

        try {
            $record->image = $this->uploadImage($_FILES['image']);
        } catch (\Exception $exception) {
            $errors[] = $exception->getMessage();
        }

        if (count($errors) > 0) {
            foreach ($errors as $value) {
                Flash::addMessage($value, Flash::DANGER);
            }

            View::renderTemplate('/Admin/Certificate/add.html', [
                'post' => $_POST,
                'files' => $_FILES
            ]);
            return;
        }

        $record->save();
        Flash::addMessage("Запись добавлена", Flash::SUCCESS);
        $this->redirect("/admin/certification/index");

    }


    public function saveChangesAction()
    {
        if (!Request::isPost()) {
            throw new \Exception("Method [${$_SERVER['REQUEST_METHOD']}] is not allowed");
        }

        /** @var Certificate $record */
        $record = Certificate::findOrFail($this->route_params['id'] ?? null);

        $errors = $this->validate($_POST);

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
            View::renderTemplate('/Admin/Certificate/edit.html', [
                'post' => $_POST,
                'files' => $_FILES,
                'record' => $record
            ]);
            return;
        }

        $record->update($_POST);

        Flash::addMessage("Изменения сохранены", Flash::SUCCESS);
        $this->redirect("/admin/certification/index");

    }

    public function uploadImage($image): string
    {
        $handle = new Upload($image, 'ru_RU');
        $handle->allowed = array('image/*');

        if ($handle->uploaded) {
            $handle->image_resize = true;
            $handle->image_ratio_y = true;
            $handle->image_x = 360;

            $pathToImages = '/img/certificates/';

            $handle->process($_SERVER['DOCUMENT_ROOT'] . $pathToImages);

            if (!$handle->processed) {
                throw new \Exception($handle->error);
            }


            return $pathToImages . $handle->file_dst_name;
        }
        throw new \Exception($handle->error);
    }

    public function validate($data = []): array
    {
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = "Введите 'Название' Сертификата";
        }
        return $errors;
    }

    public function deleteAction()
    {
        /** @var Certificate $record */
        $record = Certificate::findOrFail($this->route_params['id'] ?? null);
        if ($record->delete()) {
            Flash::addMessage("Запись удалена", Flash::SUCCESS);
        } else {
            Flash::addMessage("Возникла ошибка удаления записи. Обратитесь к разработчику", Flash::DANGER);
        }
        $this->redirect("/admin/certification/index");
    }

}