<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(empty($_POST['name'])){
	die("Email not specified.");
}
if(empty($_POST['display_name'])){
	die("Name not specified.");
}
if(empty($_POST['perms'])){
	die("perms not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized request or login session has expired.");
}
if($_SESSION['user']!==$config['mail']) {
	die("insufficient permission!");
}
$sql = mysqli_query($conn,"UPDATE `users` SET `display_name` = '".$_POST['display_name']."', `perms` = '".$_POST['perms']."' WHERE `name` = '".$_POST['name']."'");
if($sql) {
	echo '<span class="text-success">Saved.</span>';
} else {
	die('<span class="text-danger">An error has occured</span>');
}