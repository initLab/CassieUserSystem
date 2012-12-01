<?php
require_once("config.php");
header("Content-Type: text/html; charset=utf-8");

$errors = array();

if( $_SERVER['REQUEST_METHOD']=== "POST" ) {

	if (!isset($_POST["nameInput"])) {
		array_push($errors, "No name provided");
	}

	if (!isset($_POST["macInput"])) {
		array_push($errors, "No mac address provided");
	}

	if (empty($_POST["password"])) {
		array_push($errors,  "No password entered");
	}

	if ($_POST["password"] !== $_POST["password-repeat"]) {
		array_push($errors, "Passwords don't match");
	}

	if ( count($errors) === 0 ) {

		$chars = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		$salt = "";
		for($i=0; $i<16; $i++) 
			$salt.=$chars[rand(0,63)];

		$password = crypt($_POST["password"],'$5$'.$salt);
		
		$homepage = "";
		if (!empty($_POST["homepageInput"])) 
			$homepage = htmlspecialchars($_POST["homepageInput"]);

		$twitter = "";
		if (isset($_POST["twitterInput"])) {
			$twitter = str_replace('@', '' ,htmlspecialchars($_POST["twitterInput"]));
		}

		$name = htmlspecialchars($_POST["nameInput"]);

		$sql = "INSERT INTO users(name, url, twitter, password) VALUES(?, ?, ?, ?)";
		$res = $database -> exec($sql, array(
			$name,
			$homepage,
			$twitter,
			$password
		));

		$userId = $database -> lastInsertId ();
		
		$mac = strtoupper(str_replace('-', '', str_replace(':','',$_POST["macInput"])));

		$macSql = "INSERT INTO objects(userid, type, value) VALUES(?, ?, ?)";
		$database -> exec($macSql, array(
			$userId,
			"mac",
			$mac
		));
?>

	<head>
		<link rel="stylesheet" href="http://current.bootstrapcdn.com/bootstrap-v204/css/bootstrap-combined.min.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="http://current.bootstrapcdn.com/bootstrap-v204/js/bootstrap.min.js"></script>
		<title> Successfully added user </title>
	</head>
	<body>
		<div class="container">
		<p> user: <?=$_POST["nameInput"]?> </p>
		<p> mac: <? echo strtoupper(str_replace('-', '', str_replace(':','',$_POST["macInput"]))); ?> </p>
		</div>
	</body>


<?
	
		exit();
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="http://current.bootstrapcdn.com/bootstrap-v204/css/bootstrap-combined.min.css" />
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="http://current.bootstrapcdn.com/bootstrap-v204/js/bootstrap.min.js"></script>
		<title> Add user/mac </title>
	</head>
	<body>
	<? 
		foreach ($errors as $err) {
			echo '<h2>'.$err.'</h2>';
		} 
	?>

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

				<div class="control-group">
				  <label class="control-label" for="input02">password ?</label>
				  <div class="controls">
					<input type="password" class="input-xlarge" id="input02" name="password" />
				  </div>
				</div>

				<div class="control-group">
				  <label class="control-label" for="input02">repeat password ?</label>
				  <div class="controls">
					<input type="password" class="input-xlarge" id="input02" name="password-repeat" />
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
