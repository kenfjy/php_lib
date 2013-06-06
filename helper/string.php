<?php

// this library is made to escape the strings that are gimven and to make them safe
function h($string) {
	echo nl2br(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
}

function m_esc($string) {
	$val = mysql_real_escape_string($string);
	return $val;
}
