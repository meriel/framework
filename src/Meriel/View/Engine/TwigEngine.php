<?php
/*
 * This file is part of the Meriel package.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meriel\View\Engine;

use Config;

class TwigEngine implements EngineInterface
{

    protected $engine;

    public function __construct()
    {
        $view_config = Config::get('view');

        $twig_loader = new \Twig_Loader_Filesystem($view_config['paths']);
        $twig = new \Twig_Environment($twig_loader);

        $function = new \Twig\TwigFunction('url', function ($path = '') {
            return url($path);
        });
        $twig->addFunction($function);

        $this->engine = $twig;
    }


    public function addFunction(\Twig\TwigFunction $function)
    {
        $this->engine->addFunction($function);
    }

    public function addGlobal($key, $data)
    {
        $this->engine->addGlobal($key, $data);
    }

    public function addFilter(\Twig\TwigFilter $filter)
    {
        $this->engine->addFilter($filter);
    }

    public function getEngine()
    {
        return $this->engine;

    }


    public function render($template, $data)
    {
        return $this->engine->render($template, $data);
    }

}