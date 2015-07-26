<?php 
namespace wunderman\wunderforms;

class Security 
{
    public static function isLogged() 
    {
        $token=f::getParam("_api_key");
        $userIp=$_SERVER["REMOTE_ADDR"];

        $session = f::dbFirstRow("select user_id from fm_sessions where user_ip = {userIp} and token = {token} and status=1 ", array("userIp" => $userIp, "token" => $token));
		$userId = isset($session["user_id"]) ? $session["user_id"] : 0;

		
		
        if ($userId) {
            $userName = f::dbRes("select name from fm_users where id='$userId'");
			$isAdmin = (f::dbRes("select is_admin from fm_users where id='$userId'")==1);

			if (!defined("USER_ID")) { 
                define("USER_ID", $userId);
                define("USER_NAME", $userName);
                define("USER_IS_ADMIN", $isAdmin);
            }
            return true;
        } else {
			define("USER_ID", "");
               define("USER_NAME", "");
               define("USER_IS_ADMIN", "");

            f::setError(401,"Unauthenticated");
            return false;
        }
    }
}
