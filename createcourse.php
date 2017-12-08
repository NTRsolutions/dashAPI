<?php

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//json decode the values recieved
$_POST = json_decode(file_get_contents('php://input'), true);

//Params passed or not
if(!isset($_POST['courseName'])){
	$output = array(
		"status" => false,
		"error" => "Course Name is not set is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['courseCode'])){
	$output = array(
		"status" => false,
		"error" => "course code is not set",
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
$courseName=$_POST['courseName'];
$courseCode=$_POST['courseCode'];
$subjectCode=$_POST['subjectCode'];

//check if the subject already exists
$clash = false;
$course=mysql_query("SELECT `courseName`, `courseCode`, `subjectCode` FROM `dash_course` WHERE `courseName`='{$courseName}' AND `courseCode`='{$courseCode}'");

while($row = mysql_fetch_assoc($course)){
	$clash=true;
	$output = array(
			"status" => false,
			"error" => "This particular course already Exists",
			"errorCode" => "201",
			"response" => ""
		);
		die(json_encode($output));
}

//if new subject add to database
if(!$clash){
	mysql_query("INSERT INTO `dash_course`(`courseName`, `courseCode`, `subjectCode`) VALUES ('{$courseName}','{$courseCode}','{$subjectCode}')");
		$response = array(
		"message" => "course was created sucessfully"
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

