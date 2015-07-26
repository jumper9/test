<?php 
namespace wunderman\wunderforms;

class FormsController {

	public static function get() 
	{
		if(!security::isLogged() || !USER_IS_ADMIN) { return; }
		$clients = f::dbFullRes("select id, name, status from fm_clients");
		
		$forms=f::dbFullRes("select f.id, f.client_id, f.name, f.detail, f.description, date_format(f.available_from,'%d/%m/%Y') as available_from, date_format(f.available_to,'%d/%m/%Y') as available_to, f.status, c.name client_name 
							from fm_forms f 
							left join fm_clients c on (c.id=f.client_id)
							where (f.status = 1 or f.status = 0)
							order by client_id, status desc, name");
		foreach($forms as $k=>$v) {
			$forms[$k]["available_from"] = ($forms[$k]["available_from"]=='00/00/0000' ? "" : $forms[$k]["available_from"]);
			$forms[$k]["available_to"] = ($forms[$k]["available_to"]=='00/00/0000' ? "" : $forms[$k]["available_to"]);
			$forms[$k]["detail"] = json_decode($forms[$k]["detail"],true);
			$forms[$k]["path"] = "/form/".$forms[$k]["client_id"]."/".$forms[$k]["id"];
		}					
		f::setResponseJson(array("data" => $forms, "clients" => $clients));
	}
	
	public static function edit() 
	{
		if(!security::isLogged() || !USER_IS_ADMIN) { return; }
		$clientId = f::getParam("client_id");
		$name = f::getParam("name");
		$detail = f::getParam("detail");
		$availableFrom = f::date2sql(f::getParam("available_from"));
		$availableTo = f::date2sql(f::getParam("available_to"));
		$status = f::getParam("status");
		
		if($status!=1 and $status!=0 and $status!=2) {
			f::setError(400, "Wrong Status");
		}
		if(!$name) {
			f::setError(400, "Invalid form name");
		}
		
		$clientExists = f::dbRes("select 1 from fm_clients where id = {p:client_id}");

		if(!$clientExists) {
			f::setError(400, "Client does not Exist");
		}
		if(!f::hasErrors()) {
			
			if(f::getParam("form_id")) {
				f::dbQuery("insert into fm_forms_log (created_date, form_id, client_id, name, enabled_domains, detail, available_from, available_to, status, description)
					select now(), id, client_id, name, enabled_domains, detail, available_from, available_to, status, description from fm_forms where id = {p:form_id}");
	
				f::dbQuery("update fm_forms set name = {p:name}, detail = {p:detail}, available_from = {availableFrom}, available_to = {availableTo}, status = {p:status} where id = {p:form_id}",
					array("availableFrom" => $availableFrom, "availableTo" => $availableTo ));
			} else {
				f::dbQuery("insert into fm_forms set client_id = {p:client_id}, name = {p:name}, detail = {p:detail}, available_from = {availableFrom}, available_to = {availableTo}, status = {p:status} ",
					array("availableFrom" => $availableFrom, "availableTo" => $availableTo ));
			}
			f::setResponseJson(array("ok"=>1));
		}
	}
	
}