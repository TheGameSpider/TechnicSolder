<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Must be logged in!");
}
$result = mysqli_query($conn, "INSERT INTO `builds` (`id`, `name`, `minecraft`, `java`, `memory`, `modpack`) VALUES (NULL, '".$_POST['name']."', '".$_POST['mcv']."', '".$_POST['jv']."', '".$_POST['memory']."','".$_POST['modpack']."')");
if(mysqli_affected_rows($conn)!==1) {
	die(mysqli_error($result));
}
header("Location: buildeditor.php?build=".mysqli_insert_id($conn));