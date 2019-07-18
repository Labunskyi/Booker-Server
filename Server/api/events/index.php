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
	
	public function getEvents($params){
		$idroom = $params[0];
		$sqlQuery = "SELECT `id`, `is_recurring`, `idrec`, `description`, `start_time`,
		`end_time`, `date`, `idroom`, `iduser` FROM `booker_events` where `idroom` = '$idroom'";
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
		$sqlQuery = "SELECT booker_events.id, booker_events.is_recurring, booker_events.idrec, 
		booker_events.description, booker_events.start_time, booker_events.end_time, booker_events.date, 
		booker_events.created_time, booker_events.idroom, booker_events.iduser, booker_users.username 
		FROM booker_events INNER JOIN booker_users ON booker_events.iduser = booker_users.id 
		WHERE booker_events.id = '$id';";
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
	
	public function putEventEdit($request){
		
		$request = file_get_contents('php://input');
		$data = (array) json_decode($request);
		$id = $data['id'];
		$description = $data['description'];
		$start_time = $data['start_time'];
		$end_time = $data['end_time'];
				
		$sqlQuery = "UPDATE `booker_events` SET `description` = '$description', 
		`start_time` = '$start_time', `end_time` = '$end_time' WHERE id = '$id'";
		$result = $this->conn->query($sqlQuery);
		
		return $result;
		
	}
	
	public function deleteEvent($params){
		$id = $params[0];
		$sqlQuery = "DELETE FROM `booker_events` WHERE `id` = '$id'";
        $result = $this->conn->query($sqlQuery);
		return $result;
		
	}
	
}
$events = new Events();
$server = new RestServer($events);
