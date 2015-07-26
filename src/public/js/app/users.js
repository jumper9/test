w.ttypes["users"] = function (t){
	t.constructor = function(t) { }

	t.runTile = function(t) {
		$.ajax({
			url: "/admin/users",
			type: 'GET',
			data: { "_api_key": w.apiKey },
			success: function(d) {
				t.set("clients",d.clients);
				t.set("users",d.data);
				t.draw();
			},
			fail: function(d) {
				t.set("clients",null);
				t.set("users",null);
				t.draw();
			}
		});	

	}
	
	t.changePwd = function() {


		var out = "<div style='width:450px;height:200px;position:absolute;margin:0 auto;background-color:white'><div style='position:relative;top:30px;'>"
				+ "<div class='clientTitle'>Wunderforms password change</div>"
				+"<table style='width:400px'>"
			+ "<tbody>"
			+"<tr><td height=30>Current Password:</td><td colspan=2><input type=password id='users_password_actual' value=''></td></tr>"
			+"<tr><td height=30>New Password:</td><td colspan=2><input type=password id='users_password_new' value=''></td></tr>"
			+"<tr><td height=30>Repeat New Password:</td><td colspan=2><input type=password id='users_password_new2' value=''></td></tr>"
			+ "<tr><td  colspan=2></td><td colspan=2 style='text-align:right'>"
					+"<a href='javascript:w.users.changePwdSave()' class='button green'>Ok, change my password</a></td></tr>"
			+"</tbody></table></div></div>";
		
		w.showBox(out);
		w.doResize();
	}
	t.changePwdSave = function(userId) {
		var passwordActual = $("#users_password_actual").val();
		var passwordNew = $("#users_password_new").val();
		var passwordNew2 = $("#users_password_new2").val();
		$.ajax({
			url: "/admin/users/changepwd",
			type: 'POST',
			data: { "passwordActual":passwordActual, "passwordNew":passwordNew, "passwordNew2":passwordNew2, "_api_key": w.apiKey } })
			.success(function(result) { alert("Password has been changed"); w.dashboard.run();	})
			.fail(function(result) { alert("Incorrect Current Password"); });

	
	}
	t.editUser = function(userId) {

		var readonly="readonly='true' disabled='true'";
		if(userId==0) {
			user=Array();
			user.name="";
			user.email="";
			user.status="0";
			readonly="";
		} else {
			var data = t.get("users");
			var user = null;
			for (var i in data) {
				user = data[i];
				if(user.id==userId) {
					break;
				}
			}			
		}
		
		var out = "<table class='listTable' style='margin:0 0 0 20px'>"
				+ "<caption style='height:30px'>"+(userId>0?"Change":"Add")+" User</caption>"
			+ "<tbody class='data'>"
			+"<tr><td nowrap height=30>User Name:</td><td colspan=2><input "+readonly+" id='users_name' value='"+user.name+"'></td></tr>"
			+"<tr><td height=30>Email:</td><td colspan=2><input id='users_email' value='"+user.email+"'></td></tr>"
			+"<tr><td height=30>Status:</td><td colspan=2>"+t.selectStatus(user.status)+"</td></tr>"
			+"<tr><td valign=top>Clients:</td><td colspan=2>"+t.selectClients(user.clients)+"</td></tr>"

			+"<tr><td height=30>Password:</td><td colspan=2><input id='users_password1' value=''></td></tr>"
			+"<tr><td height=30>Repeat Password:</td><td colspan=2><input id='users_password2' value=''></td></tr>"
			+ "<tr><td  colspan=10>"
					+"<div style='float:left'><a href='javascript:w.users.run()' class='button brownish'>Cancel & go back</a></div>"
					+"<div style='float:right'><a href='javascript:w.users.save("+userId+")' class='button green'>"+(userId>0?"Save Changes":"Create User")+"</a></div>"
					+"</td></tr>"
			+"</tbody></table></div></div>";
		
		w.showBox(out);
	}
	
	t.save = function(userId) {
		var name = $("#users_name").val();
		var email = $("#users_email").val();
		var status = $("#users_status").val();
		var password1 = $("#users_password1").val();
		var password2 = $("#users_password2").val();
		
		var clients = t.get("clients");
		var userClients = {};
		for(var i in clients) {
			if($("#users_client_"+clients[i].id).is(':checked')) {
				userClients[clients[i].id] = true;
			}
		}
		
		if(userId>0) {
			$.ajax({
				url: "/admin/users/edit",
				type: 'POST',
				data: { "userId":userId, "email": email, "status": status, "password1": password1, "password2": password2, "_api_key": w.apiKey, "userClients": userClients } })	
				.success(function(result) {
					alert("User data has been saved.");
					w.users.run();
				})
				.error(function(xhr, status, error) {
					var txt = JSON.parse(xhr.responseText);
					var errorTxt="Could not save user data. Please try again.";
					if(txt.errors && txt.errors[0] && txt.errors[0].message) {
						errorTxt=txt.errors[0].message;
					}
					alert(errorTxt);
				});				
			
			
		} else {	
			$.ajax({
				url: "/admin/users/add",
				type: 'POST',
				data: { "name": name, "email": email, "status": status, "password1": password1, "password2": password2, "_api_key": w.apiKey, "userClients": userClients }
				})
				.success(function(result) {
					alert("User has been added.");
					w.users.run();
				})
				.error(function(xhr, status, error) {
					var txt = JSON.parse(xhr.responseText);
					var errorTxt="Could not save user data. Please try again.";
					if(txt.errors && txt.errors[0] && txt.errors[0].message) {
						errorTxt=txt.errors[0].message;
					}
					alert(errorTxt);
				});				
				
		}
	}
	
	t.selectClients = function(selectedClients) {

		var sc = Array();
		for(var i in selectedClients) {
			sc[selectedClients[i].clientId] = true;
		}
		var clients = t.get("clients");
		var out = "";
		for(var i in clients) {
			out += "<input style='width:20px' type='checkbox' id = 'users_client_"+clients[i].id+"' value = '1' "+(sc[clients[i].id]?"checked":"")+"> "+clients[i].name + (clients[i].status==0?" (Disabled)":"")+"<br>";
		}
		return out;
	}
	
	t.selectStatus = function(selectedStatus) {
		var out = "<select id='users_status'>"
			+ "<option value='0' "+(selectedStatus==0?"selected":"")+">Disabled</option>"
			+ "<option value='1' "+(selectedStatus==1?"selected":"")+">Enabled</option>";
		out += "</select>";
		return out;
	}
	t.draw = function(destinationDiv) {
		if(destinationDiv) { t.set("destinationDiv",destinationDiv); }
	
		var out = "<table class='listTable' style='margin:0 0 0 20px'>"
				+ "<caption style='height:30px'>Users</caption>"
			+ "<thead><tr>"
				+"<td style='width:200px' nowrap>User Name</td>"
				+"<td style='width:200px' nowrap>Email</td>"
				+"<td style='width:100px;text-align:center'>Status</td>"
			  +"</tr></thead>"
			+ "<tbody class='data'>";
		
		window.scrollTo(0, 0);
		users = t.get("users");
		for (var u in users) {
			user = users[u];
			
			out += "<tr onclick='w.users.editUser("+user.id+")'>"
				+"<td nowrap>"+user.name +(user.is_admin==1?" (Administrator)":"")+"</td>"
				+"<td nowrap>"+user.email +"</td>"
				+"<td nowrap style='text-align:center'>"+(user.status==0?"<span style='color:#A51026'>Disabled":"<span style='color:#2F7512'>Enabled</span>") +"</td>"
				+"</tr>"
				;
		}
		out += "<tbody></table>";
	
		var addButton ="<div style='position:fixed;right:45px; bottom:45px;'><div class='btn-round' onclick='w.users.editUser(0)'><div style='color:white;margin:10px 0 0 18px'>+</div></div></div>";

		w.showBox(out, addButton);
	
		return this;
	}

	
	
}