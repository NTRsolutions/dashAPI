<?php

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//json decode the values recieved
$_POST = json_decode(file_get_contents('php://input'), true);

//Params passed or not
if(!isset($_POST['fromMob'])){
	$output = array(
		"status" => false,
		"error" => "from is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['toMob'])){
	$output = array(
		"status" => false,
		"error" => "to is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['message'])){
	$output = array(
		"status" => false,
		"error" => "message is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

//assign the values to local varibles
$fromMob=$_POST['fromMob'];
$toMob=$_POST['toMob'];
$message=$_POST['message'];

mysql_query("INSERT INTO `dash_messages`(`fromMob`, `toMob` , `message`) VALUES ('{$fromMob}','{$toMob}','{$message}')");
		$response = array(
		"message" => "message was created sucessfully"
			);
		$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"response" => $response
		);
	echo json_encode($output);

?>