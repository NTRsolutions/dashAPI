<?php 

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//json decode the values recieved
$_POST = json_decode(file_get_contents('php://input'), true);

//Params passed or not
if(!isset($_POST['code'])){
	$output = array(
		"status" => false,
		"error" => "code is not set",
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

if(!isset($_POST['contact'])){
	$output = array(
		"status" => false,
		"error" => "contact is not set",
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

//assign the values to local varibles
$code=$_POST['code'];
$name=$_POST['name'];
$password=$_POST['password'];
$contact=$_POST['contact'];

//params validation
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
$admin=mysql_query("SELECT `code`, `name`, `password`, `lastLogin`, `contact` FROM `dash_admins` WHERE `code`='{$code}'");

while($row = mysql_fetch_assoc($admin)){
	$clash=true;
	$output = array(
			"status" => false,
			"error" => "Admin already Exists",
			"errorCode" => "201",
			"response" => ""
		);
		die(json_encode($output));
}

//add Admin to database
if(!$clash){
	mysql_query("INSERT INTO `dash_admins`(`code`, `name`, `password`, `lastLogin`, `contact`) VALUES ('{$code}','{$name}','{$password}','','{$contact}')");
		$response = array(
		"message" => "Admin was created sucessfully"
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