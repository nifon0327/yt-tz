var menuTimer =null;
function showmenu(obj1,obj2,state,location,addTop,addLeft){ 
    var btn=document.getElementById(obj1);
    var obj=document.getElementById(obj2);
    var h=btn.offsetHeight;
    var w=btn.offsetWidth;
    var x=btn.offsetLeft;
    var y=btn.offsetTop;
   obj.onmouseover =function(){
        showmenu(obj1,obj2,'show',location,addTop,addLeft);
    }
    obj.onmouseout =function(){
        showmenu(obj1,obj2,'hide',location,addTop,addLeft);
    }
  
    while(btn=btn.offsetParent){y+=btn.offsetTop;x+=btn.offsetLeft;}
    
    var hh=obj.offsetHeight;
    var ww=obj.offsetWidth;
    var xx=obj.offsetLeft;//style.left;
    var yy=obj.offsetTop;//style.top;
    var obj2state=state.toLowerCase();
    var obj2location=location.toLowerCase();
    
    var showx,showy;

    if(obj2location=="left" || obj2location=="l" || obj2location=="top" || obj2location=="t" || obj2location=="u" || obj2location=="b" || obj2location=="r" || obj2location=="up" || obj2location=="right" || obj2location=="bottom"){
        if(obj2location=="left" || obj2location=="l"){showx=x-ww;showy=y;}
        if(obj2location=="top" || obj2location=="t" || obj2location=="u"){showx=x;showy=y-hh;}
        if(obj2location=="right" || obj2location=="r"){showx=x+w;showy=y;}
        if(obj2location=="bottom" || obj2location=="b"){showx=x;showy=y+h;}
    }else{ 
        showx=xx;showy=yy;
    }
	showx=showx+addLeft;
	showy=showy+addTop;
    obj.style.left=showx+"px";
	obj.style.top=showy+"px";
    if(state =="hide"){
        menuTimer =setTimeout("hiddenmenu('"+ obj2 +"')", 500);
    }else{
        clearTimeout(menuTimer);
        obj.style.visibility ="visible";
    }
}
function hiddenmenu(psObjId){
    document.getElementById(psObjId).style.visibility ="hidden";
} 