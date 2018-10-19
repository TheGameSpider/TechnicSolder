<?php
header('Content-Type: application/json');
session_start();
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die('{"status":"error","message":"Login session has expired"}');
}
if(substr($_SESSION['perms'],3,1)!=="1") {
	echo '{"status":"error","message":"Insufficient permission!"}';
	exit();
}
$config = require("config.php");
require("dbconnect.php");
$fileName = $_FILES["fiels"]["name"];
$fileTmpLoc = $_FILES["fiels"]["tmp_name"];
if (!$fileTmpLoc) {
    echo '{"status":"error","message":"File is too big! Check your post_max_size (current value '.ini_get('post_max_size').') andupload_max_filesize (current value '.ini_get('upload_max_filesize').') values in '.php_ini_loaded_file().'"}';
    exit();
}

function slugify($text) {
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  $text = preg_replace('~[^-\w]+~', '', $text);
  $text = trim($text, '-');
  $text = preg_replace('~-+~', '-', $text);
  $text = strtolower($text);
  if (empty($text)) {
    return 'n-a';
  }
  return $text;
}
if(file_exists("../others/".$fileName)) {
	echo '{"status":"error","message":"File already exists!"}';
	exit();
}
if(move_uploaded_file($fileTmpLoc, "../others/".$fileName)){
	$pretty_name = mysqli_real_escape_string($conn, $fileName);
	$name = slugify($pretty_name);
	$author = $config['author'];
	$url = "http://".$config['host'].$config['dir']."others/".$fileName;
	$md5 = md5_file("../others/".$fileName);
	$res = mysqli_query($conn, "INSERT INTO `mods` (`name`,`pretty_name`,`md5`,`url`,`author`,`description`,`filename`,`type`) VALUES ('".$name."','".$pretty_name."','".$md5."','".$url."','".$author."','Custom file by ".$author."','".$fileName."','other')");
	if($res) {
			echo '{"status":"succ","message":"File has been saved."}';
			exit();
	} else {
		echo '{"status":"error","message":"File could not be added to database"}';
		exit();
	}
} else {
    echo '{"status":"error","message":"Permission denied! Open SSH and run chown -R www-data '.dirname(dirname(get_included_files()[0])).'"}';
    exit();
}
?>