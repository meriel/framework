<?php

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 */
class Controller{
    /**
     * @var null Database Connection
     */
    public $db = null;
    private $controller = null;
    private $action = null;
    private $params = array();
    /**
     * Whenever a controller is created, open a database connection too. The idea behind is to have ONE connection
     * that can be used by multiple models (there are frameworks that open one connection per model).
     */
    function __construct(){}
    
    
    function missingMethod($params = array()){
        return "404";
    }

   

  
    public function loadModel($model_name)
    {
        require 'app/models/' . strtolower($model_name) . '.php';
        // return new model (and pass the database connection to the model)
        return new $model_name($this->db);
    }

    
}
