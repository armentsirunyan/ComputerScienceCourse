<?php

session_start();
setcookie('userid', 1);
setcookie('token', 1);
session_unset();
session_destroy();

header('Location:login.php');
?>