<?php

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//json decode the values recieved
$_POST = json_decode(file_get_contents('php://input'), true);

//Params passed or not
if(!isset($_POST['brief'])){
	$output = array(
		"status" => false,
		"error" => "brief is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['timestamp'])){
	$output = array(
		"status" => false,
		"error" => "timestamp is not set ",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['userID'])){
	$output = array(
		"status" => false,
		"error" => "userID is not set ",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['audienceFlag'])){
	$output = array(
		"status" => false,
		"error" => "Audience Flag is not set ",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['audienceList'])){
	$output = array(
		"status" => false,
		"error" => "audience List is not set ",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['venue'])){
	$output = array(
		"status" => false,
		"error" => "venue is not set ",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['startTime'])){
	$output = array(
		"status" => false,
		"error" => "start time is not set ",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['endTime'])){
	$output = array(
		"status" => false,
		"error" => "end time is not set ",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}


if(!isset($_POST['displayFlag'])){
	$output = array(
		"status" => false,
		"error" => "display flag is not set ",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

//assign the values to local varibles
$brief=$_POST['brief'];
$audienceFlag=$_POST['audienceFlag'];
$audienceList=json_encode($_POST['audienceList']);
$userID=$_POST['userID'];
$photoURL=$_POST['photoURL'];
$photoFlag= $photoURL=="" ? 0 : 1 ;
$startTime=$_POST['startTime'];
$endTime=$_POST['endTime'];
$venue=$_POST['venue'];
$displayFlag=$_POST['displayFlag'];


//check if the event already exists
$clash = false;
$event=mysql_query("SELECT `id`, `brief`, `audienceFlag`, `audienceList`, `venue`, `startTime`, `endTime`, `userID`, `photoFlag`, `photoURL`, `displayFlag`, `timestamp` FROM `dash_events` WHERE `brief`='{$brief}' AND `audienceFlag` = '{$audienceFlag}' AND `audienceList`='{$audienceList}'");

while($row = mysql_fetch_assoc($event)){
	$clash=true;
	$output = array(
			"status" => false,
			"error" => "This particular event already Exists",
			"errorCode" => "201",
			"response" => ""
		);
		die(json_encode($output));
}

//if new notofication add to database
if(!$clash){
	mysql_query("INSERT INTO `dash_events`(`id`, `brief`, `audienceFlag`, `audienceList`, `venue`, `startTime`, `endTime`, `userID`, `photoFlag`, `photoURL`, `displayFlag`, `timestamp`) VALUES ('','{$brief}','{$audienceFlag}','{$audienceList}','{$venue}','{$startTime}','{$endTime}','{$userID}','{$photoFlag}','{$photoURL}','{$displayFlag}','')");
		$response = array(
		"message" => "event was created sucessfully"
			);
		$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"response" => $response
		);
	echo json_encode($output);
}
?>




