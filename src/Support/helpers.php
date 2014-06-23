<?php

if ( ! function_exists('path') ){
    
    function path($folder){
        
        //return App::get("path.{$folder}");
        
    }
    
}

if ( ! function_exists('escape') )
{
	/**
	 * Escape HTML entities in a string.
	 *
	 * @param  string  $value
	 * @return string
	 */
	function escape($string)
	{
		return htmlentities($string, ENT_QUOTES, 'UTF-8', false);
	}
}