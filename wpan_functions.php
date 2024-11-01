<?php

//get permission settings
function zv_wpan_get_permission($zv_wpan_settings,$action) {
$allowed_levelid = array();
if ($action != 'view') { $action = 'edit'; }

if (!empty($zv_wpan_settings) && !empty($zv_wpan_settings[$action.'permission'])) {
  if (in_array('editor',$zv_wpan_settings[$action.'permission'])) { $allowed_levelid[] = '5';$allowed_levelid[] = '6';$allowed_levelid[] = '7'; }
  if (in_array('author',$zv_wpan_settings[$action.'permission'])) { $allowed_levelid[] = '2';$allowed_levelid[] = '3';$allowed_levelid[] = '4'; }
  if (in_array('contributor',$zv_wpan_settings[$action.'permission'])) { $allowed_levelid[] = '1';}
  if (in_array('subscriber',$zv_wpan_settings[$action.'permission'])) { $allowed_levelid[] = null; $allowed_levelid[] = '0';}
}

$allowed_levelid[] = '8';$allowed_levelid[] = '9';$allowed_levelid[] = '10';
return $allowed_levelid;
}


function zv_wpan_gettimediff($stamp) {
$time_used = $stamp;
$current_time = strtotime(date("Y-m-d H:i:s"));
$secsdiff = $current_time-$time_used;
 
if ($secsdiff < 0) { // future date
$secsdiff = $time_used-$current_time;
$futureorpast = 'later';
} else { // past date
$futureorpast = 'ago';
}

if ($secsdiff == 0) { // now
$smart = 'Now';
} elseif ($secsdiff < 60) { // this minute
  $smart = $secsdiff.' seconds '.$futureorpast;
  } else { // not this minute
	$minutediff = round($secsdiff/60);
	  if ($minutediff < 60) { // this hour
		$smart = $minutediff;
		$smart .= ($minutediff>1)?' minutes '.$futureorpast:' minute '.$futureorpast;
	  } else { // not this hour
		  $hourdiff = round($minutediff/60);
		  if ($hourdiff < 24) { // this day
			$smart = $hourdiff;
			$smart .= ($hourdiff>1)?' hours '.$futureorpast:' hour '.$futureorpast;
		  } else { // not this day
			$daydiff = round($hourdiff/24);
			if ($daydiff < 31) {
			  $smart = $daydiff;
			  $smart .= ($daydiff>1)?' days '.$futureorpast:' day '.$futureorpast;
			} else {
			  $monthdiff = round($daydiff/31);
				$comparemonth = date("n")-date("n",$stamp);
				if ($comparemonth > 0 && $comparemonth < 12) {
				  $monthdiff = $comparemonth;
				}
			  if ($monthdiff < 12) {
				$smart = $monthdiff;
				$smart .= ($monthdiff>1)?' months '.$futureorpast:' month '.$futureorpast;
			  } else {
				$yeardiff = round($monthdiff/12);
				$smart = $yeardiff;
				$smart .= ($yeardiff>1)?' years '.$futureorpast:' year '.$futureorpast;
			  }
			}
		  }
	  }
  }
 
return $smart;
}

function wp_version_2digits() {
$zv_wpan_wp_version = (int)(@str_replace('.','',substr(get_bloginfo('version'),0,3)));
return $zv_wpan_wp_version;
}

?>
