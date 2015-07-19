<?php
namespace sancor\prizes;

class CaptchaController {

    public static function get() {

		f::setResponseJson(array("captcha" => f::getCaptcha()));

	}
}
