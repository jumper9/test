<?php
namespace wunderman\wunderforms;

class LogoutController {

    public static function post() {
 
        $token=f::getParam("_api_key");
        $userIp=$_SERVER["REMOTE_ADDR"];

        $sessionId=f::dbRes("select id from ge_sessions where user_ip='$userIp' and token='$token' and status=1");
        if($sessionId) {
            if(defined("DELETE_SESSIONS")) {
                f::dbQuery("delete from ge_sessions where id='$sessionId'");
            } else {
                f::dbQuery("update ge_sessions set status=0 where id='$sessionId'");
            }
            f::setResponseJson(array("ok"=>1));
        } else {
            f::setError(400,"Sesion invalida");
        }
    }
}
