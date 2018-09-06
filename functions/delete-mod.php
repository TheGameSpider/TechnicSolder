<?php
header('Content-Type: application/json');
session_start();
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Id not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("You need to be logged in!");
}
$modq = mysqli_query($conn, "SELECT * FROM `mods` WHERE `id` = '".$_GET['id']."'");
$mod = mysqli_fetch_array($modq);
unlink("../".$mod['type']."s/".$mod['filename']);
mysqli_query($conn, "DELETE FROM `mods` WHERE `id` = '".$_GET['id']."'");
exit();