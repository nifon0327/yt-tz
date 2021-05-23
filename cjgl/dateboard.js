function DateBoard(){
	var dateboard=this;
	this.target;
	this.Value="";
	this.DateNum=7;
	this.ObjectStyle=1;
	this.DateboardPad=null;
	this.onPickFun=null;
	this.onClearFun=null;
	
	this.setup=function(){ //初始化
         dateboard.addDateBoard();
     }
	
	this.addDateBoard=function(){
		 var eachDate;
		 var mousecss=" onMouseOver=this.style.backgroundColor='#55C8FF'  onMouseOut=this.style.backgroundColor='#CCC' ";
		 var licss=" style='height:28px;line-height:28px;' ";
         document.write("<div id='divDateboard' style='position:absolute;width:120px;height:275px;z-index:9999;display:none;background:#CCC;border: 2px solid #D0E9F0;-moz-border-radius:8px;-webkit-border-radius:8px;'>");
		 document.write ("<ul style='padding: 0;list-style:none;text-align:center;'>");
		 for (i=0;i<dateboard.DateNum;i++){
		   eachDate=dateboard.showDate(i);   
		   document.write ("<li onclick=dateboard.selDate('"+eachDate+"') "+ mousecss + licss +">"+eachDate+"</li>");
		}
		document.write ("<li onclick='dateboard.delClear()'  "+ licss + mousecss +">清除日期</li>"); 
	     document.write ("<li onclick='dateboard.hide()'  "+ licss + mousecss +">关&nbsp;&nbsp;闭</li></ul></div>"); 
         dateboard.DateboardPad=document.getElementById("divDateboard");
   }  
   
   this.show=function(targetObject,ObjectStyle,onpickFun,onClearFun){
     if(targetObject==undefined) {
         alert("未设置目标对象. \n方法DateBoard.show(obj 目标对象,目标类型(1-value;2-innerHTML)选择日期后执行自定义函数过程");
         return false;
     }
	 else dateboard.target=targetObject;
	 
	  if(ObjectStyle==2){
		 dateboard.ObjectStyle=2;
	  }
	 else dateboard.ObjectStyle=1;
	
    if(onpickFun==""){
		  dateboard.onPickFun="";
	}
    else dateboard.onPickFun=onpickFun;

    if(onClearFun==""){
		  dateboard.onClearFun="";
	}
    else dateboard.onClearFun=onClearFun;
	
	 dateboard.DateboardPad.style.display="block";
	 dateboard.DateboardPad.style.visibility ="visible";
	 
	//调整位置;
	var offsetPos=dateboard.getAbsolutePos(dateboard.target);
	  if(offsetPos.y-document.body.scrollTop<dateboard.DateboardPad.offsetHeight){
    var calTop=offsetPos.y;
   }
   else{ 
    var calTop=offsetPos.y-dateboard.DateboardPad.offsetHeight+dateboard.target.offsetHeight;
   }
	
   if((document.body.offsetWidth-(offsetPos.x+dateboard.target.offsetWidth-document.body.scrollLeft))> dateboard.DateboardPad.offsetWidth){
    var calLeft=offsetPos.x;
   }
   else{
    var calLeft=offsetPos.x-dateboard.DateboardPad.offsetWidth;
   }
   dateboard.DateboardPad.style.left=calLeft+"px";
   dateboard.DateboardPad.style.top=calTop+"px";
   }
   
  this.showDate=function(n)
    {
      var d = new Date();
      d.setDate(d.getDate()+n);
	  var dm=d.getMonth()+1;
	  if (dm<10) dm="0"+dm;
	  var dd= d.getDate();
	  if (dd<10) dd="0"+dd;
      d = d.getFullYear() + "-" + dm + "-" + dd;
      return d;
   }

  this.hide=function(){
     dateboard.DateboardPad.style.display="none";
     dateboard.DateboardPad.style.visibility ="hidden";
  }
  
  this.delClear=function(){
    dateboard.hide();
	if(dateboard.onClearFun!=""){
		dateboard.onClearFun();
	}	  
  }
 
  this.selDate=function(date){
	if (dateboard.ObjectStyle==2){
		 dateboard.target.innerHTML=date;
	}
	else dateboard.target.value=date;
	
    dateboard.hide();
	if(dateboard.onPickFun!=""){
		dateboard.onPickFun();
	}
  }
  
  this.getAbsolutePos = function(el) { //取得对象的绝对位置
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent) {
    var tmp = dateboard.getAbsolutePos(el.offsetParent);
    r.x += tmp.x;
    r.y += tmp.y;
   }
   return r;
  };
  dateboard.setup();
}