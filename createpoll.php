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
	Brief: To create a poll by the Teachers or Admin staff

	Author: Shafeef
	First Created: 19-09-2017
	Last Modified: 05-12-2017 @Shafeef
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
if(!isset($_POST['title'])){
	$output = array(
		"status" => false,
		"error" => "title is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['brief'])){
	$output = array(
		"status" => false,
		"error" => "brief is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['endDate'])){
	$output = array(
		"status" => false,
		"error" => "endDate is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['pollContent'])){
	$output = array(
		"status" => false,
		"error" => "Poll Content is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

if(!isset($_POST['target'])){
	$output = array(
		"status" => false,
		"error" => "target is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}




//REQUIRED PARAMETERS

date_default_timezone_set('Asia/Kolkata');

//assign the values to local varibles
$title=$_POST['title'];
$brief=$_POST['brief'];
$pollContent=$_POST['pollContent'];
$target=$_POST['target'];
$endDate=$_POST['endDate'];

$count = 0;
foreach ($pollContent as $key) {
	$tmp[] = array(
		"value" => $key,
		"option" => $count,
		"count" => ''
	);
	$count++;
}

$pollContent = json_encode($tmp);


/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect_'.$schoolCode.'.php';

$targetCount = 0;
$temp = explode(", ", $target);
if($target == 0 || $target == ''){
	$query = mysql_query("SELECT COUNT(`roll`) AS total FROM `d_studentsmasterlist` WHERE 1");
	$row = mysql_fetch_assoc($query);
	$targetCount = $row['total'];
}

else{
	foreach ($temp as $key) {
		$temp1 = explode("-", $key);
		$class = $temp1[0];
		$division = $temp1[1];

		$query = mysql_query("SELECT COUNT(`roll`) AS total FROM `d_studentsmasterlist` WHERE `class`= '{$class}' && `division`='{$division}'");
		$row = mysql_fetch_assoc($query);
		$targetCount = $targetCount + $row['total'];

	}
}

//3.2 MAIN OPERATION (INSERTION or DELETION or UPDATION) 

		mysql_query("INSERT INTO `d_polls` (`title`, `brief`, `pollContent`, `target`, `isActive`, `endDate`, `targetCount` ) VALUES ('{$title}','{$brief}','{$pollContent}','{$target}', 1, '{$endDate}', '{$targetCount}')");
		$response = array(
		"message" => "Poll was created sucessfully"
			);

	$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"response" => $response
		);
	echo json_encode($output);
?>

