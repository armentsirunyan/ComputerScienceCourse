<?php
session_start();

$dbhost = "localhost"; 
$dbname = "CourseDB"; 
$dbuser = "root"; 
$dbpass = "";

$sqllink = mysql_connect($dbhost, $dbuser, $dbpass)
	or die("Unable to connect to MySQL");
	
mysql_select_db($dbname, $sqllink)
	or die("Unable to select database ".$dbname);

?>
