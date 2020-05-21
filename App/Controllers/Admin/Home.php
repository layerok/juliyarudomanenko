<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Appointment;
use \App\Flash;
use \App\Paginator;

class Home extends Authenticated
{
    public $per_page_limit = 10;
    public $max_page_count = 10;

    public function indexAction()
    {
        $page = isset($this->route_params['page']) ? (int)$this->route_params['page'] : 1;
        $records = Appointment::getAll();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($page)
                                    ->setRecordsCount(count($records))
                                    ->setPerPageLimit($this->per_page_limit)
                                    ->setMaxPageCount($this->max_page_count)
                                    ->getPages();

        $updatedRecords = array_slice($records, ($page-1)* $this->per_page_limit, $this->per_page_limit);
         
        View::renderTemplate('/admin/home/index.html',[
            'appointments' => $updatedRecords,
            'pages' => $pages
        ]);
    }

    public function deleteAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $record = new Appointment(); 
            if($record->delete($id)){
                Flash::addMessage("Запись удалена",Flash::SUCCESS);
            }else{
                Flash::addMessage("Возникла ошибка удаления записи. Обратитесь к разработчику",Flash::DANGER);
            }
            $this->redirect("/admin");
        }else{
            throw new \Exception("Id is not specified",404);
        }
    }

}