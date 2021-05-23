<?php   
//电信-zxq 2012-08-01
//include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
?>
<html>
<head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<meta http-equiv="Page-Enter" content="revealTrans(duration=1, transition=3)"> 
<script src='../model/pagefun.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script src='../model/lookup.js' type=text/javascript></script>
<?php   
ChangeWtitle("$SubCompany 行事历");
?>
<style type="text/css">
body {text-align:left;
    font-size:12px;
	background-color:#eff0f2;
	padding: 20px;}
td {font-size: 12px;line-height:160%;}
.todyaColor {BACKGROUND-COLOR: #006600;}
ul.menu_tabbed li {
	display: inline;
	margin-right: 5px;
	}	
ul.menu_tabbed li a {
	color:#999999;
	text-decoration: none;
	background: #f7f7f7;
	border: 1px #CCCCCC solid;
	padding: 10px 14px;
	}	
ul.menu_tabbed li a:hover,a:active{
	color:#000;
	background:#FFFFFF;
	border: 1px #999999 solid;
	border-bottom: 1px #FFFFFF solid;
	padding: 14px 14px 10px 14px;	
	}	
ul.menu_tabbed li a.selected{
	color:#000;
	background:#DCDCDC;
	border: 1px #999999 solid;
	border-bottom: 1px #DCDCDC solid;
	padding: 14px 14px 10px 14px;	
	}	
	
ul.ullist li{
   text-align:left;
   font-size:15px;
   font-weight:bold;
   height:30px;
   line-height:30px;
   list-style-position:inside;
   list-style-type:square;
   border-bottom:1px dashed #999;
}
ul.ullist li span{
float:right;
}
ul.ullist li a{
  color:#06F;
  text-decoration: none;
}
ul.ullist li a:hover,a:active{
	color:#0C6;
	}	

textarea{
  display:none;
  }
.Atoday{
color:#FF0000;
text-decoration:none;
}
.spantoday{
color:#FF0000;
float:right;
}
/*弹出编辑窗口样式*/  
#massage_box {  
position: absolute;  
left: expression((body.clientWidth-600)/2);  
top: expression((body.clientHeight-450)/2);  
width: 600px;  
height:450px;  
filter: dropshadow(color=#666666,offx=3,offy=3,positive=2);  
z-index: 2;  
display:none;
}  
#mask {  
position: absolute;
top: 0%;  
left: 0%;  
width: expression(body.scrollWidth);  
height: expression(body.scrollHeight);  
background: #666;  
filter: ALPHA(opacity=60);  
z-index: 1;  
display:none;
}  
.massage {  
border: #2D9ECA solid;  
border-width: 1 1 3 1;  
width: 95%;  
height: 95%;
background-color:#2D9ECA;  
font-size: 12px;  
line-height: 150%;  
}  

.header {  
background: #2D9ECA;  
height: 25px;  
font-family: 思源黑体,Verdana, Arial, Helvetica, sans-serif;
font-size: 14px;  
padding: 3 5 0 5;  
color: #fff;  
}    
p{
text-align:center;
font-size:24px;
color:#000066;
font-weight:bold;
  }
span{
text-align:center;
font-size:14px;
Line-height:24px;
}
dd{ text-align:left;height:auto;padding:8px 12px;}  
dd span{text-align:left;font-size:14px;Line-height:24px;}
</style>
</head>

<script language=JavaScript>
 var xmlHttp;
 var Obj='';
 document.onmouseup=MUp;
 document.onmousemove=MMove ;
      
function MDown(Object){  
        Obj=Object.id ;
        document.all(Obj).setCapture() ; 
        pX=event.x-document.all(Obj).style.pixelLeft;  
        pY=event.y-document.all(Obj).style.pixelTop;  
    }  
          
function MMove(){  
        if(Obj!=''){  
           document.all(Obj).style.left=event.x-pX;  
           document.all(Obj).style.top=event.y-pY;  
        }  
    }  
          
function MUp(){  
        if(Obj!=''){  
           document.all(Obj).releaseCapture();  
           Obj='';  
        }  
    }  

function getOs() 
{ 
   var OsObject = ""; 
   if(navigator.userAgent.indexOf("MSIE")>0) { 
        return "MSIE";       //IE浏览器
   }
   if(isFirefox=navigator.userAgent.indexOf("Firefox")>0){ 
        return "Firefox";     //Firefox浏览器
   }
   if(isSafari=navigator.userAgent.indexOf("Safari")>0) { 
        return "Safari";      //Safan浏览器
   }
   if(isCamino=navigator.userAgent.indexOf("Camino")>0){ 
        return "Camino";   //Camino浏览器
   }
   if(isMozilla=navigator.userAgent.indexOf("Gecko/")>0){ 
        return "Gecko";    //Gecko浏览器
   } 
} 

function selIndex(index)
{
var selobj;
switch (index) {
   case 1:
      document.getElementById('SignType').value='1';
	  break;
   case 2:
      document.getElementById('SignType').value='2';
	  break;
   case 3:
      document.getElementById('SignType').value='3';
	  break;
   case 4:
      document.getElementById('SignType').value='4';
	  break;
   default:
      document.getElementById('SignType').value='1';
	  break;
  } 
  for (i=1;i<=4;i++){
    selobj="sel"+i;
	if (i==index) {
	   document.getElementById(selobj).className='selected'; }
	  else{
	   document.getElementById(selobj).className='';}
   }
  showMsg();
}

function showMsg()
{ 
var SignTypeValue=document.getElementById('SignType').value;
var dateTypeValue=document.getElementById('dateType').value; 
var url="calendar_read.php";
url=url+"?SignType="+SignTypeValue+"&dateType="+dateTypeValue;
url=url+"&do="+Math.random();
document.getElementById("Title").innerHTML=getData(url);
}

function getData(php_url) {
	var request=false;
	var requestText="";
	var browsetype=getOs();
   try {
     request = new XMLHttpRequest();
   } catch (trymicrosoft) {
     try {
       request = new ActiveXObject("Msxml2.XMLHTTP");
     } catch (othermicrosoft) {
       try {
         request = new ActiveXObject("Microsoft.XMLHTTP");
       } catch (failed) {
         request = false;
       }  
     }
   }

   if (!request){
     alert("Error initializing XMLHttpRequest!");
    }
   else
   {
      request.open("POST",php_url,false);
	  request.setRequestHeader("cache-control","no-cache");
      request.setRequestHeader('Content-type','application/x-www-form-urlencoded');  
	  if(browsetype!="Firefox")
      {
        request.onreadystatechange=function(){
		   if(request.readyState == 4 ) {if(request.status == 200) requestText=request.responseText;}
		  }
	  }
      request.send(null);
	  if(browsetype=="Firefox") requestText=request.responseText; 
    }
     return (requestText);
   }
 function listClick(index)
 {
  var msgIndex="textMsg"+index;
  var str_data=document.getElementById(msgIndex).value;
  var url="calendar_read.php";
  url=url+"?SignType=8"+"&dateType="+str_data;
  url=url+"&do="+Math.random();
  var dHeight =document.body.scrollHeight;
  var dWidth= document.body.scrollWidth;
  var boxLeft=(document.body.clientWidth-600)/2; 
  var boxTop=(document.body.clientHeight-450)/2;
  document.getElementById("mask").style.height = dHeight + "px";
  document.getElementById("mask").style.width = dWidth + "px";
  document.getElementById("mask").style.display='block';
  document.getElementById("massage_box").style.top =boxTop + "px";
  document.getElementById("massage_box").style.left =boxLeft + "px";
  document.getElementById("massage_box").style.height ="450 px";
  document.getElementById("massage_box").style.width = "600 px";
   document.getElementById("massage_box").style.height ="450 px";
  document.getElementById("massage_box").style.width = "600 px";
  document.getElementById("massage_box").style.display='block';
  document.getElementById("listMsg").innerHTML=getData(url);
//  var url="calendar_msg.php"+"?ID="+"textMsg" + index +"&do="+Math.random();
 //设置模式窗口的一些状态值
 // var windowStatus = "dialogWidth:600px;dialogHeight:300px;center:1;status:0;";
  //在模式窗口中打开的页面
  //将模式窗口返回的值临时保存
 // showModalDialog(url,window,windowStatus);
  }
  
  function closeShow()
  {
   document.getElementById("mask").style.display='none';
   document.getElementById("massage_box").style.display='none';	  
  }
</script>
<body onload='selIndex(1)'>
<FORM name=CLD>
    <TABLE   width="1024" border="0" align="center"  cellpadding="0" cellspacing="0">
      <TBODY>
	  <tr>
	  <TD  width="545" align="center"><img src="../model/calendar/images/logo.gif" border="0"></TD>
	  <TD  width="479" >
	  <ul class="menu_tabbed">
	  <br/>
         <li  onclick='selIndex(1)'><a href="#" id='sel1'>最新通告</a></li> 
         <li  onclick='selIndex(2)'><a href="#" id='sel2'>加班通知</a></li> 
         <li  onclick='selIndex(3)'><a href="#" id='sel3'>人事通知</a></li> 
         <li  onclick='selIndex(4)'><a href="#" id='sel4'>公司公告</a></li>
		 <li><select name='dateType' id='dateType' onchange='showMsg()'>
					<option value='5'>最近一周</option>
					<option value='1'>最近一月</option>
					<option value='2'>最近三月</option>
					<option value='3'>最近半年</option>
					<option value='4'>最近一年</option>
					<option value='0'>所有日期</option>
			  </select></li></ul> 
		 <input name="SignType" id="SignType" value="" type="hidden" size="8">
	  </TD> </TR>
        <TR>
          <TD  width="545">
				<div><iframe src="../wnlDate/wns.php" id="wnDateFrm" name="wnDateFrm"  frameborder="0"  width="500" height="520" margwidth="0" margheight="0" scrolling="no" allowTransparency="true" align="top"></iframe></div>
          </TD>
           <td  width="479" valign="top"><ul class='ullist'>
          <div style=" height:450;max-height:450; overflow:auto;background: #f7f7f7;">
		    <span id="Title"></span>
		  </div>
		  </ul>
       	 </td>
            
        </TR>
      </TBODY>
  </TABLE>
</FORM>
<!--弹出窗口-->
<div id="massage_box">  
  <div class='massage'>
   <div class="header" onMouseDown="MDown(massage_box)">  
          <span onClick="closeShow()" style="float: right; display: block; CURSOR: pointer"> × </span>
	</div>
	<div style="height:420px;max-height:420px;overflow:auto;background-color:#fff;">
     	<dd><span id="listMsg"> </span></dd>
	</div>
    <div style="background-color:#2D9ECA;text-align:center;">
       <br /><span><input type="button" value="关   闭" onClick="closeShow()"></span><br /><br />
    </div>
	</div>
    </div>  
    <div id="mask"></div>  
</body>
</html>