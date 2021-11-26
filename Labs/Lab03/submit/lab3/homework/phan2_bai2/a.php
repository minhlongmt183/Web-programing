<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>a.php</title>
	<style type="text/css">
		table, th, td {
			border: 1px solid black;
		}
	</style>
</head>
<body>
<?php

	include('connect.php');

	$query = "SELECT * From cars";

	// Perform Query
	$result = mysqli_query($conn, $query);


	
	// Show in table
	echo "<table bolder=1>";

	echo "<tr><th>Id</th><th>Name</th><th>Year</th></tr>";

	while ($Row=mysqli_fetch_assoc($result)){
		echo "<tr><td>{$Row['id']}</td>";
		echo "<td>{$Row['name']}</td>";
		echo "<td>{$Row['year']}</td></tr>";
	}

	echo "</table>";

	mysqli_close($conn);
?>
</body>
</html>


