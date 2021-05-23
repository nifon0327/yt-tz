function checkSignBoard(){
	var checkSignboard=this;
	this.target;
	this.Value="";
	this.ObjectStyle=1;
	this.checkSignboardPad=null;
	this.onPickFun=null;
	this.onClearFun=null;
	
	this.setup=function(){ //初始化
         checkSignboard.addcheckSignboard();
     }
	
	this.addcheckSignboard=function(){
		 var echoId=new Array(0,1);
		 var echoStr=new Array('抽检','全检');
		 var mousecss=" onMouseOver=this.style.backgroundColor='#55C8FF'  onMouseOut=this.style.backgroundColor='#CCC' ";
		 var licss=" style='height:35px;line-height:35px;' ";
         document.write("<div id='divcheckSignboard' style='position:absolute;width:80px;height:120px;z-index:9999;display:none;background:#EEE;border: 2px solid #D0E9F0;-moz-border-radius:8px;-webkit-border-radius:8px;'>");
		 document.write ("<ul style='padding: 0;list-style:none;text-align:center;'>");
		 for (i=0;i<echoId.length;i++){ 
		   document.write ("<li onclick=checkSignboard.selected('"+echoId[i]+"') "+ mousecss + licss +">"+echoStr[i]+"</li>");
		}
	     document.write ("<li onclick='checkSignboard.hide()'  "+ licss + mousecss +">关&nbsp;&nbsp;闭</li></ul></div>"); 
         checkSignboard.checkSignboardPad=document.getElementById("divcheckSignboard");
   }  
   
   this.show=function(targetObject,ObjectStyle,onpickFun,onClearFun){
     if(targetObject==undefined) {
         alert("未设置目标对象. \n方法checkSignboard.show(obj 目标对象,目标类型(1-value;2-innerHTML)选择日期后执行自定义函数过程");
         return false;
     }
	 else checkSignboard.target=targetObject;
	 
	  if(ObjectStyle==2){
		 checkSignboard.ObjectStyle=2;
	  }
	 else checkSignboard.ObjectStyle=1;
	
    if(onpickFun==""){
		  checkSignboard.onPickFun="";
	}
    else checkSignboard.onPickFun=onpickFun;

    if(onClearFun==""){
		  checkSignboard.onClearFun="";
	}
    else checkSignboard.onClearFun=onClearFun;
	
	 checkSignboard.Value="";
	 checkSignboard.checkSignboardPad.style.display="block";
	 checkSignboard.checkSignboardPad.style.visibility ="visible";
	 
	//调整位置;
	var offsetPos=checkSignboard.getAbsolutePos(checkSignboard.target);
	  if(offsetPos.y-document.body.scrollTop<checkSignboard.checkSignboardPad.offsetHeight){
    var calTop=offsetPos.y;
   }
   else{ 
    var calTop=offsetPos.y-checkSignboard.checkSignboardPad.offsetHeight+checkSignboard.target.offsetHeight;
   }
	
   if((document.body.offsetWidth-(offsetPos.x+checkSignboard.target.offsetWidth-document.body.scrollLeft))> checkSignboard.checkSignboardPad.offsetWidth){
    var calLeft=offsetPos.x;
   }
   else{
    var calLeft=offsetPos.x-checkSignboard.checkSignboardPad.offsetWidth;
   }
   checkSignboard.checkSignboardPad.style.left=calLeft+"px";
   checkSignboard.checkSignboardPad.style.top=calTop+"px";
   }
   
  this.hide=function(){
     checkSignboard.checkSignboardPad.style.display="none";
     checkSignboard.checkSignboardPad.style.visibility ="hidden";
  }
  
  this.delClear=function(){
    checkSignboard.hide();
	if(checkSignboard.onClearFun!=""){
		checkSignboard.onClearFun();
	}	  
  }
 
  this.selected=function(id){
	/*if (checkSignboard.ObjectStyle==2){
		 checkSignboard.target.innerHTML=date;
	}
	else checkSignboard.target.value=date;
	*/
	checkSignboard.Value=id;
    checkSignboard.hide();
	if(checkSignboard.onPickFun!=""){
		checkSignboard.onPickFun();
	}
  }
  
  this.getAbsolutePos = function(el) { //取得对象的绝对位置
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent) {
    var tmp = checkSignboard.getAbsolutePos(el.offsetParent);
    r.x += tmp.x;
    r.y += tmp.y;
   }
   return r;
  };
  checkSignboard.setup();
}