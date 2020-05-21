<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Certificate; 
use \App\Paginator;

class Certification extends \Core\Controller
{
    public $per_page_limit = 10;
    public $max_page_count = 5;

    public function indexAction()
    {
        $page = isset($this->route_params['page']) ? (int)$this->route_params['page'] : 1;
        $records = Certificate::getAll();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($page)
                                    ->setRecordsCount(count($records))
                                    ->setPerPageLimit($this->per_page_limit)
                                    ->setMaxPageCount($this->max_page_count)
                                    ->getPages();

        $updatedRecords = array_slice($records, ($page-1)* $this->per_page_limit, $this->per_page_limit);
        $records = Certificate::getAll();
        View::renderTemplate('/Certificate/index.html',[
            'records' => $updatedRecords,
            'pages' => $pages
        ]);
    }

}