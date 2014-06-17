<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class Request{
    
    
    
    static function method(){
        return $_SERVER['REQUEST_METHOD'];
    }
    
    
    static function ajax(){
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
           return true;
        }
        return false;
    }
    
    static function is($path){
        
    }
    
}