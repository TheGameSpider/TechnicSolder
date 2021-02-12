<?php
session_start();
$config = require("./config.php");
require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Unauthorized request or login session has expired.");
}
if($_SESSION['user']!==$config['mail']) {
	die("insufficient permission!");
}
//$sql = mysqli_query($conn,"UPDATE `users` SET `display_name` = '".$_POST['display_name']."', `perms` = '".$_POST['perms']."' WHERE `name` = '".$_POST['name']."'");
if(!isset($config['encrypted'])||$config['encrypted']==false||!isset($config['betterencryption'])||$config['betterencryption']==false) {
	$users = mysqli_query($conn,"SELECT * FROM `users`");
	$alreadydone = false
	// If better encryption isnt used and passwords arent already using encryption.
	if((!isset($config['betterencryption'])||$config['betterencryption']==false)&&(!isset($config['encrypted'])||$config['encrypted']==false)){
		error_log("Upgrading from no encryption at all.");
		while ($user = mysqli_fetch_array($users)) {
			mysqli_query($conn,"UPDATE `users` SET `pass` = '".password_hash(hash("sha256",$user['pass']."Solder.cf"), PASSWORD_DEFAULT)."' WHERE `name` = '".$user['name']."'");
		}
		$mainpass = password_hash(hash("sha256",$config['pass']."Solder.cf"), PASSWORD_DEFAULT);
		$cf = '<?php return array( "configured" => true, "author" => "'.$config['author'].'","mail" => "'.$config['mail'].'","pass" => "'.$mainpass.'","db-host" => "'.$config['db-host'].'","db-user" => "'.$config['db-user'].'","db-name" => "'.$config['db-name'].'","db-pass" => "'.$config['db-pass'].'","host" => "'.$config['host'].'","dir" => "'.$config['dir'].'","api_key" => "'.$config['api_key'].'", "encrypted" => true, "betterencryption" => true ';
		$alreadydone = true
		file_put_contents("./config.php", $cf." );");
		header("Location: ../");
	}
	// If better encryption isnt used, but passwords already are encrypted.
	if((!isset($config['betterencryption'])||$config['betterencryption']==false)&&(isset($config['encrypted'])||$config['encrypted']==true)&&$alreadydone==false){
		error_log("Upgrading from old encryption.");
		while ($user = mysqli_fetch_array($users)) {
			mysqli_query($conn,"UPDATE `users` SET `pass` = '".password_hash($user['pass'], PASSWORD_DEFAULT)."' WHERE `name` = '".$user['name']."'");
		}
		$mainpass = password_hash($config['pass'], PASSWORD_DEFAULT);
		$cf = '<?php return array( "configured" => true, "author" => "'.$config['author'].'","mail" => "'.$config['mail'].'","pass" => "'.$mainpass.'","db-host" => "'.$config['db-host'].'","db-user" => "'.$config['db-user'].'","db-name" => "'.$config['db-name'].'","db-pass" => "'.$config['db-pass'].'","host" => "'.$config['host'].'","dir" => "'.$config['dir'].'","api_key" => "'.$config['api_key'].'", "encrypted" => true, "betterencryption" => true ';
		file_put_contents("./config.php", $cf." );");
		header("Location: ../");
	}
} else {
	//$pass = password_hash(hash("sha256",$_POST['pass']."Solder.cf"), PASSWORD_DEFAULT);
	die("Passwords are already encrypted using new encryption.");
}