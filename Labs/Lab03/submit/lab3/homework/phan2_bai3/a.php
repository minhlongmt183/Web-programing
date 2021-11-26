<?php

	include('connect.php');

	$query = "SELECT * From cars";

	// Perform Query
	$result = mysqli_query($conn, $query);

	$data = array();
	
	// // Show in table
	// echo "<table bolder=1>";

	// echo "<tr><th>Id</th><th>Name</th><th>Year</th></tr>";

	// while ($row=mysqli_fetch_assoc($result)){
	// 	echo "<td>{$Row['id']}</td>";
	// 	echo "<td>{$Row['name']}</td>";
	// 	echo "<td>{$Row['year']}</td></tr>";
	// }

	// echo "</table>";

	while ($row = mysqli_fetch_assoc($result)){
		$data[] = $row;
	}

	echo json_encode($data);
	mysqli_close($conn);
?>


