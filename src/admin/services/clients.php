<?php 
namespace wunderman\wunderforms;

class ClientsController {

	public static function get() {

		if(!security::isLogged() || !USER_IS_ADMIN) { return; }
		$clients=f::dbFullRes("select id, name, status from fm_clients order by status desc, name");
		f::setResponseJson(array("data"=>$clients));
	}
	public static function save() {

		if(!security::isLogged() || !USER_IS_ADMIN) { return; }
		$status = f::getParam("status");
		$clientId = f::getParam("client_id");
		$name = f::getParam("name");
		
		if($status!=1 && $status!=0) {
			f::setError(400, "Invalid Client Status");
		}
		if(!$clientId && !$name) {
			f::setError(400, "Invalid Client Name");
		}

		$clientExists = (f::dbRes("select 1 from fm_clients where id = {p:client_id}") == 1);
		if($clientId && !$clientExists) {
			f::setError(400, "Invalid Client Id");
		}
		
		if(!f::hasErrors()) {
			if($clientId) {
				f::dbQuery("update fm_clients set status = {p:status} where id = {p:client_id}");
			} else {
				f::dbQuery("insert into fm_clients set name = {p:name}, status = {p:status}");
			}
			f::setResponseJson(array("ok"=>1));
		}
	}
	
}
