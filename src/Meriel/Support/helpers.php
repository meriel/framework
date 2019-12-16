<?php
/*
 * This file is part of the Meriel package.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if ( ! function_exists('public_path') ){
    
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