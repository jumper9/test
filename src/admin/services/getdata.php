<?php
namespace wunderman\wunderforms;

class GetdataController {

    public static function get() {

		if(!security::isLogged()) { return; }
		
		$formAllowed = f::dbRes("select 1
						 from fm_forms f
						 join fm_clients c on (c.id = f.client_id)
						 join fm_users_clients uc on (uc.client_id = c.id)
						 where f.id = {p:form_id} 
						 and c.status = 1
						 and uc.user_id = {userId}", array("userId" => USER_ID));
		
		if(!$formAllowed) {
			f::setError(401, "Not authorized");
		} 
		
        if(f::hasErrors()) { return; }

		
		// set pagination
		$excel = (f::getParam("excel")==1);
		$rowsPerPage=50;
		$page=max(f::getParam("page"),1);
		$start=($page-1)*$rowsPerPage;
		$previousPage = $page-1;
		$nextPage = $page+1;
		$textFilter = "";
		if(f::getParam("textFilter")) {
			$textFilter = " and user_data like '%".f::dbEscape(f::getParam("textFilter"))."%'";
		}
		// END set pagination
		$order = f::getParam("order")*1;
		$orderDesc = f::getParam("orderDesc")*1;
		$orderBy = ($order == "" ? "" : " ORDER BY $order");
		
		$clientId = f::dbRes("select client_id from fm_forms where id = {p:form_id}");
		$siteTableId = "fm_userdata_".substr("00".$clientId,-3);
		$limit = ($excel?"":" limit $start, $rowsPerPage");

		$sql = "select SQL_CALC_FOUND_ROWS id, date_format(created_date,'%d/%m/%Y %H:%i') as created_date, user_data 
						from {d:siteTableId}
						where form_id = {p:form_id} 
						{n:textFilter}
						order by id desc {d:orderBy} {d:limit}";
		$formData = f::dbFullRes($sql,array("siteTableId"=>$siteTableId, "textFilter"=>$textFilter, "orderBy"=>$orderBy, "limit"=>$limit));
		
		foreach($formData as $k=>$v) {
			/*
			if($formData[$k]["birthdate"]=="0000-00-00") {
				$formData[$k]["birthdate"]="";
			}
			*/
			$formData[$k]["user_data"] = json_decode($formData[$k]["user_data"],true);
		}
		
		
		$totalRows = f::dbRes("SELECT FOUND_ROWS()");
		if($totalRows<= $page*$rowsPerPage) { $nextPage=0; }
		

		$form = f::dbFirstRow("select id, name, enabled_domains, detail 
				from fm_forms 
				where id = {p:form_id}");
		$form["detail"] = json_decode($form["detail"], true);

	
		if(!$excel) {
			f::setResponseJson(array("form" => $form, "data" => $formData, "start" => ($start+1), "end" => min($start+$rowsPerPage,$totalRows), "previousPage"=>$previousPage,"nextPage"=>$nextPage,"page"=>$page,"totalRows"=>$totalRows,"order"=>$order,"orderDesc"=>$orderDesc ));
		} else {
			
			$out="<table><tr><td><b>Id</b></td><td><b>Date</b></td>";
			foreach($form["detail"]["fields"] as $f) {
				$out.="<td><b>".$f["name"]."</b></td>";
			}
			$out.="</tr>";
			foreach($formData as $k=>$row) {
				$data = isset($row["user_data"]) ? $row["user_data"] : array();
				$out.="<tr><td>".$row["id"]."</td><td>".$row["created_date"]."</td>";
				foreach($form["detail"]["fields"] as $f) {
					$value = isset($data[$f["name"]]) ? $data[$f["name"]] : "...";
					$out.="<td>$value</td>";
				}
				$out.="</tr>";
			}			
			$out.="</table>";
			
			$formName = preg_replace("/[^A-Za-z0-9 ]/", '', $form["name"]);
			f::setExcelOutput($form["id"]."_{$formName}_".date("Ymd_His").".xls", $out);
		}
		
	}
}