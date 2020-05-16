<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Service;
use \App\Models\FacebookComment;
/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $facebook_comment = new FacebookComment();
        $facebook_comments = $facebook_comment->getAll();
        $service = new Service();
        $services = $service->getAll();
        View::renderTemplate('Home/index.html',[
            "services" => $services,
            "facebook_comments" => $facebook_comments
            ]);
    }
}
