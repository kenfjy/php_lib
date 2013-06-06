<?php
define('STRETCH_LOOP', 1000);
define('ADDITIONAL_SALT', 'iPhonE');
define('SECOND_SALT', '1890uh21hg8190h89g');

function get_pass($hash) {
	for ($k = 0; $k < 5; $k++) {
		$hash = md5($hash . ADDITIONAL_SALT);
	}
	for ($i = 0; $i < STRETCH_LOOP; $i++) {
		$hash = sha1($hash . SECOND_SALT);
	}
	return $hash;
}

//echo get_pass("hello world");
