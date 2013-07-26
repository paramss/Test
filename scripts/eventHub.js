/*
 *         Event-Hub javascript engine,
 *         developed by Matteo Bicocchi on JQuery framework
 *        © 2002-2012 Woertz.
 */


function showMessage(msg){
  var msgBox=$("<div>").addClass("msgBox");
  $("body").append(msgBox);
  msgBox.append(msg);
  setTimeout(function(){msgBox.fadeOut(500,function(){msgBox.remove();})},3000)
}



//COOKIES
jQuery.fn.mb_setCookie = function(name,value,days) {
	var id=$(this).attr("id");
	if(!id) id="";
	if (!days) days=7;
	var date = new Date(), expires;
	date.setTime(date.getTime()+(days*24*60*60*1000));
	expires = "; expires="+date.toGMTString();
	document.cookie = name+"_"+id+"="+value+expires+"; path=/";
};

jQuery.fn.mb_getCookie = function(name) {
	var id=$(this).attr("id");
	if(!id) id="";
	var nameEQ = name+"_"+id + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
};

jQuery.fn.mb_removeCookie = function(name) {
	$(this).mb_setCookie(name,"",-1);
};

$(function(){
	$("#mainMenu").buildMenu({
		menuWidth:200,
		openOnRight:false,
		hasImages:false,
		fadeInTime:100,
		fadeOutTime:300,
		menuTop:0,
		menuLeft:0,
		submenuTop:10,
		submenuLeft:15,
		minZindex:"auto",
		opacity:.95,
		shadow:false,
		hoverIntent:100,
		openOnClick:false,
		closeOnMouseOut:false,
		closeAfter:1000,
		submenuHoverIntent:200
	});

	$(document).bind("scroll",function(){
		$(document).removeMbMenu(null,false);
	})
});



