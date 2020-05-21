<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Certificate; 

class Certification extends \Core\Controller
{

    public function indexAction()
    {
        $records = Certificate::getAll();
        View::renderTemplate('/Certificate/index.html',[
            'records' => $records
        ]);
    }

}