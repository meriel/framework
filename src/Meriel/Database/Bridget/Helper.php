<?php namespace Meriel\Database\Bridget;

class Helper {

	
	public static function studlyCase($value)
	{
		$value = ucwords(str_replace(array('-', '_'), ' ', $value));

		return str_replace(' ', '', $value);
	}

	
	public static function camelCase($value)
	{
		return lcfirst(static::studlyCase($value));
	}

}