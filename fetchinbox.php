<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

$_POST = json_decode(file_get_contents('php://input'), true);

//Encryption Validation
if(!isset($_POST['token'])){
	$output = array(
		"status" => false,
		"error" => "Access Token is missing"
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
		"error" => "Expired Token"
	);
	die(json_encode($output));
}


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



$status = false;
$error = "No results found";

$limiter = "";
if(isset($_POST['id'])){
	$range = $_POST['id'] * 10;
	$limiter = " LIMIT  {$range}, 10";	
}

//Search Enabled?
if(isset($_POST['searchkey']) || $_POST['searchkey']!=""){

	$mykey = $_POST['searchkey'];
	$isSearchSuccess = false;
	
	//Case 1: Name Search
	$main = mysql_query("SELECT * FROM `z_helpdesk` WHERE `name` LIKE '%{$mykey}%' OR `mobile` LIKE '%{$mykey}%' OR `email` LIKE '%{$mykey}%' OR `comment` LIKE  '%{$mykey}%'  OR `remarks` LIKE '%{$mykey}%' OR `type` LIKE '%{$mykey}%' ORDER BY `id` DESC".$limiter);
	
	while($rows = mysql_fetch_assoc($main))
	{	
		$agent = mysql_fetch_assoc(mysql_query("SELECT `name` FROM `z_roles` WHERE `code`='{$rows['replyAgent']}'")); 
		$response[] =array(
			"id" => $rows['id'],
			"status"=> $rows['status'],
			"userName" => $rows['name'],
			"userMobile" => $rows['mobile'],
			"userEmail" => $rows['email'],
			"isRefund" => $rows['type'] == "REFUND"? true: false,
			"comment" => $rows['comment'],
			"remarks" => $rows['remarks'],
			"date" => $rows['date'],
			"replyDate" => $rows['dateReply'],
			"replyContent" => $rows['reply'],
			"replyAgent" => $agent['name']							
		);
		
		$status = true;
		$error = "";
		$isSearchSuccess = true;
	}
	$status = true;
	$error = "No results match your search.";
	
	//Common Values
	$grand = 0;
	$grand_sum = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `z_helpdesk` WHERE `name` LIKE '%{$mykey}%' OR `mobile` LIKE '%{$mykey}%' OR `email` LIKE '%{$mykey}%' OR `comment` LIKE '%{$mykey}%'  OR `remarks` LIKE '%{$mykey}%' OR `type` LIKE '%{$mykey}%'"));
	if($grand_sum['total'] != ""){
		$grand = $grand_sum['total'];
	}
	
	$unread = 0;
	$unread_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `z_helpdesk` WHERE `status`=0"));
	if($unread_check['total'] != ""){
		$unread = $unread_check['total'];
	}
}
else{
	$main = mysql_query("SELECT * FROM `d_inbox` WHERE 1 ORDER BY `id` DESC".$limiter);
	while($rows = mysql_fetch_assoc($main))
	{	
		$fromAgent = mysql_fetch_assoc(mysql_query("SELECT `fName`, `mName`, `lName` FROM `d_humans` WHERE `uid`='{$rows['userFrom']}'")); 
		$toAgent = mysql_fetch_assoc(mysql_query("SELECT `fName`, `mName`, `lName` FROM `d_humans` WHERE `uid`='{$rows['userTo']}'")); 
		$response[] =array(
			"id" => $rows['id'],
			"status"=> 0,
			"fromName" => $fromAgent['fName']." ".$fromAgent['mName']." ".$fromAgent['lName'],
			"toName" => $toAgent['fName']." ".$toAgent['mName']." ".$toAgent['lName'],
			"fromID" => $rows['userFrom'],
			"fromBrief" => "Parent of Ameen, XII-B",
			"toID" => $rows['userTo'],
			"content" => $rows['content'],
			"status" => 0,
			"type" => "TEST",
			"isStar" => 1,
			"date" => "10.02.2017",
			"replyDate" => "10.02.2017",
			"replyContent" => "my reply",
			"replyAgent" => "7299939974"	
		);
		
		$status = true;
		$error = "";
	}
	
	
	//Common Values
	$grand = 1;
	/*
	$grand_sum = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `z_helpdesk` WHERE 1"));
	if($grand_sum['total'] != ""){
		$grand = $grand_sum['total'];
	}
	*/
	$unread = 0;
	/*
	$unread_check = mysql_fetch_assoc(mysql_query("SELECT COUNT(`id`) as total FROM `z_helpdesk` WHERE `status`=0"));
	if($unread_check['total'] != ""){
		$unread = $unread_check['total'];
	}
	*/
}


$output = array(
		"status" => $status,
		"error"=> $error,
		"response" => $response,
		"count" => $grand,
		"unreadCount" => $unread
	);

echo json_encode($output);

?>
