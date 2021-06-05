<?php
if(!$_POST['id']) {
	echo json_encode(["success"=>false]);
	exit;
}

$users = json_decode(file_get_contents("../data/users.json"), true);
$newUsers = [];
foreach($users as $user) {
	if($user['id'] == $_POST['id'])
		continue;
	$newUsers[] = $user;
}

file_put_contents("../data/users.json", json_encode($newUsers, JSON_UNESCAPED_UNICODE));
echo json_encode(["success"=>true]);