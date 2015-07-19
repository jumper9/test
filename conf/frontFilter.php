<?php
namespace sancor\prizes;

class frontFilter {
	public static function getUrl() {
		$url=f::strtoken(strtolower(f::getParam("_url")),-1,"/api");

		return $url;
	}
}

