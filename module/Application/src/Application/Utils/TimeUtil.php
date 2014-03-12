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

		$f = fopen("/tmp/log.txt");
		
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
	    //$Minutes = ($Min - ($iHours * 60)) / 100;
	    $Min = $Min - ($iHours * 60);
	    /*$tHours = $iHours + $Minutes;*/
	    /*if ($Minutes < 0)
	    {
	        $tHours = $tHours * (-1);
	    }
	    $aHours = explode(".", $tHours);
	    $iHours = $aHours[0];
	    if (empty($aHours[1]))
	    {
	        $aHours[1] = "00";
	    }*/
	    //$Minutes = $aHours[1];
	    if (strlen($Min) < 2)
	    {
	        $Min = $Min ."0";
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
		
        fwrite($f, $tHours . "\n");
		fclose($f);

	    return $tHours;
	}

}

?>