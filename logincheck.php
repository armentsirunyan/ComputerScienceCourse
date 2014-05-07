<?php
require_once("mysqlconnect.php");




function generateToken() 
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < 25; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

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

function set_session_vars($userid)
{
	global $sqllink;
	$query = "SELECT id, fullname, email, role, `group` 
			  FROM users 
			  WHERE id = $userid";
			  
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
	}
	else
	{
		die("Something went wrong. Unable to fund user with id $userid or duplicate users.");
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
	
	$query = "SELECT id
			  FROM users 
			  WHERE email = '$login'
			    AND password = '$passhash'";
	$result = mysqli_query($sqllink, $query) or die("Error executing SQL Query");
	$num_rows = mysqli_num_rows($result);
	if($num_rows == 0)
	{
		process_logon_failed();
		exit;
	}
	else 
	{
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$userid = $row['id'];
		mysqli_free_result($result);
		set_session_vars($userid);
		if(isset($_POST['remember_me']))
		{
			$epochtime = time();
			$token = generateToken();
			$query = "INSERT INTO persistentsessions (userid, token, lastupdate)
			          VALUES ($userid, '$token', $epochtime)";
            mysqli_query($sqllink, $query) or die("Error executing SQL Query");
			set_persistent_session_cookies($userid, $token);
		}
		process_session_established(true);
	}		
}

function set_persistent_session_cookies($userid, $token)
{
	$expiration = time() + 60*60*24*2;
	setcookie('userid', $userid, $expiration);
	setcookie('token', $token, $expiration);	
}

function verify_persistent_session()
{
	if(!isset($_COOKIE['userid']) or !isset($_COOKIE['token']))
	{
		return false;
	}
	global $sqllink;
	$userid = $_COOKIE['userid'];
	$token = $_COOKIE['token'];
	$query = "SELECT userid, token
			  FROM persistentsessions
			  WHERE userid = $userid AND token = '$token'";
			  
	$result = mysqli_query($sqllink, $query) or die("Error executing SQL Query");
	$num_rows = mysqli_num_rows($result);
	mysqli_free_result($result);	
	if($num_rows == 0)
	{
		return false;
	}
	$newtoken = generateToken();
	set_persistent_session_cookies($userid, $newtoken);
	$epochtime = time();
    $query = "UPDATE persistentsessions
	          SET token = '$newtoken', lastupdate = $epochtime
			  WHERE userid = $userid AND token = '$token'";
    mysqli_query($sqllink, $query) or die("Error executing SQL Query");
	return true;
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
	else if(verify_persistent_session())
	{
		set_session_vars($_COOKIE['userid']);
		process_session_established();
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