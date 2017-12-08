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

if(!isset($_POST['eventDate'])){
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
		"error" => "Mention if the event is Recursive or not",
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


if($_POST['recurranceFrequency'] != 0){
	if(!isset($_POST['recursionEndDate'])){
	$output = array(
		"status" => false,
		"error" => "Recursion End Date is not set",
		"errorCode" => 102,
		"response" => ""
	);
	die(json_encode($output));
}
}




//REQUIRED PARAMETERS

date_default_timezone_set('Asia/Kolkata');
$date = date('m/d/Y h:i:s a', time());

$brief = $_POST['brief'];
$venue  = $_POST['venue'];
$title  = $_POST['title'];
$eventDate  = $_POST['eventDate'];
$timeFrom  = $_POST['timeFrom'];
$timeTo  = $_POST['timeTo'];
$host  = $_POST['host'];


$photoURL = $_POST['photoURL'];
$photoFlag = isset($_POST['photoURL'])? true : false;

$recurranceFrequency=$_POST['recurranceFrequency'];
$isRecurring=$_POST['isRecurring'];
if($isRecurring){
	$recursionEndDate = $_POST['recursionEndDate'];
	$rand = mt_rand(100, 999);
	$recursionId = $recurranceFrequency*1000 + $rand;
	$query = mysql_query("SELECT `recursionId` FROM `d_events` WHERE 1");
	while($res = mysql_fetch_assoc($query)){
		if($res == $recursionId){
			$rand = mt_rand(100, 999);
			$recursionId = $recurranceFrequency*1000 + $rand;
		}
	}
}
else{
	$recursionEndDate = 0;
	$recursionId = 0;
}



$targetAudience = json_encode($_POST['targetAudience']);
$isRestricted = isset($_POST['targetAudience'])? true : false;
$status = 1;


/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect_'.$schoolCode.'.php';


//3.2 PARAMETER VALIDATIONS

//check if the event already exists
$clash_check = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `d_events` WHERE `brief`='{$brief}' AND `title`= '{$title}'"));
if($clash_check['id'] != ''){
	$output = array(
			"status" => false,
			"error" => "Event already exists",
			"errorCode" => 201,
			"response" => ""
		);
		die(json_encode($output));
}


//3.3 MAIN OPERATION (INSERTION or DELETION or UPDATION) 

	if($recurranceFrequency == 0){

		mysql_query("INSERT INTO `d_events`( `title`, `brief`, `venue`, `date`, `isRecurring`, `recurranceFrequency`, `recursionEndDate`, `recursionId`, `timeFrom`, `timeTo`, `host`, `isRestricted`, `targetAudience`, `isPhoto`, `photoURL`, `status`, `user`) VALUES ('{$title}','{$brief}','{$venue}','{$eventDate}','{$isRecurring}','{$recurranceFrequency}','{$recursionEndDate}', '{$recursionId}', '{$timeFrom}','{$timeTo}','{$host}','{$isRestricted}','{$targetAudience}','{$isPhoto}','{$photoUrl}','{$status}','{$admin_mobile}')");

		$response = array(
			"message" => "Event created successfully"
		);
		
		$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"response" => $response
		);

		die(json_encode($output));
	}
	

	else if($recurranceFrequency == 1) {
		$eventDt = strtotime($eventDate);
		$recEndDt = strtotime($recursionEndDate);

		while($eventDt <= $recEndDt){

			mysql_query("INSERT INTO `d_events`( `title`, `brief`, `venue`, `date`, `isRecurring`, `recurranceFrequency`, `recursionEndDate`, `recursionId`, `timeFrom`, `timeTo`, `host`, `isRestricted`, `targetAudience`, `isPhoto`, `photoURL`, `status`, `user`) VALUES ('{$title}','{$brief}','{$venue}','{$eventDate}','{$isRecurring}','{$recurranceFrequency}','{$recursionEndDate}', '{$recursionId}', '{$timeFrom}','{$timeTo}','{$host}','{$isRestricted}','{$targetAudience}','{$isPhoto}','{$photoUrl}','{$status}','{$admin_mobile}')");
			
			$eventDt = strtotime("+1 day", $eventDt);
			$eventDate = date("Y-m-d", $eventDt);

		}	

		$response = array(
			"message" => "Event created successfully"
		);
		
		$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"response" => $response
		);

		die(json_encode($output));
	}


	else if($recurranceFrequency == 2){
		$eventDt = strtotime($eventDate);
		$recEndDt = strtotime($recursionEndDate);

		while($eventDt <= $recEndDt){

			mysql_query("INSERT INTO `d_events`( `title`, `brief`, `venue`, `date`, `isRecurring`, `recurranceFrequency`, `recursionEndDate`, `recursionId`, `timeFrom`, `timeTo`, `host`, `isRestricted`, `targetAudience`, `isPhoto`, `photoURL`, `status`, `user`) VALUES ('{$title}','{$brief}','{$venue}','{$eventDate}','{$isRecurring}','{$recurranceFrequency}','{$recursionEndDate}', '{$recursionId}', '{$timeFrom}','{$timeTo}','{$host}','{$isRestricted}','{$targetAudience}','{$isPhoto}','{$photoUrl}','{$status}','{$admin_mobile}')");
			
			$eventDt = strtotime("+1 weeks", $eventDt);

			$eventDate = date("Y-m-d", $eventDt);

		}	

		$response = array(
			"message" => "Event created successfully"
		);
		
		$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"response" => $response
		);

		die(json_encode($output));
	}
?>



