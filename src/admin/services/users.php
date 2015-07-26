<?php 
namespace wunderman\wunderforms;

class UsersController {
	public static function changepwd() {
		if(!security::isLogged()) { return; }
		$passwordActual = trim(f::getParam("passwordActual"));
		$passwordNew = trim(f::getParam("passwordNew"));
		$passwordNew2 = trim(f::getParam("passwordNew2"));
		
		if($passwordNew!=$passwordNew2 or !$passwordNew) {
			f::setError(400,"Invalid Password");
		} else {
			// valido userId actual
			$token=f::getParam("_api_key");
			$userIp=$_SERVER["REMOTE_ADDR"];
			$userId=f::dbRes("select user_id from fm_sessions where user_ip = {userIp} and token = {token} and status=1", array("userIp"=>$userIp, "token"=>$token));
			
			// valido pwd actual
			$pwdOk=f::dbRes("select 1 from fm_users where id = {userId} and (password = {passwordActual} or password=md5( {passwordActual} )) and status=1", array("userId"=>$userId, "passwordActual"=>$passwordActual));
			if(!$pwdOk) {
				f::setError(400,"Clave actual o usuario invalido");
			} else {
				f::dbQuery("update fm_users set password='".md5($passwordNew)."' where id = {$userId}",array("userId", $userId));
			}
		}
	}
	public static function get() {
		
		if(!security::isLogged() || !USER_IS_ADMIN) { return; }
		$users=f::dbFullRes("select id, name, email, status, is_admin from fm_users order by status desc, name");
		foreach ($users as $k=>$u) {
			
			$users[$k]["clients"] = array();
			$clients = f::dbFullRes("select client_id from fm_users_clients where user_id = {userId}", array("userId"=>$users[$k]["id"]));
			foreach ($clients as $c) {
				$users[$k]["clients"][] = array("clientId" => $c["client_id"]);
			}
		}
		
		$clients = f::dbFullRes("select id, name, status from fm_clients order by status desc, name");
		
		f::setResponseJson(array("data"=>$users, "clients"=>$clients));
	}
	
	public static function edit() {

		if(!security::isLogged() || !USER_IS_ADMIN) { return; }
		$userId = f::getParam("userId");
		$status = f::getParam("status");
		$email = f::getParam("email");
		$password1 = trim(f::getParam("password1"));
		$password2 = trim(f::getParam("password2"));
		
		if($status!=1 and $status!=0) {
			f::setError(400, "Invalid Status");
		}
		if($password1 and $password1<>$password2) {
			f::setError(400, "Invalid Password");
		}
		$sqlPassword="";
		if($password1 and $password1==$password2) {
			$sqlPassword=", password = {pwd1} ";
		}
		
		if(!f::hasErrors()) {
			$roles=self::getSaveRoles();
			f::dbQuery("update fm_users set email= {email}, status= {status} $sqlPassword where id = {p:userId}", array("email"=>$email, "status"=>$status, "pwd1"=> md5($password1)) );

			$userClients = f::getParam("userClients");
			
			f::dbQuery("delete from fm_users_clients where user_id = {p:userId}");
			foreach ($userClients as $clientId => $value) {
				f::dbQuery("insert into fm_users_clients set user_id = {userId}, client_id = {clientId}", array("userId"=>$userId, "clientId"=>$clientId));
			}

			f::setResponseJson(array("ok"=>1));
		}
	}
	private static function getSaveRoles() {
		$roles="";
		if(f::getParam("role_admin")) { $roles.=($roles?",":"")."admin"; }
		if(f::getParam("role_content")) { $roles.=($roles?",":"")."content"; }
		if(f::getParam("role_eec")) { $roles.=($roles?",":"")."eec"; }
		if(f::getParam("role_eec_admin")) { $roles.=($roles?",":"")."eec-admin"; }
		return $roles;
		
	}
	public static function add() {

		if(!security::isLogged() || !USER_IS_ADMIN) { return; }
		$status = f::getParam("status");
		$name = f::getParam("name");
		$email = f::getParam("email");
		$password1 = trim(f::getParam("password1"));
		$password2 = trim(f::getParam("password2"));
		
		$exists=f::dbRes("select 1 from fm_users where name = {name}",array("name"=>$name));
		if(!$email) {
			f::setError(400, "Email field is missing");
		} else if(!$name) {
			f::setError(400, "Name field is missing");
		} else if($exists) {
			f::setError(400, "Failed, user already exists.");
		}
		if($status!=1 and $status!=0) {
			f::setError(400, "Incorrect Status");
		}
		$sqlPassword="";
		if($password1 and $password1<>$password2) {
			f::setError(400, "Incorrect Password");
		} else if($password1 and $password1==$password2) {
			$sqlPassword=", password = {pwd} ";
		}
		$roles=self::getSaveRoles();
		if(!f::hasErrors()) {
			$userId = f::dbInsert("insert into fm_users set email = {email}, name = {name} , status= {status} $sqlPassword ",array("pwd" => md5($password1), "email"=> $email, "name" => $name, "status" => $status ));

			$userClients = f::getParam("userClients");
			f::dbQuery("delete from fm_users_clients where user_id = {userId}");
			foreach ($userClients as $clientId => $value) {
				f::dbQuery("insert into fm_users_clients set user_id = {userId}, client_id = {clientId}", array("userId"=>$userId, "clientId"=>$clientId));
			}
			
			
			f::setResponseJson(array("userId"=>$userId));
		}

	}
		
	
}