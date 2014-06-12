function getAbsolutePos(who) {
	var x=0,y=0;
	while(who.offsetParent!=null&&who.offsetParent.id!="screen"){
		x+=who.offsetLeft;y+=who.offsetTop;who=who.offsetParent;
	}
	x+=who.offsetLeft;y+=who.offsetTop;
	return{x:x,y:y}
}
function getSrcElement(event){return(event.srcElement)?event.srcElement:event.currentTarget;}
function navigate(dest){this.location="view.asp?uid="+dest}
function coSearch(){
	var coSearchText = document.getElementById("coSearchTxt");
	if (coSearchText){
		var searchTerm = coSearchText.value;
		var newLoc = self.location.href;
		newLoc = newLoc.replace(/\&symbol\=[^&]*/,"") + "&symbol=" + searchTerm;
		newLoc = newLoc.replace(/\&pid\=[^&]*/,"");
		self.location = newLoc;
	}
	return false;
}
function initView(){
	var coSearchImg = document.getElementById("coSearchImg");	
	var btnNext = document.getElementById("btnNext");
	var btnPrev = document.getElementById("btnPrev");
	if (coSearchImg){coSearchImg.onclick=coSearch;}
	if (btnNext){btnNext.onclick=goNext;}
	if (btnPrev){btnPrev.onclick=goPrev;}
}
function goNext(){
}
function goPrev(){
}
function chartError(elem){elem.src = "/gif/x.gif";}
addEvent(window, "load", initView);