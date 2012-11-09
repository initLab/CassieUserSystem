<?php
require_once("config.php");
header("Content-Type: text/html; charset=utf-8");
if(isset($_POST["nameInput"]) && isset($_POST["twitterInput"]) && isset($_POST["macInput"])) {
	$homepage = "";

	if (!empty($_POST["homepageInput"])) $homepage = $_POST["homepageInput"];

	$sql = "INSERT INTO users(name, url, twitter) VALUES(?, ?, ?)";
	$res = $database -> exec($sql, array(
		$_POST["nameInput"],
		$homepage,
		str_replace('@', '' ,$_POST["twitterInput"])
	));
	$userId = $database -> lastInsertId ();
	
	$macSql = "INSERT INTO objects(userid, type, value) VALUES(?, ?, ?)";
	$database -> exec($macSql, array(
		$userId,
		"mac",
		strtoupper(str_replace('-', '', str_replace(':','',$_POST["macInput"])))
	));
}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="http://current.bootstrapcdn.com/bootstrap-v204/css/bootstrap-combined.min.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="http://current.bootstrapcdn.com/bootstrap-v204/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			<form class="form-horizontal well" action="" method="post">
			  <fieldset>
				<legend>Hide yo mama, hide yo name, hide yo MAC</legend>
				<div class="control-group">
				  <label class="control-label" for="input01">Име ?</label>
				  <div class="controls">
					<input type="text" class="input-xlarge" id="input01" name="nameInput" />
				  </div>
				</div>
				
				<div class="control-group">
				  <label class="control-label" for="input02">Twitter ?</label>
				  <div class="controls">
					<input type="text" class="input-xlarge" id="input02" name="twitterInput" />
				  </div>
				</div>

				<div class="control-group">
				  <label class="control-label" for="input02">homepage ?</label>
				  <div class="controls">
					<input type="text" class="input-xlarge" id="input02" name="homepageInput" />
				  </div>
				</div>
			
				<div class="control-group">
				  <label class="control-label" for="input03">MAC address ?</label>
				  <div class="controls">
					<input type="text" class="input-xlarge" id="input03" name="macInput">
					<p class="help-block">
					Add your mac without the "-"
					<ul>
						<li>Windows : <em>run -> cmd -> getmac</em></li>
						<li>Linux : <em>ifconfig -a</em></li>
					</ul>
					</p>
				  </div>
				</div>
				
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">Ready to roll!</button>
				</div>
			  </fieldset>
			</form>
		</div>
	</body>
</html>
