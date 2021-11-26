<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>validate</title>
</head>
<body>
	<?php

		function function_alert($message){
			echo "<script type='text/javascript'>alert('$message')</script>";
		}

		$reg = '/\S+@\S+\.\S+/';

		$fname = $_GET['fname'];
		$lname = $_GET['lname'];
		$passwd = $_GET['passwd'];
		$email = $_GET['email'];


		$err_msg = "chuỗi từ 2-30 kí tự";

		if (strlen($fname) < 2 || strlen($fname) > 30){
			function_alert("First name: $err_msg");
		} 
		else if (strlen($lname) < 2 || strlen($lname) > 30){
			function_alert("Last name: $err_msg");
		} 
		elseif (!preg_match($reg, $email)) {
			function_alert("theo định dạng email: <sth>@<sth>.<sth>");
		}
		else if (strlen($passwd) < 2 || strlen($passwd) > 30){
			function_alert("Password: $err_msg");
		}  
		else{
			function_alert("Complete!");
		}



	?>
</body>
</html>



