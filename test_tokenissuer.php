<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');
error_reporting(0);

$_POST = json_decode(file_get_contents('php://input'), true);


//Database Connection
define('INCLUDE_CHECK', true);
require 'connect.php';

//Encryption Credentials
define('SECURE_CHECK', true);
require 'secure.php';

$schoolID = 'HMYHSS';
$mobile = "8075326268";
$accessLevel = "ADMIN";

date_default_timezone_set('Asia/Calcutta');
$date = date("Y-m-j");

$loginjson = array(
	"mobile" => $mobile,
	"schoolId" => $schoolID,
	"accessLevel" => $accessLevel,
	"date" => $date
);

$textToEncrypt = json_encode($loginjson);

//To encrypt
$encryptedMessage = openssl_encrypt($textToEncrypt, $encryptionMethod, $secretHash);
$token = $encryptedMessage;

echo $token;

//To Decrypt
$decryptedMessage = openssl_decrypt($encryptedMessage, $encryptionMethod, $secretHash);

//Result
echo "Encrypted: $encryptedMessage <br>Decrypted: $decryptedMessage";

?>
