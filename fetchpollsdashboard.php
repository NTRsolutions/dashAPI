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
	Brief: To fetch polls resposnes

	Author: Shafeef
	First Created: 19-09-2017
	Last Modified: 28-11-2017 @Shafeef
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


//Validations

/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect_'.$schoolCode.'.php';

$status = false;
$list = mysql_query("SELECT `pollResponse` FROM `d_pollresponses` WHERE `status` = 1");



while($pollresponses = mysql_fetch_assoc($list)){

	 	$status = true;

	 	$responseCount = mysql_fetch_assoc(mysql_query("SELECT COUNT(`pollId`) AS total FROM `d_pollresponses` WHERE `pollId`= '{$pollresponses['pollId']}'"));
	 	$pollDetails = mysql_fetch_assoc(mysql_query("SELECT `pollContent`,`title` FROM `d_polls` WHERE `pollId`= '{$pollresponses['pollId']}'"));
		$pollOptions = json_decode($pollDetails['pollContent']);
		for ($i=0; $i<10; $i++) {
			$count = mysql_fetch_assoc(mysql_query("SELECT COUNT(`pollId`) AS total FROM `d_pollresponses` WHERE `pollId`= '{$pollresponses['pollId']}' && `pollResponse`= '{$i}'"));
			$counts[] = array($count['total']);
		}

		$response[] = array(
				"pollResponse"=> $pollresponses['pollResponse'],
				"pollOptions"=> $pollOptions,
				"counts"=> $counts,
				"pollTitle"=> $pollDetails['title'],
				"responseCount" =>$responseCount['total']
		);
}

		

		

if($status){
		$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"response" => $response
		);
		die(json_encode($output));
}


if(!$status){
	  $output = array(
				"status" => false,
				"error" => "No responses made for this poll so far",
				"errorCode" => "402",
				"posts" => ""
			);
			die(json_encode($output));
		}

		
?>