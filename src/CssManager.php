<?php

namespace Css2Form;

/**
 * Description of CssManager
 *
 * @author Sébastien Dugène
 */
class CssManager
{
    private static $_instance = null;
    
    protected $css = null;

    /**
     * @return void
     */
    private function __construct() {
    	
    }

    /**
     * @return EntityManager
     */
    public static function getManager()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new CssManager();
        }
        return self::$_instance;
    }
    
    /// METHODS
    private function array2css($array)
    {
    	$css = '';
    	foreach($array as $line) {
    		if (array_key_exists('comment', $line)){
    			$css .= '/* '.$line['comment'].' */'."\n";
    		}
    		
    		$css .= $line['target'].' {'."\n";
    		if (array_key_exists('values', $line) && is_array($line['values'])) {
    			foreach ($line['values'] as $value) {
    				if (array_key_exists('comment', $value)){
		    			$css .= '/* '.$value['comment'].' */'."\n";
		    		}
    				$css .= $value['name'].': '.$value['value'].';'."\n";
    			}
    		}
    		$css .= '}'."\n";
    	}
    	return $css;
    }
    
    private function cleanMatches(&$matches)
    {
    	$string = $matches;
    	foreach($this->cleanPattern() as $value){
    		$matches = Table::del_value_r($value,$matches);
    	}
    	$matches = Table::sort_r(Table::array_filter_r($matches));
    	
    }
    
    private function cleanPattern($list = false)
    {
    	$pattern = [
    		':active',':first-child',':hover',':focus',':last-child',':first-of-type'
    	];
    	
    	if ($list) {
    		return implode('|',$pattern);
    	} else {
    		return $pattern;
    	}
    }
    
    private function css2array($css)
    {
    	$array = [];
    	$css = preg_replace('/[\n\r\t]/', '', $css);
    	$css = preg_replace('/[\s]?:[\s]?/', ': ', $css);
    	$css = preg_replace('/:[\s]([a-zA-Z0-9#])/', ':$1', $css);
    	preg_match_all("/[^\.#\w]?([\.#\-\w%]?[-\w\d]([\.\sàéè\w-#%\(,\)'\">\/!@~]*(".$this->cleanPattern(true).")*)+)([*:{])?/", $css, $matches, PREG_SET_ORDER);
    	$this->cleanMatches($matches);
    	$comment = $target = $name = '';
    	$key = -1;
    	foreach ($matches as $line => $value) {
    		if (array_key_exists(2,$value) && trim($value[1]) != 'null') {
    			switch($value[2]) {
    				case '*':
    					$comment = trim($value[1]);
    					break;
    				case '{':
    					if (trim($value[1]) != $target) {
    						if (empty($array[$key]['values'])) {
    							unset($array[$key]);
    						} else {
    							$key++;
    						}
    						$target = trim($value[1]);
    						$array[$key]['target'] = trim($value[1]);
    						$array[$key]['values'] = [];
    						
    						if ($comment != '') {
    							$array[$key]['comment'] = $comment;
    							$comment = '';
    						}
    					}
    					break;
    				case ':' :
    					$name = trim($value[1]);
    					break;
    			}
    		} elseif (!array_key_exists(2,$value) && array_key_exists(1,$value) && $name != '') {
    			$values = [
    				'name' => $name,
    				'value' => trim($value[1])
    			];
    			if ($comment != '') {
					$values['comment'] = $comment;
					$comment = '';
				}
    			$array[$key]['values'][] = $values;
    		}
    	}
    	return $array;
    }
    
    public function getArray()
    {
    	return $this->css;
    }
    
    public function getCss()
    {
    	return $this->array2css($this->css);
    }
    
    public function setCss($css)
    {
    	$this->css = $this->css2array($css);
    	return $this;
    }
    
    public function setCssFromFile($path)
    {
    	$css = file_get_contents($path);
    	$this->setCss = $this->css2array($css);
    	return $this;
    }
}