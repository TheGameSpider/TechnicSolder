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
$fileName = slugify($fileName).".jar";
if(!file_exists("../mods/mods-".$fileName)) {
	mkdir("../mods/mods-".$fileName);
} else {
	echo '{"status":"error","message":"Folder mods-'.$fileName.' already exists!"}';
	exit();
}

if(move_uploaded_file($fileTmpLoc, "../mods/mods-".$fileName."/".$fileName)){
	$fileInfo = pathinfo("../mods/mods-".$fileName."/".$fileName);
	if(file_exists("../mods/".$fileInfo['filename'].".zip")) {
		$md5_1 = md5_file("../mods/mods-".$fileName."/".$fileName);
		$exisingzip = new ZipArchive();
		$exisingzip->open("../mods/".$fileInfo['filename'].".zip");
		$exisingzip->extractTo("../mods/mods-".$fileName."/tmp/");
		$exisingzip->close();
		$md5_2 = md5_file("../mods/mods-".$fileName."/tmp/mods/".$fileName);
		unlink("../mods/mods-".$fileName."/".$fileName);
		unlink("../mods/mods-".$fileName."/tmp/mods/".$fileName);
		rmdir("../mods/mods-".$fileName."/tmp/mods");
		rmdir("../mods/mods-".$fileName."/tmp");
		rmdir("../mods/mods-".$fileName);
		if($md5_1 !== $md5_2) {
			echo '{"status":"error","message":"File with name \''.$fileName.'\' already exists!","md51":"'.$md5_1.'","md52":"'.$md5_2.'","zip":"../mods/mods-'.$fileName.'/tmp/mods/'.$fileName.'"}';
			unlink("../mods/mods-".$fileName."/".$fileName);
			rmdir("../mods/mods-".$fileName);
			exit();
		} else {
			$fq = mysqli_query($conn, "SELECT `id` FROM `mods` WHERE `filename` = '".$fileInfo['filename'].".zip'");
			if(mysqli_num_rows($fq)==1){
				echo '{"status":"info","message":"This mod is already in the database.","modid":'.mysqli_fetch_array($fq)['id'].'}';
			} else {
				$result = file_get_contents("zip://../mods/mods-".$fileName."/".$fileName."#mcmod.info");
				if($result) {
					$mcmod = json_decode($result,true)[0];
					if(!$mcmod['modid']||!$mcmod['name']||!$mcmod['description']||!$mcmod['version']||!$mcmod['mcversion']||!$mcmod['url']||!$mcmod['authorList']) {
						$warn['b'] = true;
						$warn['level'] = "info";
						$warn['message'] = "There is some information missing in mcmod.info.";
					}
				} else {
					$warn['b'] = true;
					$warn['level'] = "warn";
					$warn['message'] = "File does not contain mod info. Manual configuration required.";
				}
				if(!$mcmod['name']) {
					$pretty_name = mysqli_real_escape_string($conn, $fileName);
				} else {
					$pretty_name = mysqli_real_escape_string($conn, $mcmod['name']);
				}
				if(!$mcmod['modid']) {
					$name = slugify($pretty_name);
				} else {
					if(preg_match("^[a-z0-9]+(?:-[a-z0-9]+)*$", $mcmod['modid'])) {
						$name = $mcmod['modid'];
					} else {
						$name = slugify($mcmod['modid']);
					}
				}
				$link = $mcmod['url'];
				$author = mysqli_real_escape_string($conn, implode(', ', $mcmod['authorList']));
				$description = mysqli_real_escape_string($conn, $mcmod['description']);
				$version = $mcmod['version'];
				$mcversion = $mcmod['mcversion'];
				$md5 = md5_file("../mods/".$fileInfo['filename'].".zip");
				$url = "http://".$config['host'].$config['dir']."mods/".$fileInfo['filename'].".zip";
				$res = mysqli_query($conn, "INSERT INTO `mods` (`name`,`pretty_name`,`md5`,`url`,`link`,`author`,`description`,`version`,`mcversion`,`filename`,`type`) VALUES ('".$name."','".$pretty_name."','".$md5."','".$url."','".$link."','".$author."','".$description."','".$version."','".$mcversion."','".$fileInfo['filename'].".zip','mod')");
				if($res) {
					if($warn['b']==true) {
						if($warn['level']=="info") {
							echo '{"status":"info","message":"'.$warn['message'].'","modid":'.mysqli_insert_id($conn).'}';
						} else {
							echo '{"status":"warn","message":"'.$warn['message'].'","modid":'.mysqli_insert_id($conn).'}';
						}
						
					} else {
						echo '{"status":"succ","message":"Mod has been saved.","modid":'.mysqli_insert_id($conn).'}';
					}
				} else {
					echo '{"status":"error","message":"Mod could not be added to database"}';
				}
				exit();
			}
			
		}
	} else {
		$result = file_get_contents("zip://../mods/mods-".$fileName."/".$fileName."#mcmod.info");
		if($result) {
			$mcmod = json_decode($result,true)[0];
			if(!$mcmod['modid']||!$mcmod['name']||!$mcmod['description']||!$mcmod['version']||!$mcmod['mcversion']||!$mcmod['url']||!$mcmod['authorList']) {
				$warn['b'] = true;
				$warn['level'] = "info";
				$warn['message'] = "There is some information missing in mcmod.info.";
			}
		} else {
			$warn['b'] = true;
			$warn['level'] = "warn";
			$warn['message'] = "File does not contain mod info. Manual configuration required.";
		}
		$zip = new ZipArchive();
		if ($zip->open("../mods/".$fileInfo['filename'].".zip", ZIPARCHIVE::CREATE) !== TRUE) {
			echo '{"status":"error","message":"Could not open archive"}';
			exit();
		}
		$path = "../mods/mods-".$fileName."/".$fileName;
		$pathinfobn = $fileInfo['basename'];
		$zip->addEmptyDir('mods');
		if(is_file($path)){
			$zip->addFile($path, "mods/".$pathinfobn) or die ('{"status":"error","message":"Could not add file $key"}');
		}
		$zip->close();
		unlink("../mods/mods-".$fileName."/".$fileName);
		rmdir("../mods/mods-".$fileName);
		if(!$mcmod['name']) {
			$pretty_name = mysqli_real_escape_string($conn, $fileName);
		} else {
			$pretty_name = mysqli_real_escape_string($conn, $mcmod['name']);
		}
		if(!$mcmod['modid']) {
			$name = slugify($pretty_name);
		} else {
			if(preg_match("^[a-z0-9]+(?:-[a-z0-9]+)*$", $mcmod['modid'])) {
				$name = $mcmod['modid'];
			} else {
				$name = slugify($mcmod['modid']);
			}
		}
		$link = $mcmod['url'];
		$author = mysqli_real_escape_string($conn, implode(', ', $mcmod['authorList']));
		$description = mysqli_real_escape_string($conn, $mcmod['description']);
		$version = $mcmod['version'];
		$mcversion = $mcmod['mcversion'];
		$md5 = md5_file("../mods/".$fileInfo['filename'].".zip");
		$url = "http://".$config['host'].$config['dir']."mods/".$fileInfo['filename'].".zip";
		$res = mysqli_query($conn, "INSERT INTO `mods` (`name`,`pretty_name`,`md5`,`url`,`link`,`author`,`description`,`version`,`mcversion`,`filename`,`type`) VALUES ('".$name."','".$pretty_name."','".$md5."','".$url."','".$link."','".$author."','".$description."','".$version."','".$mcversion."','".$fileInfo['filename'].".zip','mod')");
		if($res) {
			if($warn['b']==true) {
				if($warn['level']=="info") {
					echo '{"status":"info","message":"'.$warn['message'].'","modid":'.mysqli_insert_id($conn).'}';
				} else {
					echo '{"status":"warn","message":"'.$warn['message'].'","modid":'.mysqli_insert_id($conn).'}';
				}
				
			} else {
				echo '{"status":"succ","message":"Mod has been uploaded and saved.","modid":'.mysqli_insert_id($conn).'}';
			}
		} else {
			echo '{"status":"error","message":"Mod could not be added to database"}';
		}
	}
} else {
    echo '{"status":"error","message":"Permission denied! Open SSH and run chown -R www-data '.dirname(dirname(get_included_files()[0])).'"}';
}
?>
