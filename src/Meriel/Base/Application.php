<?php
/*
 * This file is part of the Meriel package.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meriel\Base;


use Meriel\Container\Container;

class Application extends Container
{

    public function __construct()
    {
    }

    public function run()
    {

        $dispatched = false;
        $routes = $this['router']->getRoutes();

        foreach ($routes as $route) {


            try {
                $dispatched = $route->run();
                if ($dispatched) {
                    break;
                }
            } catch (Exception $e) {
                continue;
            }
        }

        if (!$routes) {
            $this->notFound();

        } else {
            if (null === $dispatched) {
                $this['response']->setContent('The controler must return a response')->send();
            } else {


                $this['response']->setContent($dispatched)->send();
            }
        }
    }

    protected function notFound()
    {

        $this['response']->setStatusCode(404)->setContent($this['view']->make('404'))->send();
    }

    public function registerCoreContainerAliases()
    {

        $this->registerProviders($this->aliases);
    }

    public function bindBasePaths($paths)
    {

        if (is_array($paths)) {

            foreach ($paths as $key => $value) {
                $this['path.'.$key] = $value;
            }
        }
    }

    public function registerServices($app)
    {
    }

    public function get($type)
    {

        if ($this[$type]) {
            return $this[$type];
        }

        return null;
    }

}
