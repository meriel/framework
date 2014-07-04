<?php

if ( ! function_exists('path') ){
    
    function public_path($path = ''){
        
        return App::get("path.public").($path ? '/'.$path : $path);
        
    }
    
}


if ( ! function_exists('url'))
{
    
    function url($path = ''){
        
        $config = Config::get('app');
                
        return $config['url'].($path ? '/'.$path : $path);
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