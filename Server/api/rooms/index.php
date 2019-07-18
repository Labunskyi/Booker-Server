<?php
include_once "../../libs/RestServer.php";
include_once "../../config.php";

class Rooms
{
	
	function __construct()
    {
        
		$this->conn = new PDO("mysql:host=".HOST.";dbname=".DB_NAME.";charset=utf8", USER, PASSWORD);
           
    }
	
  
    
}
$rooms = new Rooms();
$server = new RestServer($rooms);
