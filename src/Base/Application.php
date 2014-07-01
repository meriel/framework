<?php namespace Meriel\Base;


class Application extends \Meriel\Container\Container {

    public function __construct() {}

    public function run() {

        \SassCompiler::run( $this['path.public'] . "/scss/", $this['path.public'] . "/css/");
        
        
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


            $this['response']->setContent($dispatched)->send();
        }
    }

    protected function notFound() {

        $this['response']->setContent($this['view']->make('404'))->send();
    }

    public function registerCoreContainerAliases() {
        
        $aliases = array(
            
            'router' => '\Meriel\Routing\Router',
            'request' => '\Meriel\Http\Requests',
            'view' => '\Meriel\View\Views',
            'response' => '\Meriel\Http\Responses',
            'database' => '\Meriel\Database\Database'
            
        );
        
        $this->registerProviders($aliases);
        
       
        
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
