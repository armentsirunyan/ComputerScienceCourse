<?php
$dbhost = "localhost"; 
$dbname = "CourseDB"; 
$dbuser = "root"; 
$dbpass = "";

$sqllink = mysqli_connect($dbhost, $dbuser, $dbpass)
	or die("Unable to connect to MySQL");
	
mysqli_select_db($sqllink, $dbname)
	or die("Unable to select database ".$dbname);
?>
