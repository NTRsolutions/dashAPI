<?php
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

define('INCLUDE_CHECK', true);
require 'connect.php';

$searchInput = $_GET['rollid'];

$queryResult = mysql_query("SELECT name FROM d_test WHERE roll='{$searchInput}'");
$queryResultArray = mysql_fetch_assoc($queryResult);


if(!$queryResultArray['name']){
	$output = array(
		"status" => false,
		"roll" => $searchInput,
		"name" => "",
		"age" => ""	
	);
	echo json_encode($output);
}
else{

	$output = array(
		"status" => true,
		"roll" => $searchInput,
		"name" => $queryResultArray['name'],
		"age" => 21	
	);
	echo json_encode($output);
}
?>