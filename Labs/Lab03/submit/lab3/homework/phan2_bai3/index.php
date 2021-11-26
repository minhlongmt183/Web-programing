<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<title>bai3</title>
	<style type="text/css">
		table, th, td {
			border: 1px solid black;
		}
	</style>
</head>
<body>
	<div id=contents>
		
	</div>
	<button value="show-data">Show data</button>

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
		<input type="button" value="insert">
	</form>


<script>

	var ajax = new XMLHttpRequest();
	var method = "POST";
	var url = "a.php";
	var asynchronous = true;

	$(function(){
	    $('button').click(function() {
	        var v = $(this).val();
	        switch(v){
	            case 'show-data':
	            	showdata();
	            	break;	  
	            case 'insert':
	            	insertdata();
	            	break;      
            }

	    })
	})


	function showdata()
	{
		

		ajax.open(method, url, asynchronous);

		// sending a request
		ajax.send();

		// receiving respone from a.php
		ajax.onreadystatechange = function(){
			if (this.readyState == 4 && this.status == 200){
				var data = JSON.parse(this.responseText);
				console.log(data);

				var html = "";
				html += "<table bolder=1>";
				html += "<tr><th>Id</th><th>Name</th><th>Year</th></tr>";
				// looping through the data

				for (var a = 0; a < data.length; a++)
				{
					console.log(html);
					var id = data[a].id;
					var name = data[a].name;
					var year = data[a].year;

					html += "<tr>";
					html += "<td>" + id + "</td>";
					html += "<td>" + name + "</td>";
					html += "<td>" + year + "</td>";
					html += "</tr>";
				}
				html += "</table>";

				document.getElementById("contents").innerHTML = html;



			}
		}
	}

	function insertdata()
	{
		alert("insertdata");
	}



	

</script>
</body>
</html>


