<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if (!$_SESSION['user']||$_SESSION['user']=="") {
    die("Unauthorized request or login session has expired!");
}
if (substr($_SESSION['perms'],4,1)!=="1") {
    echo 'Insufficient permission!';
    exit();
}
if (empty($_POST['id'])) {
    die("Mod not specified.");
}
mysqli_query($conn, "UPDATE `mods` SET `donlink` = '".mysqli_real_escape_string($conn, $_POST['value'])."' WHERE `name` = '".mysqli_real_escape_string($conn, $_POST['id'])."'");
exit();
