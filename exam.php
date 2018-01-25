<?php
require(__DIR__.'/config.php');
if (!in_array(PHP_SAPI, $C["allowsapi"])) {
	exit("No permission");
}
date_default_timezone_set("Asia/Taipei");

$testmode = isset($argv[1]);
$year = date("Y");
$month = date("n");
$date = date("j");
if (isset($argv[3]) && is_numeric($argv[1]) && is_numeric($argv[2]) && is_numeric($argv[3])) {
	$year = $argv[1];
	$month = $argv[2];
	$date = $argv[3];
}
echo "post as ".$year."/".$month."/".$date."\n";

$message = "";
foreach ($C['exam_list'] as $temp) {
	$today = mktime(0, 0, 0, $month, $date, $year);
	$tdehu = mktime(0, 0, 0, $temp["month"], $temp["date"], $temp["year"]);
	$diff = ($tdehu - $today) / 86400;
	if ($diff > 0) {
		$temp["text"] = preg_replace("/\\\D/", $diff, $temp["text"]);
		$temp["text"] = preg_replace("/\\\W/", floor($diff/7), $temp["text"]);
		$message .= $temp["text"]."\n";
	}
	if (isset($temp["len"]) && $diff <= 0 && $diff > -$temp["len"]) {
		$message .= $temp["thedaytext"]."\n";
	}
}
$message .= "\n".date("Y/m/d");

echo "message:\n";
echo $message."\n";

if ($testmode) {
	exit("test mode on\n");
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v2.8/me/feed");
curl_setopt($ch, CURLOPT_POST, true);
$post = array(
	"message" => $message,
	"access_token" => $C['FBpagetoken']
);
curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($post));
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
$res = curl_exec($ch);
curl_close($ch);

$res = json_decode($res, true);
if (isset($res["error"])) {
	echo json_encode($res)."\n";
} else {
	echo "Success\n";
}
