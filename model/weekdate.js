function WeekDate(){
	var weekdate=this;
	this.target;
	this.Value="";
	this.ObjectStyle=1;
	this.weekdatePad=null;
	this.weeklist=null;
	this.onPickFun=null;
	this.onClearFun=null;
	this.yearValue="2014";
	this.defaultValue="";
	this.Friday="";
	this.selWeek=null;
	this.otherWeekInfo=null;
	this.setup=function(){ //初始化
         weekdate.addWeekDate();
     }

	this.addWeekDate=function(){
		 var mousecss=" onMouseOver=this.style.backgroundColor='#55C8FF'  onMouseOut=this.style.backgroundColor='#FFF' ";
		 var licss=" style='width:40px;height:35px;line-height:35px;margin-right:20px;' ";
         document.write("<div id='divweekdate' style='position:absolute;width: 40%;height:40%;z-index:9999;display:none;background:#FFF;border: 1px solid #D0E9F0;'>");
         document.write("<div id='WeekList' name='WeekList' style='width:95%;'></div>");
         weekdate.weeklist=document.getElementById("WeekList");
         weekdate.drawWeekDate(0);
	     document.write ("<div style='text-align:right;width:100%;margin-top: 10px;'><input id='selWeek' name='selWeek' style='float:left;width:120px;margin:7px 0 0 10px;' readonly><span id='otherWeekInfo' name='otherWeekInfo' style='float:left;margin-top:5px;'></span></div><div style='float:right;margin-right:50px;margin-top:5px;'><span id='clearDweek' class='ButtonH_25' onclick='clearDeliveryWeek()'>交期待定</span><input type='hidden' id ='clearStockId'><span style='color:red'></span></div>");
	     document.write ("<div style='width:100%;float:left;margin-top: 10px;'>&nbsp;&nbsp;&nbsp;更新备注:<input id='updateWeekRemark' name='updateWeekRemark' style='width:200px;' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onclick='weekdate.hide()' style='float: right;margin-right: 50px;' class='ButtonH_25'>关&nbsp;&nbsp;闭</span><span onclick='weekdate.selected()' style='float: right;margin-right: 20px;' class='ButtonH_25'>确&nbsp;&nbsp;定</span></div></div>");
         weekdate.weekdatePad=document.getElementById("divweekdate");
         weekdate.selWeek=document.getElementById("selWeek");
         weekdate.otherWeekInfo=document.getElementById("otherWeekInfo");
         weekdate.updateWeekRemark=document.getElementById("updateWeekRemark");
   }

   this.drawWeekDate=function(year){
         var today=new Date();
         var backimg="";
         if (year==0){
             year=today.getFullYear();
         }else{
	         if (year>today.getFullYear()){
	          backimg="<img style=' float:right; background:transparent url(../model/weekdate.gif) no-repeat scroll -16px 0; width:16px;height:16px;display:block;cursor:pointer;margin-right:1px;' onclick='weekdate.changeYear(-1)'/>";
	          }
         }
         weekdate.yearValue=year;
          var mousecss2=" onMouseOver=this.style.backgroundColor='#81F7F3'  onMouseOut=this.style.backgroundColor='#FFF' ";
          weekdate.weeklist.innerHTML="";
	      var html_str="<div style='width:100%;height:15px;text-align:center;valign:middle;font-size:14px;margin-top:8px;'>"+year+"<img style=' float:right; background:transparent url(../model/weekdate.gif) no-repeat scroll -32px 0; width:16px;height:16px;display:block;cursor:pointer;' onclick='weekdate.changeYear(1)'/>"+backimg+"</div>";
		  html_str+="<table style='width:400px;height:210px;margin:5px 0px 0px 10px;background-color:#DDD;' cellspacing='1' >";
		 var maxws=weekdate.getYearWeeks(year);
		 var sdate=weekdate.getFirstWeekDate(year);
		 var ws,dstr,tdbgColor,edate,dstr,tColor;
		 var on_click="";
		 for (i=0;i<7;i++){
		     html_str+="<tr>";
		     for (j=1;j<=8;j++){
		           ws=i*8+j;
		           if (ws>maxws){
			            ws="&nbsp;";
			            dstr=""; tdbgColor="#FFF";on_click="";

		           }
		           else{
			           edate=weekdate.getEndDate(sdate,6);

			           dstr=sdate.substr(5, 5)+"~"+edate.substr(5, 5);
			           var bgArray=weekdate.getDatebgColor(sdate);
			           tdbgColor=bgArray[1];
			           tColor=bgArray[0]>0?"#000":"#AAA";
			           if (ws<10) ws="0"+ws;
			           if (bgArray[0]>0)   on_click="onclick='weekdate.weekselected(this,"+year+ws+")' ";else on_click="";
			           if (bgArray[0]==2) on_click+=mousecss2;
			           if (bgArray[0]==1) weekdate.defaultValue=year+""+ws;
		           }
		           html_str+="<td style='text-align:center;background-color:"+tdbgColor+"'"+on_click+"><span style='font-size:12px;font-weight:bold;color:"+tColor+"'>"+ws+"<span><div style='font-size:8px;color:#AAA;'>"+dstr+"</div></td>";
		           sdate=weekdate.getEndDate(edate,1);
		     }
		     html_str+="</tr>";
		}
		     html_str+="</table>";
		     weekdate.weeklist.innerHTML=html_str;
   }

   this.addOtherWeekInfo=function(htmlStr){
	     weekdate.otherWeekInfo.innerHTML=htmlStr;
   }

   this.show=function(targetObject,ObjectStyle,onpickFun,onClearFun){
     if(targetObject==undefined) {
         alert("未设置目标对象. \n方法weekdate.show(obj 目标对象,目标类型(1-value;2-innerHTML)选择日期后执行自定义函数过程");
         return false;
     }
	 else weekdate.target=targetObject;

	  if(ObjectStyle==2){
		 weekdate.ObjectStyle=2;
	  }
	 else weekdate.ObjectStyle=1;

    if(onpickFun==""){
		  weekdate.onPickFun="";
	}
    else weekdate.onPickFun=onpickFun;

    if(onClearFun==""){
		  weekdate.onClearFun="";
	}
    else weekdate.onClearFun=onClearFun;

	 weekdate.Value="";
	 weekdate.selWeek.value="";
	 weekdate.weekdatePad.style.display="block";
	 weekdate.weekdatePad.style.visibility ="visible";

	//调整位置;
	var offsetPos=weekdate.getAbsolutePos(weekdate.target);
	  if(offsetPos.y-document.body.scrollTop<weekdate.weekdatePad.offsetHeight){
    var calTop=offsetPos.y+weekdate.target.offsetHeight;
   }
   else{
    var calTop=offsetPos.y-weekdate.weekdatePad.offsetHeight;
   }

   if((document.body.offsetWidth-(offsetPos.x+weekdate.target.offsetWidth-document.body.scrollLeft))> weekdate.weekdatePad.offsetWidth){
    var calLeft=offsetPos.x;
   }
   else{
    var calLeft=offsetPos.x-weekdate.weekdatePad.offsetWidth;
   }
   weekdate.weekdatePad.style.left=calLeft+"px";
   weekdate.weekdatePad.style.top=calTop+"px";
   }

  this.hide=function(){
     weekdate.weekdatePad.style.display="none";
     weekdate.weekdatePad.style.visibility ="hidden";
  }

  this.delClear=function(){
    weekdate.Value="";
    weekdate.hide();
	if(weekdate.onClearFun!=""){
		weekdate.onClearFun();
	}
  }

  this.selected=function(){
   weekdate.Value=weekdate.selWeek.value;
    weekdate.hide();
	if(weekdate.onPickFun!="" && weekdate.Value>0){
		weekdate.onPickFun();
	}
  }

 this.weekselected=function(el,yweek){
        weekdate.selWeek.value=yweek;
 }

this.getYearWeeks=function(year){
	 var date_str=year+"-12-31";
	 var lastdate=new Date(Date.parse(date_str.replace(/-/g,   "/")));
	 if (lastdate.getDay()>3){
		  return 53;
	 }
	 else{
		  return 52;
	 }
}

this.getWedday=function(delimiter){
    return weekdate.getOneday(5,delimiter);
}

this.getFriday=function(delimiter){
      return weekdate.getOneday(3,delimiter);
}

this.getOneday=function(dimday,delimiter){
    var ws=weekdate.Value.toString();
    var year=ws.substr(0, 4);
    var step=ws.substr(4, 2);
    var date_str=year+"-01-01";
	var firstdate=new Date(Date.parse(date_str.replace(/-/g,   "/")));
	 if (firstdate.getDay()<=4){
	      firstdate.setDate(firstdate.getDate() - (firstdate.getDay()-1));
	 }
	 else{
		 firstdate.setDate(firstdate.getDate() + (7-firstdate.getDay()+1));
	 }
      firstdate.setDate(firstdate.getDate() +step*7-dimday);
     var sm=firstdate.getMonth()+1;
	  var sd=firstdate.getDate();
	  if (sm<10) sm="0"+sm;
	  if (sd<10) sd="0"+sd;
	  if (delimiter==null || delimiter==undefined) delimiter="-";
	  return firstdate.getFullYear()+delimiter+sm+delimiter+sd;
}

this.getFirstWeekDate=function(year){
	var date_str=year+"-01-01";
	var firstdate=new Date(Date.parse(date_str.replace(/-/g,   "/")));
	 if (firstdate.getDay()<=4){
	      firstdate.setDate(firstdate.getDate() - (firstdate.getDay()-1));
	 }
	 else{
		 firstdate.setDate(firstdate.getDate() + (7-firstdate.getDay()+1));
	 }
	 var sm=firstdate.getMonth()+1;
	 var sd=firstdate.getDate();
	 if (sm<10) sm="0"+sm;
	 if (sd<10) sd="0"+sd;
	 return firstdate.getFullYear()+"/"+sm+"/"+sd;
}

this.getEndDate=function(dstr,step){
	var edate=new Date(Date.parse(dstr.replace(/-/g,   "/")));
	edate.setDate(edate.getDate() +step);
	var sm=edate.getMonth()+1;
	 var sd=edate.getDate();
	 if (sm<10) sm="0"+sm;
	 if (sd<10) sd="0"+sd;
	 return edate.getFullYear()+"/"+sm+"/"+sd;
}

this.getDatebgColor=function(dstr){
	var today=new Date();
	today=today.getTime();
	var sdate=new Date(Date.parse(dstr.replace(/-/g,   "/")));
	 sdate=sdate.getTime();
	var edate=new Date(Date.parse(dstr.replace(/-/g,   "/")));
	edate.setDate(edate.getDate() +7);
	edate=edate.getTime();
	//限制只能选15周
	var edate10=new Date();
	edate10.setDate(edate10.getDate() +7*16);
	edate10=edate10.getTime();

	if (today>edate || edate>edate10){
		return new Array(0,"#FFF");
	}
   else{
	   if (today>=sdate && today<=edate){
		   return new Array(1,"#81F7F3");
	   }
	   else{
		   return new Array(2,"#FFF");
	   }
   }
}

this.changeYear=function(y){
     y=parseInt(weekdate.yearValue) +y;
      weekdate.drawWeekDate(y);
}

this.getAbsolutePos = function(el) { //取得对象的绝对位置
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent) {
    var tmp = weekdate.getAbsolutePos(el.offsetParent);
    r.x += tmp.x;
    r.y += tmp.y;
   }
   return r;
  };
  weekdate.setup();
}