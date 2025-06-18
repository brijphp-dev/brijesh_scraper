<?php

class db{
	
	protected $conn;

	function __construct(){
		$this->conn = mysqli_connect("localhost","root", "", "brij_test");
		if(mysqli_connect_errno()){
			die("Connection Error: " . mysqli_connect_error());

		}
	}
}

?>