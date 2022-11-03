<?php
header('Content-Type: application/json');
session_start();
global $conn;
require("dbconnect.php");
if (empty($_GET['id'])) {
    die("Id not specified.");
}
if (!$_SESSION['user']||$_SESSION['user']=="") {
    die("Unauthorized request or login session has expired!");
}
if (substr($_SESSION['perms'], 4, 1)!=="1") {
    echo 'Insufficient permission!';
    exit();
}
$modq = mysqli_query(
    $conn,
    "SELECT * FROM `mods` WHERE `name` = '".mysqli_real_escape_string($conn, $_GET['id'])."'"
);
while ($mod = mysqli_fetch_array($modq)) {
    unlink("../".$mod['type']."s/".$mod['filename']);
}
mysqli_query($conn, "DELETE FROM `mods` WHERE `name` = '".mysqli_real_escape_string($conn, $_GET['id'])."'");
exit();
