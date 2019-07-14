<?php
include_once "../../libs/RestServer.php";
include_once "../../config.php";

class Users
{
	
	function __construct()
    {
        
		$this->conn = new PDO("mysql:host=".HOST.";dbname=".DB_NAME.";charset=utf8", USER, PASSWORD);
           
    }
	public function postUsers()
    {	
		
        $username = $_POST['username'];
        $password = $_POST['password'];
		
		if ($this->isUserExist($username)) {
			$sqlQuery = "INSERT INTO `booker_users` (`username`, `password`) VALUES ('$username', '$password')";
			$result = $this->conn->query($sqlQuery) ;  
			return ['name' => $username];
		} 
		return false;
    }
	
	public function postUsersLogin()
	{	
	
		/* $request = file_get_contents('php://input');
		$data = (array) json_decode($request);
		$username = $data['username'];
		$password = $data['password']; */
		
		$username = $_POST['username'];
        $password = $_POST['password'];
		
		$sqlQuery = "SELECT id, username, password FROM `booker_users` WHERE username = '$username'";
        $result = $this->conn->query($sqlQuery);
		$resultArray = array ();
			while ($row = $result->fetchAll(PDO::FETCH_ASSOC) ) {
				$resultArray[] = $row;
			}
		$passwordComperative = $resultArray[0][0]['password'];
		if ($password === $passwordComperative) {
			$token = md5($resultArray[0][0]['username']);
			$id = $resultArray[0][0]['id'];
			$sqlQuery = "UPDATE `booker_users` SET token = '$token' WHERE id = '$id'";
			$result = $this->conn->query($sqlQuery);
			return ['token' => $token, 'username' => $username, 'id' => $id];
		} else {
			return false;
		}
	}
	
	private function isUserExist($username)
    {
        $sqlQuery = "SELECT username FROM `booker_users` WHERE username = '$username'";
        $result = $this->conn->query($sqlQuery);
		$resultArray = array ();
			while ($row = $result->fetchAll(PDO::FETCH_ASSOC) ) {
				$resultArray[] = $row;
			}
			
        if(!empty($resultArray)) {
            return false;
        }
        return true;
    }
	
	
}
$users = new Users();
$server = new RestServer($users);
