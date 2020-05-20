<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Message;
use \App\Flash;

class Feedback extends Authenticated
{

    public function indexAction()
    {
        $records = Message::getAll();
        View::renderTemplate('/admin/feedback/index.html',[
            'records' => $records
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