<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Post;
use \App\Flash;
use \App\Paginator;
use \App\Request;

class Publishment extends Authenticated
{
    public $per_page_limit = 10;
    public $max_page_count = 10;

    public function indexAction()
    {
        $page = isset($this->route_params['page']) ? (int)$this->route_params['page'] : 1;
        $records = Post::getAll();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($page)
                                    ->setRecordsCount(count($records))
                                    ->setPerPageLimit($this->per_page_limit)
                                    ->setMaxPageCount($this->max_page_count)
                                    ->getPages();

        $updatedRecords = array_slice($records, ($page-1)* $this->per_page_limit, $this->per_page_limit);
         
        View::renderTemplate('/Admin/Publishment/index.html',[
            'records' => $updatedRecords,
            'pages' => $pages
        ]);
    }

    public function addAction()
    {
        View::renderTemplate('/Admin/Publishment/add.html');
    }

    public function editAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $post = new Post(); 
            $post = $post->getOne($id);
            
            View::renderTemplate('/Admin/Publishment/edit.html',[
                'record' => $post
            ]);
        }else{
            throw new \Exception("Id is not specified",404);
        }
    }

    public function saveAction()
    {
        if(Request::isPost()){
            
            
            $post = new Post($_POST,$_FILES); 
            
            if($post->save()){
                Flash::addMessage("Запись добавлена",Flash::SUCCESS);
                $this->redirect("/admin/publishment/index");
            }else{

                foreach($post->errors as $value){
                    Flash::addMessage($value,Flash::DANGER);
                }
                View::renderTemplate('/Admin/Publishment/add.html',[
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
                $post = new Post(array_merge($_POST, ['id'=> $id]),$_FILES); 
                
                if($post->saveChanges($id)){
                    Flash::addMessage("Изменения сохранены",Flash::SUCCESS);
                    $this->redirect("/admin/publishment/index");
                }else{

                    foreach($post->errors as $value){
                        Flash::addMessage($value,Flash::DANGER);
                    }
                    $post->id = $id;
                    View::renderTemplate('/Admin/Publishment/edit.html',[
                        'post' => $_POST,
                        'files' => $_FILES,
                        'record' => $post
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
            
            $post = new Post(); 
            if($post->delete($id)){
                Flash::addMessage("Запись удалена",Flash::SUCCESS);
                $this->redirect("/admin/publishment/index");
            }else{
                Flash::addMessage("Возникла ошибка удаления записи. Обратитесь к разработчику",Flash::DANGER);
                $this->redirect("/admin/publishment/index");
            }
        }else{
            throw new \Exception("Id is not specified",404);
        }
    }

    public function checkSlugAction(){

        $id = $_POST['id'] ?? null;
        
        $slug = $_POST['slug'];
        $data = ['response' => 'error'];
        if(!Post::slugExists($slug, $id)){
            $data = ['response' => 'success'];
        }
        echo json_encode($data);
        
    }

}