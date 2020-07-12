<?php
//this file contains al the basic configurations we will require from process to process. 

// date time function
function tdate(){
date_default_timezone_set('Africa/Lagos'); //set default time zone to the current local time zone
$date = date('d/m/Y');
$timeh = date('h');
$timem = date('i'); //minutes
$timeap = date('a'); //am or pm
$timeR = "$timeh:$timem$timeap"; //current time
$datetime = "$date $timeR";
return $datetime;
		 }
				 
?>