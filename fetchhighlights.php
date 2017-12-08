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
	Brief: To fetch highlights of actions done

	Author: Ameen
	First Created: 02-10-2017
	Last Modified: 
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




/****************
  3. MAIN LOGIC
*****************/


//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect_'.$schoolCode.'.php';


$response [] = array(
		"id"=>"1",
		"user"=>"Shabeer",
		"brief"=>"Attendance for class 12-A updated",
		"timeLapsed"=>"45 mins ago"
);

$response [] = array(
		"id"=>"2",
		"user"=>"Hashim",
		"brief"=>"Football Match - event created",
		"timeLapsed"=>"2 Hours ago"
);
$response [] = array(
		"id"=>"3",
		"user"=>"Suresh",
		"brief"=>"Leave request by Suresh",
		"timeLapsed"=>"6 Hours ago"
);
$response [] = array(
		"id"=>"4",
		"user"=>"Shafeef",
		"brief"=>"Christmas Exam marks for class 10-A uploaded",
		"timeLapsed"=>"1 Day ago"
);

$response [] = array(
		"id"=>"5",
		"user"=>"Abhijith",
		"brief"=>"Salary Payment Issued",
		"timeLapsed"=>"2 Days ago"
);

$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response 
);
		
		die(json_encode($output));
	
?>



