<?php namespace Meriel\Base;

use Response;

class Application extends \Meriel\Container\Container {

    public function __construct() {}

    public function run() {

        //ob_start();
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

        if (!$dispatched) {
            $this->notFound();
        } else {



            Response::setContent($dispatched)->send();
        }
    }

    protected function notFound() {

        Response::setContent(View::make('404'))->send();
    }

    public function registerCoreContainerAliases() {
        
        $this->register('router', function() {
            return new \Meriel\Routing\Router();
        });
        $this->register('request', function() {
            return new \Meriel\Http\Requests();
        });
        $this->register('view', function() {
            return new \Meriel\View\Views();
        });
        $this->register('response', function() {
            return new \Meriel\Http\Responses();
        });
        
       
        
    }

    public function bindBasePaths($paths) {

        if (is_array($paths)) {

            foreach ($paths as $key => $value) {
                $this['path.' . $key] = $value;
            }
        }
    }

    public function registerServices($app) {
        
    }

    public function __get($key) {
        return $this[$key];
    }

    public function __set($key, $value) {
        $this[$key] = $value;
    }

}
