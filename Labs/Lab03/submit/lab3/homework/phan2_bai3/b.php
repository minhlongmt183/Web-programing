

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

	// validate name
		// validate id
	if (empty(trim($_POST['name']))){
		$errors['name']['required'] = "name khong duoc de trong";
	}

	else {
		$name_len = strlen(trim($_POST['name']));

		if ($name_len < 5 || $name_len > 40)
			$errors['name']['range'] = "name dai 5-40 ki tu";
	} 

	// validate year
	if (empty(trim($_POST['year']))){
		$errors['year']['required'] = "Year khong duoc de trong";
	}

	else {
		if (!filter_var(trim($_POST['year']), FILTER_VALIDATE_INT, [
			'options' => ['min_range' => 1990, 'max_range' <= 2015]
		]))
			$errors['year']['invalid'] = "Year la so nguyen, nam khoang 1990-2015";
	}

	
	if (empty($errors)){

		$newid = (int)trim($_POST['id']);
		$newname = $_POST['name'];
		$newyear = (int)trim($_POST['year']);

		
		include('connect.php');

		$query = "INSERT INTO cars(id, name, year) VALUES($newid, '$newname', $newyear);";

		echo "$query <br/>";
		// Perform Query
		$result = mysqli_query($conn, $query);



		if ($result)
		{
			echo "Insert thanh cong";
		}
		else
		{
			echo "Insert that bai ". mysqli_error($conn);
		}

		mysqli_close($conn);







	}
	else{
		echo "Du lieu khong hop le<br/>";
	}


}



?>





<form  method="post">
	<h1>Them du lieu</h1>
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
		<tr>
			<td>Name</td>
			<td>
				<input type="text" name="name"> <br>

				<?php

					echo (!empty($errors['name']['required']))? '<span style="color: red;">'.$errors['name']['required'].'<?php echo $id_error; ?></span>' : '';

					echo (!empty($errors['name']['range']))? '<span style="color: red;">'.$errors['name']['range'].'<?php echo $id_error; ?></span>' : '';
				?>

				
			</td>
		</tr>
		<tr>
			<td>Year</td>
			<td>
				<input type="text" name="year"> <br>

				<?php

					echo (!empty($errors['year']['required']))? '<span style="color: red;">'.$errors['year']['required'].'<?php echo $id_error; ?></span>' : '';

					echo (!empty($errors['year']['invalid']))? '<span style="color: red;">'.$errors['year']['invalid'].'<?php echo $id_error; ?></span>' : '';
				?>

			</td>
		</tr>
	</table>
	<input type="submit" value="Insert">
</form>
