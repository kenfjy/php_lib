<?php

//for encoding, I'm currently using the utf-8,
//since thats the encode used for the html files.
//the encoding is being set within the connect function,
//so if there's need for change, feel free to do so.
//maybe, you would like to run a mb_detect_encoding to 
//make it the best one for the program

class Mysql{
	var $connect;
	var $user;
	var $passwd;
	var $link;
	
	//construct function mainly consists of loading the setting from the setting.php
	function Mysql($database) {
		require_once('setting.php');
		//before connecting the database, the script gets the user infos from setting.php
	//	$this->connect	= $setting->get_connect();
	//	$this->user 	= $setting->get_user();
	//	$this->passwd	= $setting->get_passwd(); 
		$this->connect	= CONNECT;
		$this->user 	= USER;
		$this->passwd	= PASSWD;
//	}	
//
//	//a support function for mysql_connect method
//	function connect($database) {
		$link = mysql_connect($connect, $user, $passwd);
		//for connection errors
		if (!$link) {
			exit('connection failure' . mysql_error());
		}
		$db_select = mysql_select_db($database, $link);
		if (!$db_select) {
			exit('selecting database failed' . mysql_error());
		}
		$this->link = $link;
		return $this;
	}

	function que($string, $array) {
		//var array is not an array, so convert it into array
		if (!is_array($array)) {
			$array = (array)$array;
		}
		//check the number of arrays and ?s in string
		if (mb_substr_count($string, '?', 'utf-8') != count($array)) {
			//this should return an error
			exit('mysql query error');
		} else {
			$string = str_replace('?', '%s', $string);
		}
		//escaping the parameters so you won't have to think about escaping them
		foreach ($array as $key => $val) {
			$array[$key] = mysql_real_escape_string($val);
		}
		$clean = vsprintf($string, $array);
		$result = mysql_query($clean, $this->link);
		if (!$result) {
			exit('Database error. Try again.');
		} else {
			return $result;
		}
	}


	function push($table, $value_array, $id='') {
		/*
		 * first parameter(MUST): name of the table you would like to push the data from
		 * second parameter(MUST): an array that has the columns as the key and the values you would like to insert as values
		 * third parameter: id number of the row you would like to push the data from
		 */

		if (empty($this->link)) {
			exit('Not connected to database yet');
		}
		if (empty($table) OR empty($value_array)) {
			exit('Missing Parameter');
		}
		if (!is_array($value_array)) {
			$value_array = (array)$value_array;
		}
		$query = '';
		if (empty($id) && $id != 0) {
			$query_line = 'INSERT INTO ' . $table . '(%s) values(%s)';
			$query_key = array();
			$query_value = array();
			foreach ($value_array as $key => $val) {
				$query_key[] = mysql_real_escape_string($key);
				$query_value[] = "'" . mysql_real_escape_string($val) . "'";
			}
			$query_key = implode($query_key, ',');
			$query_value = implode($query_value, ',');
			$query = sprintf($query_line, $query_key, $query_value);
		} else {
			if (is_array($id)) {
				$query_line = 'UPDATE ' . $table . ' SET%s WHERE %s';

				$query_value = '';
				foreach ($value_array as $key => $val) {
					$query_value = $query_value . ' '. mysql_real_escape_string($key) . '=\'' . mysql_real_escape_string($val) . '\'';
				}

				foreach ($id as $key => $val) {
					$id[$key] = '\'' . mysql_real_escape_string($val) . '\''; 
				}
				$id = implode($id, ',');
				$id = "id IN (".$id.")";

				$query = sprintf($query_line, $query_value, $id);
				unset($value_array,$id);
			} else {
				$query_line = 'UPDATE ' . $table . ' SET%s WHERE id = ' . $id;
				$query_value = '';
				foreach ($value_array as $key => $val) {
					$query_value = $query_value . ' '. mysql_real_escape_string($key) . '=\'' . mysql_real_escape_string($val) . '\'';
				}
				$query = sprintf($query_line, $query_value);
				unset($value_array,$id);
			} else {
			}
		}
		$result = mysql_query($query, $this->link);
		if (!$result) {
			exit('Database error. Try again');
		} else {
			if (mysql_affected_rows() == 0) {
				return 0;
			} else {
				return $result;
				mysql_free_result($result);
			}
		}
	}

	function search($table, $column, $keyword) {
		/*
		 * not implemented yet, but will soon
		 */

		if (empty($this->link)) {
			exit('Not connected to database yet');
		}
		if (empty($table)) {
			exit('Missing Parameter');
		}


	}

	function pull($table, $column_array='', $id_array = '') {
		/*
		 * first parameter(MUST): name of the table you would like to pull the data from
		 * second parameter: name of the column you would like to pull the data from
		 * third parameter: id number of the row you would like to pull the data from
		 */

		if (empty($this->link)) {
			exit('Not connected to database yet');
		}
		if (empty($table)) {
			exit('Missing Parameter');
		}
		if ($column_array == NULL) {
			$column_array = '*';
		}
		if (!is_array($column_array)) {
			$column_array = (array)$column_array;
		}
		foreach ($column_array as $key => $value) {
			$column_array[$key] = mysql_real_escape_string($value);
		}
		//mysql_affected_rows
		$query_line = 'SELECT %s FROM '. $table;
		$query = sprintf($query_line, implode($column_array, ', '));
		unset($column_array);
		if (!empty($id_array)) {
			if (!is_array($id_array)) {
				$id_array = 'id = \'' . mysql_real_escape_string($id_array) . '\'';
			} else {
				foreach ($id_array as $key => $value) {
					$id_array[$key] = '\'' . mysql_real_escape_string($value) . '\'';
				}
				$id_array = "id IN (".implode($id_array, ',').")";
			}
			$query = $query . ' WHERE ' . $id_array;
			unset($id_array);
		}
		//return $query;
		$result = mysql_query($query, $this->link);
		unset($query);
		if (!$result) {
			exit('Database error. Try again');
		} else { 
			if (mysql_num_rows($result) == 0) { 
				return 0;
			} else {
				return $result;
				mysql_free_result($result);
			}
		}

	}

	function begin() {
		mysql_query("BEGIN", $this->link);
		if (mysql_errno != 0) {
			return TRUE;
		} else {
			exit('Database error. Try again');
		}
	}

	function quit() {
		if (mysql_errno != 0) {
			mysql_query("ROLLBACK", $this->link);
			exit('Database error. Try again');
		} else {
			mysql_query("COMMIT", $this->link);
			return TRUE;
		}
	}
	//a support and additional version of mysql_close method
	function close() {
		return(mysql_close($this->link));
	}
}
