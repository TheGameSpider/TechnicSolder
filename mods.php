<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(isset($_GET['logout'])){
	if($_GET['logout']==true){
		session_destroy();
		header("Refresh:0; url=index.php");
	}
}
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("You need to be logged in!");
}
function generateSlugFrom($string)
{
    $string = preg_replace('[^A-Za-z0-9 ]', '', $string);
    $string = preg_replace('/\s/', '_', $string);
    $string = preg_replace('/\-\-+/', '_', $string);
    $string = str_replace('-', '_', $string);
    $string = strtolower(trim($string, '_'));

    return $string;
}

?><html>
<head>
	<link rel="stylesheet" href="css/style.css"></link>	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.2.0/js/all.js" integrity="sha384-4oV5EgaV02iISL2ban6c/RmotsABqE4yZxZLcYMAdG7FAPsyHYAPpywE9PJo+Khy" crossorigin="anonymous"></script>
</head>
<body>
<?php if(!empty($_GET['succ'])){?>
	<div id="alert" style="position:fixed;bottom:1em;z-index:4;left:20%" class="success"><?php echo $_GET['succ']?></div>
	<script>
		$(document).ready(function(){
			setTimeout(function(){
				$('#alert').animate({left:'100%'});
			},5000)
		});
	</script>
<?php }?>
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
			<div class="modpack" onclick="nm()">
				<img height="32px" style="margin:15px;float:left" src="http://cdn.onlinewebfonts.com/svg/img_27750.png"></img>
				<h4>Add modpack</h4>
			</div>
		</div>
		<div id="main">
			<center>
				<h1>Mods</h1>
				<?php
				$result = mysqli_query($conn, "SELECT * FROM `mods`");
				if(mysqli_num_rows($result)==0) {
					Echo "
					<div class='container'>
						No Mods
					</div>";
				} else {
				while($mod=mysqli_fetch_array($result)){
				?>
				<div class="container" style="overflow:auto">
					<p style="float:left"><span style="font-weight:bold;font-size:1.4em"><?php echo $mod['pretty_name']?></span> by <?php echo $mod['author']?><span style="margin:1em;padding:3px;border-radius:3px;color:white;background-color:#1c84c9"><?php echo $mod['version']?></span></p><div style="float:right"><button onclick="$('#editmod-<?php echo $mod['id']?>').show();" class="animate" style="margin:1em;height:35px;width:35px"><i class="fas fa-cogs"></i></button><a target="_blank" href="<?php echo $mod['url']?>"><button class="animate" style="margin:1em;height:35px;width:35px"><i class="fas fa-download"></i></button></a><button onclick="$('#delete-<?php echo $mod['id']?>').show();" class="animate red" style="margin:1em;height:35px;width:35px"><i class="fas fa-trash"></i></button></div>
				</div>
				<div class="popup" id="delete-<?php echo $mod['id']?>" style="display:none;width:20em;height:13em">
					<h2>Detele Mod?</h2>
					<p>Are you sure you want to delete this mod? <?php echo $mod['pretty_name']?></p>
					<button style="padding:10px" onclick="$('#delete-<?php echo $mod['id']?>').hide()" class="animate"><span style="font-size:2em">No</span></button>
					<a href="delete-mod.php?mn=<?php echo $mod['id']?>"><button style="padding:10px" class="animate red"><span style="font-size:2em">Yes</span></button></a>
				</div>
				<div id="editmod-<?php echo $mod['id']?>" class="newmod popup" style="display:none">
					<h1>Edit Mod</h1>
					<form autocomplete="off" id="editmod-<?php echo $mod['id']?>" method="POST" action="edit-mod.php?mod=<?php echo $mod['id']?>">
						<span style="font-size:1.5em">Mod name:</span><input name="pretty_name" type="text" required placeholder="Mod pretty name" value="<?php echo $mod['pretty_name']?>" />
						<br><span style="font-size:1.5em">Mod website:</span><input name="link" type="text" placeholder="Mod website" value="<?php echo $mod['link']?>" />
						<br><span style="font-size:1.5em">Mod author:</span><input name="author" type="text" placeholder="Mod author" value="<?php echo $mod['author']?>" />
						<br><span style="font-size:1.5em">Mod donation link:</span><input name="donlink" type="text" placeholder="Mod author donation link" value="<?php echo $mod['donlink']?>" />
						<br><span style="font-size:1.5em">Mod version:</span><input name="version" type="text" required placeholder="Mod version" value="<?php echo $mod['version']?>" />
						<input name="md5" type="text" required placeholder="MD5 of zip file" value="<?php echo $mod['md5']?>" />
						<br>
						<textarea style="width:90%;height:10em" name="description" placeholder="Mod description"><?php echo $mod['description']?></textarea>
						<br>
						<input class="animate" style="font-size: 1.5em;padding:10px;margin:10px" type="submit" value="Save" />
					</form>
					<button onclick="$('#editmod-<?php echo $mod['id']?>').hide();" class="animate" style="font-size: 1.5em;padding:10px;margin:10px" >Cancel</button>
				</div>
				<?php }}?>
				<script>		
function _(el) {
  return document.getElementById(el);
}
function uploadFile() {
  file = _("file1").files[0];
  var formdata = new FormData();
  formdata.append("file1", file);
  var ajax = new XMLHttpRequest();
  ajax.upload.addEventListener("progress", progressHandler, false);
  ajax.addEventListener("load", completeHandler, false);
  ajax.addEventListener("error", errorHandler, false);
  ajax.addEventListener("abort", abortHandler, false);
  ajax.open("POST", "mod_upload.php");
  ajax.send(formdata);
  $('#file1').prop('disabled', true);
  $('#file1').hide();
}
function progressHandler(event) {
  var percent = (event.loaded / event.total) * 100;
  _("progressBar").value = Math.round(percent);
  _("status").innerHTML = "Uploading: " + Math.round(percent) + "%";
}
function completeHandler(event) {
  _("status").innerHTML = event.target.responseText;
  $("#url").attr("value","<?php echo $config['mirror_url']?>" + file.name);
}
function errorHandler(event) {
  _("status").innerHTML = "Upload Failed";
}
function abortHandler(event) {
  _("status").innerHTML = "Upload Aborted";
}

				</script>
				<div id="addmod" class="newmod popup" style="display:none">
					<h1>Add Mod</h1>
					<form autocomplete="off" id="addmod" enctype="multipart/form-data" method="POST" action="add-mod.php">
						<p>You must zip the mods directory and name it according to the Technic Solder naming convention ([modslug]-[version].zip)</p>
						<input accept="application/x-zip-compressed" required type="file" name="file1" id="file1" onchange="uploadFile()"><br><br>
						<progress id="progressBar" value="0" max="100" style="width:60%;"></progress>
						<h3 id="status"></h3>
						<input id="url" name="url" type="text" hidden />
						<input name="name" type="text" required pattern="[a-z]+" placeholder="Mod slug name (a-z)" />
						<input name="pretty_name" type="text" required placeholder="Mod pretty name" />
						<input name="link" type="text" placeholder="Mod website" />
						<input name="author" type="text" placeholder="Mod author" />
						<input name="donlink" type="text" placeholder="Mod author donation link" />
						<input name="version" type="text" required placeholder="Mod version" />
						<input name="md5" type="text" required placeholder="MD5 of zip file" />
						<br>
						<textarea style="width:90%;height:10em" name="description" placeholder="Mod description"></textarea>
						<br>
						<input class="animate" style="font-size: 1.5em;padding:10px;margin:10px" type="submit" value="Add Mod" />
					</form>
					<button onclick="$('#addmod').hide();" class="animate" style="font-size: 1.5em;padding:10px;margin:10px" >Cancel</button>
				</div>
				<script>
				</script>
				<div class="container" style="cursor:pointer" onclick="$('#addmod').show();">
					<i style="color:#4d4d4d" class="fas fa-plus-circle fa-lg"> </i>  Add Mod
				</div>
			</center>
		</div>
	</div>
</body>
</html>