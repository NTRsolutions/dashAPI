<?php

error_reporting(0);

 //http://stackoverflow.com/questions/18382740/cors-not-working-php
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
 
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
 
        exit(0);
    }

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//json decode the values recieved
$_POST = json_decode(file_get_contents('php://input'), true);

/*
//token related operations
$mobile=$tokenId['mobile'];
*/
$mobile='8075326268';

//Params passed or not
if(!isset($_POST['rollNo'])){
	$output = array(
		"status" => false,
		"error" => "Roll No is not set",
		"errorCode" => "102",
		"response" => ""
	);
	die(json_encode($output));
}

//assign value to local variable
$rollNo = $_POST['rollNo'];

//check if a student exists
$exists = false;
$student = mysql_query("SELECT `rollNo`, `name`, `mobile`, `class`, `division`, `stream`, `address`, `email`, `dob`, `image`, `about`  FROM `dash_student` WHERE `rollNo`='{$rollNo}' ");

//if exists
while($row = mysql_fetch_assoc($student)){
	$exists=true;
	$response=array(
			"message" => "the student detials found",
			"rollNo" => $row['rollNo'],
			"name" => $row['name'],
			"class" => $row['class'],
			"mobile" => $row['mobile'],
			"division" => $row['division'],
			"stream" => $row['stream'],
		//	"lastLogin" => $row['lastLogin'],
			"address" => json_decode($row['address']),
			"email" => $row['email'],
			"dob" => $row['dob'],
			"image" => $row['image'],
			"about" =>  $row['about']
			);
	
	$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"response" => $response
		);
	echo json_encode($output);
}

//if not found
if(!$exists){
	$output = array(
			"status" => false,
			"error" => "User does not Exists",
			"errorCode" => "202",
			"response" => ""
		);
		die(json_encode($output));
}
?>
