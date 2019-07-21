<?php
include_once "../../libs/RestServer.php";
include_once "../../libs/SQL.php";


class Events
{
	
	function __construct()
    {
        
		$this->sql = new SQL();
           
    }
	
	// create single event
	
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
            $this->insertSingleEvent($is_recurring, $idrec = false, $description, $start_time, $end_time, $idroom, $iduser);
        }
        return false;
	}	
	
	// create an array with reccuring events
	
	public function createRecDates($start_time, $end_time, $period, $duration) 
    {
        $dates = [[$start_time, $end_time]];
        $periods = ['weekly' =>[1], 'bi-weekly' => [2], 'monthly' => [4]];
        $limits = ['weekly' =>[4], 'bi-weekly' => [4], 'monthly' => [4]];
        if ($duration > $limits[$period][0])
        {
            throw new Exception('Duration limit exceeded');
        }
        $nPeriods = $periods[$period][0];
        for ($i = 0; $i < $duration; $i++)
        {
            $start = strtotime($start_time . ' +' . $nPeriods * ($i + 1) . ' '. 'week');
            $end = strtotime($end_time . ' +' . $nPeriods * ($i + 1) . ' '. 'week');
            $dates[] = [date('Y-m-d H:i', $start), date('Y-m-d H:i', $end)];
        }
        return $dates;
    }
	
	//check if the time has already booked
	
	private function isFreeRange($idroom, $start_time, $end_time, $current_id = false)
    {
        $sqlQuery = 'SELECT `id`, `start_time`, `end_time`, `idroom`, `iduser` FROM `booker_events`';
        $sqlQuery .= ' WHERE `idroom` =' . $idroom;
        $sqlQuery .= " AND (((`start_time` >= '$start_time') AND (`end_time` <= '$end_time'))"
                    ." OR ((`start_time` >= '$start_time') AND (`start_time` <= '$end_time'))"
                    ." OR ((`end_time` > '$start_time') AND (`end_time` <= '$end_time')))";
		if ($current_id)
        {
            $sqlQuery .= " AND `id` <> '$current_id'";
        }
		$result = $this->sql->conn->query($sqlQuery);
        $events = $result->fetchAll(PDO::FETCH_ASSOC);
        if (count($events)>0)
        {
            
            throw new Exception("This time has already booked!");
        }
        return true;
    }
	
	// make single event
	
	private function insertSingleEvent($is_recurring, $idrec, $description, $start_time, $end_time, $idroom, $iduser)
    {
	   $sqlQuery = "INSERT INTO `booker_events` (`is_recurring`, `idrec`, `description`, `start_time`,
		`end_time`, `idroom`, `iduser`) VALUES ('$is_recurring', '$idrec','$description', '$start_time',
		'$end_time', '$idroom', '$iduser')";
		$result = $this->sql->conn->query($sqlQuery);
       
        return $result;
    }
	
	// get unix timestamp
	
	private function getNewIdRec()
    {
        return strtotime('now');
    }
	
	// get all events
	
	public function getEvents($params)
	{
		$idroom = $params[0];
		$sqlQuery = "SELECT `id`, `is_recurring`, `idrec`, `description`, `start_time`,
		`end_time`, `idroom`, `iduser` FROM `booker_events` where `idroom` = '$idroom'";
		$result = $this->sql->conn->query($sqlQuery) ;
       
        $resultArray = array ();
			while ($row = $result->fetchAll(PDO::FETCH_OBJ) ) {
				$resultArray[] = $row;
			}
			$resultArr = array ();
			foreach ($resultArray as $resultArr) {
				return $resultArr;
			}	
	}
	
	// get a single event by id
	
	public function getEventById($params)
	{
		$id = $params[0];
		$sqlQuery = "SELECT booker_events.id, booker_events.is_recurring, booker_events.idrec, 
		booker_events.description, booker_events.start_time, booker_events.end_time,  
		booker_events.created_time, booker_events.idroom, booker_events.iduser, booker_users.username 
		FROM booker_events INNER JOIN booker_users ON booker_events.iduser = booker_users.id 
		WHERE booker_events.id = '$id';";
		$result = $this->sql->conn->query($sqlQuery);    
        
		$resultArray = array ();
		while ($row = $result->fetchAll(PDO::FETCH_OBJ) ) {
			$resultArray[] = $row;
		}
		$resultArr = array ();
		foreach ($resultArray as $resultArr) {
			return $resultArr;
		}
	}
	
	// edit an event or reccuring events
	
	public function putEventEdit($request)
	{
		
		$request = file_get_contents('php://input');
		$data = (array) json_decode($request);
		$id = $data['id'];
		$description = $data['description'];
		date_default_timezone_set('UTC');
		$start_time = $data['start_time'];
		$end_time = $data['end_time'];
		$idroom = $data['idroom'];
		$is_recurring = ($data['is_recurring'] == '1') ? 1 : 0;
		$applyToAllRec = ($data['applyToAllRec'] == 'true') ? 1 : 0;
		$idrec = $data['idrec'];
		$iduser = $data['iduser'];
		
		if ($is_recurring && $applyToAllRec)
        {	
            $rec_events = $this->getRecEvents($idrec);
            foreach ($rec_events as $event)
            {
                $id = $event['id'];
                $start = $this->changeTime($event['start_time'], $start_time);
                $end = $this->changeTime($event['end_time'], $end_time);
				$this->isFreeRange($idroom, $start, $end, $id);
				if (strtotime($start)>=strtotime($start_time)) 
				{
					$sqlQuery = "UPDATE `booker_events` SET `is_recurring` = '$is_recurring', `description` = '$description', 
					`start_time` = '$start', `end_time` = '$end', `iduser` = '$iduser' WHERE `id` = '$id'";
					$result = $this->sql->conn->query($sqlQuery);
				}
            }
        }
        else
        {	
			$this->isFreeRange($idroom, $start_time, $end_time, $id);
			$sqlQuery = "UPDATE `booker_events` SET `is_recurring` = '$is_recurring', `description` = '$description', 
			`start_time` = '$start_time', `end_time` = '$end_time', `iduser` = '$iduser' WHERE `id` = '$id'";
			$result = $this->sql->conn->query($sqlQuery);
        }
		return $result;
	}
	
	// get reccuring events
	
	public function getRecEvents($idrec)
    {	
        $sqlQuery = "SELECT `id`, `is_recurring`, `idrec`, `description`, `start_time`, `end_time`, `created_time`,
     	`idroom`, `iduser` FROM `booker_events` WHERE `idrec` = '$idrec'";
        $result = $this->sql->conn->query($sqlQuery);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
	
	// chang time in reccuring events
	
	private function changeTime($srcDate, $srcTime)
    {
        $time1 = strtotime($srcDate);
        $time2 = strtotime($srcTime);
        $resDate = strtotime(date('Y-m-d', $time1).' '.date('H:i', $time2) );
        return date('Y-m-d H:i',$resDate);
    }
	
	//delete an event or reccuring events
	
	public function postDeleteEvent($params){
		$id = $params[0];
		date_default_timezone_set('UTC');
		$start_time = $_POST['start_time'];
		$is_recurring = ($_POST['is_recurring'] == '1') ? 1 : 0;
		$applyToAllRec = ($_POST['applyToAllRec'] == 'true') ? 1 : 0;
		$idrec = $_POST['idrec'];
		
        if ($is_recurring && $applyToAllRec)
        {
            $rec_events = $this->getRecEvents($idrec);
            foreach ($rec_events as $event)
            {
                $id = $event['id'];
                $start = $this->changeTime($event['start_time'], $start_time);
                if (strtotime($start)>=strtotime($start_time))
                {
                    $sqlQuery = "DELETE FROM `booker_events` WHERE `id` = '$id'";
					$result = $this->sql->conn->query($sqlQuery);
					
                }
            }
        }else
        {
            $sqlQuery = "DELETE FROM `booker_events` WHERE `id` = '$id'";
			$result = $this->sql->conn->query($sqlQuery);
			return $result;
        }
       
        return false;
		
		
	}
	
}
$events = new Events();
$server = new RestServer($events);