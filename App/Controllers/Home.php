<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Service;
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
        $service = new Service();
        $services = $service->getAll();
        View::renderTemplate('Home/index.html',[
            "services" => $services
            ]);
    }
}
