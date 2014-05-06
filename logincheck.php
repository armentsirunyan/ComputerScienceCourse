<?php
require_once("mysqlconnect.php");

function process_session_established($session_just_established = false)
{
	if(isset($_GET['location']))
	{
		$location = $_GET['location'];		
		header("Location:$location");
		exit;
	}
	else if($session_just_established)
	{
		$location = 'index.php';
		header("Location:$location");
		exit;
	}
}

function process_logon_failed()
{
	$_SESSION['loginerror'] = 'Неправильный адрес эл.почты или пароль';
	$loginlink = "login.php";
	if(isset($_GET['location']))
	{
		$location = $_GET['location'];
		$loginlink.="?location=$location";
	}
	header("Location: $loginlink");
	exit;	
}

function process_verify_credentials()
{
	global $sqllink;
	
	$login = $_POST['login'];
	$password = $_POST['password'];
	$passhash = sha1($password);
	
	$query = "SELECT id, fullname, email, role, `group` 
			  FROM users 
			  WHERE email = '$login'
			    AND password = '$passhash'";
	$result = mysqli_query($sqllink, $query) or die("Error executing SQL Query");
	$num_rows = mysqli_num_rows($result);
	if($num_rows == 1)
	{
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$_SESSION['userid'] = $row['id'];
		$_SESSION['fullname'] = $row['fullname'];
		$_SESSION['email'] = $row['email'];
		$_SESSION['role'] = $row['role'];
		$_SESSION['group'] = $row['group'];
		mysqli_free_result($result);
		process_session_established(true);
	}
	else 
	{
		process_logon_failed();
	}		
	exit;
}

function redirect_to_logon_page()
{
	header("Location:login.php?location=".urlencode($_SERVER['REQUEST_URI']));
	exit;
}

function check_session_and_permissions()
{	
	if(isset($_SESSION['userid']))
	{
		process_session_established();
	}
	else if(isset($_POST['login']) and isset($_POST['password']))
	{
		process_verify_credentials();
	}
	else
	{
		redirect_to_logon_page();	
	}
}

session_start();
unset($_SESSION['loginerror']);
check_session_and_permissions();

?>