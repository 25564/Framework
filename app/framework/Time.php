<?php
class Time {
	public static function ago($unix){ //Convert Unix time to string detailing difference in time
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
		$difference     = self::get() - $unix;
		
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}
				
		if(round($difference) != 1) {
			$periods[$j].= "s";
		}
		
		return round($difference) . " $periods[$j] ago ";
	}
	
	public static function get(){
		return time();	
	}
	
	public static function unixDate($unix = null, $format = "j-n-Y"){
		$unix = (!$unix) ? time() : $unix;
		return gmdate($format, $unix);
	}
}
?>