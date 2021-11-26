<?php
    echo "Dung vong for <br>";

    for ($i = 1; $i < 100; $i+=2){
        echo $i." ";
    }
    
    echo("<br>");
    echo "Dung vong While <br>";
    $i = 1;
    while ($i < 100){
        echo $i." ";
        $i += 2;
    }
?>