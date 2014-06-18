<?php


class Controller{
  
    
    public $db = null;
    private $controller = null;
    private $action = null;
    private $params = array();
   
    
    
    public function __construct(){}
    
    
    public function missingMethod($params = array()){
        return "404";
    }

   

  
    public function loadModel($model_name)
    {
        require 'app/models/' . strtolower($model_name) . '.php';
        // return new model (and pass the database connection to the model)
        return new $model_name($this->db);
    }

    
}
