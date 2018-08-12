<?php
require(__DIR__.'/config.php');
if (!in_array(PHP_SAPI, $C["allowsapi"])) {
	exit("No permission");
}
require(__DIR__.'/function.php');

$testmode = isset($argv[1]);
$today = time();
if (isset($argv[1])) {
	$today = strtotime($argv[1]);
}
echo "post as ".date("Y/m/d", $today)."\n";
$today = tzcorrection($today);

$message = getmessage($today);
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
