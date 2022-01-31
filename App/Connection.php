<?php

namespace App;

class Connection {

	public static function getDb() {
		try {

			$con = new \PDO(
				"mysql:host=localhost;dbname=twitter;charset=utf8",
				"root",
				"" 
			);

			return $con;

		} catch (\PDOException $e) {
			echo $e;
		}
	}
}

?>