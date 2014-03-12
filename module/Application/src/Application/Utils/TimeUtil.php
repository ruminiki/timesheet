<?php

namespace Application\Utils;

class TimeUtil
{

	public static function hours2minutes($hours)
	{

		if (strpos($hours,':') !== false) {
		    list($h, $m) = explode(":", $hours);
			return $h * 60 + $m;
		}else{
			return $hours * 60;
		}

	}

	public static function minutes2hours($Minutes)
	{

		$Minutes = (int) $Minutes;

		if ($Minutes < 0)
	    {
	        $Min = Abs($Minutes);
	    }
	    else
	    {
	        $Min = $Minutes;
	    }
	    $iHours = (int) ($Min / 60);

	    $Min = $Min - ($iHours * 60);

	    if ($Min < 10)
	    {
	        $Min = "0".$Min;
	    }
	    
	    if ($iHours < 10)
	    {
	   		$iHours = "0" . $iHours;
	    }

 		$tHours = $iHours .":". $Min;

 		if ($Minutes < 0)
	    {
	        return " - " . $tHours;
	    }
	    return $tHours;
	}

}

?>