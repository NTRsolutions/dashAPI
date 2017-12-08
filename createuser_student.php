<?php

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//json decode the values recieved
$_POST = json_decode(file_get_contents('php://input'), true);

//Params passed or not
if(!isset($_POST['rollNo'])){
	$output = array(
		"status" => false,
		"error" => "Roll No is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['name'])){
	$output = array(
		"status" => false,
		"error" => "Name is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['mobile'])){
	$output = array(
		"status" => false,
		"error" => "mobile is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['class'])){
	$output = array(
		"status" => false,
		"error" => "Class is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['division'])){
	$output = array(
		"status" => false,
		"error" => "Division is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['stream'])){
	$output = array(
		"status" => false,
		"error" => "Stream is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['address'])){
	$output = array(
		"status" => false,
		"error" => "Address is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['email'])){
	$output = array(
		"status" => false,
		"error" => "E-mail is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['dob'])){
	$output = array(
		"status" => false,
		"error" => "Date-Of-Birth is not set",
		"errorCode" => "102",
		"response" => ""

	);
	die(json_encode($output));
}

//assign the values to local variables
$rollNo=$_POST['rollNo'];
$name=$_POST['name'];
$class=$_POST['class'];
$mobile=$_POST['mobile'];
$division=$_POST['division'];
$stream=$_POST['stream'];
$address=json_encode($_POST['address']);
$email=$_POST['email'];
$dob=$_POST['dob'];

//params validation
	//email-id
	if(!preg_match("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/", $email)){
		$output = array(
			"status" => false,
			"error" => "E-mail ID is invalid",
			"errorCode" => "101",
			"response" => ""
		);
		die(json_encode($output));
	}

	//mobile no
	if(!preg_match("(^(\+\d{1,3}[- ]?)?\d{10}$)", $mobile)){
		$output = array(
			"status" => false,
			"error" => "Mobile No is invalid",
			"errorCode" => "101",
			"response" => ""
		);
		die(json_encode($output));
	}

	//name
	if(!preg_match("(^[A-Za-z\s]{1,}[\.]{0,1}[A-Za-z\s]{0,}$)", $name)){
		$output = array(
			"status" => false,
			"error" => "Name is invalid",
			"errorCode" => "101",
			"response" => ""
		);
		die(json_encode($output));
	}

//check if the user already exists
$clash = false;
$student = mysql_query("SELECT `rollNo`, `name`, `mobile`, `class`, `division`, `stream`, `lastLogin`, `address`, `email`, `dob` FROM `dash_students` WHERE `mobile`='{$mobile}' AND `name`='{$name}'");

while($row = mysql_fetch_assoc($student)){
	$clash=true;
	$output = array(
			"status" => false,
			"error" => "User already Exists",
			"errorCode" => "201",
			"response" => ""
		);
		die(json_encode($output));
}

//add student to database
if(!$clash){
	mysql_query("INSERT INTO `dash_students`(`rollNo`, `name`, `mobile`, `class`, `division`, `stream`, `lastLogin`, `address`, `email`, `dob`) VALUES ('{$rollNo}' , '{$name}' , '{$mobile}' , '{$class}' , '{$division}' , '{$stream}' , '' , '{$address}' , '{$email}' , '{$dob}')");

	$response = array(
		"message" => "Student was created sucessfully"
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