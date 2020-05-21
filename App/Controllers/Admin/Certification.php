<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Certificate; 
use \App\Request;
use \App\Flash;
use \App\Paginator;

class Certification extends Authenticated
{
    public $per_page_limit = 10;
    public $max_page_count = 5;
    
    public function indexAction()
    {

        $page = isset($this->route_params['page']) ? (int)$this->route_params['page'] : 1;
        $records = Certificate::getAll();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($page)
                                    ->setRecordsCount(count($records))
                                    ->setPerPageLimit($this->per_page_limit)
                                    ->setMaxPageCount($this->max_page_count)
                                    ->getPages();

        $updatedRecords = array_slice($records, ($page-1)* $this->per_page_limit, $this->per_page_limit);
        
        
        View::renderTemplate('/admin/certificate/index.html',[
            'records' => $updatedRecords,
            'pages' => $pages
        ]);
    }

    public function addAction()
    {
        View::renderTemplate('/admin/certificate/add.html');
    }

    public function editAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $record = new Certificate(); 
            $record = $record->getOne($id);
            
            View::renderTemplate('/admin/certificate/edit.html',[
                'record' => $record
            ]);
        }else{
            throw new \Exception("Id is not specified",404);
        }
    }

    public function saveAction()
    {
        if(Request::isPost()){
            
            
            $record = new Certificate($_POST, $_FILES); 
            
            if($record->save()){
                Flash::addMessage("Запись добавлена",Flash::SUCCESS);
                $this->redirect("/admin/certification/index");
            }else{

                foreach($record->errors as $value){
                    Flash::addMessage($value,Flash::DANGER);
                }
                View::renderTemplate('/admin/certificate/add.html',[
                    'post' => $_POST,
                    'files' => $_FILES
                ]);
                
            } 
        }
    }
    public function saveChangesAction()
    {
        if(Request::isPost()){
            
            $id = $this->route_params['id'] ?? null;
            if(isset($id)){
                $record = new Certificate($_POST, $_FILES); 
                
                if($record->saveChanges($id)){
                    Flash::addMessage("Изменения сохранены",Flash::SUCCESS);
                    $this->redirect("/admin/certification/index");
                }else{

                    foreach($record->errors as $value){
                        Flash::addMessage($value,Flash::DANGER);
                    }
                    $record->id = $id;
                    View::renderTemplate('/admin/certificate/edit.html',[
                        'post' => $_POST,
                        'files' => $_FILES,
                        'record' => $record
                    ]);
                }
            } else{
                throw new \Exception("Id is not specified",404);
            }
            
        }
    }
    public function deleteAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $record = new Certificate(); 
            if($record->delete($id)){
                Flash::addMessage("Запись удалена",Flash::SUCCESS);
            }else{
                Flash::addMessage("Возникла ошибка удаления записи. Обратитесь к разработчику",Flash::DANGER);
            }
            $this->redirect("/admin/certification/index");
        }else{
            throw new \Exception("Id is not specified",404);
        }
    }

}