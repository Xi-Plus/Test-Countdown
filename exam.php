<?php
require(__DIR__.'/config.php');
if (!in_array(PHP_SAPI, $C["allowsapi"])) {
	exit("No permission");
}

$testmode = isset($argv[1]);
$today = time();
if (isset($argv[1])) {
	$today = strtotime($argv[1]);
}
$today = floor($today / 86400) * 86400;
echo "post as ".date("Y/m/d", $today)."\n";

$message = "";
foreach ($C['exam_list'] as $temp) {
	if (isset($temp["start_post_date"]) && $today < $temp["start_post_date"]) {
		continue;
	}
	$tdehu = $temp["date_start"];
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
	} else if (isset($temp["date_end"]) && $today <= $temp["date_end"]) {
		if (isset($temp["thedaytext"])) {
			$message .= $temp["thedaytext"]."\n";
		}
	}
}
if ($message === "") {
	exit("nothing to post\n");
}
$message .= "\n".date("Y/m/d", $today);

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
