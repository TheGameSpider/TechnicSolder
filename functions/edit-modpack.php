<?php
session_start();
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Modpack not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("You need to be logged in!");
}
mysqli_query($conn, "UPDATE `modpacks` SET `name` = '".$_GET['name']."', `display_name` = '".mysqli_real_escape_string($conn, $_GET['display_name'])."' WHERE `id`=".$_GET['id']);
header('Location: /modpack?id='.$_GET['id']);