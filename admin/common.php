<?php

$months=array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

function get_display_date_str($dtVal){

    global $months;

    $vals=explode(" ",$dtVal);
    $dVals=$vals[0];
    if (count($vals)>1){
        $tVals=$vals[1];
    }

    //YYYY-mm-dd
    $dVals=explode("-",$dVals);

    $monthName=$months[$dVals[1]-1];
    $year=$dVals[0];
    $day=$dVals[2];

    $dateStr=$day." ".$monthName." ".$year;

    if (count($vals)>1){
        //hh:mm:ss
        $tVals=explode(":",$tVals);
        $hour=$tVals[0];
        $amPm="AM";
        if ($hour>=12){
            $amPm="PM";
        }
        if ($hour==0){
            $hour=12;
        }
        if ($hour>12){
            $hour-=12;
            $amPm="PM";
        }
        if ($hour<10){
            //$hour="0".$hour;
        }
        $min=$tVals[1];
        if ($min<10){
            //$min="0".$min;
        }
        $dateStr=$dateStr." ".$hour.":".$min." ".$amPm;
    }

    return $dateStr;
}

function get_hours_from_my_timezone($tzone){
    $multiplier=1;
    //format GMT+5:00 or GMT-5:00
    $vals=explode("+",$tzone);
    if (count($vals)<=1){
        $vals=explode("-",$tzone);
        if (count($vals)<=1){
            return 0;
        }
        $multiplier=-1;
    }

    $offset=$vals[1];
    $vals=explode(":",$offset);
    $offset=intval($vals[0]);
    return ($offset*$multiplier);
}


function get_gmt_from_my_timezone($format,$tzone){
    $currTime=time();
    $hrs=get_hours_from_my_timezone($tzone);
    $currTime+=($hrs*60*60);
    return gmdate($format,$currTime);
}

function get_round_to_digits($number,$dec,$thousand=false){
	$val=number_format((float)$number,$dec,'.','');
	if ($thousand){
		$val=number_format((float)$val,$dec,'.',',');
	}
	return $val;
}

?>