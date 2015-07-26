<?php
namespace wunderman\wunderforms;

class GetformsController {
        
    public static function get() {
		
        if(!security::isLogged()) { return; }

		$clients = f::dbFullRes("select distinct c.id, c.name 
								 from fm_clients c
								 join fm_users_clients uc on (uc.client_id = c.id)
								 where c.status = 1
								 and uc.user_id = {userId}
								 order by c.name ", array("userId" => USER_ID));
		
		$forms = f::dbFullRes("select c.id client_id, f.id, f.name, f.status
								 from fm_forms f
								 join fm_clients c on (c.id = f.client_id)
								 join fm_users_clients uc on (uc.client_id = c.id)
								 where c.status = 1
								 and uc.user_id = {userId}
								 order by c.id, f.status desc, f.id desc ", array("userId" => USER_ID));

		foreach ($forms as $k=>$v) {
			$siteTableId = "fm_userdata_".substr("00".$forms[$k]["client_id"],-3);

			$forms[$k]["data_7_days"] = f::dbRes("select count(*) from {d:siteTableId} ud where ud.form_id = {formId} and date(created_date) >= (CURDATE() - INTERVAL 7 DAY)",array("siteTableId"=>$siteTableId, "formId"=>$forms[$k]["id"]));
			$forms[$k]["data_total"] = f::dbRes("select count(*) from {d:siteTableId} ud where ud.form_id = {formId}",array("siteTableId"=>$siteTableId, "formId"=>$forms[$k]["id"]));

		}						 
								 
		f::setResponseJson(array("clients" => $clients, "forms"=> $forms));
		
	}

}