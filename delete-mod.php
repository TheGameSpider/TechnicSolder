<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(empty($_GET['mn'])){
	die("Mod not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("You need to be logged in!");
}
mysqli_query($conn, "DELETE FROM `mods` WHERE `id` = '".$_GET['mn']."'");
header('Location: mods.php');