var menuskin=1
var display_url=0
var showmenuFlag=0 //new
var showIpad=1;
function showmenuie5(event){
    if (showIpad==0) showmenuFlag=2; 
        else showmenuFlag=1; //new
	event = event ? event : (window.event ? window.event : null);  
	var menu = $("ie5menu");
	var Color = $("ColorSide");
	menu.style.display="block"; 
	menu.style.visibility ="visible";
    var rightedge=document.body.clientWidth-event.clientX;
	var bottomedge=document.body.clientHeight-event.clientY;
	if(rightedge<menu.offsetWidth){
		menu.style.left=document.body.scrollLeft+event.clientX-menu.offsetWidth;
		}
	else{
		menu.style.left=document.body.scrollLeft+event.clientX;
		}
	if(bottomedge<menu.offsetHeight){
		menu.style.top=document.body.scrollTop+event.clientY-menu.offsetHeight;
		}
	else{
		menu.style.top=document.body.scrollTop+event.clientY;               
	    }
	Color.style.height=menu.offsetHeight;
	   return false;
	}
                
function hidemenuie5(){
	var menu = $("ie5menu"); 
		menu.style.display="none";
		menu.style.visibility ="hidden";
		showmenuFlag=0; //new
	}
                
function myover(obj){
	obj.className = "itemshovor";
	}

function myout(obj){
	obj.className = "menuitems";
	}

function ToPrint(ChooseMonth){
	alert(ChooseMonth);
	}
	
function $(objName){

	if(document.getElementById){
		return document.getElementById(objName );
		}
	else 
		if(document.layers){
       		return eval("document.layers['" + objName +"']");
			}
    	else{
			return eval('document.all.' + objName);
			}
}	

if(menuskin==0)
	ie5menu.className="skin0";
else
	ie5menu.className="skin1";
if(document.all){
	showIpad=0;
    document.oncontextmenu=showmenuie5;
    }
else{
    document.oncontextmenu= function (event){showIpad=0;return showmenuie5(event); }
    } 
   document.body.onclick=function(){  //new
       if(showmenuFlag==1) showmenuFlag=2; 
	   else if(showmenuFlag==2){hidemenuie5();}
	   
	} 
