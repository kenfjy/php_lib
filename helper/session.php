<?php
/* SESSION HANDLER
 * the session handler limits the usage of the session as well
 * as the invalid session parameters
 */

function session_enforce_start() {
	if(session_id() != '') {
		return;
	} else {
		session_cache_limiter('private_no_expire');
		session_cache_expire(30);
		session_start();
		return;
	}
}

function hash($session) {
	$salt = "3yn8towbg38";
	for ($i = 0; $i < 100; $i++) {
		$session = md5($session . $salt);
	}
	return $session;
}

function error() {
	session_enforce_start();
	if (!empty($_SESSION['error'])) {
		$errno = $_SESSION['error'];
		$_SESSION['error'] = "";
		require_once('error_messages.php');
		if (is_array($errno)){
			foreach($errno as $key => $val) {
				$errno[$key] = $message[$key];
			}
			echo "<p color='red'>".implode($errno, "\n")."</p>";
			unset $message;
			return;
		} else {
			echo "<p color='red'>$message[$errno]</p>";
			unset $message;
			return;
		}
	} else {
		return;
	}
}

function session_enforce_close() {
	session_unset();
	session_destroy();
}

session_start();
$_SESSION['error'] = 12;
error();
