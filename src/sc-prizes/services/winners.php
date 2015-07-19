<?php
namespace sancor\prizes;

class WinnersController {

    public static function get() {
		
		$data = f::dbFullRes("SELECT w.id, date_format(w.created_date,'%d/%m/%Y %H:%i%s') as win_date,
							u.dni, u.email, u.data as user_data, u.created_date as registration_date,
							p.name as prize_name, p.code as prize_code, date_format(p.start_date,'%d/%m/%Y %H:%i%s') as prize_start_date, date_format(p.end_date,'%d/%m/%Y %H:%i%s') as prize_end_date
							FROM sc_winners w
							LEFT JOIN sc_prizes p ON (w.prize_id = p.id)
							LEFT JOIN sc_users u ON (w.user_id = u.id)
							ORDER BY w.created_date desc");
	
		foreach($data as $k=>$v) {
			$data[$k]["user_data"] = json_decode($data[$k]["user_data"], true);
		}	
		
		f::setResponseJson(array("data" => $data));
		
	}
}
