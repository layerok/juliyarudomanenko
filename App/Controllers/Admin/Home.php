<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Appointment;
use \App\Flash;

class Home extends Authenticated
{

    public function indexAction()
    {
        
        $appointments = Appointment::getAll(); 
        View::renderTemplate('/admin/home/index.html',[
            'appointments' => $appointments
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