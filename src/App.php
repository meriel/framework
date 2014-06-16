<?php

class App
{
    private $url_controller = null;

    private $url_action = null;

    private $url_params = array();
 
    public function __construct()
    {
        // create array with URL parts in $url
       
        
        if(!empty(Routes::$routes)){
	       
		        
	        	if(isset(Routes::$routes['callback'])){

	        		echo call_user_func(Routes::$routes['callback']);

	        	}else if (file_exists('./app/controllers/' . Routes::$routes['controller'] . '.php')) {

		        	require './app/controllers/' . Routes::$routes['controller'] . '.php';
		        	
		        	$this->url_controller = new Routes::$routes['controller']();
		        	
		        	 if (method_exists($this->url_controller, Routes::$routes['method'])) {
		        	 
		        	 	echo $this->url_controller->{Routes::$routes['method']}(Routes::$routes['data']);
		        	 
		        	 }
		        }
		       
	        
		      
	        
	        
	    }else{
	    
	    	$this->splitUrl();
	    	

	        // check for controller: does such a controller exist ?
	        if (file_exists('./app/controllers/' . $this->url_controller . '.php')) {
	
	            // if so, then load this file and create this controller
	            // example: if controller would be "car", then this line would translate into: $this->car = new car();
	            require './app/controllers/' . $this->url_controller . '.php';
	            $this->url_controller = new $this->url_controller();
	
	            // check for method: does such a method exist in the controller ?
	            if (method_exists($this->url_controller, $this->url_action)) {
	
	                /*/ call the method and pass the arguments to it
	                if (isset($this->url_parameter_3)) {
	                    // will translate to something like $this->home->method($param_1, $param_2, $param_3);
	                    $this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2, $this->url_parameter_3);
	                } elseif (isset($this->url_parameter_2)) {
	                    // will translate to something like $this->home->method($param_1, $param_2);
	                    $this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2);
	                } elseif (isset($this->url_parameter_1)) {
	                    // will translate to something like $this->home->method($param_1);
	                    $this->url_controller->{$this->url_action}($this->url_parameter_1);
	                } else {
	                    // if no parameters given, just call the method without parameters, like $this->home->method();
	                    $this->url_controller->{$this->url_action}();
	                }*/
	                if(!empty($this->url_params)){
		                echo $this->url_controller->{$this->url_action}($this->url_params);
	                }else{
		                echo $this->url_controller->{$this->url_action}();
	                }
	                
	            } else {
	                // default/fallback: call the index() method of a selected controller
	                $this->url_controller->index();
	            }
	        } else {
	            // invalid URL, so simply show home/index
	            require './app/controllers/HomeController.php';
	            $home = new HomeController();
	            echo $home->index();
	        }
        
        }
    }

    /**
     * Get and split the URL
     */
    private function splitUrl()
    {
        if (isset($_GET['url'])) {

            // split URL
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            
            
            if($url[0]){
	            $this->url_controller = $url[0];
	            $this->url_action = (isset($url[1]) ? $url[1] : null);
	            
	            unset($url[0],$url[1]);
	            $url = array_values($url);
	            $this->url_params = $url;
	           
            }
         
        }
    }
}
