<?php

namespace App\Controllers\Admin;

use \Core\View;

class Service extends Authenticated
{

    public function indexAction()
    {
        View::renderTemplate('/admin/service/index.html');
    }

}