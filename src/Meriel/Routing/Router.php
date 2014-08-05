<?php namespace Meriel\Routing;

class Router {

    private $routes = array();
    
    public  $matchedRoutes = array();


    public function get($uri, $action) {

        return $this->addRoute("GET", $uri, $action);
        
    }

    public function post($uri, $action) {

        return $this->addRoute("POST", $uri, $action);
    }
    
    public function put($uri, $action) {

        return $this->addRoute("PUT", $uri, $action);
    }
    
    public function delete($uri, $action) {

        return $this->addRoute("DELETE", $uri, $action);
    }

    protected function addRoute($methods, $uri, $action) {
        return $this->add($this->createRoute($methods, $uri, $action));
    }

    protected function createRoute($methods, $uri, $action) {

        if ($this->routingToController($action)) {

            $action = $this->getControllerAction($action);
        }

        //var_dump($action);

        $route = new \Meriel\Routing\Routes($methods, $uri, $action);

        return $route;
    }

    protected function routingToController($action) {
        if (is_string($action) && is_array(explode("@", $action))) {
            return true;
        }
        return false;
    }

    protected function getControllerAction($action) {
        if (is_string($action))
            $action = array('uses' => $action);


        $action['controller'] = $action['uses'];

        $closure = $this->getClassClosure($action['uses']);

        return $action = array('uses' => $closure);
    }

    protected function getClassClosure($controller) {

        list($class, $method) = explode('@', $controller);

        $closure = new $class();
        
        if(!method_exists($closure, $method)){
           $method = "missingMethod";
        }

        return array($closure, $method);
    }

    protected function add(Routes $route) {
        return $this->routes[] = $route;
    }

    //public function dispatch(Request $request) {}

    public function getRoutes() {
        
    
        $this->matchedRoutes = array();
        foreach ($this->routes as $route) {
            
             if($route->matches()){
                 $this->matchedRoutes[] = $route;
             }
             
        }
        
        return $this->matchedRoutes;
    }

    
    public function controllers(array $controllers) {
        foreach ($controllers as $uri => $name) {
            $this->controller($uri, $name);
        }
    }

    public function controller($uri, $controller, $names = array()) {}


}