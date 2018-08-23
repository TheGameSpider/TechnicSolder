<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Must be logged in!");
}
$result = mysqli_query($conn, "INSERT INTO `mods` (`id`, `name`, `pretty_name`, `url`, `link`, `author`, `donlink`, `description`, `version`, `md5`) VALUES (NULL, '".$_POST['name']."', '".$_POST['pretty_name']."', '".$_POST['url']."', '".$_POST['link']."', '".$_POST['author']."', '".$_POST['donlink']."', '".$_POST['description']."', '".$_POST['version']."', '".$_POST['md5']."')");
if(mysqli_affected_rows($conn)!==1) {
	print_r(mysqli_error($result));
}
header("Location: mods.php?succ=Mod ".$_POST['pretty_name']." successfuly added!");