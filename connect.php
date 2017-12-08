<?php
	if(!defined('INCLUDE_CHECK')) die('Warning: You are not authorized to access this page.');

	$db_host		= 'localhost';
	$db_user		= 'accelerate_admin';
	$db_pass		= 'Jafry@123';
	$db_database		= 'dash_hmyhss'; 

	$link = mysql_connect($db_host, $db_user, $db_pass) or die('Scheduled Website Migration: Please visit after 06:00 PM today. Sorry for the inconvenience.');
	mysql_select_db($db_database, $link);
?>
