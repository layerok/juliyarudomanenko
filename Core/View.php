<?php

namespace Core;

use App\Auth;
use App\Config;
use App\Flash;
use App\Models\Message;
use App\Models\Message as MessageModel;
use Twig\Environment;
use Twig\Extra\String\StringExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

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
     * @param string $view The view file
     * @param array $args Associative array of data to display in the view (optional)
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
     * @param string $template The template file
     * @param array $args Associative array of data to display in the view (optional)
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
            $twig->addFunction(new TwigFunction('msg', function ($name, $default = null) {
                /** @var MessageModel $message */
                $message = MessageModel::where('name', '=', $name)->first();
                if(!$message) {
                    $model = new MessageModel();
                    $model->name = $name;
                    $model->content = $default;
                    $model->save();
                }
                return $message->content ?? $default;
            }));

            function data_get($data, $key)
            {
                $result = $data;

                $parts = explode('.', $key);

                foreach ($parts as $part) {
                    if (isset($result[$part])) {
                        $result = $result[$part];
                    } else {
                        return null;
                    }
                }
                return $result;
            }

            $twig->addFunction(new TwigFunction('trans', function ($key) {

                $paths = [
                    dirname(__DIR__) . '/lang/' . Config::LOCALE . '.php',
                    dirname(__DIR__) . '/lang/' . Config::FALLBACK_LOCALE . '.php',
                ];

                foreach($paths as $path) {
                    if (file_exists($path)) {
                        $source = require $path;
                        if($translation = data_get($source, $key)) {
                            return $translation;
                        }
                    }
                }

                return null;
            }));
            $twig->addExtension(new StringExtension());
            $twig->addGlobal('current_admin', Auth::getAdmin());
            $twig->addGlobal('flash_messages', Flash::getMessages());
        }

        echo $twig->render($template, $args);
    }
}
