<?php

namespace App\Controllers;

use \Core\View;

class Hub extends Authenticated
{

    public function indexAction()
    {
        View::renderTemplate('admin/hub/index.html');
    }

}