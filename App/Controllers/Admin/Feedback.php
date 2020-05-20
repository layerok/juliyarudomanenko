<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Message;

class Feedback extends Authenticated
{

    public function indexAction()
    {
        $records = Message::getAll();
        View::renderTemplate('/admin/feedback/index.html',[
            'records' => $records
        ]);
    }

}