<?php
namespace wunderman\wunderforms;

class LoginController {

    public static function post() {
 
        $user=f::getParam("user");
        $pass=f::getParam("pass");
        
        $userId=f::dbRes("select id from fm_users where email='$user' and (password='$pass' or password='".md5($pass)."') and status=1");
        $userIp=$_SERVER["REMOTE_ADDR"];
        
        if(!$userId) {
            f::setError(400,"Invalid user");
            
        } else {
            // create token
            $token=md5(uniqid($userId, true)).md5(uniqid());
            
        }
        if(!f::hasErrors()) {
            $userName = f::dbRes("select name from fm_users where id='$userId'");
			$isAdmin = (f::dbRes("select is_admin from fm_users where id='$userId'")==1);

            f::dbQuery("insert into fm_sessions set user_id='$userId', user_ip='$userIp', token='$token', status=1, created_date=now()");
            f::setResponseJson(array("userName"=>$userName, "_api_key"=>$token, "isAdmin"=>$isAdmin));
        }
        
    }
}