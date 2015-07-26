<?php
namespace wunderman\wunderforms;

header('Content-Type: application/json; charset=utf-8');

class ListController {
        
    public static function get() {
    
		$forms=f::dbFullRes("select f.id, f.client_id, f.name, f.detail, f.description, date_format(f.available_from,'%d/%m/%Y') as available_from, date_format(f.available_to,'%d/%m/%Y') as available_to, f.status, c.name client_name 
							from fm_forms f 
							left join fm_clients c on (c.id=f.client_id)
							where (c.status=1 or c.status=0)
							order by client_id, status desc, name");
		
		$fieldTypes["letters"]="string";
		$fieldTypes["email"]="string";
		$fieldTypes["integer"]="integer";
		$fieldTypes["number"]="long";
		$fieldTypes["string"]="string";

		foreach ($forms as $f) {

			$detail = json_decode($f["detail"],true);

			$parameters=array();
			if(isset($detail["captcha"]) && $detail["captcha"]) {
				$parameters[] = array("in"=>"formData", "name"=>"captcha", "type"=>"integer", "description"=>"Call captcha service or get a form definition for the capthca image.<br>Use number \"9999\" for test purposes.");
			}
			$error400 = "";
			
			if(isset($detail["fields"]) && is_array($detail["fields"])) {
				foreach($detail["fields"] as $field) {
					$p["in"] = "formData";
					if(isset($field["type"]) && isset($field["name"])) {
						$p["name"] = (isset($field["name"]) ? $field["name"] : "FIELD NAME IS MISSING!");
						if(isset($fieldTypes[$field["type"]])) {
							$p["type"] = $fieldTypes[$field["type"]];
						} else {
							$p["type"] = "string";
						}
						$description = "";
						$error400Field = false;
						if(isset($field["minlength"]) && $field["minlength"]>0) { $description.=" MinLength: ".$field["minlength"]; $error400Field = true; }
						if(isset($field["maxlength"]) && $field["maxlength"]>0) { $description.=" MaxLength: ".$field["maxlength"]; $error400Field = true; }
						if(isset($field["minvalue"]) && $field["minvalue"]>0) { $description.=" MinValue: ".$field["minvalue"]; $error400Field = true; }
						if(isset($field["maxvalue"]) && $field["maxvalue"]>0) { $description.=" MaxValue: ".$field["maxvalue"]; $error400Field = true; }
						if($p["type"]!="string") { $error400Field = true; }
						
						$p["description"] = $description;
						if($error400Field) {
							$error400 .= ($error400?"<br>":"")."Invalid ".$p["name"];
						}
						$parameters[] = $p;
					}
				}
			}
			
			$responses["200"] = array("description"=>"ok");
			if($error400) {
				$responses["400"] = array("description"=>$error400);
			}
			$form["post"] = array("tags"=>array($f["client_name"]), "summary"=> $f["name"].($f["status"]==0?" (Disabled)":""), "description" => $f["description"], "responses"=>$responses, "parameters"=>$parameters);

			$paths["/form/".$f["client_id"]."/".$f["id"]] = $form;
		}
		echo '
{
    "swagger": "2.0",
    "info": {
        "description": "Wunderforms API",
        "version": "1.0.0",
        "title": "Wunderforms API",
        "termsOfService": ""
    },
    "host": "",
    "basePath": "",
    "schemes": [
        "http"
    ],
    "paths": 
        ';
	echo json_encode($paths, JSON_UNESCAPED_UNICODE);

	echo '}';

    }    
    
    
}
