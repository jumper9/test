w.ttypes["forms"] = function (t){
	t.constructor = function(t) { }
	t.runTile = function(t) {
		$.ajax({
			url: "/admin/forms",
			type: 'GET',
			data: { "_api_key": w.apiKey },
			success: function(d) {
				t.set("clients",d.clients);
				t.set("forms",d.data);
				t.draw();
			},
			fail: function(d) {
				t.set("clients",null);
				t.set("forms",null);
				t.draw();
			}
		});	

	}
	
	t.editForm = function(formId) {

		var readonly="readonly='true' disabled='true'";
		var example = "";
		if(formId==0) {
			form=Array();
			form.name="";
			form.client_id="";
			form.detail = '';
			example = 'Example:<br>{ "captcha": true, "fields": [ '+"\n"
							+'{ "name": "name", "type": "letters", "minlength": 1, "maxlength": 255 },'+"\n"
							+' { "name": "document", "type": "string", "minlength": 1, "maxlength": 20 },'+"\n"
							+' { "name": "address", "type": "string", "minlength": 1, "maxlength": 255 },'+"\n"
							+' { "name": "address2", "type": "string", "minlength": 0, "maxlength": 255 },'+"\n"
							+' { "name": "city", "type": "string", "minlength": 1, "maxlength": 255 },'+"\n"
							+' { "name": "state", "type": "string", "minlength": 1, "maxlength": 255 },'+"\n"
							+' { "name": "zip", "type": "string", "minlength": 1, "maxlength": 255 },'+"\n"
							+' { "name": "country", "type": "letters", "minlength": 1, "maxlength": 255 },'+"\n"
							+' { "name": "phone", "type": "integer", "maxlength": 255 },'+"\n"
							+' { "name": "phone2", "type": "integer", "maxlength": 255 },'+"\n"
							+' { "name": "email", "type": "email" },'+"\n"
							+' { "name": "birthdate", "type": "date" }'+"\n"
							+' ]'+"\n"
							+'}';
			form.available_from="";
			form.available_to="";
			form.status="0";
			readonly="";
		} else {
			var data = t.get("forms");
			var form = null;
			for (var i in data) {
				form = data[i];
				if(form.id==formId) {
					break;
				}
			}			
		}
			
		var captcha = (form.detail && form.detail.captcha) ? "1" : "0";
		var fields = (form.detail && form.detail.fields) ? form.detail.fields : [];
		
		var out = "<table class='editTable' style='width:600px'>"
				+ "<caption style='height:30px'>"+(formId>0?"Change":"Add")+" Form</caption>"
			+ "<tbody class='data'>"
			+"<tr><td nowrap height=30>Client:</td><td colspan=2>" + t.selectClient(form.client_id, (form.id>0)) + "</td></tr>"
			+"<tr><td nowrap height=30>Form Name:</td><td colspan=2><input style='width:300px' id='forms_name' value='"+form.name+"'></td></tr>"
			+"<tr><td height=30>Status:</td><td colspan=2>"+t.selectStatus(form.status)+"</td></tr>"
			+"<tr><td height=30>Captcha:</td><td colspan=2>"+t.selectCaptcha(captcha)+"</td></tr>"
//			+"<tr><td nowrap height=30>Available From:</td><td colspan=2><input id='forms_available_from' value='"+form.available_from+"'></td></tr>"
//			+"<tr><td nowrap height=30>Available To:</td><td colspan=2><input id='forms_available_to' value='"+form.available_to+"'></td></tr>"

			+"<tr><td colspan=3 style='border-bottom:0'>"+t.detailField(fields)+"</td></tr>"
			+ "<tr><td  colspan=10 style='text-align:right; border-bottom:0'>"
					+"<div style='float:left'><a href='javascript:w.forms.run()' class='button brownish'>Cancel & go back</a></div>"
					+"<div style='float:right'><a href='javascript:w.forms.save("+formId+")' class='button green'>"+(formId>0?"Save Changes":"Create Form")+"</a></div>"
					+"</td></tr>"
			+"</tbody></table>";
		
		
		w.showBox(out);	
	}
	
	t.detailField = function (fields) {

		out="";
		out += "<table style='width:800px' cellpadding=2 cellspacing=0 class='simple'>"
		+"<tr><td style='text-align:center;border-bottom:0;padding:0'>Fields: Name</td>"
			+"<td style='text-align:center;border-bottom:0;padding:0'>Field Type</td>"
			+"<td style='text-align:center;border-bottom:0;padding:0'>MinLengh</td>"
			+"<td style='text-align:center;border-bottom:0;padding:0'>MaxLength</td>"
			+"<td style='text-align:center;border-bottom:0;padding:0'>MinValue</td>"
			+"<td style='text-align:center;border-bottom:0;padding:0'>MaxValue</td></tr>";

		var maxResponse=1;
		for(var i=1;i<=30;i++) {
			if(fields[i-1] && fields[i-1].name) {
				maxResponse = i;
			}
		}
		t.set("maxResponse",maxResponse);

		for(var i=1;i<=30;i++) {
			var field = (fields[i-1]?fields[i-1]:{} );
			var name = (field.name?field.name:"");
			var type = (field.type?field.type:"");
			var minlength = (field.minlength?field.minlength:"");
			var maxlength = (field.maxlength?field.maxlength:"");
			var minvalue = (field.minvalue?field.minvalue:"");
			var maxvalue = (field.maxvalue?field.maxvalue:"");
	
		out +="<tbody id='forms_"+i+"_div' style='display:"+(i<=maxResponse?"":"none")+"'><tr>"
				+"<td style='text-align:center' valign=top><input value='"+name+"' id='forms_"+i+"_name' style='width:180px'></td>"
				+"<td style='text-align:center' valign=top>"+t.selectFieldType("forms_"+i+"_type",type)+"</td>"
				+"<td style='text-align:center' valign=top><input value='"+minlength+"' id='forms_"+i+"_minlength' style='width:50px' type=number></td>"
				+"<td style='text-align:center' valign=top><input value='"+maxlength+"' id='forms_"+i+"_maxlength' style='width:50px' type=number></td>"
				+"<td style='text-align:center' valign=top><input value='"+minvalue+"' id='forms_"+i+"_minvalue' style='width:50px' type=number></td>"
				+"<td style='text-align:center' valign=top><input value='"+maxvalue+"' id='forms_"+i+"_maxvalue' style='width:50px' type=number></td>"
				+"</tr></tbody>"
				;
		}
		out += "<tr><td style='padding-left:35px; border-bottom:0'><a style='width:10px;display:"+(maxResponse<30?"":"none")+"' id='"+name+"_add' class='button blue' href='javascript:w.forms.addField(\""+name+"\")'>[+]</a></td></tr>";
		out += "</table>";

		return out;
	
	}

	t.addField = function(name) {
		var maxResponse = t.get("maxResponse");
		maxResponse++;
		t.set("maxResponse",maxResponse);
		
		if(maxResponse==10) {
			$("#"+name+"_add").hide();
		}
		
		$("#forms_"+maxResponse+"_div").fadeIn();
	}

	t.getFormDetail = function() {
		var fields = [];
		for(var i=1;i<=30;i++) {
			var name = $("#forms_"+i+"_name").val();
			var type = $("#forms_"+i+"_type").val();
			var minlength = $("#forms_"+i+"_minlength").val();
			var maxlength = $("#forms_"+i+"_maxlength").val();
			var minvalue = $("#forms_"+i+"_minvalue").val();
			var maxvalue = $("#forms_"+i+"_maxvalue").val();
			
			if(name!="" && type!="") {
				fields[fields.length] = { "name": name, "type": type, "minlength":minlength, "maxlength":maxlength, "minvalue":minvalue, "maxvalue":maxvalue};
			}
		}	
		detail = {"captcha" : ($("#forms_captcha").val()==1), "fields" : fields };
		
		return JSON.stringify(detail);
	}
	
	t.save = function(formId) {
		var name = $("#forms_name").val();
		var clientId = $("#forms_client_id").val();
		var status = $("#forms_status").val();
		var detail = t.getFormDetail();
		var availableFrom = $("#forms_available_from").val();
		var availableTo = $("#forms_available_to").val();
		
		
		if(formId>0) {
			$.ajax({
				url: "/admin/forms/edit",
				type: 'POST',
				data: { "form_id":formId, "client_id": clientId, "detail":detail, "name": name, "status": status, "available_from": availableFrom, "available_to": availableTo, "_api_key": w.apiKey } })	
				.success(function(result) {
					alert("Form data has been saved.");
					w.forms.run();
				})
				.error(function(xhr, status, error) {
					var txt = JSON.parse(xhr.responseText);
					var errorTxt="Could not save form data. Please try again.";
					if(txt.errors && txt.errors[0] && txt.errors[0].message) {
						errorTxt=txt.errors[0].message;
					}
					alert(errorTxt);
				});				
			
			
		} else {	
			$.ajax({
				url: "/admin/forms/edit",
				type: 'POST',
				data: {"client_id": clientId, "detail":detail, "name": name, "status": status, "available_from": availableFrom, "available_to": availableTo, "_api_key": w.apiKey }
				})
				.success(function(result) {
					alert("Form has been added.");
					w.forms.run();
				})
				.error(function(xhr, status, error) {
					var txt = JSON.parse(xhr.responseText);
					var errorTxt="Could not save form data. Please try again.";
					if(txt.errors && txt.errors[0] && txt.errors[0].message) {
						errorTxt=txt.errors[0].message;
					}
					alert(errorTxt);
				});				
				
		}
	}
	
	t.selectClient = function(selectedClient, disabled) {
		var clients = t.get("clients");
		var out = "<select style='width:300px' id='forms_client_id' "+(disabled?"disabled":"")+">";
		for(var i in clients) {
			out += "<option value='"+clients[i].id+"' "+(selectedClient==clients[i].id?"selected":"")+">"+clients[i].name + (clients[i].status==0?" (Disabled)":"")+"</option>"
		}
		out += "</select>";
		return out;
	}

	t.selectCaptcha = function(captcha) {
		var out = "<select style='width:100px' id='forms_captcha'>"
			+ "<option value='0'>No</option>"
			+ "<option value='1' "+(captcha=="1"?"selected":"")+">Yes</option>";
		out += "</select>";
		return out;
	}


	t.selectStatus = function(selectedStatus) {
		var out = "<select style='width:100px' id='forms_status'>"
			+ "<option value='0' "+(selectedStatus==0?"selected":"")+">Disabled</option>"
			+ "<option value='1' "+(selectedStatus==1?"selected":"")+">Enabled</option>"
			+ (selectedStatus!=1?"<option value='2' "+(selectedStatus==2?"selected":"")+">Deleted</option>":"");
		out += "</select>";
		return out;
	}
	
	t.selectFieldType = function(fieldName,type) {
		var out = "<select style='width:180px' id='"+fieldName+"'>"
			+ "<option value='' "+(type==""?"selected":"")+">(no field)</option>"
			+ "<option value='string' "+(type=="string"?"selected":"")+">Address (any string)</option>"
			+ "<option value='letters' "+(type=="letters"?"selected":"")+">Name (letters only)</option>"
			+ "<option value='integer' "+(type=="integer"?"selected":"")+">Integer Number</option>"
			+ "<option value='number' "+(type=="integer"?"selected":"")+">Any number</option>"
			+ "<option value='email' "+(type=="email"?"selected":"")+">Email</option>"
			+ "<option value='date' "+(type=="date"?"selected":"")+">Date</option>"
		out += "</select>";
		return out;
	}

	t.draw = function(destinationDiv) {
		if(destinationDiv) { t.set("destinationDiv",destinationDiv); }
	
	
		var out = "<table class='listTable' style='margin:0 0 0 20px'>"
				+ "<caption style='height:30px'>Form Designer</caption>"
			+ "<thead><tr>"
				+"<td style='text-align:left;width:80px' nowrap>Path</td>"
				+"<td style='width:200px; ' nowrap>Client</td>"
				+"<td style='width:200px' nowrap>Form Name</td>"
//				+"<td style='width:120px' nowrap>Available From</td>"
//				+"<td style='width:120px' nowrap>Available To</td>"
				+"<td style='width:140px;text-align:center'>Status</td>"
			  +"</tr></thead>"
			+ "<tbody class='data'>";
		
		window.scrollTo(0, 0);
		forms = t.get("forms");
		for (var f in forms) {
			form = forms[f];
			
			out += "<tr onclick='w.forms.editForm("+form.id+")'>"
				+"<td nowrap style='text-align:left;'>POST: "+form.path+"</td>"
				+"<td nowrap>"+form.client_name+"</td>"
				+"<td nowrap>"+form.name+"</td>"
//				+"<td nowrap>"+form.available_from+"</td>"
//				+"<td nowrap>"+form.available_to+"</td>"
				+"<td nowrap style='text-align:center'>"+(form.status==0?"<span style='color:#A51026'>Disabled":"<span style='color:#2F7512'>Enabled</span>") +"</td>"
				+"</tr>"
				;
		}
		out += "<tbody></table>";
		var addButton ="<div style='position:fixed;right:45px; bottom:45px;'><div class='btn-round' onclick='w.forms.editForm(0)'><div style='color:white;margin:10px 0 0 18px'>+</div></div></div>";

		w.showBox(out, addButton);
	}

	
	
}