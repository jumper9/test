window.w = {

	load : function () {

		w.addTile("dashboard");
		w.addTile("security");
		w.addTile("users");
		w.addTile("clients");
		w.addTile("forms");
		w.addTile("api");
/*
		w.addTile("cms");
		
		w.addTile("api");
		w.addTile("deploys");
		w.addTile("qaZone");
		w.addTile("history");
		
		w.addTile("eec");		
		w.addTile("upg");
		w.addTile("upv");
		w.addTile("cards");
		w.addTile("reports");
		w.addTile("admin");
*/
		w.doResize();
		w.security.loginCheck();
	}
}
w.ttypes = {};
w.tiles = [];


w.addTile = function(tileName) {
	w.tiles[w.tiles.length]=new w.tile(tileName);
}
w.tile = function (type,destinationDiv,synonym) {
	id=w.tiles.length+1;
	if(!w.ttypes[type]) { console.log("ERROR - Incorrect TileType: "+type+" - id:"+id); return ; } 
	if(synonym) { w[synonym]=this; } else { w[type]=this; } 
	this.values={};
	if(w.ttypes[type]) w.ttypes[type](this);
	this.type=type;
	this.id=id;
	this.set = function(name,value) { this.values[name]=value; return this; };
	this.get = function(name) { return (this.values[name]!=null?this.values[name]:null); }
	this.login = function() { return this.draw(); }

	this.set( "destinationDiv", destinationDiv ? destinationDiv : w.config.defaultDestinationDiv ); 
	if(this.constructor) this.constructor(this);
	this.run = function() { $("#modal").hide();this.runTile(this); }
};

w.config = {
	defaultDestinationDiv : "content"
}

$(window).resize(function(){ w.doResize();});
w.values={};
w.set = function(name,value) { w.values[name]=value; };
w.get = function(name) { return (w.values[name]!=null?w.values[name]:null); }

w.doResize = function() { 
	$("#main").height($( window ).height()-70);
	$("#content").height($( window ).height()-70);
	$("#detail").height($( window ).height()-70);

	$("#cardsContent").height($( window ).height()-80);
	$("#cardsContent").width($( window ).width()-50);
	$("#cardsContentDetail").height($( window ).height()-96);

	
	$("#fullScreenTable").height($( window ).height()-120);
	$("#fullScreenTable").width($( window ).width()-60);

	
}
w.showNoAnimate = function (out,div) {
	if(!div) { div = w.config.defaultDestinationDiv; }
	$("#"+div).hide().html(out).show();		
}
w.showBox = function (out,out2) {
	var width = $( window ).width()-60;
	var height = $( window ).height()-120;
	w.show("<div id='fullScreenTable' class='formsList card card--big' style='overflow:auto;width:"+width+"px;height:"+height+"px'>"+out+"</div>"+out2);
	//w.doResize();
}
w.show = function (out,div) {
	if(!div) { div = w.config.defaultDestinationDiv; }
	
	$("#"+div).html(out);		
	$("#dimScreen").show();
	$("#"+div).parent().css("overflow","hidden")
	$("#"+div).hide().fadeIn({easing: "easeOutCirc", queue: false, duration:300});
	$("#"+div).css("margin-top","5px").animate({marginTop: "0px"}, 300, "easeOutCirc", function() { $("#dimScreen").hide();$("#"+div).parent().css("overflow","hidden")}  );
}

w.navigateHome = function() {

	var module="";
	if((" "+document.location.pathname).indexOf("/index")) {
		module="index";
	}
	w.gotoPage(module+document.location.search);
	
}

w.getQueryParams = function (qs) {
    qs = qs.split('+').join(' ');
    var params = {},
        tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;
    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
    }
    return params;
}

window.onpopstate = function(e){
	w.gotoPage(e.state,true);
};
w.pageState = function(obj,url) {
	//if(!url) url=obj;
	//window.history.pushState(obj, "Title", "#"+url);
}
w.gotoPage = function (url,doNotPushState) {
	if(!doNotPushState) {
		window.history.pushState(url, "Title", url);
	}
	
	var module = "";
	if(url && url.indexOf("?")>0) { module=url.substr(0, url.indexOf('?')); } else { module=url; }
	var queryParams = url.substr(url.indexOf('?')+1,255); 
	var params = w.getQueryParams(queryParams);

	if(module == "index") {
		if(params.admin) {
			if(params.admin=="users") {
				w.selectMenu("users");
				w.users.run();
			} else if(params.admin=="forms") {
				w.selectMenu("forms");
				w.forms.run();
			} else if(params.admin=="clients") {
				w.selectMenu("clients");
				w.clients.run();
			} else if(params.admin=="api") {
				w.selectMenu("api");
				w.api.run();
			}
		} else {
			w.selectMenu("dashboard");
			w.dashboard.run();
		}
	} else {
		w.selectMenu("dashboard");
		w.dashboard.run();

	}
	
}

$('.noKeyPress').keypress(function(e){ e.preventDefault();return false;  });
$(document).keydown( function(e){  
	if( e.which == 8 && w.hasClass(document.activeElement,'noKeyPress')){   
		e.preventDefault();  
		return false;   
	} 
});
w.hasClass = function (element, cls) {
	return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
}

w.htmlEncode = function (value){
  //create a in-memory div, set it's inner text(which jQuery automatically encodes)
  //then grab the encoded contents back out.  The div never exists on the page.
  return $('<div/>').text(value).html();
}

w.htmlDecode = function (value){
  return $('<div/>').html(value).text();
}

w.selectMenu = function (menu) {
	if(!w.get("userIsAdmin")) { 
		$("#topmenu_clients").hide();	
		$("#topmenu_forms").hide();	
		$("#topmenu_users").hide();	
		$("#topmenu_api").hide();	
	} else {
		$("#menu-wrapper li").show(); 
	}
	$("#topmenu_dashboard").show();	
	$("#menu-wrapper li").removeClass("selected");
	$("#topmenu_"+menu).addClass("selected");	
}