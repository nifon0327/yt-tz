//在线用户检查 暂时禁用(20180518 by xfy)
// function toOnline(){ //如果已提示一次则不再提示
// 	var exitInfo=0;
// 	var url="../online_count.php";
// 　	var ajax=InitAjax();
// 　	ajax.open("GET",url,true);
// 	ajax.onreadystatechange =function(){
// 	　　if(ajax.readyState==4 && ajax.status ==200){
// 			var BackData=ajax.responseText;
// 			var DataArray=BackData.split("`");
// 			/*
// 			if (DataArray[2]!=0){//踢出		//
// 				if(DataArray[2]==1){//被踢出
// 					alert("系统更新或网络掉线等原因!你的帐号将退出！如有问题请跟管理员反映!");
// 					}
// 				else{//重复登录
// 					alert("你的帐号在 "+DataArray[2]+" 重新登录!当前窗口将退出！");
// 					}
// 				parent.location.href="";
// 				exitInfo=1;
// 				}
// 				*/
// 			}
// 		}
// 　	//发送空
// 　	ajax.send(null);
// 	if(exitInfo==0){
// 		setTimeout( "toOnline() ",10000);
// 		}
// 	}

//更新生管备注
function InputRemark(RowId,Id){
		divShadow.innerHTML="";
	var url="item1_sgRemark.php?Id="+Id;
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	document.getElementById("divShadow").innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
	//定位对话框
	divShadow.style.left = window.pageXOffset+(window.innerWidth-800)/2+"px";
	divShadow.style.top = window.pageYOffset+(window.innerHeight-330)/2+"px";
	document.getElementById('divPageMask').style.display='block';
	document.getElementById('divShadow').style.display='block';
	document.getElementById('divPageMask').style.width = document.body.scrollWidth;
	document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
	}
function ToUpdatedsgRemark(){
	//后台保存
	var Id=document.getElementById("Id").value;
	var RemarkValue=document.getElementById("sgRemark").value;
	var url="item1_1_updated.php?Id="+Id+"&sgRemark="+RemarkValue;
	var ajax=InitAjax();
	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
		if(ajax.readyState==4){// && ajax.status ==200
			if(ajax.responseText=="Y"){//更新成功
				document.form1.submit();
				}
			}
		}
	ajax.send(null);
	}

function fucCheckNUM(NUM,Objects)
{
 var i,j,strTemp;
 if (Objects!="Price"){
 strTemp="0123456789";}
 else{
	strTemp=".0123456789";
	 }
 if ( NUM.length== 0)
  return 0
 for (i=0;i<NUM.length;i++)
 {
  j=strTemp.indexOf(NUM.charAt(i));
  if (j==-1)
  {
  //说明有字符不是数字
   return 0;
  }
 }
 //说明是数字
 return 1;
}

//14	焦点自动下移
function init(){
	document.onkeydown=keyDown ;
	}
function keyDown(e) {
	if(event.keyCode==13){
		event.keyCode=9 ;
		}
	}

function toTempValue(textValue){
	document.form1.TempValue.value=textValue;
	}

function OpenOrLoad(d,f,Action,Type){//Action下载6
	win=window.open("../admin/openorload.php?d="+d+"&f="+f+"&Action="+Action+"&Type="+Type,"new","toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no");
	}

function selectReadOnly(selectedId){
	var obj = document.getElementById(selectedId);
    	obj.onmouseover = function(){
     	obj.setCapture();
    	}
    obj.onmouseout = function(){
    	obj.releaseCapture();
    	}
    obj.onfocus = function(){
    	obj.blur();
    	}
    obj.onbeforeactivate = function(){
     	return false;
    	}
 	}

function init(){
	selectReadOnly("id_select");
  	}

function ResetPage(e,f){//如果e是0，则清除客户、产品分类,f为主项目来源
	document.form1.SignS.value=e;
	document.form1.m.value=f;
	document.form1.submit();
	}
function closeMaskDiv(){	//隐藏遮罩对话框
	document.getElementById('divShadow').style.display='none';
	document.getElementById('divPageMask').style.display='none';
	}
function InitAjax(){
	var ajax=false;
	try{
　　	ajax=new ActiveXObject("Msxml2.XMLHTTP");
		}
	catch(e){
　　	try{
　　　		ajax=new ActiveXObject("Microsoft.XMLHTTP");
			}
		catch(E){
　　　		ajax=false;
			}
　		}
　	if(!ajax && typeof XMLHttpRequest!='undefined'){
		ajax=new XMLHttpRequest();
		}
　	return ajax;
	}
function StuffSOrH(e,f,RowId,ProductId){//行，列，行号
	e.style.display=(e.style.display=="none")?"":"none";
	f.innerHTML=f.innerText=="[ + ]"?"[ - ]":"[ + ]";
	var yy=f.src;
	if (f.innerText=="[ - ]"){//展开
		//动态加入采购明细
		if(ProductId!=""){
			var url="product_ajax.php?ProductId="+ProductId+"&RowId="+RowId;
		　	var show=eval("ShowDiv"+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					show.innerHTML=DataArray[0];
					}
				}
			ajax.send(null);
			}
		}
	}

function ShowOrHide(e,f,RowId,POrderId){//行，列，行号
	e.style.display=(e.style.display=="none")?"":"none";
	f.innerHTML=f.innerText=="[ + ]"?"[ - ]":"[ + ]";
	var yy=f.src;
	if (f.innerText=="[ - ]"){//展开
		//动态加入采购明细
		if(POrderId!=""){
			var url="item1_ajax.php?POrderId="+POrderId+"&RowId="+RowId;
		　	var show=eval("ShowDiv"+RowId);
		　	var ajax=InitAjax();
		　	ajax.open("GET",url,true);
			ajax.onreadystatechange =function(){
		　		if(ajax.readyState==4){// && ajax.status ==200
					var BackData=ajax.responseText;
					var DataArray=BackData.split("`");
					show.innerHTML=DataArray[0];
					//订单状态更新
					switch(DataArray[1]){
						case "1"://白色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#FFFFFF";
							break;
						case "2"://黄色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#FFCC00";
							break;
						case "3"://绿色
							eval("ListTable"+RowId).rows[0].cells[1].bgColor="#339900";
							break;
						}
					}
				}
			ajax.send(null);
			}
		}
	}

function RegisterQty(POrderId,TypeId)
{
	var divShadow=document.getElementById('divShadow');
	divShadow.innerHTML="";
	var url="item1_scdj.php?POrderId="+POrderId+"&TypeId="+TypeId;
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	document.getElementById("divShadow").innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
	//定位对话框
   if(!-[1,]){  //判断是否为IE
    divShadow.style.left =document.documentElement.scrollLeft +(document.documentElement.clientWidth-800)/2+"px";
	divShadow.style.top =document.documentElement.scrollTop +(document.documentElement.clientHeight -330)/2+"px";
   }
   else{
	divShadow.style.left = window.pageXOffset+(window.innerWidth-800)/2+"px";
	divShadow.style.top = window.pageYOffset+(window.innerHeight-330)/2+"px";
   }
	document.getElementById('divPageMask').style.display='block';
	document.getElementById('divShadow').style.display='block';
	document.getElementById('divPageMask').style.width = document.body.scrollWidth+"px";
	document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
}

function PrintTasks(POrderId){
	var divShadow=document.getElementById('divShadow');
	divShadow.innerHTML="";
	var url="item1_scdy.php?POrderId="+POrderId;
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	document.getElementById("divShadow").innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
	//定位对话框
  if(!-[1,]){  //判断是否为IE
    divShadow.style.left =document.documentElement.scrollLeft +(document.documentElement.clientWidth-800)/2+"px";
	divShadow.style.top =document.documentElement.scrollTop +(document.documentElement.clientHeight -330)/2+"px";
   }
   else{
	divShadow.style.left = window.pageXOffset+(window.innerWidth-800)/2+"px";
	divShadow.style.top = window.pageYOffset+(window.innerHeight-330)/2+"px";
   }
	document.getElementById('divPageMask').style.display='block';
	document.getElementById('divShadow').style.display='block';
	document.getElementById('divPageMask').style.width = document.body.scrollWidth+"px";
	document.getElementById('divPageMask').style.height =document.body.offsetHeight+"px";
	}
function ToSavePT(){
	var Qty1=document.getElementById("Qty1").value;
	var CheckSTR1=fucCheckNUM(Qty1,"");
	var Msg=1;
	if(CheckSTR1==0 && Qty1!=""){
		document.getElementById("InfoBack").innerHTML="背卡条码打印数量不是规范的数字！";
		document.form1.Qty1.value="";
		Msg=0;
		return false;
		}
	var Qty2=document.getElementById("Qty2").value;
	var CheckSTR2=fucCheckNUM(Qty2,"");
	if(CheckSTR2==0 && Qty2!=""){
		document.getElementById("InfoBack").innerHTML="PE袋标签打印数量不是规范的数字！";
		document.form1.Qty2.value="";
		Msg=0;
		return false;
		}
	var Qty3=document.getElementById("Qty3").value;
	var CheckSTR3=fucCheckNUM(Qty3,"");
	if(CheckSTR3==0 && Qty3!=""){
		document.getElementById("InfoBack").innerHTML="外箱标签打印数量不是规范的数字！";
		document.form1.Qty3.value="";
		Msg=0;
		return false;
		}
    var Qty4=document.getElementById("Qty4").value;
	var CheckSTR4=fucCheckNUM(Qty4,"");
	if(CheckSTR4==0 && Qty4!=""){
		document.getElementById("InfoBack").innerHTML="白盒/坑盒标签打印数量不是规范的数字！";
		document.form1.Qty4.value="";
		Msg=0;
		return false;
		}
	if(Msg==1){
		//检查数字是否合法
		var POrderId=document.getElementById("POrderId").value;
		var url="item1_scdy_ajax.php?POrderId="+POrderId+"&Qty1="+Qty1+"&Qty2="+Qty2+"&Qty3="+Qty3+"&Qty4="+Qty4;
	　	var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
				var BackData=ajax.responseText;
				if(BackData=="Y"){
					document.form1.submit();
					}
				}
			}
		ajax.send(null);
		}
	}
function ToSaveDj(TypeId){
	var Qty=document.getElementById("Qty").value;
	var CheckSTR=fucCheckNUM(Qty,"");
	if(CheckSTR==0){
		document.getElementById("InfoBack").innerHTML="不是规范的数字！";
		document.form1.Qty.value="";
		return false;
		}
	else{
		//检查数字是否合法
		var MaxValue=document.getElementById("UnPQty").value;
		thisValue=Number(Qty);
		MaxValue=Number(MaxValue);
		if((thisValue>MaxValue) || thisValue==0){
			document.getElementById("InfoBack").innerHTML="超出范围！";
			document.getElementById("Qty").value="";
			return false;
			}
		else{
			document.getElementById("InfoBack").innerHTML="&nbsp;";
			}
		var POrderId=document.getElementById("POrderId").value;
		var url="item1_scdj_ajax.php?TypeId="+TypeId+"&POrderId="+POrderId+"&Qty="+Qty;
	　	var ajax=InitAjax();
	　	ajax.open("GET",url,true);
		ajax.onreadystatechange =function(){
		　　if(ajax.readyState==4 && ajax.status ==200){
				var BackData=ajax.responseText;
				if(BackData=="Y"){
					document.form1.submit();
					}
				}
			}
		ajax.send(null);
		}
	}
function ClearStr(){
	document.getElementById('Qty').value="";
	}

function Outdepot(thisE){

	var thisValue=thisE.value;
	//检查是否数字，以及是否在范围内
	if(thisValue==""){
		thisE.value="这里输入生产数量";
		return false;
		}
	else{
		var CheckSTR=fucCheckNUM(thisValue,"");
		if(CheckSTR==0){
			document.getElementById("InfoBack").innerHTML="不是规范的数字！";
			thisE.value="";
			return false;
			}
		else{
			thisValue=Number(thisValue);
			MaxValue=Number(MaxValue);
			if((thisValue>MaxValue) || thisValue==0){
				document.getElementById("InfoBack").innerHTML="超出范围！";
				thisE.value="";
				return false;
				}
			else{
				document.getElementById("InfoBack").innerHTML="&nbsp;";
				}
			}
		}
	}

function fucCheckNUM(NUM,Objects)
{
 var i,j,strTemp;
 if (Objects!="Price"){
 strTemp="0123456789";}
 else{
	strTemp=".0123456789";
	 }
 if ( NUM.length== 0)
  return 0
 for (i=0;i<NUM.length;i++)
 {
  j=strTemp.indexOf(NUM.charAt(i));
  if (j==-1)
  {
  //说明有字符不是数字
   return 0;
  }
 }
 //说明是数字
 return 1;
}

function viewImage(PId,RT,ST){
	win=window.open("../admin/Productimage/Ipadopenimage.php?ID=" +PId+"&RT="+RT+"&ST="+ST,"new","toolbar=no, menubar=no, scrollbars=yes,resizable=yes,location=no, status=no,width="+(screen.availWidth-20)+",height="+(screen.availHeight-30));
	win.focus();
}

function getAbsolutePos(el) { //取得对象的绝对位置
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent) {
    var tmp = getAbsolutePos(el.offsetParent);
    r.x += tmp.x;
    r.y += tmp.y;
   }
   return r;
 }

//打开DIV弹出窗口
function openWinDialog(e,url,w,h,Pos){
	var showDialog=document.getElementById("winDialog");
	showDialog.innerHTML="";
　	var ajax=InitAjax();
　	ajax.open("GET",url,true);
	ajax.onreadystatechange =function(){
	　　if(ajax.readyState==4 && ajax.status ==200){
	　　　	showDialog.innerHTML=ajax.responseText;
			}
		}
　	ajax.send(null);
    showDialog.style.width=w+"px";
    //showDialog.style.height=h+"px";
	//定位对话框
	//var Pos="center";
	var offsetPos=getAbsolutePos(e);
	switch(Pos){
		case "top":
		   var calTop=offsetPos.y-h.height-5;
	       var calLeft=offsetPos.x-(w-e.offsetWidth)/2;
		   break;
		case "left":
		   var calTop=offsetPos.y-(h-e.offsetHeight)/2;
		   var calLeft=offsetPos.x-w -5;
		   break;
		case "right":
		   var calTop=offsetPos.y-(h-e.offsetHeight)/2;
		   var calLeft=offsetPos.x+e.offsetWidth+5;
		   break;
		case "bottom":
		   var calTop=offsetPos.y+e.offsetHeight+5;
	       var calLeft=offsetPos.x-(w-e.offsetWidth)/2;
		   break;
		case "center":
		   if(!-[1,]){  //判断是否为IE
		     calTop=document.documentElement.scrollTop +(document.documentElement.clientHeight -h)/2;
             calLeft =document.documentElement.scrollLeft +(document.documentElement.clientWidth-w)/2;
               }
           else{
	           calLeft = window.pageXOffset+(window.innerWidth-w)/2;
	           calTop= window.pageYOffset+(window.innerHeight-h)/2;
            }
		  break;
	}
	  if (calTop<=0) calTop=5;
	  if (calLeft<=0) calLeft=5;
	 showDialog.style.left =calLeft+"px";
	 showDialog.style.top =calTop+"px";
	 showDialog.style.display='block';
}

function closeWinDialog(){
	document.getElementById('winDialog').style.display='none';
}