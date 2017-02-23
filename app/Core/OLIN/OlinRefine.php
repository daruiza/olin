<?php

namespace App\Core\OLIN;

class OlinRefine
{	
   
	public function cero_espacios($string){
		 $string = trim($string);
	 	$string = str_replace('&nbsp;', ' ', $string);
	 	$string = preg_replace('/\s\s+/', ' ', $string);
	 	return $string;
	}
		
	
}
