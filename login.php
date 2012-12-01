<?php
ob_start();
require_once('auth.php');
$userId = is_logged_in();

if ( $userId === FALSE ){
	if ( !empty($_POST['id']) && !empty($_POST['pass'])) {
		$id = (int)($_POST['id']);
		$pass = mysql_real_escape_string($_POST['pass']);

		if(login($id, $pass) === TRUE) {
			header("Location: /edit.php?id=" . $id);
		}
	}
} else {
	header("Location: /edit.php?id=" . $userId);
}
?>
<form method='POST'>
<p> User id (numeric): <input type='text' name='id'></p>
<p> Password: <input type='password' name='pass'></p>
<input type='submit' value="Login">
</form>
