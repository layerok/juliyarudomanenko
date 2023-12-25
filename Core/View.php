<?php

namespace Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

/**
 * View
 *
 * PHP version 7.0
 */
class View
{

    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new FilesystemLoader([
                dirname(__DIR__) . '/App/Views'
            ], getcwd());
            $twig = new Environment($loader);
            $twig->addFilter(new TwigFilter('html_entity_decode', 'html_entity_decode'));
            $twig->addExtension(new \Twig_Extensions_Extension_Text());
            $twig->addGlobal('current_admin',\App\Auth::getAdmin());
            $twig->addGlobal('flash_messages',\App\Flash::getMessages());
        }

        echo $twig->render($template, $args);
    }
}
