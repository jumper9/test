<?php
namespace sancor\prizes;

class LoginController {

    public static function post() {

		$captcha = f::getCaptcha();

		$userExists = (f::dbRes("select 1 from sc_users where dni = {p:dni} and email = {p:email}") == 1); 

		f::setResponseJson(array("ok" => $userExists, "captcha" => $captcha));

	}
}