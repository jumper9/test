w.ttypes["clients"] = function (t){
	t.constructor = function(t) { }

	t.runTile = function(t) {
		$.ajax({
			url: "/admin/clients",
			type: 'GET',
			data: { "_api_key": w.apiKey },
			success: function(d) {
				t.set("clients",d.data);
				t.draw();
			},
			fail: function(d) {
				t.set("clients",null);
				t.draw();
			}
		});	

	}
	
	t.changePwdSave = function(clientId) {
		var passwordActual = $("#clients_password_actual").val();
		var passwordNew = $("#clients_password_new").val();
		var passwordNew2 = $("#clients_password_new2").val();
		$.ajax({
			url: "/admin/clients/changepwd",
			type: 'POST',
			data: { "passwordActual":passwordActual, "passwordNew":passwordNew, "passwordNew2":passwordNew2, "_api_key": w.apiKey } })
			.success(function(result) { alert("Password has been changed"); w.dashboard.run();	})
			.fail(function(result) { alert("Incorrect Current Password"); });

	
	}

	t.editClient = function(clientId) {

		var readonly="readonly='true' disabled='true'";
		if(clientId==0) {
			client=Array();
			client.name="";
			client.status="0";
			readonly="";
		} else {
			var data = t.get("clients");
			var client = null;
			for (var i in data) {
				client = data[i];
				if(client.id==clientId) {
					break;
				}
			}			
		}
		
		var out = "<table class='listTable' style='margin:0 0 0 20px'>"
				+ "<caption style='height:30px'>"+(clientId>0?"Change":"Add")+" client</caption>"
			+ "<tbody class='data'>"
			+"<tr><td nowrap height=30>Client Id:</td><td colspan=2>"+client.id+"</td></tr>"
			+"<tr><td nowrap height=30>Client Name:</td><td colspan=2><input "+readonly+" id='clients_name' value='"+client.name+"'></td></tr>"
			+"<tr><td height=30>Status:</td><td colspan=2>"+t.selectStatus(client.status)+"</td></tr>"

			+ "<tr><td  colspan=10>"
					+"<div style='float:left'><a href='javascript:w.clients.run()' class='button brownish'>Cancel & go back</a></div>"
					+"<div style='float:right'><a href='javascript:w.clients.save("+clientId+")' class='button green'>"+(clientId>0?"Save Changes":"Create Client")+"</a></div>"
					+"</td></tr>"
			+"</tbody></table></div></div>";
		
		w.showBox(out);
	}
	
	t.save = function(clientId) {
		var name = $("#clients_name").val();
		var status = $("#clients_status").val();
		
		
		if(clientId>0) {
			$.ajax({
				url: "/admin/clients/save",
				type: 'POST',
				data: { "client_id":clientId, "status": status, "_api_key": w.apiKey } })	
				.success(function(result) {
					alert("Client data has been saved.");
					w.clients.run();
				})
				.error(function(xhr, status, error) {
					var txt = JSON.parse(xhr.responseText);
					var errorTxt="Could not save client data. Please try again.";
					if(txt.errors && txt.errors[0] && txt.errors[0].message) {
						errorTxt=txt.errors[0].message;
					}
					alert(errorTxt);
				});				
			
			
		} else {	
			$.ajax({
				url: "/admin/clients/save",
				type: 'POST',
				data: { "name": name, "status": status, "_api_key": w.apiKey }
				})
				.success(function(result) {
					alert("Client has been added.");
					w.clients.run();
				})
				.error(function(xhr, status, error) {
					var txt = JSON.parse(xhr.responseText);
					var errorTxt="Could not save client data. Please try again.";
					if(txt.errors && txt.errors[0] && txt.errors[0].message) {
						errorTxt=txt.errors[0].message;
					}
					alert(errorTxt);
				});				
				
		}
	}
	
	t.selectStatus = function(selectedStatus) {
		var out = "<select id='clients_status'>"
			+ "<option value='0' "+(selectedStatus==0?"selected":"")+">Disabled</option>"
			+ "<option value='1' "+(selectedStatus==1?"selected":"")+">Enabled</option>";
		out += "</select>";
		return out;
	}

	t.draw = function(destinationDiv) {
		if(destinationDiv) { t.set("destinationDiv",destinationDiv); }
	
		var out = "<table class='listTable' style='margin:0 0 0 20px'>"
				+ "<caption style='height:30px'>Clients</caption>"
			+ "<thead><tr>"
				+"<td style='width:100px;text-align:center' nowrap>Client Id</td>"
				+"<td style='width:200px' nowrap>Client Name</td>"
				+"<td style='width:100px;text-align:center'>Status</td>"
			  +"</tr></thead>"
			+ "<tbody class='data'>";
		
		window.scrollTo(0, 0);
		clients = t.get("clients");
		for (var u in clients) {
			client = clients[u];
			
			out += "<tr onclick='w.clients.editClient("+client.id+")'>"
				+"<td style='text-align:center'>"+client.id +"</td>"
				+"<td nowrap>"+client.name +"</td>"
				+"<td nowrap style='text-align:center'>"+(client.status==0?"<span style='color:#A51026'>Disabled":"<span style='color:#2F7512'>Enabled</span>") +"</td>"
				+"</tr>"
				;
		}
		out += "<tbody></table>";
	
		var addButton ="<div style='position:fixed;right:45px; bottom:45px;'><div class='btn-round' onclick='w.clients.editClient(0)'><div style='color:white;margin:10px 0 0 18px'>+</div></div></div>";

		w.showBox(out, addButton);
	
		return this;
	}

	
	
}