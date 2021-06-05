<?php
if(!$_POST['id'] || !$_POST['name'] || !$_POST['startedAt']) {
	echo json_encode(["success"=>false]);
	exit;
}

$users = json_decode(file_get_contents("../data/users.json"), true);
$users[] = [
	"id" => $_POST['id'],
	"name" => $_POST['name'],
	"startedAt" => $_POST['startedAt']
];

file_put_contents("../data/users.json", json_encode($users, JSON_UNESCAPED_UNICODE));
echo json_encode(["success"=>true]);