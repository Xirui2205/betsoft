$(document).ready(function() {
	$("#navmenu > li > a").not(":first").find("+ ul").hide();
	$("#navmenu > li > a > img").attr({'src':'_img/plus.png'}); // add an indicator to the menu items to show there is a child menu
	$("#navmenu > li > a").each(function() {
		toggleMenu(this);
		checkCookie(this);
	});
	$("#navmenu > li > ul > li > a").each(function() {
		toggleMenu(this);
		checkCookie(this);
	});

	function checkCookie(id) {
		var cookieName = id.id;
		var c = readCookie(cookieName); 
		if(c === 'show') {
			$(id).each(function() {
				$(this).children("img").attr({'src':'_img/minus.png'});
				$(this).find("+ ul").show();
			});

		}
	}

	function toggleMenu(id) {
		$(id).click(function() {
			togglePlusMinus(this);
			$(this).find("+ ul").slideToggle("fast");
		});
	}

	function togglePlusMinus(id) {
		$(id).each(function() {
			if($(this).find("+ ul").is(':visible')) {
				$(this).children("img").attr({'src':'_img/plus.png'});
				eraseCookie(this.id);
			} else {
				$(this).children("img").attr({'src':'_img/minus.png'});
				createCookie(this.id, 'show', 365);
			}

		});
	}
 
});
 
// cookie functions http://www.quirksmode.org/js/cookies.html
 
function createCookie(name,value,days)
	{
		if (days)
		{
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	}
function readCookie(name)
	{
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++)
		{
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
function eraseCookie(name)
	{
		createCookie(name,"",-1);
	}

//LAEGACY CODE..
	var navHover = function() {
  var lis = document.getElementById("navmenu").getElementsByTagName("LI");
  for (var i=0; i<lis.length; i++) {
	lis[i].onmouseover=function() {
	  this.className+=" iehover";
	}
	lis[i].onmouseout=function() {
	  this.className=this.className.replace(new RegExp(" iehover\\b"), "");
	}
  }
}
if (window.attachEvent) window.attachEvent("onload", navHover);
