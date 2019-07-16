<?php
include_once "../../libs/RestServer.php";
include_once "../../config.php";

class Users
{
	
	function __construct()
    {
        
		$this->conn = new PDO("mysql:host=".HOST.";dbname=".DB_NAME.";charset=utf8", USER, PASSWORD);
           
    }
	public function postUser()
    {	
		
        $username = $_POST['username'];
        $password = $_POST['password'];
		$email = $_POST['email'];
		$is_admin = $_POST['is_admin'];
		$is_active = $_POST['is_active'];
		
		if ($this->isUserExist($email)) {
			$sqlQuery = "INSERT INTO `booker_users` (`username`, `password`, `email`, `is_admin`, `is_active`)
			VALUES ('$username', '$password', '$email', '$is_admin', '$is_active')";
			$result = $this->conn->query($sqlQuery) ;  
			return ['name' => $username];
		} 
		return false;
    }
	
	public function getUserList()
    {	
		$sqlQuery = "SELECT `id`, `username`, `email` FROM `booker_users` WHERE `is_admin` = 0 AND `is_active` = 1";
		$result = $this->conn->query($sqlQuery) ;  
		$resultArray = array ();
			while ($row = $result->fetchAll(PDO::FETCH_OBJ) ) {
				$resultArray[] = $row;
			}
			$resultArr = array ();
			foreach ($resultArray as $resultArr) {
				return $resultArr;
			}
    }
	
	public function putUserRemove($request)
    {	
		$request = file_get_contents('php://input');
		$data = (array) json_decode($request);
		$id = $data['id'];
		$sqlQuery = "UPDATE `booker_users` SET `is_active` = 0 WHERE id = '$id'";
		$result = $this->conn->query($sqlQuery) ;  
		
		return $result;
    }
	
	public function getUserById($params)
    {	
		$id = $params[0];
		$sqlQuery = "SELECT `id`, `username`, `email`, `password` FROM `booker_users` WHERE `id` = '$id'";
		$result = $this->conn->query($sqlQuery) ;  
		$resultArray = array ();
			while ($row = $result->fetchAll(PDO::FETCH_OBJ) ) {
				$resultArray[] = $row;
			}
			$resultArr = array ();
			foreach ($resultArray as $resultArr) {
				return $resultArr;
			}
    }
	
	public function putUserEdit($request)
    {	
		$request = file_get_contents('php://input');
		$data = (array) json_decode($request);
		$id = $data['id'];
		$username = $data['username'];
        $password = $data['password'];
		$email = $data['email'];
		$sqlQuery = "UPDATE `booker_users` SET `username` = '$username', `password` = '$password',
		`email` = '$email' WHERE id = '$id'";
		$result = $this->conn->query($sqlQuery) ;  
		
		return $result;
    }
	
	public function postUsersLogin()
	{	
	
		/* $request = file_get_contents('php://input');
		$data = (array) json_decode($request);
		$username = $data['username'];
		$password = $data['password']; */
		
		$username = $_POST['username'];
		$email = $_POST['email'];
        $password = $_POST['password'];
		
		$sqlQuery = "SELECT `id`,`username`, `password`, `email` FROM `booker_users` WHERE `email` = '$email' 
		AND `is_active` = 1";
        $result = $this->conn->query($sqlQuery);
		$resultArray = array ();
			while ($row = $result->fetchAll(PDO::FETCH_ASSOC) ) {
				$resultArray[] = $row;
			}
		$passwordComperative = $resultArray[0][0]['password'];
		$usernameComperative = $resultArray[0][0]['username'];
		if ($password === $passwordComperative && $username === $usernameComperative) {
			$token = md5($resultArray[0][0]['email']);
			$id = $resultArray[0][0]['id'];
			$sqlQuery = "UPDATE `booker_users` SET token = '$token' WHERE id = '$id'";
			$result = $this->conn->query($sqlQuery);
			return ['token' => $token, 'username' => $username, 'id' => $id, 'email' => $email];
		} else {
			return false;
		}
	}
	
	private function isUserExist($email)
    {
        $sqlQuery = "SELECT `email` FROM `booker_users` WHERE `email` = '$email'";
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
