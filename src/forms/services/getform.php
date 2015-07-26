<?php
namespace wunderman\wunderforms;

class GetformController {

    public static function get() {


		$form = f::dbFirstRow("select name, enabled_domains, detail 
						from fm_forms 
						where id = {p:form_id} 
						and client_id = {p:client_id} 
						and (available_from = '' or available_from <= curdate()) 
						and (available_to = '' or available_to >= curdate()) 
						and status = 1");
		
		if(!$form) {
			f::setError(400,"Form not found");
		} else {
			
			if($form["enabled_domains"]) {
				$enabledDomains = explode(",",$form["enabled_domains"]);
				$host = f::strtoken($_SERVER["HTTP_HOST"],1,":");
				$host2 = f::strtoken($_SERVER["X-Forwarded-For"],1,":");
				$hostOk = false;
				foreach ($enabledDomains as $enabledDomain) {
					$enabledDomain =  trim($enabledDomain);
					if($enabledDomain and ( $enabledDomain == $host or $enabledDomain == $host2 )) {
						$hostOk = true;
					}
				}
				if(!$hostOk) {
					f::setError(400,"Hostname not allowed");
				}
			}
			
		}
		
		if(f::hasErrors()) { return; }
		$formDetail = json_decode($form["detail"],true);
		$uniqId = sha1(uniqid());
		$captcha = f::getCaptcha();
		
		f::setResponseJson(array("id" => $uniqId, "captcha" => $captcha, "form" => $formDetail ));
	}
}
