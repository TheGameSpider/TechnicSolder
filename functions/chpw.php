<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized request or login session has expired.");
}
if(empty($_POST['pass'])) {
	die("Password not specified.");
}
$pass = $_POST['pass'];
$sql = mysqli_query($conn,"UPDATE `users` SET `pass` = '".$pass."' WHERE `name` = '".$_SESSION['user']."'");
echo mysqli_error($conn);
header("Location: ".$config['dir']."user");
exit();