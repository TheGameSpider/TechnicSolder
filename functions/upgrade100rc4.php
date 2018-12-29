<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized request or login session has expired!");
}
mysqli_query($conn, "ALTER TABLE `builds` ADD COLUMN `public` TINYINT(1);");
mysqli_query($conn, "ALTER TABLE `builds` ADD COLUMN `clients` LONGTEXT;");
mysqli_query($conn, "ALTER TABLE `modpacks` ADD COLUMN `public` TINYINT(1);");
mysqli_query($conn, "ALTER TABLE `modpacks` ADD COLUMN `clients` LONGTEXT;");
mysqli_query($conn, "UPDATE `modpacks` SET `public` = 1;");
mysqli_query($conn, "UPDATE `builds` SET `public` = 1;");
$sql = "
CREATE TABLE clients (
id int(8) AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(128),
UUID VARCHAR(128),
UNIQUE (UUID)
);
";
mysqli_query($conn, $sql);
header("Location: ".$config['dir']."clients");
exit();