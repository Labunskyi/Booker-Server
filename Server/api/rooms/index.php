<?php
include_once "../../libs/RestServer.php";
include_once "../../config.php";

class Carshop
{
	
	function __construct()
    {
        
		$this->conn = new PDO("mysql:host=".HOST.";dbname=".DB_NAME.";charset=utf8", USER, PASSWORD);
           
    }
	
    public function getCar($params = false)
    {
        if ($params)
        {
            $id = $params[0];
            if ($id || is_numeric($id) || $id>0)
            {
                return $this->getById($id);
            } else {
				
            throw new Exception(ERR_CAR_ID_INVALID);
 
			}
        }
        
        try
        {
            $sqlQuery = "SELECT CarId, Brand, Model FROM car INNER JOIN colour ON car.colour = colour.colourid ORDER BY carid ASC";
			$result = $this->conn->query($sqlQuery);    
        
			$resultArray = array ();
			while ($row = $result->fetchAll(PDO::FETCH_OBJ) ) {
				$resultArray[] = $row;
			}
			$resultArr = array ();
			foreach ($resultArray as $resultArr) {
				return $resultArr;
			}
			
            
        }catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
      
    }
 
    private function getById($id)
    {
       
        if ( !$id || !is_numeric($id) || $id < 0)
        {
            throw new Exception(ERR_CAR_ID_INVALID);
        }
        try
        {
            $sqlQuery = "SELECT car.CarId, car.Brand, car.Model, car.Capacity, car.Year, car.Speed, car.Price, colour.Color FROM car INNER JOIN colour ON car.colour = colour.colourid where carid = '$id'";
			$result = $this->conn->query($sqlQuery);    
        
			$resultArray = array ();
			while ($row = $result->fetchAll(PDO::FETCH_OBJ) ) {
				$resultArray[] = $row;
			}
			$resultArr = array ();
			foreach ($resultArray as $resultArr) {
				return $resultArr;
			}
			}catch(Exception $e)
			{
            throw new Exception($e->getMessage());
			}
       
    }
	
	public function postFindCars()
	{	
	
		$brand = $_POST['brand'];
		$model = $_POST['model'];
		$capacity = $_POST['capacity']; 
		$year = $_POST['year']; 
		$colour = $_POST['colour'];
		$speed = $_POST['speed'];
		$price = $_POST['price'];
		
		$sqlQuery = "SELECT car.Carid, car.Brand, car.Model, car.Capacity, car.Year, car.Speed, 
		car.Price, colour.Color FROM `car` INNER JOIN colour ON car.Colour = colour.Colourid 
		WHERE `brand` LIKE '%{$brand}%' AND `model` LIKE '%{$model}%' 
        AND `capacity` LIKE '%{$capacity}%' AND `year` LIKE '%{$year}%' AND `color` LIKE '%{$colour}%' 
        AND `speed` LIKE '%{$speed}%' AND `price` LIKE '%{$price}%'";
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
   
    
}
$cars = new Carshop();
$server = new RestServer($cars);
