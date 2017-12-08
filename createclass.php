<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);
$_POST = json_decode(file_get_contents('php://input'), true);

/*******************
  0. DOCUMENTATION
********************/
/*
	Version: 1.0
	Admin API: YES
	Brief: To create an event by the Teachers or Admin staff

	Author: Ameen
	First Created: 19-09-2017
	Last Modified: 19-09-2017 @Abhijith
*/


/**********************************
  1.1 AUTHENTICATION STANDARD PART
***********************************/

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';


//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
			"status" => false,
			"error" => "Access Token Missing",
			"errorCode" => 103,
			"response" => ""
	);
	die(json_encode($output));
}

$token = $_POST['token'];
$decryptedtoken = openssl_decrypt($token, $encryptionMethod, $secretHash);
$tokenid = json_decode($decryptedtoken, true);

//Expiry Validation
date_default_timezone_set('Asia/Calcutta');
$dateStamp = date_create($tokenid['date']);
$today = date_create(date("Y-m-j"));
$interval = date_diff($dateStamp, $today);
$interval = $interval->format('%a');

if($interval > $tokenExpiryDays){
	$output = array(
			"status" => false,
			"error" => "Login Expired",
			"errorCode" => 401,
			"response" => ""
	);
	die(json_encode($output));
}

/**********************************
  1.2 AUTHENTICATION CUSTOM PART
***********************************/

//Check if the token is valid
if(!($tokenid['schoolCode'] == "")){
	$schoolCode = $tokenid['schoolCode'];
	$admin_mobile = $tokenid['mobile'];
	$admin_role = $tokenid['role'];
}
else{
	$output = array(
		"status" => false,
		"error" => "Invalid Token",
		"errorCode" => 402,
		"response" => ""		
	);
	die(json_encode($output));
}

//Check if the user has permission to access this API
if($admin_role != "ADMIN" && $admin_role != "TEACHER"){
	$output = array(
			"status" => false,
			"error" => "Access Restricted",
			"errorCode" => 403,
			"response" => ""
		);
	die(json_encode($output));
}




//REQUIRED PARAMETERS

date_default_timezone_set('Asia/Kolkata');
$date = date('m/d/Y h:i:s a', time());

//Valdiations

if(!isset($_POST['class'])){
	$output = array(
			"status" => false,
			"error" => "Class Missing",
			"errorCode" => 103,
			"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['division'])){
	$output = array(
			"status" => false,
			"error" => "Division Missing",
			"errorCode" => 103,
			"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['teacherId'])){
	$output = array(
			"status" => false,
			"error" => "Teacher Id is Missing",
			"errorCode" => 103,
			"response" => ""
	);
	die(json_encode($output));
}

/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect_'.$schoolCode.'.php';

$class = $_POST['class'];
$division = $_POST['division'];
$stream = $_POST['stream'];
$teacherId = $_POST['teacherId'];
if($class == "PKG" || $class == "LKG" || $class == "UKG"){
	$type = "KG";
}
else if($class >= 1 && $class <= 4){
	$type = "LP";
}
else if($class >= 5 && $class <= 8){
	$type = "UP";
}
else if($class >= 9 && $class <= 10){
	$type = "HS";
}
else if($class >= 11 && $class <= 12){
	$type = "HSS";
}

$clash = false;
$query = mysql_query("SELECT * FROM `d_classmasterlist` WHERE `class` = '{$class}' && `division` = '{$division}'");

while($result = mysql_fetch_assoc($query)){
	$clash = true;
}
	if(!$clash){

		mysql_query("INSERT INTO `d_classmasterlist`( `class`, `division`, `stream`, `classTeacherId`, `type`) VALUES ('{$class}','{$division}','{$stream}','{$teacherId}', '{$type}')");

			$response = array(
				"message" => "Class created successfully"
			);

			$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"response" => $response
		);

		die(json_encode($output));

		
	}
	else{
		$output = array(
			"status" => false,
			"error" => "Class already exists",
			"errorCode" => "",
			"response" => ""
		);

		die(json_encode($output));

	}
			
?>