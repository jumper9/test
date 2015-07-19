<?php
namespace sancor\prizes;

class RedeemController {

    public static function post() {

	f::validateParam("captcha", array("captcha"), "Captcha incorrecto");
		
		$userId = f::dbRes("select id from sc_users where dni = {p:dni} and email = {p:email}");
		if(!$userId) {
			f::setError(400, "Usuario Inexistente");
		} 
		
		$prizeId = f::dbRes("select id from sc_prizes where code = {p:prizecode} and start_date <= now() and end_date >= now() and actual_stock > 0");
		if(!$prizeId) {
			f::setError(400, "No ganaste");
		}

		if (f::dbRes("select 1 from sc_winners where user_id = {userId} and prize_id = {prizeId}", array("userId" => $userId, "prizeId" => $prizeId))) {
			f::setError(400, "Ya ganaste este premio");
		}
		
		if(f::hasErrors()) { return; }

		// Ganaste!
		f::dbQuery("update sc_prizes set actual_stock = actual_stock - 1 where id = {prizeId}", array("prizeId" => $prizeId));
		
		$winnerCode = f::dbInsert("insert into sc_winners set user_id = {userId}, prize_id = {prizeId}, created_date = now(), status = 1", array("userId" => $userId, "prizeId" => $prizeId));
		
		f::setResponseJson(array("winner" => true, "winnerCode" => $winnerCode));
	
	}
}