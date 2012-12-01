<?php
require_once "users_functions.php";

// TO DO : MUST USE SESSIONS
function is_logged_in() {
	if( !isset($_COOKIE['1337h4x0rz']) ) {
		echo "Fails on isset <br />";
		return FALSE;
	}

	list($userId, $ts, $hash) = explode("|", $_COOKIE["1337h4x0rz"], 3);
	$timeStamp = (int) $ts;

	if(  $timeStamp <= time() && $timeStamp > time() - THIRTY_DAYS /*1 month === 30 days*/ ) {
		$password = get_password($userId);

		// if there is no user found with that id
		if($password === FALSE) {
			return FALSE;
		}
		
		// if the hash is not valid
		if($hash !== sha1($userId . $timeStamp . $password . OVERKILL_SECURITY_CHECK_STRING )) {
			return FALSE;
		}

		return $userId;
	}
	echo "Timestamp check fails";
	return FALSE;
}

function login($userId, $password) {
	$userPassword = get_password($userId);

	if( $userPassword !== FALSE ){
		if( crypt($password, $userPassword) === $userPassword ) {
			$time = time();
			setcookie('1337h4x0rz', $userId . '|' . $time . '|' . sha1($userId . $time . $userPassword . OVERKILL_SECURITY_CHECK_STRING), $time + THIRTY_DAYS, '/', 'db.initlab.ludost.net');
			return TRUE;
		}
	}

	setcookie('1337h4x0rz', '', 0, '/', 'db.initlab.ludost.net');
	return FALSE;
}

// yoda says :
// filtered must be the id
// -1 means there is no user with that id
// else returns the privacy
function user_level($userId) {
	$query = "SELECT privacy FROM %s WHERE id = %d LIMIT 1";
	$query = sprintf($query, USERS_TABLE, $userId);
	$res = mysql_query($query);
	$row = mysql_fetch_object($res);
	if($row === FALSE) {
		return -1;
	}

	return $row->privacy;
}
