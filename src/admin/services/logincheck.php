<?php
namespace wunderman\wunderforms;

class LogincheckController {

    public static function get() {
        
        if(security::isLogged()) {
            f::setResponseJson(array("userName"=>USER_NAME, "isAdmin"=>USER_IS_ADMIN));
        } 
        
    }
}