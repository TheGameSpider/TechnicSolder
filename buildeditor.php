<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Must be logged in!");
}
if(isset($_GET['logout'])){
	if($_GET['logout']==true){
		session_destroy();
		header("Refresh:0; url=index.php");
	}
}
if(empty($_GET['build'])){
	header("Location: index.php");
}
if(isset($_POST['submit'])){
	mysqli_query($conn, "UPDATE `builds` SET `name` = '".$_POST['name']."' , `minecraft` = '".$_POST['mcv']."' , `java` = '".$_POST['jv']."' , `memory` = '".$_POST['memory']."' WHERE `id` =".$_GET['build']);
}
$buildres = mysqli_query($conn, "select * from `builds` WHERE `id` =".$_GET['build']);
$build = mysqli_fetch_array($buildres);
$modpackres = mysqli_query($conn, "select * from `modpacks` WHERE `id` =".$build['modpack']);
$modpack = mysqli_fetch_array($modpackres);
?>
<html>

<head>
	<link rel="stylesheet" href="css/style.css"></link>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.2.0/js/all.js" integrity="sha384-4oV5EgaV02iISL2ban6c/RmotsABqE4yZxZLcYMAdG7FAPsyHYAPpywE9PJo+Khy" crossorigin="anonymous"></script>
</head>
<body>
	<div style="top:0em" class="head">
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
	<div style="margin-top:100px;">
		<center>
			<h1>Build Editor - <?php echo $modpack['pretty_name']?> build <?php echo $build['name']?></h1>
			<form method="POST" action="buildeditor.php?build=<?php echo $_GET['build']?>">
			<table>
					<tr>
						<td><span style="font-size:1.5em">Build Name: </span></td><td><input value="<?php echo $build['name']?>" required type="text" name="name" style="width:100%" placeholder="e.g. 1.0"></input>
						</td></tr>
					<tr>
						<td><span style="width:15em;font-size:1.5em">Minecraft Version: </span></td><td><select style="width:100%" name="mcv" id="mcv" required>
							<option value="1.12.2">1.12.2</option>
							<!--option value="61">1.12.1</option>
							<option value="59">1.12</option>
							<option value="53">1.11.2</option>
							<option value="56">1.11.1</option>
							<option value="49">1.11</option>
							<option value="45">1.10.2</option>
							<option value="46">1.10.1</option>
							<option value="72">1.10</option>
							<option value="41">1.9.4</option>
							<option value="42">1.9.3</option>
							<option value="43">1.9.2</option>
							<option value="44">1.9.1</option>
							<option value="36">1.9</option>
							<option value="34">1.8.9</option>
							<option value="35">1.8.8</option>
							<option value="30">1.8.7</option>
							<option value="31">1.8.6</option>
							<option value="32">1.8.5</option>
							<option value="33">1.8.4</option>
							<option value="28">1.8.3</option>
							<option value="29">1.8.2</option>
							<option value="27">1.8.1</option>
							<option value="26">1.8</option>
							<option value="24">1.7.10</option>
							<option value="25">1.7.9</option>
							<option value="37">1.7.8</option>
							<option value="38">1.7.7</option>
							<option value="39">1.7.6</option>
							<option value="23">1.7.5</option>
							<option value="22">1.7.4</option>
							<option value="40">1.7.3</option>
							<option value="21">1.7.2</option>
							<option value="8">1.6.4</option>
							<option value="9">1.6.2</option>
							<option value="10">1.6.1</option>
							<option value="7">1.5.2</option>
							<option value="6">1.5.1</option>
							<option value="5">1.5</option>
							<option value="1">1.4.7</option>
							<option value="3">1.4.6</option>
							<option value="11">1.4.5</option>
							<option value="12">1.4.4</option>
							<option value="13">1.4.2</option>
							<option value="14">1.3.2</option>
							<option value="15">1.3.1</option>
							<option value="2">1.2.5</option>
							<option value="16">1.2.4</option>
							<option value="4">1.2.3</option>
							<option value="17">1.2.2</option>
							<option value="18">1.2.1</option>
							<option value="19">1.1</option>
							<option value="19">1.0</option-->
						</select></td></tr>
						<tr>
						<td><span style="font-size:1.5em">Java Version: </span></td><td><select id="jv" style="width:100%" name="jv">
							<option value="1.8">1.8</option>
							<option value="1.7">1.7</option>
						</select></td></tr>
						<tr>
						<td><span style="font-size:1.5em">Memory (in MB): </span></td><td><input style="width:100%" min="512" step="512" type="number" name="memory" placeholder="Recommented 2048MB" value="<?php echo $build['memory']?>"></input>
						</td></tr>
						</table>
						<input name="submit" class="animate" style="font-size: 1.5em;padding:10px;margin:10px" type="submit" value="Save" />
					</form>
					
					<h1>Mods:</h1>
					<div style="width:80%" id="mods">
					<h3 id="lm">Loading mods...</h3>
					</div>
					<div id="modselect-div" style="width:80%">
					<select style="width:80%" id="modselect">
						<?php
							$modsres = mysqli_query($conn, "SELECT * FROM `mods`");
							$modslist= explode(',', $build['mods']);
							while ($mod = mysqli_fetch_array($modsres)) {
								if(!in_array($mod['id'], $modslist)) {
									echo "<option value='".$mod['id']."'>".$mod['pretty_name']." - ".$mod['version']."</option>";
								}
							}
						?>
					</select>
					<button id="add" class="animate" style="padding:10px"><i id="check" style="font-size:2em" class="fas fa-check"></i><i id="add-cog" style="font-size:2em;display:none" class="fas fa-cog fa-spin"></i></button>
					</div>
					<a href="release.php?build=<?php echo $build['id']?>"><button style="font-size:2em;padding:10px" class="animate">Release</button></a>
		</cenetr>
		<script>
			$(document).ready(function(){
				$("#mcv").val("<?php echo $build['minecraft']?>");
				$("#jv").val("<?php echo $build['java']?>");
				
				$.get("buildmods.php?build=<?php echo $_GET['build']?>",function(data){
					$("#lm").hide();
					data.mods.forEach(function(mod){
						if($("#mod-" + mod.id).length == 0) {
							$("#mods").append("<div id='mod-"+mod.id+"' style='width:80%;overflow:auto' class='container'><p style='float:left'><span style='font-weight:bold;font-size:1.4em'>"+mod.pretty_name+"</span> by "+mod.author+"<span style='margin:1em;padding:3px;border-radius:3px;color:white;background-color:#1c84c9'>"+mod.version+"</span></p><div style='float:right'><a href='buildmods.php?build=<?php echo $_GET['build']?>&remove="+mod.id+"'><button id='remove-"+mod.id+"' class='animate red' style='margin:1em;height:35px;width:35px'><i class='fas fa-times'></i></button></a></div></div>");
						}
					});
				});
				$('#modselect:not(:has(option))').attr('disabled',function(){
					$("#modselect-div").html("<h2>No mods to add. Please add a mod in the <i>mods</i> section.</h2>");;
					return true;
				});

				$("#add").click(function(){
					$("#add").attr("disabled",true);
					$("#check").hide();
					$("#add-cog").show();
					var mod = $("#modselect").val();
					$("#modselect option[value='"+mod+"']").remove();
					$('#modselect:not(:has(option))').attr('disabled',function(){
						$("#modselect-div").html("<h2>No mods to add. Please add a mod in the <i>mods</i> section.</h2>");;
						return true;
					});
					$.get("buildmods.php?build=<?php echo $_GET['build']?>&add="+mod,function(){
						$.get("buildmods.php?build=<?php echo $_GET['build']?>",function(data){
							data.mods.forEach(function(mod){
								if($("#mod-" + mod.id).length == 0) {
									$("#mods").append("<div id='mod-"+mod.id+"' style='width:80%;overflow:auto' class='container'><p style='float:left'><span style='font-weight:bold;font-size:1.4em'>"+mod.pretty_name+"</span> by "+mod.author+"<span style='margin:1em;padding:3px;border-radius:3px;color:white;background-color:#1c84c9'>"+mod.version+"</span></p><div style='float:right'><a href='buildmods.php?build=<?php echo $_GET['build']?>&remove="+mod.id+"'><button id='remove-"+mod.id+"' class='animate red' style='margin:1em;height:35px;width:35px'><i class='fas fa-times'></i></button></a></div></div>");
								}
							});
							$("#add-cog").hide();
							$("#check").show();
							$("#add").attr("disabled",false);
						});
						
					});
				});
			});
		</script>
		
	</div>
</body>