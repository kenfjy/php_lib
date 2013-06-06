<?php

// this library is made to produce a form of some kind

function begin($formname) {
	echo "<form action=\"post\">";
}

function type($name, $value='') {
	if (empty($value)) {
		echo '<input name = "'.$name.'">';
	} else {
		echo '<input name = "'.$name.'" value = "'.$value.'">';
	}
}

function close() {
	echo "</form>";
}
