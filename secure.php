<?php

	$output = array(
			"status" => false,
			"error" => "Invalid Access",
			"errorCode" => 502,
			"response" => ""
	);
	
	if(!defined('SECURE_CHECK')) die(json_encode($output));

	$encryptionMethod = "AES-256-CBC";
	$secretHash = "7a6169746f6f6e746f6b656e";
	$tokenExpiryDays = 14; //14 days

?>
