<?php

	$host='localhost';
	$userName = 'root';
	$password = '';
	$database = 'examples';

	$conn = mysqli_connect($host, $userName, $password);

	if (!$conn){
		die('COuld not connect: ' . mysqli_error($conn));
	}

	$db_selected = mysqli_select_db($conn, $database);
	if (!$db_selected){
		die('Cant\'t use examples: '.mysqli_error($conn));
	}


?>