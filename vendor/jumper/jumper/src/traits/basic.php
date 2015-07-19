<?php
namespace jumper;

trait basicTrait 
{
    
    public static function hasErrors() 
    {
        return self::$errorCode;
    }

    public static function setError($errorCode,$errorMessage,$errorMessage2="") 
    {
            
        if ($errorMessage2) {
            $errorType=$errorMessage;
            $errorMessage=$errorMessage2;
        } else {
            $errorType=0;
        }
        
        self::$errorCode=$errorCode;
        self::$errorMessages[]=array("code"=>$errorCode,"type"=>$errorType,"message"=>$errorMessage);
    }
    
    public static function initialize() 
    {
		$configClass = "\\Config";
		if(defined("APP_NAMESPACE")) {
			$configClass = APP_NAMESPACE."\\Config";
		}
		$configClass::dbConnect();
		
		if (defined("DEBUG")) { 
			set_error_handler('exceptions_error_handler');
		}

		function exceptions_error_handler($severity, $message, $filename, $lineno) {
		  if (error_reporting() == 0) {
			return;
		  }
		  self::setError(400,"Server Error: $message - Severity: $severity - File $filename, line $lineno");
		}

        $params=$_GET;
        foreach($_POST as $k=>$v) {
            if ($v) {
                $params[$k]=$v;
            }
        }
        $request_body = file_get_contents('php://input');
        try {
            $data = json_decode($request_body,true);
        } catch(\Exception $e) {
            // do nothing
        }
        if (is_array($data)) {
            foreach($data as $k=>$v) {
                if ($v) {
                    $params[$k]=$v;
                }
            }
        }
		foreach($params as $k=>$v) {
			$params[strtolower($k)] = $v;
		}
        self::setParams($params);

    }
    
    public static function dieError($p1,$p2=null,$p3=null) 
    {
        self::setError($p1,$p2,$p3);
        self::execute();
    }

    public static function execute() 
    {
        if (self::$errorCode) {
            http_response_code(self::$errorCode);
            header('Content-Type: application/json');
            $errorData=array("apiVersion"=> "2.0", "errors"=>self::$errorMessages);
            if (ENV!="PROD") {
                $errorData["env"]=ENV;
                $errorData["server"]=$_SERVER;
                $errorData["post"]=$_POST;
                $errorData["params"]=self::getParams();
            }
            echo json_encode($errorData);
        } else {    
            if (self::$responseJson) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(self::$responseJson,JSON_UNESCAPED_UNICODE);
            } 
            if (self::$view) {
                include(self::$view);
            }
        }
    }
    
    public static function setView($viewName) 
    {
        self::$view=$viewName;        
    }
    
    public static function setResponseJson($responseJson) 
    {
        self::$responseJson=$responseJson;
    }

    public static function responseTxtJson($txt) 
    {
        self::setResponseJson(json_decode($txt,true));
    }

    public static function strtoken($string, $pos, $token) 
    {
        $explode = explode($token, $string);
        if (abs($pos) > sizeof($explode) || $pos == 0) {
                $out = '';
        } else if ($pos > 0) {
                $out = $explode [$pos-1];
        } else if ($pos < 0) {
                $out = $explode [sizeof($explode) + $pos];
        }
        return trim($out);
    }

	public static function validateCaptcha($id, $code) {
		
		if(defined("ENV") && (ENV=="LOCAL" || ENV=="DEV") && $code == "1234") {
			return true;
		}
		
		self::dbQuery("delete from sc_captcha where created_date < DATE_SUB(NOW(),INTERVAL 15 MINUTE)");
		$ok = (self::dbRes("select 1 from sc_captcha where id = {id} and code = {code} and remote_ip = {remote_ip}", array("id" => $id, "code" => $code, "remote_ip" => $_SERVER["REMOTE_ADDR"])) == 1 );
		self::dbQuery("delete from sc_captcha where id = {id} and remote_ip = {remote_ip}", array("id" => $id, "remote_ip" => $_SERVER["REMOTE_ADDR"]));
				
		return $ok;
	}
	
	public static function getCaptcha($params = array()) {
	
		$width = isset($params["width"]) ? $params["width"] : 55;
		$height = isset($params["height"]) ? $params["height"] : 25;
		$image = imagecreatetruecolor($width, $height);
		$bg = imagecolorallocate($image, 255, 255,255);
		imagefill($image, 0, 0, $bg);
		$code = rand(str_repeat("1", (isset($params["digits"]) ? $params["digits"] : 25) ) * 1, str_repeat("9", (isset($params["digits"]) ? $params["digits"] : 25) ) * 1 ); 
		$len = mb_strlen($code, "UTF-8");
		$x = 8;
		$y = 5;
		for ($i = 0; $i < $len; $i++) {
			$char = mb_substr($code, $i, 1, "UTF-8");
			$color = imagecolorallocate($image, mt_rand(0, 125), mt_rand(0, 125), mt_rand(0, 125));
			imagestring ($image , 4 , $x , $y , $char , $color );
			$x += 10;
		}
		ob_start();
		imagejpeg($image, null, 90);
		$jpgImage = ob_get_clean();
		
		$data = "data:image/jpeg;base64," . base64_encode($jpgImage);
		$id = self::dbInsert("insert into sc_captcha set code = {code}, created_date = now(), remote_ip = {remote-ip}", array("code" => $code, "remote-ip" => $_SERVER["REMOTE_ADDR"]));
		
		return array("id" => $id, "data" => $data);
	}

	public static function setExcelOutput($filename, $out) {
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		echo utf8_decode($out);
		die;
	}
}
