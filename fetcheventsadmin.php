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




/****************
  3. MAIN LOGIC
*****************/

//3.1 CONNECTION TO CUSTOM DATABASE
define('INCLUDE_CHECK', true);
require 'connect_'.$schoolCode.'.php';

$status = false;
$limiter = "";
if(isset($_POST['page'])){
	$range = $_POST['page'] * 10;
	$limiter = " LIMIT  {$range}, 10";	
}


$list = mysql_query("SELECT * FROM `d_events` WHERE 1 ORDER BY `id`".$limiter);


while($event = mysql_fetch_assoc($list)){
		$status = true;
	 // to grant edit or delete status to owner
		$event['date'] = date("d-m-Y", strtotime($event['date']));
	 	
		$response [] = array(
				"id"=>$event['id'],
				"title"=>$event['title'],
				"brief"=>$event['brief'],
				"venue"=>$event['venue'],
				"eventDate"=>$event['date'],
				"isRecurring"=>$event['isRecurring']==1? true:false,
				"recurranceFrequency"=>$event['recurranceFrequency'],
				"timeFrom"=>$event['timeFrom'],
				"timeTo"=>$event['timeTo'],
				"host"=>$event['host'],
				"isRestricted"=>$event['isRestricted']==1?true:false,
				"targetAudience"=>$event['targetAudience'],
				"isPhoto"=>$event['isPhoto'] == 1? true: false,
				"photoURL"=>$event['photoURL'],
				"status"=>$event['status'],
				"user"=>$event['user'],
				"isOwner"=>($event['user'] == $admin_mobile)? true: false,
				"timestamp"=>$event['timestamp'],
				"recursionId"=>$event['recursionId']

		);
}

$figure_event_total = 0;
$figure_event = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) AS total FROM `d_events` WHERE 1"));
if($figure_event['total'] != "")
{
	$figure_event_total = $figure_event['total'];
}

/*******************
//event last created on
$event_last = "";
$event_last_check = mysql_fetch_assoc(mysql_query("SELECT `date` FROM `d_events` WHERE 1 ORDER BY `id` LIMIT 1"));
if($event_last_check['date'] != ""){
	$event_last = $event_last_check['date'];
}
**********************/

//no of events on that particular day
$figure_event_today = 0;
$date = strtotime($today);
$currentDate = date("Y-m-d", $date);


$event_today = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) AS total FROM `d_events` WHERE `date` = '{$currentDate}'"));
if($event_today['total'] != "")
{
	$figure_event_today = $event_today['total'];
}





$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response ,
	"totalEvents" => $figure_event_total ,
	"eventToday" => $figure_event_today
	
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