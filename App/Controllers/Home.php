<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Service;
use \App\Models\Message;
use \App\Request;

use \App\Models\FacebookComment;
/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{


    public function before(){
        $this->facebook_comment = new FacebookComment();
        $this->facebook_posts = $this->facebook_comment->getAll();
        $this->service = new Service();
        $this->services = $this->service->getAll();
    }
    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Home/index.html',[
            "services" => $this->services,
            "facebook_posts" => $this->facebook_posts
            ]);
    }
    
    

    /**
     * Send message
     *
     * @return void
     */
    public function sendAction()
    {
        if(Request::isPost()){
            $message = new Message($_POST);
            $message->save();
            if(Request::isAjax()){
                $response = [
                    "errors" => $message->errors
                ];
                echo json_encode($response);

            }else{
                View::renderTemplate('Home/index.html',[
                    "services" => $this->services,
                    "facebook_comments" => $this->facebook_comments,
                    "errors" => $message->errors,
                    "post_data" => $_POST
                ]);

            }

        }
        
            
    }
}
