<?php
namespace sancor\prizes;

class ExcelwinnersController {

    public static function get() {
		
		$data = f::dbFullRes("SELECT w.id, date_format(w.created_date,'%d/%m/%Y %H:%i:%s') as win_date,
							u.dni, u.email, u.data, u.created_date as registration_date,
							p.name as prize_name, p.code as prize_code, date_format(p.start_date,'%d/%m/%Y %H:%i:%s') as prize_start_date, date_format(p.end_date,'%d/%m/%Y %H:%i:%s') as prize_end_date
							FROM sc_winners w
							LEFT JOIN sc_prizes p ON (w.prize_id = p.id)
							LEFT JOIN sc_users u ON (w.user_id = u.id)
							ORDER BY w.created_date desc");
							
		$out = "<table>"
			."<thead><tr><td>ID</td><td>Fecha</td><td>DNI</td><td>Email</td>"
			."<td>Nombre</td><td>Domicilio</td><td>Localidad</td><td>Provincia</td><td>C.Postal</td><td>Teléfono</td>"
			."<td>Premio</td><td>Codigo</td><td>Inicio Premio</td><td>Fin Premio</td>"
			."</tr></thead>";

		foreach ($data as $d) {
			$userData = json_decode($d["data"], true);
			
			$out .= "<tr>"
					."<td>{$d["id"]}</td>"
					."<td>{$d["win_date"]}</td>"
					."<td>{$d["dni"]}</td>"
					."<td>{$d["email"]}</td>"
					
					."<td>{$userData["nombre"]}</td>"
					."<td>{$userData["domicilio"]}</td>"
					."<td>{$userData["localidad"]}</td>"
					."<td>{$userData["provincia"]}</td>"
					."<td>{$userData["codigopostal"]}</td>"
					."<td>{$userData["telefono"]}</td>"
					
					."<td>{$d["prize_name"]}</td>"
					."<td>{$d["prize_code"]}</td>"
					."<td>{$d["prize_start_date"]}</td>"
					."<td>{$d["prize_end_date"]}</td>"
					."</tr>";
			
		}
		$out .= "</table>";
			
		f::setExcelOutput("Sancor Ganadores - ".date("Y-m-d_H-i-s") . ".xls", $out);
		
	}
}