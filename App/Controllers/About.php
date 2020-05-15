<?php

namespace App\Controllers;

use \Core\View;

/**
 * About controller
 *
 * PHP version 7.0
 */
class About extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('About/index.html');
    }
}