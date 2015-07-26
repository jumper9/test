w.ttypes["security"] = function (t){
	t.constructor = function(t) {
		t.set("status",-1);
	}
	t.runTile = function(t) {
		t.draw();
	}
	t.draw = function(error) {
		w.set("userIsAdmin",false);
		$("#menu-wrapper").show();
		$("#topmenu_clients").hide();	
		$("#topmenu_forms").hide();	
		$("#topmenu_users").hide();	
		$("#topmenu_api").hide();	
		$("#topmenu_dashboard").hide();	

		
		out="<div id='security_form'>"
			+ "<div class='clientTitle'>Wunderforms Login</div>"
			+ "<form action='?' onsubmit='w.security.doLogin();return false;'><table>"
			+"<tr><td>User:</td><td><input id=login_user value='test@email.com'></td></tr>"
			+"<tr><td>Password:</td><td><input type=password id=login_pass value='1234'></td><td style='color:red'>"+(error?"Usuario o clave incorrectos":"")+"</td></tr>"
			+"<tr><td></td><td><input type=submit style='display:none'><a href='javascript:w.security.doLogin();' class='button blue'>Login</a></td></tr>"
			+"</table></form></div>";

		w.showBox(out);		
		w.doResize();

	}
	t.doLogout = function() {
		var apiKey = localStorage.apiKey ? localStorage.apiKey : "";
		localStorage.apiKey = null;
		$.post( "/admin/logout", { "_api_key": apiKey } ).done(function( data ) {
			localStorage.apiKey = data._api_key;
			t.run();

		}).fail(function() {
			t.draw();
			
		});
	}
	t.doLogin = function() {
		var user = $("#login_user").val();
		var pass = $("#login_pass").val();
		
		w.show("");
		w.set("userIsAdmin", false);
		
		$.post( "/admin/login", { "user": user, "pass": pass } ).done(function( data ) {
			if(data._api_key) {
				localStorage.apiKey = data._api_key;
				w.apiKey = data._api_key;
			} 

			$("#menu-wrapper").show();
			$("#top_username").html(data.userName);
			w.set("userIsAdmin",data.isAdmin);
			w.navigateHome();
			
		}).fail(function() {
			t.draw(1);
			
		});
		
	}
	t.loginCheck = function() {
		w.apiKey = localStorage.apiKey ? localStorage.apiKey : "";

		$.get( "/admin/logincheck", { "_api_key": w.apiKey }
		).done(function( data ) {
			$("#top_username").html(data.userName);
			w.set("userIsAdmin",data.isAdmin);
			$("#menu-wrapper").show();
			w.navigateHome();

		}).fail(function() {
			t.run();
			
		});
		
	}
}