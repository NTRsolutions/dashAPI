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
	Brief: To create an leave by the Teachers or Admin staff

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




/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect_'.$schoolCode.'.php';

$limiter = "";
if(isset($_POST['page'])){
	$range = $_POST['page'] * 10;
	$limiter = " LIMIT  {$range}, 10";	
}


$list = mysql_query("SELECT * FROM `d_leaves` WHERE 1 ORDER BY `status`, `id`".$limiter);



while($leave = mysql_fetch_assoc($list)){

		$studentInfo = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_studentsmasterlist` WHERE `roll` = '{$leave['studentRoll']}' "));
	 	
		$response[] = array(
				"id"=>$leave['id'],
				"brief"=>$leave['brief'],
				"leaveFrom"=>$leave['leaveFrom'],
				"leaveTo"=>$leave['leaveTo'],
				"createdDate"=>$leave['createdDate'],
				"createdUser"=>$leave['createdUser'],
				"studentRoll"=>$leave['studentRoll'],
				"studentfName"=>$studentInfo['fName'],
				"studentlName"=>$studentInfo['lName'],
				"studentClass"=>$studentInfo['class'],
				"studentDivision"=>$studentInfo['division'],
				"status"=>$leave['status']
		);
}

$figure_leave_total = 0;
$figure_leave = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) AS total FROM `d_leaves` WHERE 1"));
if($figure_leave['total'] != "")
{
	$figure_leave_total = $figure_leave['total'];
}

/*******************
//leave last created on
$leave_last = "";
$leave_last_check = mysql_fetch_assoc(mysql_query("SELECT `date` FROM `d_leaves` WHERE 1 ORDER BY `id` LIMIT 1"));
if($leave_last_check['date'] != ""){
	$leave_last = $leave_last_check['date'];
}
**********************/



$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response ,
	"totalLeaves" => $figure_leave_total ,
	
);
die(json_encode($output));



if(!$status){
  $output = array(
			"status" => false,
			"error" => "",
			"errorCode" => "402",
			"posts" => ""
		);
		die(json_encode($output));
	}

		
?>