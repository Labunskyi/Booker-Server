<?php
include_once "../../libs/RestServer.php";
include_once "../../libs/SQL.php";

class Rooms
{
	
	function __construct()
    {
        
		$this->sql = new SQL();
           
    }
	
  
    
}
$rooms = new Rooms();
$server = new RestServer($rooms);
