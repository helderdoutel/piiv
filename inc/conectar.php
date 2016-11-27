<?php
	
	function ConectarDB() {
		$host = "127.0.0.1";
		$user = "root";
		$pass = "";
		$database = "sist_carros";
		$conn = mysqli_connect($host, $user, $pass, $database) or die(mysql_error());
		return $conn;
	}
	
	function DesconectarDB($conn) {
		mysqli_close($conn) or die(mysql_error());
		unset($conn);
	}
?>
