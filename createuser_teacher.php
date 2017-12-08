<?php

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//json decode the values recieved
$_POST = json_decode(file_get_contents('php://input'), true);

//Params passed or not
if(!isset($_POST['employeeId'])){
	$output = array(
		"status" => false,
		"error" => "Employee ID is not set",
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

if(!isset($_POST['password'])){
	$output = array(
		"status" => false,
		"error" => "password is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['email'])){
	$output = array(
		"status" => false,
		"error" => "email is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['subjects'])){
	$output = array(
		"status" => false,
		"error" => "subjects is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

//assign the values to local variables
$employeeId=$_POST['employeeId'];
$name=$_POST['name'];
$mobile=$_POST['mobile'];
$subjects=json_encode($_POST['subjects']);
$email=$_POST['email'];
$password=$_POST['password'];

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

	//password
	if(!preg_match("((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20})", $password)){
		$output = array(
			"status" => false,
			"error" => "Password is invalid",
			"errorCode" => "101",
			"response" => ""
		);
		die(json_encode($output));
	}

//check if the user already exists
$clash = false;
$teacher = mysql_query("SELECT `employeeId`, `name`, `mobile`, `subjects`, `lastLogin`, `password`, `email` FROM `dash_teachers` WHERE `employeeId`='{$employeeId}'");

while($row = mysql_fetch_assoc($teacher)){
	$clash=true;
	$output = array(
			"status" => false,
			"error" => "User already Exists",
			"errorCode" => "201",
			"response" => ""
		);
		die(json_encode($output));
}

//add teacher to database
if(!$clash){
	mysql_query("INSERT INTO `dash_teachers`(`employeeId`, `name`, `mobile`, `subjects`, `lastLogin`, `password`, `email`) VALUES ('{$employeeId}','{$name}','{$mobile}','{$subjects}','','{$password}','{$email}')");

	$response = array(
		"message" => "teacher was created sucessfully"
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