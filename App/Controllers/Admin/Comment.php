<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\FacebookComment as FacebookCommentModel;
use \App\Request;
use \App\Flash;
use \App\Paginator;

class Comment extends Authenticated
{
    public $per_page_limit = 10;
    public $max_page_count = 10;

    public function indexAction()
    {
        $page = isset($this->route_params['page']) ? (int)$this->route_params['page'] : 1;
        $records = FacebookCommentModel::getAll();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($page)
                                    ->setRecordsCount(count($records))
                                    ->setPerPageLimit($this->per_page_limit)
                                    ->setMaxPageCount($this->max_page_count)
                                    ->getPages();

        $updatedRecords = array_slice($records, ($page-1)* $this->per_page_limit, $this->per_page_limit);

        View::renderTemplate('/Admin/Facebook-comment/index.html',[
            'records' => $updatedRecords,
            'pages' => $pages
        ]);
    }

    public function addAction()
    {
        View::renderTemplate('/Admin/Facebook-comment/add.html');
    }

    public function editAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $record = new FacebookCommentModel(); 
            $record = $record->getOne($id);
            
            View::renderTemplate('/Admin/Facebook-comment/edit.html',[
                'record' => $record
            ]);
        }else{
            throw new \Exception("Id is not specified",404);
        }
    }

    public function saveAction()
    {
        if(Request::isPost()){
            
            
            $record = new FacebookCommentModel($_POST); 
            
            if($record->save()){
                Flash::addMessage("Комментарий добавлен",Flash::SUCCESS);
                $this->redirect("/admin/comment/index");
            }else{

                foreach($record->errors as $value){
                    Flash::addMessage($value,Flash::DANGER);
                }
                View::renderTemplate('/Admin/Facebook-comment/add.html',[
                    'post' => $_POST
                ]);
                
            } 
        }
    }
    public function saveChangesAction()
    {
        if(Request::isPost()){
            
            $id = $this->route_params['id'] ?? null;
            if(isset($id)){
                $record = new FacebookCommentModel($_POST); 
                
                if($record->saveChanges($id)){
                    Flash::addMessage("Изменения сохранены",Flash::SUCCESS);
                    $this->redirect("/admin/comment/index");
                }else{

                    foreach($record->errors as $value){
                        Flash::addMessage($value,Flash::DANGER);
                    }
                    $record->id = $id;
                    View::renderTemplate('/Admin/Facebook-comment/edit.html',[
                        'post' => $_POST,
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
            
            $record = new FacebookCommentModel(); 
            if($record->delete($id)){
                Flash::addMessage("Комментарий удален",Flash::SUCCESS);
                $this->redirect("/Admin/Comment/index");
            }else{
                Flash::addMessage("Возникла ошибка удаления записи. Обратитесь к разработчику",Flash::DANGER);
                $this->redirect("/admin/comment/index");
            }
        }else{
            throw new \Exception("Id is not specified",404);
        }
    }

}