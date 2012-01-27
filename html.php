<?php
/* 
   (f) html.php
   (c) 2011, emaniacs
   (s) chars19.blogspot.com
   (l) GPL+

   ?? static only

   Mon Aug  1 14:32:53 WIT 2011
   Created by : GNU Emacs 23.2.3
*/

class LIB_HTML {
    private static function parser ($val=array()) {
		$ret = '';
		foreach($val as $q => $r)
			$ret .= " $q='{$r}'";
		return $ret;
    }
    
    public static function script ($src='', $show=true) {
		$ret = "<script type='text/javascript' src='{$src}'></script>";
	
		if($show) echo $ret;
		else return $ret;
    }
    
    public static function style ($href, $show=true) {
		$ret = "<link rel='stylesheet' href='{$href}' type='text/css' media='screen' />";
	
		if ($show) echo $ret;
		else return $ret;
    }
    
    public static function form_start ($val='', $show=true) {
		if (is_array($val))
			$ret = '<form ' . self::parser ($val) . '>';
		else
			$ret = "<form method='post' action='{$val}' >";  
	
		if($show) echo $ret;
		else return $ret;

    }

    public static function form_end ($show=true) {
		$ret = '</form>';

		if($show) echo $ret;
		else return $ret;
    }

    public static function input($val, $show=true) {
		if (is_array ($val))
			$ret = '<input ' . self::parser($val) . ' />';
		else
			$ret = "<input type='{$val}' />";

		if($show) echo $ret;
		else return $ret;
    }

    public static function textarea($val='', $show=true) {
		$ret = '<textarea ';
		if(is_array($val)){
			if(array_key_exists('text', $val)){
				$txt = $val['text'];
				unset($val['text']);
			}
			$ret .= self::parser($val);
		}
		else 
			$txt = $val;

		$ret .= " >{$txt}</textarea>";

		if($show) echo $ret;
		else return $ret;
    }

    public static function select(array $val, array $options, $show=true) {
		/* $val harus array */
		/* $options harus array */

		$ret = '<select ';
		$ret .= self::parser($val) . ' >';

		$c = count($options);

		for($i=0; $i<$c; $i++) {
			if(is_array($options[$i])){
				if(array_key_exists('text', $options[$i])){
					$txt = $options[$i]['text'];
					unset($options[$i]['text']);
				}
				$o = self::parser($options[$i]);
			}
			else {
                $txt = $options[$i];
                $o = 'value="'. $options[$i] .'"';
            }

			$ret .= "<option {$o} >{$txt}</option>";
		}

		$ret .= '</select>';

		if($show) echo $ret;
		else return $ret;
    }

    public static function anchor ($val='', $show=true) {
		$ret = "<a ";
		if(is_array($val)) {
			if(array_key_exists('text', $val)) {
				$txt = $val['text'];
				unset($val['text']);
			}
			$ret = '<a ' . self::parser($val) ;
		}
		else {
			$ret = '<a href=# ';
			$txt = $val;
		}
  
		$ret .= ">{$txt}</a>";

		if($show) echo $ret;
		else return $ret;
    }
}


?>
