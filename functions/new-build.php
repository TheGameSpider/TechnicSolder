<?php
session_start();
require("dbconnect.php");
if(empty($_GET['id'])){
	die("Modpack not specified.");
}
if(empty($_GET['name'])){
	die("Name not specified.");
}
if(empty($_GET['type'])){
	die("type not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("You need to be logged in!");
}
if($_GET['type']=="update") {
	mysqli_query($conn, "INSERT INTO builds(`name`,`minecraft`,`java`,`mods`,`modpack`) SELECT `name`,`minecraft`,`java`,`mods`,`modpack` FROM `builds` WHERE `modpack` = '".$_GET['id']."' ORDER BY `id` DESC LIMIT 1");
	mysqli_query($conn, "UPDATE `builds` SET `name` = '".$_GET['name']."' WHERE `modpack` = ".$_GET['id']." ORDER BY `id` DESC LIMIT 1");
	mysqli_query($conn, "UPDATE `modpacks` SET `latest` = '".$_GET['name']."' WHERE `id` = ".$_GET['id']);
	header('Location: /modpack?id='.$_GET['id']);
} else {
	mysqli_query($conn, "INSERT INTO builds(`name`,`modpack`) VALUES ('".$_GET['name']."','".$_GET['id']."')");
	mysqli_query($conn, "UPDATE `modpacks` SET `latest` = '".$_GET['name']."' WHERE `id` = ".$_GET['id']);
	header('Location: /modpack?id='.$_GET['id']);
}