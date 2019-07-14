<?php

class RestServer
{

    public function __construct($className)
    {
        try
        {
            $this->parseMethod($className);
        }
        catch (Exception $e)
        {
            echo json_encode(['errors' => $e->getMessage()]);
        }
    }
    private function parseMethod($className)
    {
        $this->className = $className;
        $url = $_SERVER['REQUEST_URI'];
        list($b, $a, $db, $className, $methodName, $path) = explode('/', $url, 6);
        $params = explode('/', $url, 2);
        $method = $_SERVER['REQUEST_METHOD'];
        $funcParams = explode('/', $path);
        $result = '';
        switch ($method) {
            case 'GET':
                $result = $this->setMethod('get' . $methodName, $funcParams);
                break;
            case 'POST':
                $result = $this->setMethod('post' . $methodName, $funcParams);
                break;
            case 'PUT':
            $result = $this->setMethod('put' . $methodName, $funcParams);
                break;
            case 'DELETE':
            $result = $this->setMethod('delete' . $methodName, $funcParams);
                break;
            default:
                return false;
        }
		
        return $this->show_results($result);
    }
    public function setMethod($methodName, $param = false)
    {
        if (method_exists($this->className, $methodName))
        {
            return call_user_func([$this->className, $methodName], $param);
        } else {
			return "No such method: " . $methodName;
		}

    }
	public function show_results($result)
	{
        header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Headers: *');
		header('Access-Control-Allow-Methods: GET, POST, PUT');
        header('Content-Type: application/json');
        echo json_encode($result);
               
    }
    
}

