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
        $fb_posts = FacebookComment::all()->toArray();
        View::renderTemplate('Home/index.html',[
            "services" => Service::all(),
            "facebook_posts" => $fb_posts,
        ]);
    }

}
