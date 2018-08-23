<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if($_GET['logout']==true){
	session_destroy();
	header("Refresh:0; url=index.php");
	
}
if(isset($_POST['submit'])){
	if($_POST['mail']==$config['mail'] & $_POST['pass']==$config['pass']) {
		$_SESSION['user'] = $_POST['mail'];
	} else {
		header("Refresh:0; url=?ic");
	}
}
?><html>

<head>
	<link rel="stylesheet" href="css/style.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.2.0/js/all.js" integrity="sha384-4oV5EgaV02iISL2ban6c/RmotsABqE4yZxZLcYMAdG7FAPsyHYAPpywE9PJo+Khy" crossorigin="anonymous"></script>
</head>
<body>
<?phpif(!empty($_GET['succ'])){?>
	<div id="alert" style="position:fixed;bottom:1em;z-index:4;left:20%" class="success"><?php echo $_GET['succ']?></div>
	<script>
		$(document).ready(function(){
			setTimeout(function(){
				$('#alert').animate({left:'100%'});
			},5000)
		});
	</script>
<?php}?>
<?php
if(!$_SESSION['user']||$_SESSION['user']=="") {

?>
<?phpif(isset($_GET['ic'])){?><div class="danger">Invalid credentials!</div><?php}?>
<div class="login">
	<center style="margin-top:8em">
		<img src="http://wiki.technicpack.net/skins/common/images/wiki.png"></img>
		<h2>Technic solder</h2>
	</center>
	
	<form method="POST" action="index.php">
		<input name="mail" type="email" placeholder="Email Adress" style="width:100%"><br>
		<input name="pass" type="password" placeholder="Password" style="width:100%">
		<input name="submit" type="submit" value="Log In" class="button animate" style="width:100%;margin:10px;font-size:1.5em;padding:10px">
	</form>
</div>

<?php

}
else {


// test modpacks db
$result = mysqli_query($conn, "SHOW TABLES LIKE 'modpacks'");
$res = mysqli_fetch_array($result);
if(!$res){
$result = mysqli_query($conn, "CREATE TABLE modpacks (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
name VARCHAR(128) NOT NULL,
pretty_name VARCHAR(128) NOT NULL,
url VARCHAR(512),
icon VARCHAR(512),
icon_md5 VARCHAR(512),
logo VARCHAR(512),
logo_md5 VARCHAR(512),
background VARCHAR(512),
background_md5 VARCHAR(512),
latest VARCHAR(512),
recommented VARCHAR(512),
UNIQUE (name)
)");
if($result==true){
	echo "<br>Created table modpacks<br>";
} else {
	echo "err".mysqli_error($conn);
}
}

//test builds db
$result = mysqli_query($conn, "SHOW TABLES LIKE 'builds'");
$res = mysqli_fetch_array($result);
if(!$res){
$result = mysqli_query($conn, "CREATE TABLE builds (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
modpack INT(6) NOT NULL,
name VARCHAR(128) NOT NULL,
minecraft VARCHAR(128) NOT NULL,
java VARCHAR(512),
memory VARCHAR(512),
mods VARCHAR(1024)
)");
if($result==true){
	echo "Created table builds<br>";
} else {
	echo "err".mysqli_error($conn);
}
}

//test mods db
$result = mysqli_query($conn, "SHOW TABLES LIKE 'mods'");
$res = mysqli_fetch_array($result);
if(!$res){
$result = mysqli_query($conn, "CREATE TABLE mods (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
name VARCHAR(128) NOT NULL,
pretty_name VARCHAR(128) NOT NULL,
url VARCHAR(512),
link VARCHAR(512),
author VARCHAR(512),
donlink VARCHAR(512),
description VARCHAR(1024),
version VARCHAR(512),
md5 VARCHAR(512)
)");
if($result==true){
	echo "Created table mods<br>";
} else {
	echo "err".mysqli_error($conn);
}
}
function generateSlugFrom($string)
{
    // Put any language specific filters here, 
    // like, for example, turning the Swedish letter "å" into "a"

    // Remove any character that is not alphanumeric, white-space, or a hyphen 
    $string = preg_replace('[^A-Za-z0-9 ]', '', $string);
    // Replace all spaces with hyphens
    $string = preg_replace('/\s/', '_', $string);
    // Replace multiple hyphens with a single hyphen
    $string = preg_replace('/\-\-+/', '_', $string);
    $string = str_replace('-', '_', $string);
    // Remove leading and trailing hyphens, and then lowercase the URL
    $string = strtolower(trim($string, '_'));

    return $string;
}
	?>
	<div class="main">
		<div class="head">
			<img id="logo" style="float:left" src="https://www.technicpack.net/assets/images/logo_small.png"></img>
			<p id="title" style="margin:5px;font-size:1.5em;float:left">Technic Solder</p> <i id="cog" style="color:#1c84c9;display:none;position:absolute;left:5px;top:2px;margin-top:5px" class="fas fa-cog fa-spin fa-3x"></i>
			<div class="right">
				<div style="float:left;margin-right:25px;">
					<a href="./"><p style="float:left;overflow:auto;margin:25px;margin-top:0;font-weight:bold">Home</p></a>
					<a href="mods.php"><p style="float:left;overflow:auto;margin:25px;margin-top:0;font-weight:bold">Mods</p></a>
				</div>
				<div style="float:right"><?php echo $_SESSION['user']?> <a href="?logout=true"><button>Log Out</button></a></div>
			</div>
		</div>
		<div class="side">
			<div style="overflow:auto;height:25px"></div>
			<center><h2 style="margin-top:0px">Modpacks</h2></center>
			<?php
				$result = mysqli_query($conn, "SELECT * FROM `modpacks`");
				if(mysqli_num_rows($result)==0) {
					//no modpacks
				} else {
				while($modpack=mysqli_fetch_array($result)){
					?>
						<script>
							function load_<?php echo generateSlugFrom($modpack['name'])?>() {
								$('#logo').hide();
								$('#cog').show();
								$('#title').css("margin-left","45px");
								$('#main').load('edit-modpack.php?mp=<?php echo $modpack['name']?>',function(){
									$('#logo').show();
									$('#cog').hide();
									$('#title').css("margin-left", "5px");
								});
							}
						</script>
						<div class="modpack" onclick="load_<?php echo generateSlugFrom($modpack['name'])?>()">
							<img height="32px" style="margin:15px;float:left" src="<?php echo $modpack['icon']?>"></img>
							<h4><?php echo $modpack['pretty_name']?></h4>
						</div>
					<?php
				}
				}
			?>
			<script>
				function nm() {
					$('#logo').hide();
					$('#cog').show();
					$('#title').css("margin-left","45px");
					$('#main').load('add-modpack.php',function(){
						$('#logo').show();
						$('#cog').hide();
						$('#title').css("margin-left", "5px");
					});
				}
			</script>
<?phpif(!empty($_GET['pack'])){?>
	<script>
	$(document).ready(function(){
		$('#logo').hide();
		$('#cog').show();
		$('#title').css("margin-left","45px");
		$('#main').load('edit-modpack.php?mp=<?php echo $_GET['pack']?>',function(){
			$('#logo').show();
			$('#cog').hide();
			$('#title').css("margin-left", "5px");
		});
	});
	</script>
<?php}?>
			<div class="modpack" onclick="nm()">
				<img height="32px" style="margin:15px;float:left" src="http://cdn.onlinewebfonts.com/svg/img_27750.png"></img>
				<h4>Add modpack</h4>
			</div>
		</div>
		<div id="main">
			<center>
				<h1>Welcome to Technic Solder!</h1>
				<div class="container">
					f
				</div>
			</center>
		</div>
	</div>
	<?php
}
?>
</body>
</html>