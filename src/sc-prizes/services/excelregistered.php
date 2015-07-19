<?php
namespace sancor\prizes;

class ExcelregisteredController {

    public static function get() {

		$data = f::dbFullRes("SELECT u.id, u.dni, u.email, u.data, u.created_date as registration_date
							FROM sc_users u 
							ORDER BY u.created_date desc");
							
		$out = "<table>"
			."<thead><tr><td>ID</td><td>Fecha</td><td>DNI</td><td>Email</td>"
			."<td>Nombre</td><td>Domicilio</td><td>Localidad</td><td>Provincia</td><td>C.Postal</td><td>Teléfono</td>"
			."</tr></thead>";
			
		foreach ($data as $d) {
			
			$userData = json_decode($d["data"], true);

			$out .= "<tr>"
					."<td>{$d["id"]}</td>"
					."<td>{$d["registration_date"]}</td>"
					."<td>{$d["dni"]}</td>"
					."<td>{$d["email"]}</td>"
					
					."<td>{$userData["nombre"]}</td>"
					."<td>{$userData["domicilio"]}</td>"
					."<td>{$userData["localidad"]}</td>"
					."<td>{$userData["provincia"]}</td>"
					."<td>{$userData["codigopostal"]}</td>"
					."<td>{$userData["telefono"]}</td>"
					."</tr>";
			
		}
		$out .= "</table>";
		
		f::setExcelOutput("Sancor Registrados - ".date("Y-m-d_H-i-s"). ".xls", $out);
		
	}
}