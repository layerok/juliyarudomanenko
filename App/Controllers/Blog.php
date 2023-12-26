<?php

namespace App\Controllers;

use Core\Controller;
use \Core\View;
use \App\Models\Post;
use \App\Paginator;


class Blog extends Controller
{
    public function indexAction()
    {
        $records = Post::orderBy('id', 'desc')->get()->toArray();

        $paginator = new Paginator();
        $pages = $paginator->setCurrentPage($this->route_params['page'] ?? 1)
            ->setRecordsCount(count($records))
            ->setPerPageLimit(4)
            ->setMaxPageCount(10)
            ->getPages();

        View::renderTemplate('/Blog/index.html', [
            'records' => array_slice($records, ($paginator->getCurrentPage() - 1) * $paginator->getPerPageLimit(), $paginator->getPerPageLimit()),
            'pages' => $pages
        ]);
    }

    public function showAction()
    {
        if (!($slug = $this->route_params['id'] ?? null)) {
            throw new \Exception("There is no such record", 404);
        }

        if (!$post = Post::where('slug', '=', $slug)->first()) {
            throw new \Exception("There is no such record", 404);
        }

        View::renderTemplate('Blog/show.html', [
            'record' => $post
        ]);
    }
}