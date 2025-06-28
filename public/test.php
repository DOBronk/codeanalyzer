<?php

$host = '172.19.0.2'; 
$port = 3306; 
$waitTimeoutInSeconds = 1; 
if($fp = fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds)){   
   echo "Poort open!";
} else {
   echo "Poort gesloten!";

} 
fclose($fp);
?>