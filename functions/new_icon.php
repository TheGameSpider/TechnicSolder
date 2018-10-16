<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized request or login session has expired.");
}
$icon = $_FILES["newIcon"]["tmp_name"];
$iconbase = base64_encode(file_get_contents($icon));
$sql = mysqli_query($conn,"UPDATE `users` SET `icon` = '".$iconbase."' WHERE `name` = '".$_SESSION['user']."'");
echo mysqli_error($conn);