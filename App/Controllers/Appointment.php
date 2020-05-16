<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Service;

/**
 * Appointment controller
 *
 * PHP version 7.0
 */
class Appointment extends \Core\Controller
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
        View::renderTemplate('Appointment/index.html',[
            "services" => $services
            ]
        );
    }

     /**
     * Show the appointment page
     *
     * @return void
     */
    public function showAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $service = new Service();
            $service = $service->getService($id);
            View::renderTemplate('Appointment/show.html',[
                'service'=> $service
            ]
            
        );
        }
        
    }
}