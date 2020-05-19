<?php

namespace App\Controllers\Admin;

use \Core\View;

class Home extends Authenticated
{

    public function indexAction()
    {
        View::renderTemplate('/admin/home/index.html');
    }

}