<?php

namespace App\Controllers;

use \Core\View;
use \App\Request;
use \App\Models\Message;

/**
 * About controller
 *
 * PHP version 7.0
 */
class About extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('About/index.html');
    }


    public function contactAction()
    {
        View::renderTemplate('About/contact.html');
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
                View::renderTemplate('About/contact.html',[
                    "errors" => $message->errors,
                    "post_data" => $_POST
                ]);

            }

        }
        
            
    }
}