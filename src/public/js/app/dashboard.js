w.ttypes["dashboard"] = function (t){
	t.constructor = function(t) {
		t.set("status",-1);
	}
	t.runTile = function(t) {
		t.draw();
	}
	t.draw = function(destinationDiv) {
		if(destinationDiv) { t.set("destinationDiv","content"); }
		
		$.get( "/admin/getforms?a=1&_api_key="+w.apiKey, function( d ) {
			t.set("clients",d.clients);
			t.set("forms",d.forms);
			t.set("textFilter","");
			t.draw2();
		});
		
		
	}

	t.draw2 = function(destinationDiv) {

		var clients = t.get("clients");
		var forms = t.get("forms");
		var out = "";
		
		for(var c in clients) {
			out += "<table style='width:600px; margin-bottom:30px' class='listTable'>"
				+ "<caption>Form Data: "+clients[c].name+"</caption>"
				+ "<thead><tr><td style='border:0' colspan=3></td><td style='height:14px;border:0;text-align:center;' colspan=2>Registrations</td></tr>"
					+"<tr><td style='text-align:center'>ID</td><td>Form Name</td><td style='text-align:center'>Status</td><td style='width:80px; text-align:center'>Last 7 Days</td><td style='width:80px; text-align:center'>Total</td><td colspan=3></td></tr></thead>"
					+"<tbody class='data'>";
				
			for(var f in forms) {
				
				if(forms[f].client_id == clients[c].id) {
					var id = forms[f].id;
					out +="<tr onclick='w.dashboard.viewData(\""+id+"\",1,true)'>"
						+ "<td style='width:20px;text-align:center'>" + id + "</td>"
						+ "<td>" + forms[f].name + "</td>"
//						+ "<td>" + forms[f].data_30_days + "</td>"
						+ "<td style='text-align:center'>" + (forms[f].status==0?"<span style='color:#A51026'>Disabled":"<span style='color:#2F7512'>Enabled</span>") + "</td>"
						+ "<td style='width:80px; text-align:center'>" + forms[f].data_7_days + "</td>"
//						+ "<td>" + forms[f].data_yesterday + "</td>"
						+ "<td style='width:80px; text-align:center'>" + forms[f].data_total + "</td>"
//						+ "<td>" + forms[f].data_unanswered + "</td>"
//						+ "<td style='width:90px'><a class='button blue' href='javascript:w.dashboard.viewData(\""+id+"\")'>Edit Form</a></td>"
//						+ "<td style='width:60px'><a class='button blue' href='javascript:'>View</a></td>"
//							+ "<td style='width:60px'><a class='button blue' href='javascript:w.dashboard.viewData(\""+id+"\")'>Export</a></td>"
						+"</tr>";
				}
			}
			out += "</tbody></table>";
		}

		w.showBox(out);
		w.doResize();
		
	}

	t.viewData = function(formId,page,resize) {
		if(!page) { page=1; }
		var textFilter = ($("#dashboard_filter") && $("#dashboard_filter").val()?$("#dashboard_filter").val():"");
		t.set("textFilter",textFilter);
		t.set("formId",formId);
		$.get( "/admin/getdata?textFilter="+textFilter+"&page="+page+"&form_id="+formId+"&_api_key="+w.apiKey, function( d ) {

			t.set("formData",d.data);
			t.set("form",d.form);
			
			t.set("page",d.page);
			t.set("start",d.start);
			t.set("end",d.end);
			t.set("nextPage",d.nextPage);
			t.set("previousPage",d.previousPage);
			t.set("totalRows",d.totalRows);
			t.set("order",d.order);
			t.set("orderDesc",d.orderDesc);

			t.viewData2(resize);

		});
	}
	
	t.viewData2 = function(drawAll) {
		var formId = t.get("formId");
		var formData = t.get("formData");
		var form = t.get("form");
		var fields = form.detail.fields;


		var pagination = "<div style='float:right'>"
			+"<div style='float:left'><input style='width:100px' id='dashboard_filter' value='"+t.get("textFilter")+"'><a class='button blue' href='javascript:w.dashboard.filter()'>Search</a>&nbsp;&nbsp;&nbsp;</div>"
			+"<div style='float:left;margin-top:7px;margin-right:10px;'>"+t.get("start")+" - "+t.get("end")+" of "+t.get("totalRows")+"</div>"
			+"<div style='float:left'>"
			+(t.get("previousPage")>0 ? "<a href='javascript:w.dashboard.viewData("+formId+","+t.get("previousPage")+",false)' class='button blue'  style='padding:0px;'><</a>":"<a style='padding:0px;' class='button'><</a>")+" "
			+(t.get("nextPage")>0 ? "<a href='javascript:w.dashboard.viewData("+formId+","+t.get("nextPage")+",false)' class='button blue' style='padding:0px;'>></a>":"<a style='padding:0px;' class='button'>></a>")+" "
			+"<a class='button blue' href='/admin/getdata?form_id="+formId+"&_api_key="+w.apiKey+"&excel=1'>Export</a>"
			+"</div>"
			
			+"</div>";
		
		out ="<div class='clientTitle'><a href='javascript:w.dashboard.run()'>Dashboard</a> > "+form.name+"</div>"
			+ "<table style='width:98%'>"
			+ "<caption>"+pagination+"</caption>"
			+ "<thead><tr>";
			for(var i in fields) { 
				out +="<td>" + fields[i].name + "</td>";	
			}
		out +="</tr></thead>"
			+"<tbody class='data'>";
		
		var row = null;
		var value = null;
		for(var d in formData) {
			row = (formData[d].user_data?formData[d].user_data:{});
			out += "<tr>";
			for(var i in fields) { 
				value = (row[fields[i].name]?row[fields[i].name]:"...");
				out += "<td style='height:14px'>" + value + "</td>";	
			}
			out += "</tr>";
		
		}
		
		out +="</tbody></table>";
		
		
		if(drawAll) {
			out = "<div id='fullScreenTable' class='formsList card card--big' style='overflow:auto;width:70%;height:90%'>"+out+"</div>";
			w.show(out);
			w.doResize();
		} else {
			$("#fullScreenTable").html(out);
		}

	}
	
	t.filter = function() {
		t.viewData(t.get("formId"));
	}
}