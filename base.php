<?php
session_start();

$dbhost = "localhost"; 
$dbname = "ComputerScienceCourse"; 
$dbuser = "root"; 
$dbpass = "";

$sqllink = mysql_connect($dbhost, $dbuser, $dbpass)
	or die("Unable to connect to MySQL");
	
mysql_select_db($dbname, $sqllink)
	or die("Ubable to select database ".$dbname);

?>
