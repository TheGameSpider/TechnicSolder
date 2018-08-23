<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(empty($_GET['build'])){
	die("Build not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("You need to be logged in!");
}
mysqli_query($conn, "DELETE FROM `builds` WHERE `id` = '".$_GET['build']."'");
header('Location: index.php;