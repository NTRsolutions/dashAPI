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


/**********************
  2. PARAMETERS CHECK
***********************/

//Params passed or not
if(!isset($_POST['brief'])){
	$output = array(
		"status" => false,
		"error" => "Event brief is not set",
		"errorCode" => 102,
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['title'])){
	$output = array(
		"status" => false,
		"error" => "Event title is not set",
		"errorCode" => 102,
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['venue'])){
	$output = array(
		"status" => false,
		"error" => "Event venue is not set",
		"errorCode" => 102,
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['date'])){
	$output = array(
		"status" => false,
		"error" => "Event date is not set",
		"errorCode" => 102,
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['isRecurring'])){
	$output = array(
		"status" => false,
		"error" => "Event isRecurring flag is not set",
		"errorCode" => 102,
		"response" => ""
	);
	die(json_encode($output));
}


if(!isset($_POST['timeFrom'])){
	$output = array(
		"status" => false,
		"error" => "Event timeFrom is not set",
		"errorCode" => 102,
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['timeTo'])){
	$output = array(
		"status" => false,
		"error" => "Event timeTo is not set",
		"errorCode" => 102,
		"response" => ""
	);
	die(json_encode($output));
}


if(!isset($_POST['host'])){
	$output = array(
		"status" => false,
		"error" => "Event host is not set",
		"errorCode" => 102,
		"response" => ""
	);
	die(json_encode($output));
}




//REQUIRED PARAMETERS

date_default_timezone_set('Asia/Kolkata');
$date = date('m/d/Y h:i:s a', time());

$brief = $_POST['brief'];
$timestamp = $_POST['timestamp'];
$userID = $_POST['userID'];
$id = $_POST['id'];
$viewers = $_POST['viewers'];
$viewersList = json_encode($_POST['viewersList']);


$photoURL = $_POST['photoURL'];
$photoFlag = isset($_POST['photoURL'])? true : false;



/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect_'.$schoolCode.'.php';


//3.2 PARAMETER VALIDATIONS

//check if the event already exists
$clash_check = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `d_events` WHERE `brief`='{$brief}'"));
if($clash_check['id'] != ''){
	$output = array(
			"status" => false,
			"error" => "Notification already exists",
			"errorCode" => 201,
			"response" => ""
		);
		die(json_encode($output));
}


//3.3 MAIN OPERATION (INSERTION or DELETION or UPDATION) 

		mysql_query("INSERT INTO `d_notifications`(`timestamp`, `userID`, `photoFlag`, `photoURL`, `brief`, `viewers`, `viewersList`) VALUES ('{$timestamp}','{$userID}','{$photoFlag}','{$photoURL}','{$brief}','{$viwers}','{$viewrsList}')");
		
		$response = array(
			"message" => "notification created sucessfully"
		);
		
		$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"response" => $response
		);

		die(json_encode($output));
?>




