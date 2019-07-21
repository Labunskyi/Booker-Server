<?php
include_once "../../config.php";

class SQL
{

    public function __construct()
    {
       try 
        {
            $this->conn = new PDO("mysql:host=".HOST.";dbname=".DB_NAME.";charset=utf8", USER, PASSWORD);
			
        }catch (PDOException $e) 
        {
            throw new Exception('Connection error: ' . $e->getMessage());
        }
    }
}
