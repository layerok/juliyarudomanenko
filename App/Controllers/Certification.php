<?php

namespace App\Controllers;

use Core\Controller;
use \Core\View;
use \App\Models\Certificate;
use \App\Paginator;

class Certification extends Controller
{

    public function indexAction()
    {
        $records = Certificate::all()->toArray();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($this->route_params['page'] ?? 1)
            ->setRecordsCount(count($records))
            ->setPerPageLimit(10)
            ->setMaxPageCount(5)
            ->getPages();

        View::renderTemplate('/Certificate/index.html', [
            'records' => array_slice($records, ($paginator->getCurrentPage() - 1) * $paginator->getPerPageLimit(), $paginator->getPerPageLimit()),
            'pages' => $pages,
        ]);
    }

}