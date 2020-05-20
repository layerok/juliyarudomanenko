<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Appointment;

class Home extends Authenticated
{

    public function indexAction()
    {
        
        $appointments = Appointment::getAll(); 
        View::renderTemplate('/admin/home/index.html',[
            'appointments' => $appointments
        ]);
    }

}