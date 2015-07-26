w.ttypes["api"] = function (t){
	t.constructor = function(t) {
		t.set("status",-1);
	}
	t.runTile = function(t) {
		//w.login.pageState("list");
		$("#"+t.get("destinationDiv")).html("<iframe frameborder=0 style='width:100%;height:99%' src='/doc/index#!/Microsoft'></iframe>");		
	}

}