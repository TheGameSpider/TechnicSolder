<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
    die("Unauthorized request or login session has expired!");
}
if(substr($_SESSION['perms'],4,1)!=="1") {
    echo 'Insufficient permission!';
    exit();
}
if(empty($_GET['id'])){
    die("Mod not specified.");
}
$result = mysqli_query($conn, "SELECT * FROM `mods` WHERE `id` = ".$_GET['id']);
$mod = mysqli_fetch_array($result);
mysqli_query($conn, "UPDATE `mods` SET `link` = '".mysqli_real_escape_string($conn, $_POST['link'])."',`author` = '".mysqli_real_escape_string($conn, $_POST['author'])."', `donlink` = '".mysqli_real_escape_string($conn, $_POST['donlink'])."', `version` = '".mysqli_real_escape_string($conn, $_POST['version'])."', `mcversion` = '".mysqli_real_escape_string($conn, $_POST['mcversion'])."', `url` = '".mysqli_real_escape_string($conn, $_POST['url'])."', `md5` = '".mysqli_real_escape_string($conn, $_POST['md5'])."' WHERE `id` = ".mysqli_real_escape_string($conn, $_GET['id']));

if($_POST['submit']=="Save and close") {
    header("Location: ".$config['dir']."mod?id=".$mod['name']);
    exit();
}
header("Location: ".$config['dir']."modv?id=".$_GET['id']);
exit();
