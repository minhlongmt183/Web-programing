<?php


	function exponential($a){
		$result = 1;

		for($x = 1; $x <= $a; $x++){
			$result *= $x;
		}

		
		return $result;
	}


	$a = $b = $op = $result = '';
	if (!empty($_POST)){
		if (isset($_POST['a'])){
			$a = $_POST['a'];
		}
		if (isset($_POST['b'])){
			$b = $_POST['b'];
		}
		if (isset($_POST['op'])){
			$op = $_POST['op'];

		}


		switch ($op) {
			case '+':
				$result = $a + $b;
				break;
			case '-':
				$result = $a - $b;
				break;
			case '*':
				$result = $a * $b;
				break;
			case '/':
				$result = $a / $b;
				break;
			case '%':
				$result = $a % $b;
				break;
			case 'inv':
				$result = 1.0 / $a;
				break;
			case '!':
				$result = exponential($a);
				break;
			
		}


	}

	echo $result;
?>