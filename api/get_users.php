<?php
$users = json_decode(file_get_contents("../data/users.json"), true);

$weekStart = date("Y-m-d", strtotime("last saturday") + 86400);
$weekEnd = date("Y-m-d", strtotime("this saturday"));

$ret = [];

foreach($users as $user) {
	$github = file_get_contents("https://github.com/{$user['id']}");
	preg_match_all("/<rect.*?data\-count=\"(\d+?)\" data\-date=\"(\d{4}\-\d{2}\-\d{2})\".*?>/", $github, $result);
	preg_match("/<a itemprop=\"image\" href=\"(.+?)\">/", $github, $profile);
	
	$nCommit = 0;
	$nConsecutive = 0;
	$fine = 0;
	$fineThisWeek = 0;
	$nDate = 0;
	$isConsecutive = true;
	$state = "F";

	for($i = count($result[2]) - 1; $i >= 0; $i--) {
		$date = $result[2][$i];
		$commit = $result[1][$i];

		if($date < $user['startedAt'])
			break;

		$nDate++;
		$nCommit += $commit;

		if($commit > 0) {
			if($isConsecutive)
				$nConsecutive++;
		} else {
			$fine++;
			if($date >= $weekStart && $date <= $weekEnd)
				$fineThisWeek++;
			$isConsecutive = false;
		}
	}

	if($nDate > 0) {
		$per = $nCommit / $nDate * 100;
		if($per >= 90)
			$state = "A";
		elseif($per >= 80)
			$state = "B";
		elseif($per >= 70)
			$state = "C";
		elseif($per >= 60)
			$state = "D";
	}

	$ret[] = [
		"id" => $user['id'],
		"name" => $user['name'],
		"startedAt" => $user['startedAt'],
		"profile" => $profile[1],
		"nCommit" => $nCommit,
		"nConsecutive" => $nConsecutive,
		"fine" => $fine * 1000,
		"fineThisWeek" => $fineThisWeek * 1000,
		"state" => $state
	];
}

echo json_encode($ret, JSON_UNESCAPED_UNICODE);