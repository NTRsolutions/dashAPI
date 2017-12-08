<?php

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//json decode the values recieved
$_POST = json_decode(file_get_contents('php://input'), true);

//Params passed or not
if(!isset($_POST['subjectName'])){
	$output = array(
		"status" => false,
		"error" => "std is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['subjectCode'])){
	$output = array(
		"status" => false,
		"error" => "division is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

//assign the values to local varibles
$subjectName=$_POST['subjectName'];
$subjectCode=$_POST['subjectCode'];

//check if the subject already exists
$clash = false;
$subject=mysql_query("SELECT `subjectName`, `subjectCode` FROM `dash_subject` WHERE `subjectCode`= '{$subjectCode}'");

while($row = mysql_fetch_assoc($subject)){
	$clash=true;
	$output = array(
			"status" => false,
			"error" => "This particular subject already Exists",
			"errorCode" => "201",
			"response" => ""
		);
		die(json_encode($output));
}

//if new subject add to database
if(!$clash){
	mysql_query("INSERT INTO `dash_subject`(`subjectName`, `subjectCode`) VALUES ('{$subjectName}','{$subjectCode}')");
		$response = array(
		"message" => "subject was created sucessfully"
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