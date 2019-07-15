<?php
include_once "../../libs/RestServer.php";
include_once "../../config.php";

class Events
{
	
	function __construct()
    {
        
		$this->conn = new PDO("mysql:host=".HOST.";dbname=".DB_NAME.";charset=utf8", USER, PASSWORD);
           
    }
	public function postSingleEvent()
    {
		
		$is_recurring = $_POST['is_recurring'];
        $idrec = $_POST['idrec'];
		$description = $_POST['description'];
        $start_time = $_POST['start_time'];
		$end_time = $_POST['end_time'];
		$date = $_POST['date'];
		$idroom = $_POST['idroom'];
		$iduser = $_POST['iduser'];
       
        $sqlQuery = "INSERT INTO `booker_events` (`is_recurring`, `idrec`, `description`, `start_time`,
		`end_time`, `date`, `idroom`, `iduser`) VALUES ('$is_recurring', '$idrec', '$description', '$start_time',
		'$end_time', '$date', '$idroom', '$iduser')";
		$result = $this->conn->query($sqlQuery) ;
       
        return $result;

    }
	
	public function getEvents(){
		$sqlQuery = "SELECT * FROM `booker_events`";
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
	
	public function getEventById($params){
		$id = $params[0];
		$sqlQuery = "SELECT * FROM `booker_events` where `id` = '$id'";
		$result = $this->conn->query($sqlQuery);    
        
		$resultArray = array ();
		while ($row = $result->fetchAll(PDO::FETCH_OBJ) ) {
			$resultArray[] = $row;
		}

		$resultArr = array ();
		foreach ($resultArray as $resultArr) {
			return $resultArr;
		}

	}
	
	public function postOrderList()
    {
		$userid = $_POST['userid'];
        
		$sqlQuery = "SELECT car.Brand, car.Model, car.Capacity, car.Year, car.Speed, car.Price FROM car INNER JOIN `order` AS ord ON car.carid = ord.carid 
		WHERE ord.userid = '$userid'";
		$result = $this->conn->query($sqlQuery);    
        
		$resultArray = array ();
		while ($row = $result->fetchAll(PDO::FETCH_OBJ) ) {
			$resultArray[] = $row;
		}
		$resultArr = array ();
		foreach ($resultArray as $resultArr) {
			return $resultArr;
		}


    }
	
	public function postToken()
	{	
		if (isset($_POST['token'])) {
		$token = $_POST['token'];
		$sqlQuery = "SELECT UserId, username, password, token FROM `users` WHERE token = '$token'";
        $result = $this->conn->query($sqlQuery);
		
		$resultArray = array ();
			while ($row = $result->fetchAll(PDO::FETCH_ASSOC) ) {
				$resultArray[] = $row;
			}
		$tokenComperative = $resultArray[0][0]['token'];
		$userid = $resultArray[0][0]['UserId'];
        if($token === $tokenComperative) {
            return ['token' => $token, 'userid' => $userid ];
        }
        return false;
		}
		
	}
	
}
$events = new Events();
$server = new RestServer($events);
