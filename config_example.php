<?php

$C['FBpageid'] = 'page_id';
$C['FBpagetoken'] = 'page_token';

$C['exam_list'] = [
	[
		"text" => "107指考 還有 \D 天 (\W 週)",
		"textwithoutweek" => "107指考 還有 \D 天",
		"thedaytext" => "今天是107指考，祝各位考生考試順利～",
		"date_start" => strtotime("2018/7/1 UTC"),
		"date_end" => strtotime("2018/7/3 UTC"),
	],
	[
		"text" => "108學測(預計) 還有 \D 天 (\W 週)",
		"textwithoutweek" => "108學測(預計) 還有 \D 天",
		"thedaytext" => "今天是108學測，祝各位考生考試順利～",
		"date_start" => strtotime("2019/1/25 UTC"),
		"date_end" => strtotime("2019/1/26 UTC"),
		"start_post_date" => strtotime("2018/7/4 UTC")
	],
];

$C["allowsapi"] = array("cli");
