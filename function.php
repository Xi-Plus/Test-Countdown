<?php

function tzcorrection($time) {
	$tzcorrection = strtotime("2018/1/1 GMT+0")-strtotime("2018/1/1");
	return (int)floor(($time + $tzcorrection) / 86400) * 86400;
}

function getmessage($today) {
	global $C;

	$message = "";
	foreach ($C['exam_list'] as $temp) {
		if (isset($temp["start_post_date"]) && $today < $temp["start_post_date"]) {
			continue;
		}
		$tdehu = $temp["date_start"];
		$tdehu = tzcorrection($tdehu);
		$diff = round(($tdehu - $today) / 86400);
		if ($diff > 0) {
			if (floor($diff/7) == 0) {
				$temp["textwithoutweek"] = preg_replace("/\\\D/", $diff, $temp["textwithoutweek"]);
				$message .= $temp["textwithoutweek"]."\n";
			} else {
				$temp["text"] = preg_replace("/\\\D/", $diff, $temp["text"]);
				$temp["text"] = preg_replace("/\\\W/", floor($diff/7), $temp["text"]);
				$message .= $temp["text"]."\n";
			}
		} else if (isset($temp["date_end"]) && $today <= tzcorrection($temp["date_end"])) {
			if (isset($temp["thedaytext"])) {
				$message .= $temp["thedaytext"]."\n";
			}
		}
	}
	return $message;
}
