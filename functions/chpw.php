<?php
session_start();
$config = require("./config.php");
global $conn;
require("dbconnect.php");
if (!$_SESSION['user']||$_SESSION['user']=="") {
    die("Unauthorized request or login session has expired.");
}
if (empty($_POST['pass'])) {
    die("Password not specified.");
}
if (!isset($config['encrypted'])|| !$config['encrypted']) {
    $pass = $_POST['pass'];
} else {
    // OLD HASHING METHOD (INSECURE)
    // $pass = hash("sha256",$_POST['pass']."Solder.cf");
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
}
$sql = mysqli_query(
    $conn,
    "UPDATE `users` SET `pass` = '".$pass."' WHERE `name` = '".$_SESSION['user']."'"
);
echo mysqli_error($conn);
header("Location: ".$config['dir']."user");
exit();
