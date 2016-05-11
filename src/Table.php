<?php

namespace Css2Form;

/**
 * Description of Table
 *
 * @author Sébastien Dugène
 */
class Table
{
	public static function array_filter_r($array) 
	{ 
		foreach ($array as &$value) { 
			if (is_array($value)) { 
				$value = self::array_filter_r($value); 
			} 
		}
		return array_filter($array, function($value) {return ($value !== null && $value !== false && $value !== '');});
	}
	
	
	public static function del_value_r($needle, $array)
	{
		foreach ($array as &$value) { 
			if (is_array($value)) { 
				$value = self::del_value_r($needle, $value); 
			} elseif ($value == $needle) {
				$value = '';
			}
		}
		return $array;
	}	
	
    public static function flatArray($array)
    {
        $objTmp = (object) array('aFlat' => array());
        array_walk_recursive($array, create_function('&$v, $k, &$t', '$t->aFlat[] = $v;'), $objTmp);
        return $objTmp->aFlat;
    }
    
    public static function preg_match_r($regex, $array)
    {
    	foreach ($array as $value) {
    		if (is_array($value)) {
    			if (self::preg_match_r($regex, $value))
    				return true;
    		} else {
    			if (preg_match($regex, $value))
    				return true;
    		}
    	}
    	return false;
    }
    
	public static function sort_r($array)
	{
		$newArray = []; 
		foreach ($array as $value) {
			if (is_array($value)) {
				$newArray[] = self::sort_r($value);
			} else {
				$newArray[] = $value;
			}
		}
		return $newArray;
	}


    public static function serialize($array = [])
    {
        $return = [];
        foreach ($array as $key => $value) {
            $return[] = $key . '=' . $value;
        }
        return implode('&', $return);
    }


    public static function varGet($var, $index, $default = null)
    {
        if (! is_object($index) && ! is_array($index)) {

            if (is_array($var)) {
                if (isset($var[$index])) {
                    return $var[$index];
                }
            } else {
                if (is_object($var)) {
                    if (property_exists($var, $index)) {
                        return $var->$index;
                    }
                }
            }
        }
        return $default;
    }
    
    
    public static function in_array_r($needle, $haystack, $strict = false)
    {
    	foreach ($haystack as $item) {
        	if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && Table::in_array_r($needle, $item, $strict))) {
            	return true;
        	}
    	}
    	return false;
	}
}
