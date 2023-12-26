<?php

namespace App\Controllers;

use Core\Controller;
use \Core\View;
use \App\Models\Service;
use \App\Models\CustomerMessage;
use \App\Request;
use \App\Models\FacebookComment;

class Home extends Controller
{

    public function indexAction()
    {
        $fb_posts = FacebookComment::all()->toArray();
        View::renderTemplate('Home/index.html',[
            "services" => Service::all(),
            "facebook_posts" => $fb_posts,
        ]);
    }

    public function sendAction()
    {
        if(!Request::isPost()){
           return;
        }
        $message = new CustomerMessage($_POST);
        $message->save();

        if(Request::isAjax()){
            $response = [
                "errors" => []
            ];
            echo json_encode($response);
        }else{
            View::renderTemplate('Home/index.html',[
                "services" => Service::all(),
                "facebook_comments" => FacebookComment::all()->toArray(),
                "errors" => [],
                "post_data" => $_POST
            ]);
        }
    }
}
