<?php
session_start();
require("dbconnect.php");
if(empty($_POST['pretty_name'])){
	die("Name not specified.");
}
if(empty($_POST['name'])){
	die("Slug not specified.");
}
if(empty($_POST['version'])){
	die("Version not specified.");
}
if(empty($_POST['url'])){
	die("URL not specified.");
}
if(empty($_POST['md5'])){
	die("Md5 not specified.");
}
if(empty($_POST['mcversion'])){
	die("Minecraft version not specified.");
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized request or login session has expired!");
}
if(substr($_SESSION['perms'],3,1)!=="1") {
	die("Insufficient permission!");
}
mysqli_query($conn, "INSERT INTO `mods` (`name`,`pretty_name`,`md5`,`url`,`link`,`author`,`donlink`,`description`,`version`,`mcversion`,`type`) VALUES ('".mysqli_real_escape_string($conn, $_POST['name'])."','".mysqli_real_escape_string($conn, $_POST['pretty_name'])."','".mysqli_real_escape_string($conn, $_POST['md5'])."','".mysqli_real_escape_string($conn, $_POST['url'])."','".mysqli_real_escape_string($conn, $_POST['link'])."','".mysqli_real_escape_string($conn, $_POST['author'])."','".mysqli_real_escape_string($conn, $_POST['donlink'])."','".mysqli_real_escape_string($conn, $_POST['dscription'])."','".mysqli_real_escape_string($conn, $_POST['version'])."','".mysqli_real_escape_string($conn, $_POST['mcversion'])."','mod')");
header("Location: ../lib-mods");
exit();
