<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(empty($_GET['modpack'])){
	die("Modpack not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("You need to be logged in!");
}
mysqli_query($conn, "DELETE FROM `builds` WHERE `modpack` = '".$_GET['modpack']."'");
mysqli_query($conn, "DELETE FROM `modpacks` WHERE `id` = '".$_GET['modpack']."'");
header('Location: index.php');