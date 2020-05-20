<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\Service;

class Session extends Authenticated
{

    public function indexAction()
    {
        $services = \App\Models\Service::getAll();
        View::renderTemplate('/admin/session/index.html',[
            'records' => $services
        ]);
    }

}