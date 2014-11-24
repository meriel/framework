<?php namespace Meriel\Routing;


/*
 * This file is part of the Meriel framework.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class Controller{
  
    
    public function __construct(){}
    
    
    public function missingMethod($params = array()){
        return "404";
    }

  
    public function loadModel($model_name)
    {
        require 'app/models/' . ucfirst($model_name) . '.php';
        
        return new $model_name();
    }

    
}
