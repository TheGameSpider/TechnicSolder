<?php
session_start();
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Modpack not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("You need to be logged in!");
}
mysqli_query($conn, "DELETE FROM `builds` WHERE `modpack` = '".$_GET['id']."'");
mysqli_query($conn, "DELETE FROM `modpacks` WHERE `id` = '".$_GET['id']."'");
header('Location: /dashboard');