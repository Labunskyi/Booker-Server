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
		$idroom = $_POST['idroom'];
		$iduser = $_POST['iduser'];
		$is_recurring = ($_POST['is_recurring'] == '1') ? 1 : 0;
		
		if ($is_recurring)
        {

            $period = $_POST['period'];
            $duration_recurring = $_POST['duration_recurring'];
        }
        $idrec = $this->getNewIdRec();
		$date = $_POST['date'];
        date_default_timezone_set('UTC');
		
		 if ($is_recurring)
        {
            $dates = $this->createRecDates($start_time, $end_time, $period, $duration_recurring);
            foreach ($dates as $rec_date)
            {
                $rec_start_time = $rec_date[0];
                $rec_end_time = $rec_date[1];
                $this->isFreeRange($idroom,$rec_start_time,$rec_end_time);
            }
            foreach ($dates as $rec_date)
            {
                $rec_start_time = $rec_date[0];
                $rec_end_time = $rec_date[1];
                $this->insertSingleEvent($is_recurring, $idrec, $description, $rec_start_time, $rec_end_time, $idroom, $iduser);
            }
        }
        else
        {	$this->isFreeRange($idroom, $start_time, $end_time);
            $this->insertSingleEvent($is_recurring, $idrec, $description, $start_time, $end_time, $idroom, $iduser);
        }
        return false;
	}	
	
	
	private function createRecDates($start_time, $end_time, $period, $duration)
    {
        $dates = [[$start_time, $end_time]];
        $n = +$duration;
        $periods = ['weekly' =>['week',1], 'bi-weekly' => ['week',2], 'monthly' => ['week',4]];
        $limits = ['weekly' =>['week',4], 'bi-weekly' => ['week',3], 'monthly' => ['week',1]];

        $timeMeasure = $periods[$period][0];
        if ($n > $limits[$period][1])
        {
            http_response_code(400);
            throw new Exception('Date limit exceeded');
        }

        $nPeriods = $periods[$period][1];
        for ($i = 0; $i < $n; $i++)
        {
            $start = strtotime($start_time . ' +' . $nPeriods * ($i + 1) . ' '. $timeMeasure);
            $end = strtotime($end_time . ' +' . $nPeriods * ($i + 1) . ' '. $timeMeasure);
            $dates[] = [date('Y-m-d H:i', $start), date('Y-m-d H:i', $end)];
        }
        return $dates;
    }
	
	
	private function isFreeRange($idroom, $start_time, $end_time, $current_id=false)
    {
        $sqlQuery = 'SELECT id, start_time, end_time, idroom, iduser FROM booker_events';
        $sqlQuery .= ' WHERE idroom=' . $idroom;
        $sqlQuery .= " AND (((start_time >= '$start_time') AND (end_time <= '$end_time'))"
                    ." OR ((start_time >= '$start_time') AND (start_time <= '$end_time'))"
                    ." OR ((end_time >='$start_time') AND (end_time <= '$end_time')))";
        if ($current_id)
        {
            $sqlQuery .= " AND id<>$current_id";
        }
		
		$result = $this->conn->query($sqlQuery) ;
        $events = $result->fetchAll(PDO::FETCH_ASSOC);
        if (count($events)>0)
        {
            http_response_code(403);
            throw new Exception("The time range is bisy!");
        }
        return true;
    }
	
	
	
	
		private function insertSingleEvent($is_recurring, $idrec, $description, $start_time, $end_time, $idroom, $iduser)
    {
	   $sqlQuery = "INSERT INTO `booker_events` (`is_recurring`, `idrec`, `description`, `start_time`,
		`end_time`, `idroom`, `iduser`) VALUES ('$is_recurring', '$idrec','$description', '$start_time',
		'$end_time', '$idroom', '$iduser')";
		$result = $this->conn->query($sqlQuery) ;
       
        return $result;

    }
	
	
	private function getNewIdRec()
    {
        return strtotime('now');
    }
	
	public function getEvents($params){
		$idroom = $params[0];
		$sqlQuery = "SELECT `id`, `is_recurring`, `idrec`, `description`, `start_time`,
		`end_time`, `idroom`, `iduser` FROM `booker_events` where `idroom` = '$idroom'";
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
		booker_events.description, booker_events.start_time, booker_events.end_time,  
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
