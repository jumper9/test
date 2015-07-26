<?php
namespace wunderman\wunderforms;

class PostController {

    public static function post() {
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
					if($enabledDomain && ( $enabledDomain == $host || $enabledDomain == $host2 )) {
						$hostOk = true;
					}
				}
				if(!$hostOk) {
					f::setError(400,"Hostname not allowed");
				}
			}
			
		}

		if(f::hasErrors()) { return; }
		
		// get form data
		$formDetail = json_decode($form["detail"],true);
		$fields = $formDetail["fields"];
		
		// validate captcha
		if(isset($formDetail["captcha"]) && $formDetail["captcha"]) {
			f::validateParam("captcha", array("captcha"), "Wrong captcha");
		}

		// validate fields
		$dataFields = array();
		foreach ($fields as $field) {
			if($field["name"]) {
				if(!isset($field["type"])) {
					$field["type"] = "string";
				}
				$validations[0] = $field["type"];
				if(isset($field["minlength"]) && $field["minlength"]) {
					$validations[] = "minlength:{$field["minlength"]}";
				}
				if(isset($field["maxlength"]) && $field["maxlength"]) {
					$validations[] = "maxlength:{$field["maxlength"]}";
				}
				if(isset($field["minvalue"]) && $field["minvalue"]) {
					$validations[] = "minvalue:{$field["minvalue"]}";
				}
				if(isset($field["maxvalue"]) && $field["maxvalue"]) {
					$validations[] = "maxvalue:{$field["maxvalue"]}";
				}
				if(!isset($field["errorMessage"])) {
					$field["errorMessage"] = "Invalid {$field["name"]}";
				}
				f::validateParam($field["name"], $validations, $field["errorMessage"]);
				$dataFields[$field["name"]] = f::getParam($field["name"]);
			}
		} 
		
		if(f::hasErrors()) { return; }

		$userData = json_encode($dataFields, JSON_UNESCAPED_UNICODE);
		
		// validations are ok, then insert
		$siteTableId = "fm_userdata_".substr("00".f::getParam("client_id")*1,-3);
		$insertId = f::dbInsert("insert into {d:siteTableId} set 
								created_date = now(),
								status = 0,
								form_id = {p:form_id}, 
								client_id = {p:client_id}, 
								user_data = {userData}", array("siteTableId"=>$siteTableId, "userData"=>$userData));
		
		if(!$insertId) {
			f::setError(500, "Unexpected Error");
			
		} else {
			f::setResponseJson( array("ok" => true) );
			
		}
		
	}
}
