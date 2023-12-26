<?php

namespace App\Controllers;

use App\Auth;
use App\Request;
use \App\Models\Message as MessageModel;
use \Core\Controller;


class Message extends Controller
{
    public function editAction()
    {
        if (Auth::getAdmin() && Request::isPost()) {
            foreach ($_POST as $name => $content) {
                /** @var MessageModel $message */
                $message = MessageModel::where('name', '=',$name)->first();

                if($message) {
                    $message->content = $content;
                    $message->save();
                } else {
                    $message = new MessageModel();
                    $message->name = $name;
                    $message->content = $content;
                    $message->save();
                }
            }
        }
    }
}