<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Message;
use \App\Flash;
use \App\Paginator;

class Feedback extends Authenticated
{
    public $per_page_limit = 10;
    public $max_page_count = 10;

    public function indexAction()
    {
        $page = isset($this->route_params['page']) ? (int)$this->route_params['page'] : 1;
        $records = Message::getAll();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($page)
                                    ->setRecordsCount(count($records))
                                    ->setPerPageLimit($this->per_page_limit)
                                    ->setMaxPageCount($this->max_page_count)
                                    ->getPages();

        $updatedRecords = array_slice($records, ($page-1)* $this->per_page_limit, $this->per_page_limit);

        View::renderTemplate('/admin/feedback/index.html',[
            'records' => $updatedRecords,
            'pages' => $pages
        ]);
    }
    public function deleteAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $record = new Message(); 
            if($record->delete($id)){
                Flash::addMessage("Сообщение удалено",Flash::SUCCESS);
            }else{
                Flash::addMessage("Возникла ошибка удаления сообщения. Обратитесь к разработчику",Flash::DANGER);
            }
            $this->redirect("/admin/feedback/index");
        }else{
            throw new \Exception("Id is not specified",404);
        }
    }

}