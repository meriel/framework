<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Requests{
    
    
    
    public function method(){
        return $_SERVER['REQUEST_METHOD'];
    }
    
    
    public function ajax(){
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
           return true;
        }
        return false;
    }
    
    public function is($path){
        
    }
    
    
    public function path(){
        return $_SERVER['REQUEST_URI'];
    }
    
    
    public function url(){
        return "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    public function uri(){
        return isset($_GET['url']) ? "/" . rtrim($_GET['url'], '/') : '/';
    }
    
}