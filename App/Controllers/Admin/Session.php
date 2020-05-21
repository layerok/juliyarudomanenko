<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Service;
use \App\Request;
use \App\Flash;
use \App\Paginator;

class Session extends Authenticated
{
    public $per_page_limit = 10;
    public $max_page_count = 10;

    public function indexAction()
    {
        $page = isset($this->route_params['page']) ? (int)$this->route_params['page'] : 1;
        $records = Service::getAll();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($page)
                                    ->setRecordsCount(count($records))
                                    ->setPerPageLimit($this->per_page_limit)
                                    ->setMaxPageCount($this->max_page_count)
                                    ->getPages();

        $updatedRecords = array_slice($records, ($page-1)* $this->per_page_limit, $this->per_page_limit);

        View::renderTemplate('/Admin/Session/index.html',[
            'records' => $updatedRecords,
            'pages' => $pages
        ]);
    }

    public function addAction()
    {
        View::renderTemplate('/Admin/Session/add.html');
    }

    public function editAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $service = new Service(); 
            $service = $service->getOne($id);
            
            View::renderTemplate('/Admin/Session/edit.html',[
                'record' => $service
            ]);
        }else{
            throw new \Exception("Id is not specified",404);
        }
    }

    public function saveAction()
    {
        if(Request::isPost()){
            
            
            $service = new Service($_POST, $_FILES); 
            
            if($service->save()){
                Flash::addMessage("Услуга добавлена",Flash::SUCCESS);
                $this->redirect("/admin/session/index");
            }else{

                foreach($service->errors as $value){
                    Flash::addMessage($value,Flash::DANGER);
                }
                View::renderTemplate('/Admin/Session/add.html',[
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
                $service = new Service($_POST, $_FILES); 
                
                if($service->saveChanges($id)){
                    Flash::addMessage("Изменения сохранены",Flash::SUCCESS);
                    $this->redirect("/admin/session/index");
                }else{

                    foreach($service->errors as $value){
                        Flash::addMessage($value,Flash::DANGER);
                    }
                    $service->id = $id;
                    View::renderTemplate('/Admin/Session/edit.html',[
                        'post' => $_POST,
                        'files' => $_FILES,
                        'record' => $service
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
            
            $service = new Service(); 
            if($service->delete($id)){
                Flash::addMessage("Услуга удалена",Flash::SUCCESS);
                $this->redirect("/admin/session/index");
            }else{
                Flash::addMessage("Возникла ошибка удаления услуги. Обратитесь к разработчику",Flash::DANGER);
                $this->redirect("/admin/session/index");
            }
        }else{
            throw new \Exception("Id is not specified",404);
        }
    }

}