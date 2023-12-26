<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\CustomerMessage;
use \App\Flash;
use \App\Paginator;

class Feedback extends Authenticated
{
    public function indexAction()
    {
        $records = CustomerMessage::with('customer')->get()->toArray();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($this->route_params['page'] ?? 1)
            ->setRecordsCount(count($records))
            ->setPerPageLimit(10)
            ->setMaxPageCount(10)
            ->getPages();

        View::renderTemplate('/Admin/Feedback/index.html', [
            'records' => array_slice($records, ($paginator->getCurrentPage() - 1) * $paginator->getPerPageLimit(), $paginator->getPerPageLimit()),
            'pages' => $pages,
        ]);
    }

    public function deleteAction()
    {
        /** @var CustomerMessage $record */
        $record = CustomerMessage::findOrFail($this->route_params['id'] ?? null);

        if ($record->delete()) {
            Flash::addMessage("Сообщение удалено", Flash::SUCCESS);
        } else {
            Flash::addMessage("Возникла ошибка удаления сообщения. Обратитесь к разработчику", Flash::DANGER);
        }

        $this->redirect("/admin/feedback/index");
    }

}