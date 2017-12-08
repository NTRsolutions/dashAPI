<?php

error_reporting(1);

//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//json decode the values recieved
$_POST = json_decode(file_get_contents('php://input'), true);


$mobile = "8075326268";
$roll = "45";

//$student_info = mysql_fetch_assoc(mysql_query("SELECT `division`, `class` FROM `dash_students` WHERE `mobile`='{$mobile}' AND `rollNo` = '{$roll}'"));
//$mydivision = $student_info['division'];
//$myclass = $student_info['class'];

$query = mysql_query("SELECT * FROM `dash_notifications` ORDER BY date WHERE 1");
while($notification = mysql_fetch_assoc($query)){
	if($notification['viewers'] == 0){ // Meant to be for all the students
		$response [] = array(
				"id"=>$notification['id'],
				"brief"=>$notification['brief'],
				"photoFlag"=>$notification['photoFlag'] == 1? true: false,
				"url"=>$notification['photoURL'],
				"userID"=>$notification['userID'],
				"likes"=>$notification['likes'],
				"liked"=>$notification['liked'],
				"date"=>$notification['timestamp']
		);
	}
	else if ($notification['viewers'] == 1){ //Meant to be for whole class 
		$audience = json_decode($notification['viewersList']);
		foreach($audience as $class){
			if($class->class == $myclass){
				$response [] = array(
						"id"=>$notification['id'],
						"brief"=>$notification['brief'],
						"photoFlag"=>$notification['photoFlag'] == 1? true: false,
						"url"=>$notification['photoURL'],
						"userID"=>$notification['userID'],
						"likes"=>$notification['likes'],
						"liked"=>$notification['liked'],
						"date"=>$notification['timestamp']
				);
			}
		}
	}
}

		$output = array(
			"status" => true,
			"error" => "",
			"errorCode" => "",
			"posts" => $response
		);
	echo json_encode($output);

?>