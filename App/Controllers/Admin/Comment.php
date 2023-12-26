<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\FacebookComment;
use \App\Request;
use \App\Flash;
use \App\Paginator;

class Comment extends Authenticated
{
    public function indexAction()
    {
        $records = FacebookComment::all()->toArray();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($this->route_params['page'] ?? 1)
            ->setRecordsCount(count($records))
            ->setPerPageLimit(10)
            ->setMaxPageCount(10)
            ->getPages();

        View::renderTemplate('/Admin/Facebook-comment/index.html', [
            'records' => array_slice($records, ($paginator->getCurrentPage() - 1) * $paginator->getPerPageLimit(), $paginator->getPerPageLimit()),
            'pages' => $pages,

        ]);
    }

    public function addAction()
    {
        View::renderTemplate('/Admin/Facebook-comment/add.html');
    }

    public function editAction()
    {
        View::renderTemplate('/Admin/Facebook-comment/edit.html', [
            'record' => FacebookComment::findOrFail($this->route_params['id'] ?? null)
        ]);
    }

    public function validate($id = null): array
    {
        $errors = [];
        $data = $_POST;
        if (empty($data['link'])) {
            $errors[] = "Введите 'Ссылку' комментария";
        }
        return $errors;
    }

    public function saveAction()
    {
        if (!Request::isPost()) {
            throw new \Exception('Only POST method is allowed');
        }

        $record = new FacebookComment($_POST);

        $errors = $this->validate();

        if (count($errors) > 0) {
            foreach ($errors as $value) {
                Flash::addMessage($value, Flash::DANGER);
            }
            View::renderTemplate('/Admin/Facebook-comment/add.html', [
                'post' => $_POST
            ]);
            return;
        }

        if ($record->save()) {
            Flash::addMessage("Комментарий добавлен", Flash::SUCCESS);
            $this->redirect("/admin/comment/index");
        }
    }

    public function saveChangesAction()
    {
        if (!Request::isPost()) {
            throw new \Exception('Only POST method is allowed');
        }

        if (!($id = $this->route_params['id'] ?? null)) {
            throw new \Exception("Id is not specified", 404);
        }

        $record = FacebookComment::find($id);

        $errors = $this->validate($id);

        if(count($errors) > 0) {
            foreach ($errors as $value) {
                Flash::addMessage($value, Flash::DANGER);
            }
            View::renderTemplate('/Admin/Facebook-comment/edit.html', [
                'post' => $_POST,
                'record' => $record
            ]);
            return;
        }

        if ($record->update($_POST)) {
            Flash::addMessage("Изменения сохранены", Flash::SUCCESS);
            $this->redirect("/admin/comment/index");
        }
    }

    public function deleteAction()
    {
        if (FacebookComment::findOrFail($this->route_params['id'] ?? null)->delete()) {
            Flash::addMessage("Комментарий удален", Flash::SUCCESS);
        } else {
            Flash::addMessage("Возникла ошибка удаления записи. Обратитесь к разработчику", Flash::DANGER);
        }

        $this->redirect("/admin/comment/index");
    }

}