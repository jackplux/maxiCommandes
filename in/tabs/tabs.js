window.onload=function() {
	// get tab container
	var container = document.getElementById("tabContainer");
	// set current tab
	var navitem = container.querySelector(".tabs ul li");
	//store which tab we are on
	var ident = navitem.id.split("_")[1];
	//if url #hash
	var tablive = window.location.hash;
	tablive = tablive.replace(/#/,'');//rem #
	if(document.getElementById("tabHeader_" + tablive)){//test if exist id tab
		ident = tablive?tablive:ident;//use hash
		navitem = document.getElementById("tabHeader_" + ident);//change active id
	}//fi url #hash
	navitem.parentNode.setAttribute("data-current",ident);

	navitem.setAttribute("class","tabActiveHeader");//set current tab with class of activetabheader

	var tabs = container.querySelectorAll(".tabs ul li");//this adds click event to tabs
	var pages = container.querySelectorAll(".tabpage");	//hide all tabs contents we don't need
	for (var i = 0; i < pages.length; i++) {
		tabs[i].onclick=displayPage;
		if(pages[i].id!="tabpage_" + ident)
			pages[i].style.display="none";
	}
}
// on click of one of tabs
function displayPage() {
	var current = this.parentNode.getAttribute("data-current");
	//remove class of activetabheader and hide old contents
	document.getElementById("tabHeader_" + current).removeAttribute("class");
	document.getElementById("tabpage_" + current).style.display="none";

	var ident = this.id.split("_")[1];
	//add class of activetabheader to new active tab and show contents
	this.setAttribute("class","tabActiveHeader");
	document.getElementById("tabpage_" + ident).style.display="block";
	this.parentNode.setAttribute("data-current",ident);
	window.location.hash = ident;//tablive with no cookie or session
}