<?php
header('Content-Type: application/json');
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
	$url = "http://".$config['host']."/others/".$fileName.".zip";
	$zip = new ZipArchive();
	if ($zip->open("../others/".$fileName.".zip", ZIPARCHIVE::CREATE) !== TRUE) {
		echo '{"status":"error","message":"Could not open archive"}';
		exit();
	}
	if(is_file("../others/".$fileName)){
		$zip->addFile("../others/".$fileName, $fileName) or die ('{"status":"error","message":"Could not add file to archive."}');
	}
	$zip->close();
	unlink("../others/".$fileName);
	$md5 = md5_file("../others/".$fileName.".zip");
	$res = mysqli_query($conn, "INSERT INTO `mods` (`name`,`pretty_name`,`md5`,`url`,`author`,`description`,`filename`,`type`) VALUES ('".$name."','".$pretty_name."','".$md5."','".$url."','".$author."','Custom file by ".$author."','".$fileName.".zip','other')");
	if($res) {
			echo '{"status":"succ","message":"File has been saved."}';
			exit();
	} else {
		echo '{"status":"error","message":"File could not be added to database"}';
		exit();
	}
} else {
    echo '{"status":"error","message":"File upload failed!"}';
    exit();
}
?>