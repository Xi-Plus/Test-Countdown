<?php
require(__DIR__.'/config.php');
require(__DIR__.'/function.php');

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'GET' && $_GET['hub_mode'] == 'subscribe' &&  $_GET['hub_verify_token'] == $C['FBverifytoken']) {
	echo $_GET['hub_challenge'];
} else if ($method == 'POST') {
	$inputJSON = file_get_contents('php://input');
	$input = json_decode($inputJSON, true);
	foreach ($input['entry'] as $entry) {
		foreach ($entry['messaging'] as $messaging) {
			$page_id = $messaging['recipient']['id'];
			if ($page_id != $C['FBpageid']) {
				continue;
			}
			$user_id = $messaging['sender']['id'];
			if (isset($messaging['message'])) {
				$text = $messaging['message']['text'];
				$today = strtotime($text);
				if ($today === false) {
					$response = "請傳送一個時間，例如\n"
						.date("Y/m/d")."\n"
						."Next Monday\n"
						.date("F d");
				} else {
					$today = tzcorrection($today);
					$response = date("Y/m/d", $today);
					$temp = getmessage($today);
					if ($temp === "") {
						$response .= "沒有任何倒數";
					} else {
						$response .= "\n".$temp;
					}
				}
			} else {
				$response = "Something went wrong!";
			}
			$messageData = [
				"recipient" => [
					"id" => $user_id
				],
				"message" => [
					"text" => $response
				]
			];
			$commend = 'curl -X POST -H "Content-Type: application/json" -d \''.json_encode($messageData,JSON_HEX_APOS|JSON_HEX_QUOT).'\' "https://graph.facebook.com/v2.7/me/messages?access_token='.$C['FBpagetoken'].'"';
			system($commend);
		}
	}
}
