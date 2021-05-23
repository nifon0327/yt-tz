function TasksBoard(){
	var tasksboard=this;
	this.target;
	this.Value="";
	this.ObjectStyle=1;
	this.tasksboardPad=null;
	this.onPickFun=null;
	this.onClearFun=null;
	
	this.setup=function(){ //初始化
         tasksboard.addTasksBoard();
     }
	
	this.addTasksBoard=function(){
		 var echoId=new Array(1,2,4);
		 var echoStr=new Array('背卡条码','PE袋标签','白盒/坑盒');
		 var mousecss=" onMouseOver=this.style.backgroundColor='#55C8FF'  onMouseOut=this.style.backgroundColor='#CCC' ";
		 var licss=" style='height:35px;line-height:35px;' ";
         document.write("<div id='divTasksBoard' style='position:absolute;width:120px;height:160px;z-index:9999;display:none;background:#CCC;border: 2px solid #D0E9F0;-moz-border-radius:8px;-webkit-border-radius:8px;'>");
		 document.write ("<ul style='padding: 0;list-style:none;text-align:center;'>");
		 
		 for (i=0;i<echoId.length;i++){ 
		   document.write ("<li onclick=tasksboard.selected('"+echoId[i]+"') "+ mousecss + licss +">"+echoStr[i]+"</li>");
		}
	     document.write ("<li onclick='tasksboard.hide()'  "+ licss + mousecss +">关&nbsp;&nbsp;闭</li></ul></div>"); 
         tasksboard.tasksboardPad=document.getElementById("divTasksBoard");
   }  
   
   this.show=function(targetObject,ObjectStyle,onpickFun,onClearFun){
     if(targetObject==undefined) {
         alert("未设置目标对象. \n方法TasksBoard.show(obj 目标对象,目标类型(1-value;2-innerHTML)选择日期后执行自定义函数过程");
         return false;
     }
	 else tasksboard.target=targetObject;
	 
	  if(ObjectStyle==2){
		 tasksboard.ObjectStyle=2;
	  }
	 else tasksboard.ObjectStyle=1;
	
    if(onpickFun==""){
		  tasksboard.onPickFun="";
	}
    else tasksboard.onPickFun=onpickFun;

    if(onClearFun==""){
		  tasksboard.onClearFun="";
	}
    else tasksboard.onClearFun=onClearFun;
	
	 tasksboard.Value="";
	 tasksboard.tasksboardPad.style.display="block";
	 tasksboard.tasksboardPad.style.visibility ="visible";
	 
	//调整位置;
	var offsetPos=tasksboard.getAbsolutePos(tasksboard.target);
	  if(offsetPos.y-document.body.scrollTop<tasksboard.tasksboardPad.offsetHeight){
    var calTop=offsetPos.y;
   }
   else{ 
    var calTop=offsetPos.y-tasksboard.tasksboardPad.offsetHeight+tasksboard.target.offsetHeight;
   }
	
   if((document.body.offsetWidth-(offsetPos.x+tasksboard.target.offsetWidth-document.body.scrollLeft))> tasksboard.tasksboardPad.offsetWidth){
    var calLeft=offsetPos.x;
   }
   else{
    var calLeft=offsetPos.x-tasksboard.tasksboardPad.offsetWidth;
   }
   tasksboard.tasksboardPad.style.left=calLeft+"px";
   tasksboard.tasksboardPad.style.top=calTop+"px";
   }
   
  this.hide=function(){
     tasksboard.tasksboardPad.style.display="none";
     tasksboard.tasksboardPad.style.visibility ="hidden";
  }
  
  this.delClear=function(){
    tasksboard.hide();
	if(tasksboard.onClearFun!=""){
		tasksboard.onClearFun();
	}	  
  }
 
  this.selected=function(id){
	/*if (tasksboard.ObjectStyle==2){
		 tasksboard.target.innerHTML=date;
	}
	else tasksboard.target.value=date;
	*/
	tasksboard.Value=id;
    tasksboard.hide();
	if(tasksboard.onPickFun!=""){
		tasksboard.onPickFun();
	}
  }
  
  this.getAbsolutePos = function(el) { //取得对象的绝对位置
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent) {
    var tmp = tasksboard.getAbsolutePos(el.offsetParent);
    r.x += tmp.x;
    r.y += tmp.y;
   }
   return r;
  };
  tasksboard.setup();
}