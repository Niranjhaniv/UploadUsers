<?php
	
    for ($i=1; $i<=100; $i++)
        
    {  	
        $output = "";
        if($i %3 === 0) {
            $output = $output ."foo";
        }
        if($i % 5 === 0) {
            $output = $output ."bar";
        }
        echo $output === "" ? $i : $output;
        echo "\t";
    } 

?>