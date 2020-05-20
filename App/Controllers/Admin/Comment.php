<?php

namespace App\Controllers\Admin;

use \Core\View;
use \App\Models\FacebookComment as FacebookCommentModel;

class Comment extends Authenticated
{

    public function indexAction()
    {
        $records = FacebookCommentModel::getAll();
        View::renderTemplate('/admin/facebook-comment/index.html',[
            'records' => $records
        ]);
    }

}