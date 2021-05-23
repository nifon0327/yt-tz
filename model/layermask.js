function showwin(objs,From){
	var selects=document.getElementsByTagName("SELECT"); 
	for(var i=0;i<selects.length;i++){ 
		selects[i].disabled =true; 
		}
	ControlScroll();
	if(From!="supplier"){
		parent.topFrame.showwin();
		parent.bottomFrame.showwin();
		parent.rightFrame.showwin();
		}
	document.getElementById("shadow").style.display="block";//遮罩
	if(objs==1){
		document.getElementById("detailText").style.display="block"; //提示框
		}
	else{
		document.getElementById("detailRemark").style.display="block"; //提示框
		}
	}

function recover(Action,objs,From){
	var selects=document.getElementsByTagName("SELECT"); 
	for(var i=0;i<selects.length;i++){ 
		selects[i].disabled=false; 
		}
	ControlScroll();
	if(From!="supplier"){
		parent.topFrame.recover();
		parent.bottomFrame.recover();
		parent.rightFrame.recover();
		}
	document.getElementById("shadow").style.display="none";//遮罩
	if(objs==1){
		document.getElementById("detailText").style.display="none"; //提示框
		}
	else{
		document.getElementById("detailRemark").style.display="none"; //提示框
		}
	if(Action==0){
		for (var i=0;i<form1.elements.length;i++){
			var e=form1.elements[i];
			var NameTemp=e.name;
			var Name=NameTemp.search("checkid") ;//防止有其它参数用到checkbox，所以要过滤
			if (e.type=="checkbox" && Name!=-1){
				e.disabled=true;
				} 
			}
		if(objs==1){
			document.form1.maskText.value="";
		}
		else{
			document.form1.maskRemark.value="";
			}	
		}
	}
function ControlScroll(){
	if(document.body.style.overflow=="hidden"){
		document.body.style.overflow="scroll";
		}
	else{
		document.body.style.overflow="hidden";
		}
	}
