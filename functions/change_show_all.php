<?php
session_start();
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized request or login session has expired!");
}
if($_GET['showall'] == 'true') {
	$_SESSION['showall'] = true;
} else {
	$_SESSION['showall'] = false;
}