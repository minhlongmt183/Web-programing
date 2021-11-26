

<?php
/**
 * Rules: 
 * 	id: so nguyen
 * name: chuoi, dai 5-40 ki tu
 * year: so nguyen, khoang tu: 1990-2015
 * */

$id_error = "";
$name_error = "";
$year_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

	$errors = [];


	// validate id
	if (empty(trim($_POST['id']))){
		$errors['id']['required'] = "Id khong duoc de trong";
	}

	else if (!filter_var(trim($_POST['id']), FILTER_VALIDATE_INT)){
		$errors['id']['integer'] = "Id phai la so nguyen";

	}

	
	if (empty($errors)){

		$newid = (int)trim($_POST['id']);

		
		include('connect.php');

			// Check if id is existed

		$query = "SELECT * FROM cars WHERE id = $newid";
		// echo "$query <br/>";
		// Perform Query
		$result = mysqli_query($conn, $query);



		if (!$result)
		{
			die("Lay du lieu that bai ". mysqli_error($conn));
			
		}
		else if (empty( mysqli_fetch_assoc($result)))
		{
			die("Id khong ton bai trong bang du lieu");
		}


		$query = "	DELETE FROM cars WHERE id = $newid";

		// echo "$query <br/>";
		// Perform Query
		$result = mysqli_query($conn, $query);



		if ($result)
		{
			echo "Xoa thanh cong";
		}
		else
		{
			die("Xoa that bai ". mysqli_error($conn));
		}

		mysqli_close($conn);







	}
	else{
		echo "Du lieu khong hop le<br/>";
	}


}



?>





<form  method="post">
	<h1>Xoa record</h1>
	<table>
		<tr>
			<td>Id</td>
			<td>
				<input type="text" name="id"> <br>
				<?php

					echo (!empty($errors['id']['required']))? '<span style="color: red;">'.$errors['id']['required'].'<?php echo $id_error; ?></span>' : '';

					echo (!empty($errors['id']['integer']))? '<span style="color: red;">'.$errors['id']['integer'].'<?php echo $id_error; ?></span>' : '';
				?>
				
			</td>
		</tr>
		</tr>
	</table>
	<input type="submit" value="Delete">
</form>
