<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Service;
use \App\Request;

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
            $service = $service->getOne($id);
            $service->description = htmlspecialchars_decode($service->description);
            
            if($service){
                View::renderTemplate('Appointment/show.html',[
                    'service'=> $service
                ]);
            }else{
                throw new \Exception("There is no such record",404);
            }
            
            
        
        }else{
            throw new \Exception("Id is not specified",404);
        }
        
    }
    /**
     * Show close the deal page
     *
     * @return void
     */
    public function closeTheDealAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $service = new Service();
            $service = $service->getOne($id);
            if($service){
                View::renderTemplate('Appointment/close-the-deal.html',[
                    'service'=> $service
                ]);
            }else{
                throw new \Exception("There is no such record",404);
            }
        }else{
            throw new \Exception("Id is not specified",404);
        }
        
    }
    /**
     * Send appointment form
     *
     * @return void
     */
    public function sendAction()
    {
        $id = $this->route_params['id'] ?? null;
        if(isset($id)){
            
            $service = new Service();
            $service = $service->getOne($id);
            
            if($service){

                $_POST['service_id'] = $id;
                
                if(Request::isPost()){
                    $appointment = new \App\Models\Appointment($_POST);
                    $appointment->save();
                    if(Request::isAjax()){
                        
                        $response = [
                            "errors" => $appointment->errors
                        ];
                        echo json_encode($response);
        
                    }else{
                        if(empty($appointment->errors)){
                            $this->redirect("/appointment/thank-you");
                        }else{
                            View::renderTemplate('Appointment/close-the-deal.html',[
                                "service" => $service,
                                "errors" => $appointment->errors,
                                "post_data" => $_POST
                            ]);
                        }
                        
        
                    }
        
                }
                
            }else{
                throw new \Exception("There is no such record",404);
            }
        }else{
            throw new \Exception("Id is not specified",404);
        }
        
    }
    /**
     * show thank you page
     *
     * @return void
     */
    public function thankYouAction()
    {
        View::renderTemplate('Appointment/thank-you.html');
    }
    
}