<?php
//localhost connection variables
function con_string(){	/*call the connection string*/
$con = mysqli_connect("localhost","username","password","analyzer_db");
return $con;
					  }				
?>