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
	Brief: To create an students' list

	Author: Ameen
	First Created: 19-09-2017
	Last Modified: 19-09-2017 @Shafeef
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

if(!isset($_POST['key'])){
	$output = array(
			"status" => false,
			"error" => "Search Key is Missing",
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

$key = $_POST['key'];
$searchKeys = explode("-", $key);

$name_query = mysql_query("SELECT * FROM `d_studentsmasterlist` WHERE `fName` LIKE '%{$key}%' || `lName` LIKE '%{$key}%' || CONCAT(`fName`, ' ', `lName`) LIKE '%{$key}%'");
$roll_query = mysql_query("SELECT * FROM `d_studentsmasterlist` WHERE `roll` = '{$key}'");
$regNo_query = mysql_query("SELECT * FROM `d_studentsmasterlist` WHERE `regNo` = '{$key}'");
$class_query = mysql_query("SELECT * FROM `d_studentsmasterlist` WHERE `class` = '{$searchKeys[0]}' && `division` = '{$searchKeys[1]}'");
$mobile_query = mysql_query("SELECT * FROM `d_studentsmasterlist` WHERE `contactPhone` = '{$key}'");

$exists = false;

while($name_result = mysql_fetch_assoc($name_query)){

		$exists = true;

		$father_result = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_parentsmasterlist` WHERE `parentId` = '{$name_result['fatherId']}'"));
		$mother_result = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_parentsmasterlist` WHERE `parentId` = '{$name_result['motherId']}'"));
	 	
		$response[] = array(
				"roll"=>$name_result['roll'],
				"regNo"=>$name_result['regNo'],
				"class"=>$name_result['class'],
				"fName"=>$name_result['fName'],
				"lName"=>$name_result['lName'],
				"division"=>$name_result['division'],
				"height"=>$name_result['height'],
				"weight"=>$name_result['weight'],
				"dob"=>$name_result['dob'],
				"doj"=>$name_result['doj'],
				"stream"=>$name_result['stream'],
				"section"=>$name_result['section'],
				"gender"=>$name_result['gender'],
				"fatherName"=>$father_result['name'],
				"fatherOccupation"=>$father_result['occupation'],
				"fatherIncome"=>$father_result['income'],
				"fatherPhone"=>$father_result['mobile'],
				"motherName"=>$mother_result['name'],
				"motherOccupation"=>$mother_result['occupation'],
				"motherIncome"=>$mother_result['income'],
				"motherPhone"=>$mother_result['mobile'],
				"contactAddress"=>$name_result['contactAddress'],
				"contactPhone"=>$name_result['contactPhone'],
				"isPhoto"=>$name_result['isPhoto'],
				"url"=>$name_result['url'],
				"religion"=>$name_result['religion'],
				"caste"=>$name_result['caste'],
				"location"=>$name_result['city'],
				"bloodGroup"=>$name_result['bloodGroup']
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}

while($roll_result = mysql_fetch_assoc($roll_query)){

		$exists = true;

		$father_result = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_parentsmasterlist` WHERE `parentId` = '{$roll_result['fatherId']}'"));
		$mother_result = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_parentsmasterlist` WHERE `parentId` = '{$roll_result['motherId']}' "));
	 	
		$response[] = array(
				"roll"=>$roll_result['roll'],
				"regNo"=>$roll_result['regNo'],
				"class"=>$roll_result['class'],
				"fName"=>$roll_result['fName'],
				"lName"=>$roll_result['lName'],
				"division"=>$roll_result['division'],
				"height"=>$roll_result['height'],
				"weight"=>$roll_result['weight'],
				"dob"=>$roll_result['dob'],
				"doj"=>$roll_result['doj'],
				"stream"=>$roll_result['stream'],
				"section"=>$roll_result['section'],
				"gender"=>$roll_result['gender'],
				"fatherName"=>$father_result['name'],
				"fatherOccupation"=>$father_result['occupation'],
				"fatherIncome"=>$father_result['income'],
				"fatherPhone"=>$father_result['mobile'],
				"motherName"=>$mother_result['name'],
				"motherOccupation"=>$mother_result['occupation'],
				"motherIncome"=>$mother_result['income'],
				"motherPhone"=>$mother_result['mobile'],
				"contactAddress"=>$roll_result['contactAddress'],
				"contactPhone"=>$roll_result['contactPhone'],
				"isPhoto"=>$roll_result['isPhoto'],
				"url"=>$roll_result['url'],
				"religion"=>$roll_result['religion'],
				"caste"=>$roll_result['caste'],
				"location"=>$roll_result['city'],
				"bloodGroup"=>$roll_result['bloodGroup']
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}

while($regNo_result = mysql_fetch_assoc($regNo_query)){

		$exists = true;

		$father_result = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_parentsmasterlist` WHERE `parentId` = '{$regNo_result['fatherId']}'"));
		$mother_result = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_parentsmasterlist` WHERE `parentId` = '{$regNo_result['motherId']}' "));
	 	
		$response[] = array(
				"roll"=>$regNo_result['roll'],
				"regNo"=>$regNo_result['regNo'],
				"class"=>$regNo_result['class'],
				"fName"=>$regNo_result['fName'],
				"lName"=>$regNo_result['lName'],
				"division"=>$regNo_result['division'],
				"height"=>$regNo_result['height'],
				"weight"=>$regNo_result['weight'],
				"dob"=>$regNo_result['dob'],
				"doj"=>$regNo_result['doj'],
				"stream"=>$regNo_result['stream'],
				"section"=>$regNo_result['section'],
				"gender"=>$regNo_result['gender'],
				"fatherName"=>$father_result['name'],
				"fatherOccupation"=>$father_result['occupation'],
				"fatherIncome"=>$father_result['income'],
				"fatherPhone"=>$father_result['mobile'],
				"motherName"=>$mother_result['name'],
				"motherOccupation"=>$mother_result['occupation'],
				"motherIncome"=>$mother_result['income'],
				"motherPhone"=>$mother_result['mobile'],
				"contactAddress"=>$regNo_result['contactAddress'],
				"contactPhone"=>$regNo_result['contactPhone'],
				"isPhoto"=>$regNo_result['isPhoto'],
				"url"=>$regNo_result['url'],
				"religion"=>$regNo_result['religion'],
				"caste"=>$regNo_result['caste'],
				"location"=>$regNo_result['city'],
				"bloodGroup"=>$regNo_result['bloodGroup']
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}

while($class_result = mysql_fetch_assoc($class_query)){

		$exists = true;

		$father_result = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_parentsmasterlist` WHERE `parentId` = '{$class_result['fatherId']}'"));
		$mother_result = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_parentsmasterlist` WHERE `parentId` = '{$class_result['motherId']}'"));
	 	
		$response[] = array(
				"roll"=>$class_result['roll'],
				"regNo"=>$class_result['regNo'],
				"class"=>$class_result['class'],
				"fName"=>$class_result['fName'],
				"lName"=>$class_result['lName'],
				"division"=>$class_result['division'],
				"height"=>$class_result['height'],
				"weight"=>$class_result['weight'],
				"dob"=>$class_result['dob'],
				"doj"=>$class_result['doj'],
				"stream"=>$class_result['stream'],
				"section"=>$class_result['section'],
				"gender"=>$class_result['gender'],
				"fatherName"=>$father_result['name'],
				"fatherOccupation"=>$father_result['occupation'],
				"fatherIncome"=>$father_result['income'],
				"fatherPhone"=>$father_result['mobile'],
				"motherName"=>$mother_result['name'],
				"motherOccupation"=>$mother_result['occupation'],
				"motherIncome"=>$mother_result['income'],
				"motherPhone"=>$mother_result['mobile'],
				"contactAddress"=>$class_result['contactAddress'],
				"contactPhone"=>$class_result['contactPhone'],
				"isPhoto"=>$class_result['isPhoto'],
				"url"=>$class_result['url'],
				"religion"=>$class_result['religion'],
				"caste"=>$class_result['caste'],
				"location"=>$class_result['city'],
				"bloodGroup"=>$class_result['bloodGroup']
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}

while($mobile_result = mysql_fetch_assoc($mobile_query)){

		$exists = true;

		$father_result = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_parentsmasterlist` WHERE `parentId` = '{$mobile_result['fatherId']}'"));
		$mother_result = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_parentsmasterlist` WHERE `parentId` = '{$mobile_result['motherId']}'"));
	 	
		$response[] = array(
				"roll"=>$mobile_result['roll'],
				"regNo"=>$mobile_result['regNo'],
				"class"=>$mobile_result['class'],
				"fName"=>$mobile_result['fName'],
				"lName"=>$mobile_result['lName'],
				"division"=>$mobile_result['division'],
				"height"=>$mobile_result['height'],
				"weight"=>$mobile_result['weight'],
				"dob"=>$mobile_result['dob'],
				"doj"=>$mobile_result['doj'],
				"stream"=>$mobile_result['stream'],
				"section"=>$mobile_result['section'],
				"gender"=>$mobile_result['gender'],
				"fatherName"=>$father_result['name'],
				"fatherOccupation"=>$father_result['occupation'],
				"fatherIncome"=>$father_result['income'],
				"fatherPhone"=>$father_result['mobile'],
				"motherName"=>$mother_result['name'],
				"motherOccupation"=>$mother_result['occupation'],
				"motherIncome"=>$mother_result['income'],
				"motherPhone"=>$mother_result['mobile'],
				"contactAddress"=>$mobile_result['contactAddress'],
				"contactPhone"=>$mobile_result['contactPhone'],
				"isPhoto"=>$mobile_result['isPhoto'],
				"url"=>$mobile_result['url'],
				"religion"=>$mobile_result['religion'],
				"caste"=>$mobile_result['caste'],
				"location"=>$mobile_result['city'],
				"bloodGroup"=>$mobile_result['bloodGroup']
		);
}

if($exists){

	$output = array(
	"status" => true,
	"error" => "",
	"errorCode" => "",
	"response" => $response
	
	);
	die(json_encode($output));

}

if(!$exists){
  $output = array(
			"status" => false,
			"error" => "",
			"errorCode" => "No data found with key as ".$key,
			"posts" => ""
		);
		die(json_encode($output));
	}

		
?>