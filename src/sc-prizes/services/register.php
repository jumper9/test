<?php
namespace sancor\prizes;

class RegisterController {

    public static function post() {

		$allowUserUpdate = true;
	
		f::validateParam("nombre", array("letters", "minlength:5"), "Nombre invalido");
		f::validateParam("email", array("email"), "Email invalido");
		f::validateParam("domicilio", array("address", "minlength:4"), "Domicilio invalido");
		f::validateParam("localidad", array("address", "minlength:4"), "Localidad invalido");
		f::validateParam("provincia", array("integer", "minvalue:1", "maxvalue:24"), "Provincia invalido");
		f::validateParam("codigopostal", array("address", "minlength:4"), "Codigo Postal invalido");
		f::validateParam("telefono", array("integer", "minlength:7", "maxlength:14"), "Telefono invalido");
		f::validateParam("acepto", array("integer", "minvalue:1", "maxvalue:1"), "Debe Aceptar los T&C");
		f::validateParam("captcha", array("captcha"), "Captcha incorrecto");

		if(f::hasErrors()) { return; }

		$userData = array("nombre"=>f::getParam("nombre"), 
							"domicilio"=>f::getParam("domicilio"),
							"localidad"=>f::getParam("localidad"),
							"provincia"=>f::getParam("provincia"),
							"codigopostal"=>f::getParam("codigopostal"),
							"telefono"=>f::getParam("telefono")
							);
		
		$userId = f::dbRes("select id from sc_users where dni = {p:dni} and email = {p:email}");
		if($userId && !$allowUserUpdate) {
			f::setError(400, "Usuario ya existe");
		} else if($userId && $allowUserUpdate) {
			f::dbQuery("update sc_users set data = {data} where id = {userId}", array("data" => $userData, "userId" => $userId));

		} else {	
			// creo el usuario
			$userId = f::dbInsert("insert into sc_users set  data = {data}, dni = {p:dni}, email = {p:email}, created_date = now()", array("data" => $userData));

		}

		f::setResponseJson(array("ok" => true));
		
	}
}
