<?php
session_start();
$config = require_once("./config.php");
require_once("dbconnect.php");
if (!$_SESSION['user']||$_SESSION['user']=="") {
    die("Unauthorized request or login session has expired!");
}
if (substr($_SESSION['perms'],4,1)!=="1") {
    echo 'Insufficient permission!';
    exit();
}
if (empty($_GET['id'])){
    die("Mod not specified.");
}
$result = mysqli_query($conn, "SELECT * FROM `mods` WHERE `id` = '".mysqli_real_escape_string($conn, $_GET['id'])."'");
$mod = mysqli_fetch_array($result);
mysqli_query($conn, "UPDATE `mods` SET `name` = '".mysqli_real_escape_string($conn, $_POST['name'])."',`pretty_name` = '".mysqli_real_escape_string($conn, $_POST['pretty_name'])."',`description` = '".mysqli_real_escape_string($conn, $_POST['description'])."' WHERE `name` = '".mysqli_real_escape_string($conn, $_GET['id'])."'");

if ($_POST['submit']=="Save and close") {
    header("Location: ".$config['dir']."lib-mods");
    exit();
}
header("Location: ".$config['dir']."mod?id=".$_POST['name']);
exit();
