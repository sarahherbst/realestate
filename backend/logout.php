<?php
	session_start();
	session_destroy();
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

	header('Content-Type: text/html; charset=utf-8');
	//Datenbank einlesen
	require('../connection.inc.php');
	require('../function.inc.php');

	header('Location: login.php');
?>
