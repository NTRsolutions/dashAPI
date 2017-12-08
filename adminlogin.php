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

$mobile = $_POST['mobile'];
$password = $_POST['password'];

if(!isset($_POST['mobile'])){
	$output = array(
		"status" => false,
		"error" => "Username Missing"
	);
	die(json_encode($output));
}

if(!isset($_POST['password'])){
	$output = array(
		"status" => false,
		"error" => "Password Missing"
	);
	die(json_encode($output));
}

date_default_timezone_set('Asia/Calcutta');
$date = date("Y-m-j");


$query = "SELECT * from d_admins WHERE `mobile`='{$mobile}' AND `password`='{$password}'";
$main = mysql_query($query);
$rows = mysql_fetch_assoc($main);

$status = '';
$error = '';


if(!empty($rows)){
	$responsejson = array(
		"schoolCode" => $rows['schoolCode'],
		"date" => $date,
		"role" => $rows['role'],
		"mobile" => $mobile
	);
	$textToEncrypt = json_encode($responsejson);
	$response = openssl_encrypt($textToEncrypt, $encryptionMethod, $secretHash);	
	
	//Minimum Info on School
	$info = mysql_fetch_assoc(mysql_query("SELECT name, city from d_schoolList WHERE code = '{$rows['schoolCode']}'"));
	$schoolName = $info['name'];
	$schoolCode = $rows['schoolCode'];
	$schoolCity = $info['city'];

	$status = true;
}
else{
	$response = "";
	$schoolName = "";
	$schoolCode = "";
	$schoolCity = "";
	$status = false;
	$error = 'Incorrect Username/Password';
}

$output = array(
	"status" => $status,
	"error" => $error,
	"errorCode" => 0,
	"schoolCode" => $schoolCode,
	"schoolName" => $schoolName,
	"schoolCity" => $schoolCity,	
	"response" => $response

);

echo json_encode($output);

?>
