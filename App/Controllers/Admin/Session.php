<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Service;
use \App\Request;
use \App\Flash;

class Session extends Authenticated
{

    public function indexAction()
    {
        $services = Service::getAll();
        View::renderTemplate('/admin/session/index.html',[
            'records' => $services
        ]);
    }

    public function addAction()
    {
        View::renderTemplate('/admin/session/add.html');
    }

    public function editAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $service = new Service(); 
            $service = $service->getOne($id);
            
            View::renderTemplate('/admin/session/edit.html',[
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
                View::renderTemplate('/admin/session/add.html',[
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
                    View::renderTemplate('/admin/session/edit.html',[
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