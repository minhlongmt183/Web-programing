<?php
	$print_number = function($n){
		switch($n){
			case 0:
				echo "hello<br>";
				break;
			case 1:
				echo "How are you? <br>";
				break;
			case 2:
				echo "I’m doing well, thank you <br>";
				break;
			case 3:
				echo "See you later <br>";
				break;
			case 4:
				echo "Good-bye <br>";
				break;
		}
	};

	$n = 1;
	$print_number($n);


?>