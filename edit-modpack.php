<?php
session_start();
$config = require("config.php");
$dbcon = require("dbconnect.php");
if(!$_SESSION['user']||$_SESSION['user']=="") {
	die("Must be logged in!");
}
if(empty($_GET['mp'])){
	die("Modpack not specified.");
}
if(isset($_POST['submit'])){
	
} else {
	$modpackq = mysqli_query($conn, "SELECT * FROM `modpacks` WHERE `name` = '".$_GET['mp']."'");
	$modpack = mysqli_fetch_array($modpackq);
	$buildsq = mysqli_query($conn, "SELECT * FROM `builds` WHERE `modpack` = ".$modpack['id']." ORDER BY `name`");
	?>
		<center>
				<div id="newbuild" class="newmod popup" style="display:none">
					<h1>New Build</h1>
					<form autocomplete="off" id="newbuild" method="POST" action="newbuild.php">
					<div style="text-align:left;margin:50px">
					<input hidden required name="modpack" value="<?php echo $modpack['id']?>"></input>
					<table>
					<tr>
						<td><span style="font-size:1.5em">Build Name: </span></td><td><input required type="text" name="name" style="width:100%" placeholder="e.g. 1.0"></input>
						</td></tr>
					<tr>
						<td><span style="width:15em;font-size:1.5em">Minecraft Version: </span></td><td><select style="width:100%" name="mcv" required>
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
						<td><span style="font-size:1.5em">Java Version: </span></td><td><select style="width:100%" name="jv">
							<option value="1.8">1.8</option>
							<option value="1.7">1.7</option>
						</select></td></tr>
						<tr>
						<td><span style="font-size:1.5em">Memory (in MB): </span></td><td><input style="width:100%" min="512" step="512" type="number" name="memory" placeholder="Recommented 2048MB" value="2048"></input>
						</td></tr>
						</table>
						</div>
						<br>
						<input class="animate" style="font-size: 1.5em;padding:10px;margin:10px" type="submit" value="Save" />
					</form>
					<button onclick="$('#newbuild').hide();" class="animate" style="font-size: 1.5em;padding:10px;margin:10px" >Cancel</button>
				</div>
			<h1>Edit modpack <?php echo $modpack['pretty_name']?></h1>
			<form action="index.php">
			<input name="pack" hidden type="text" required value="<?php echo $modpack['name']?>" />
			<input name="pretty_name" type="text" required placeholder="Modpack Name" value="<?php echo $modpack['pretty_name']?>" />
			<input type="submit" value="Save" class="animate" style="margin:10px;padding:10px;font-size:1.5em">
			</form>
			<h2>Builds</h2>
			<?php
			if(mysqli_num_rows($buildsq)==0) {
				Echo "
				<div class='container'>
					No Builds
				</div>";
			}
			?>
			<?php while($build = mysqli_fetch_array($buildsq)){
				?>
				<div class="popup" id="deletebuild-<?php echo $build['id']?>" style="display:none;width:20em;height:13em">
					<h2>Detele Build?</h2>
					<p>Are you sure you want to delete this build? <?php echo $build['name']?></p>
					<button style="padding:10px" onclick="$('#deletebuild-<?php echo $build['id']?>').hide()" class="animate"><span style="font-size:2em">No</span></button>
					<a href="delete-build.php?build=<?php echo $build['id']?>"><button style="padding:10px" class="animate red"><span style="font-size:2em">Yes</span></button></a>
				</div>
					<div class="container" style="overflow:auto">
						<p style="float:left"><span style="font-weight:bold;font-size:1.4em"><?php echo $build['name']?></span></p><div style="float:right"><a href="buildeditor.php?build=<?php echo $build['id']?>"><button class="animate" style="margin:1em;height:35px;width:35px"><i class="fas fa-cogs"></i></button></a><button onclick="$('#deletebuild-<?php echo $build['id']?>').show();" class="animate red" style="margin:1em;height:35px;width:35px"><i class="fas fa-trash"></i></button></div>
					</div>
				<?php
			}?>
			<div class="container" style="cursor:pointer;" onclick="$('#newbuild').show();">
				<i class="fas fa-plus-circle"></i> New build
			</div>
			<div class="popup" id="rmp" style="display:none;width:20em;height:13em">
					<h2>Detele Modpack?</h2>
					<p>Are you sure you want to delete this modpack and all it's builds? <?php echo $modpack['pretty_name']?></p>
					<button style="padding:10px" onclick="$('#rmp').hide()" class="animate"><span style="font-size:2em">No</span></button>
					<a href="rmp.php?modpack=<?php echo $modpack['id']?>"><button style="padding:10px" class="animate red"><span style="font-size:2em">Yes</span></button></a>
				</div>
			<button onclick="$('#rmp').show();" class="animate red" style="font-size:2em;padding:10px;margin:20px">Remove modpack</button>
		</center>
	<?php
}