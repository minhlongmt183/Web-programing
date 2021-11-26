<?php
    echo "<table style='border-collapse: collapse; background-color: #ffff00' >";

    for ($i = 1; $i < 8; $i++){
        echo "<tr style='border: 1px solid black;'>";

        for($j = 1; $j <8; $j++){
            $val = $i*$j;
            echo "<td style='border: 1px solid black;'> $val </td>";
        }
        echo "</tr>";
    }
?>
