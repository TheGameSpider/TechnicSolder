<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Must be logged in!");
}
if (!file_exists($config['mirror_location'].$_POST['name'])) {
    mkdir($config['mirror_location'].$_POST['name'], 0777, true);
}
if(!empty($_POST['name'])){
$config = require("config.php");
$icon_fileName = $_FILES["icon"]["name"]; 
$icon_fileTmpLoc = $_FILES["icon"]["tmp_name"]; 
$icon_fileType = $_FILES["icon"]["type"]; 
$icon_fileSize = $_FILES["icon"]["size"]; 
$icon_fileErrorMsg = $_FILES["icon"]["error"];

if (!$icon_fileTmpLoc) {
    echo "ERROR: No icon<br>";
    exit();
}
if($icon_fileType !== "image/png") {
	echo "ERROR: Icon is not image/png.<br>";
    exit();
}
if($error!==true) {
if(!move_uploaded_file($icon_fileTmpLoc, $config['mirror_location'].$_POST['name']."/icon.png")){
    echo "Icon move_uploaded_file function failed<br>";
	exit();
}
}
$logo_fileName = $_FILES["logo"]["name"]; 
$logo_fileTmpLoc = $_FILES["logo"]["tmp_name"]; 
$logo_fileType = $_FILES["logo"]["type"]; 
$logo_fileSize = $_FILES["logo"]["size"]; 
$logo_fileErrorMsg = $_FILES["logo"]["error"]; 
if (!$logo_fileTmpLoc) {
    echo "ERROR: No logo<br>";
    exit();
}
if($logo_fileType !== "image/png") {
	echo "ERROR: logo is not image/png.<br>";
    exit();
}
if($error!==true) {
if(!move_uploaded_file($logo_fileTmpLoc, $config['mirror_location'].$_POST['name']."/logo.png")){
    echo "Logo move_uploaded_file function failed<br>";
	exit();
}
}
$background_fileName = $_FILES["background"]["name"]; 
$background_fileTmpLoc = $_FILES["background"]["tmp_name"]; 
$background_fileType = $_FILES["background"]["type"]; 
$background_fileSize = $_FILES["background"]["size"]; 
$background_fileErrorMsg = $_FILES["background"]["error"]; 
if (!$background_fileTmpLoc) {
    echo "ERROR: No background<br>";
    exit();
}
if($background_fileType !== "image/png") {
	echo "ERROR: background is not image/png.<br>";
    exit();
}
if($error!==true) {
if(!move_uploaded_file($background_fileTmpLoc, $config['mirror_location'].$_POST['name']."/background.png")){
    echo "Backgrount move_uploaded_file function failed<br>";
	exit();
}
}

$icon_loc = $config['mirror_url'].$_POST['name']."/icon.png";
$logo_loc = $config['mirror_url'].$_POST['name']."/logo.png";
$background_loc = $config['mirror_url'].$_POST['name']."/background.png";
$result = mysqli_query($conn, "INSERT INTO `modpacks` (`id`, `name`, `pretty_name`, `icon`, `icon_md5`, `logo`, `logo_md5`, `background`, `background_md5`) VALUES (NULL, '".$_POST['name']."', '".$_POST['display_name']."', '".$icon_loc."', '".$_POST['icon_md5']."', '".$logo_loc."', '".$_POST['logo_md5']."', '".$background_loc."', '".$_POST['background_md5']."')");
if(mysqli_affected_rows($conn)!==1) {
	print_r(mysqli_error($result));
	exit();
}
header("Location: index.php?succ=Modpack ".$_POST['display_name']." successfuly added!&pack=".$_POST['name']);
}

?>
<center>
<div class="newmod popup">
	<h1>Add Modpack</h1>
	<form autocomplete="off" enctype="multipart/form-data"  method="POST" action="add-modpack.php">
		<input name="name" type="text" required placeholder="Modpack slug name (a-z)" value="<?php echo $mod['pretty_name']?>" />
		<input name="display_name" type="text" required placeholder="Modpack dispaly name" value="<?php echo $mod['pretty_name']?>" />
		<hr>
		<h3>Modpack icon (Should be 50x50):<h3><input accept=".png" required type="file" name="icon" id="icon"><input name="icon_md5" type="text" placeholder="MD5" required />
		<h3>Modpack logo (Should be 370x220):</h3><input accept=".png" required type="file" name="logo" id="logo"><input name="logo_md5" type="text" placeholder="MD5" required />
		<h3>Modpack Background (Should be 900x600):</h3><input accept=".png" required type="file" name="background" id="backgroud"><input name="background_md5" type="text" placeholder="MD5" required />
		<hr>
		<input class="animate" style="font-size: 1.5em;padding:10px;margin:10px" type="submit" value="Add Modpack" />
	</form>
	<a href="index.php"><button class="animate" style="font-size: 1.5em;padding:10px;margin:10px">Cancel</button>
</div>
</center>