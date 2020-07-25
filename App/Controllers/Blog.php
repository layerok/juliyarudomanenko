<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Post;
use \App\Flash;
use \App\Paginator;
use \App\Request;

class Blog extends \Core\Controller
{
    public $per_page_limit = 4;
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
         
        View::renderTemplate('/Blog/index.html',[
            'records' => $updatedRecords,
            'pages' => $pages
        ]);
    }

    /**
     * Show the blog post
     *
     * @return void
     */
    public function showAction()
    {
        
        $slug = $this->route_params['id'] ?? null;
        if(isset($slug)){
            
            
            $post = Post::findBySlug($slug);
            
            
            if($post){
                View::renderTemplate('Blog/show.html',[
                    'record'=> $post
                ]);
            }else{
                throw new \Exception("There is no such record",404);
            }
            
            
        
        }else{
            throw new \Exception("Slug is not specified",404);
        }
        
    }
}