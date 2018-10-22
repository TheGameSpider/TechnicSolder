<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(empty($_POST['id'])){
	die("id not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized request or login session has expired.");
}
if($_SESSION['user']!==$config['mail']) {
	die("insufficient permission!");
}
$sql = mysqli_query($conn,"DELETE FROM `users` WHERE `id` = ".$_POST['id']);
if($sql) {
	echo '<span class="text-success">User removed.</span>';
} else {
	die('<span class="text-danger">An error has occured</span>');
}