<?php

namespace App\Controllers;

use Core\Controller;
use \Core\View;
use \App\Models\Service;
use \App\Models\FacebookComment;

class Home extends Controller
{

    public function indexAction()
    {
        View::renderTemplate('Home/index.html',[
            "services" => Service::orderBy('id', 'desc')->get(),
            "facebook_posts" => FacebookComment::orderBy('id', 'desc')->get(),
        ]);
    }

}
