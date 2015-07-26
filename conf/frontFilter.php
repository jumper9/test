<?php
namespace wunderman\wunderforms;

class FrontFilter {
	public static function getUrl() {
		
		$url=f::getParam("_url");
		if(substr($url,0,6) == "/form/") {
			f::setParam("client_id", f::strtoken($url,3,"/"));
			f::setParam("form_id", f::strtoken($url,4,"/"));
			$url = "/forms/post";
			
		} else if($url == "/" || $url == "/index") {
			header("Location:/admin/index");
			die;
		} 
		
		return $url;
	}
}

