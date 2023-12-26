<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Appointment;
use \App\Flash;
use \App\Paginator;

class Home extends Authenticated
{
    public function indexAction()
    {
        $records = Appointment::with(['service', 'customer'])->get()->toArray();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($this->route_params['page'] ?? 1)
            ->setRecordsCount(count($records))
            ->setPerPageLimit(10)
            ->setMaxPageCount(10)
            ->getPages();

        View::renderTemplate('/Admin/Home/index.html', [
            'appointments' => array_slice($records, ($paginator->getCurrentPage() - 1) * $paginator->getPerPageLimit(), $paginator->getPerPageLimit()),
            'pages' => $pages,
        ]);
    }

    public function deleteAction()
    {
        if (Appointment::findOrFail($this->route_params['id'] ?? null)->delete()) {
            Flash::addMessage("Запись удалена", Flash::SUCCESS);
        } else {
            Flash::addMessage("Возникла ошибка удаления записи. Обратитесь к разработчику", Flash::DANGER);
        }
        $this->redirect("/admin");
    }

}