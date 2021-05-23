<?php   
//电信---yang 20120801
//include "../basic/chksession.php";
include "../basic/parameter.inc";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>采购计算器</title>
<script src='../model/pagefun.js' type=text/javascript></script>
<script type="text/javascript" src="../model/js/jquery.js"></script>
<?php   
echo"<link rel='stylesheet' href='../model/default/read_line.css'><link rel='stylesheet' href='../model/css/sharing.css'>";
?>
<style type="text/css">
<!--
.style1 {color: #999999;}
body {
	background: #f0f0f0;
	margin: 0;
	padding: 0;
	font: 10px normal Verdana, Arial, Helvetica, sans-serif;
	color: #444;
}
h1 {font-size: 3em; margin: 20px 0;}
.container {width: 500px; margin: 10px auto;}
ul.tabs {
	margin: 0;
	padding: 0;
	float: left;
	list-style: none;
	height: 32px;
	border-bottom: 1px solid #999;
	border-left: 1px solid #999;
	width: 100%;
}
ul.tabs li {
	float: left;
	margin: 0;
	padding: 0;
	height: 31px;
	line-height: 31px;
	border: 1px solid #999;
	border-left: none;
	margin-bottom: -1px;
	background: #e0e0e0;
	overflow: hidden;
	position: relative;
}
ul.tabs li a {
	text-decoration: none;
	color: #000;
	display: block;
	font-size: 1.2em;
	padding: 0 20px;
	border: 1px solid #fff;
	outline: none;
}
ul.tabs li a:hover {
	background: #ccc;
}	
html ul.tabs li.active, html ul.tabs li.active a:hover  {
	background: #fff;
	border-bottom: 1px solid #fff;
}
.tab_container {
	border: 1px solid #999;
	border-top: none;
	clear: both;
	float: left; 
	width: 100%;
	background: #fff;
	-moz-border-radius-bottomright: 5px;
	-khtml-border-radius-bottomright: 5px;
	-webkit-border-bottom-right-radius: 5px;
	-moz-border-radius-bottomleft: 5px;
	-khtml-border-radius-bottomleft: 5px;
	-webkit-border-bottom-left-radius: 5px;
}
.tab_content {
	padding: 20px;
	font-size: 1.2em;
}
.tab_content h2 {
	font-weight: normal;
	padding-bottom: 10px;
	border-bottom: 1px dashed #ddd;
	font-size: 1.8em;
}
.tab_content h3 a{
	color: #254588;
}
.tab_content img {
	float: left;
	margin: 0 20px 20px 0;
	border: 1px solid #ddd;
	padding: 5px;
}
-->
</style>
<script type="text/javascript">
$(document).ready(function() {
	//Default Action
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content
	
	//On Click Event
	$("ul.tabs li").click(function() {
		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content		
	});
});
</script>
</head>

<body>
<form name="form1" method="post" action=""><input name="TempValue" type="hidden" id="TempValue">
<div class="container">
    <ul class="tabs">
        <li><a href="#tab1">保护膜计价器</a></li>
        <li><a href="#tab2">纸箱计价器</a></li>
        <li><a href="#tab3">佑普发纸箱计价器</a></li>
    </ul>
  <div class="tab_container">
    <div id="tab1" class="tab_content">
<table id="ListTable" border="0" cellpadding="0" cellspacing="0" style="width:463px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
   <tr align="center" bgcolor="#999999">
      <td   height="35" class="A1111">保护膜计价</td>
    </tr>
  </table>
  
<table id="ListTable" border="0" cellpadding="0" cellspacing="0" style="width:463px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:13px;">
  <tr bgcolor="#CCCCCC">
    <td style="width: 100px;height:30px" align="right" class="A0110">公式分类</td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
       <input type="radio" name="Itemize"  onclick='bCalculation()' checked>透明防刮(70) 
       <input type="radio" name="Itemize"  onclick='bCalculation()'>磨沙防刮(100)
	   <input type="radio" name="Itemize"  onclick='bCalculation()'>高清防指纹(130)</br>
	   <input type="radio" name="Itemize"  onclick='bCalculation()'>镜子膜(290)
     <!--  <select id='Itemize' name='Itemize' style="width:75px;" onchange='bCalculation()'>
       <option value='100'>透明防刮</option><option value='70'>磨沙防刮</option></select>-->
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">长(cm)</td>
     <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
     <td style="width:320px;" align="left" class="A0100">
        <input name="bLen" type="text" id="bLen"  style='width:70px;text-align:right' onfocus='toTempValue(this)' onchange='toCheck(this,1)'>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
   <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">宽(cm)</td>
     <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
        <input name="bWidth" type="text" id="bWidth"  style='width:70px;text-align:right' onfocus='toTempValue(this)' onchange='toCheck(this,1)'>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
   <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">数量(pcs)</td>
     <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
     <td style="width:320px;" align="left" class="A0100">
        <input name="bNumber" type="text" id="bNumber"  style='width:70px;text-align:right' onfocus='toTempValue(this)' onchange='toCheck(this,1)' value='1'>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
    <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">印刷类型</td>
    <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
      <input type="radio" name="printStyle"  onclick='bCalculation()' checked>无印字(0) 
      <input type="radio" name="printStyle"  onclick='bCalculation()'>有印字(0.14)
      <!-- <select id='printStyle' name='printStyle' style="width:75px;" onchange='bCalculation()'>
       <option value='0'>无</option><option value='0.15'>有</option></select>-->
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
  </tr>
    <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">眼镜布大小</td>
    <td style="width:20px;" align="left" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
     <input type="radio" name="clothStyle"  onclick='bCalculation()' checked>无(0)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
     <input type="radio" name="clothStyle"  onclick='bCalculation()'>10X10cm(0.085)
     <input type="radio" name="clothStyle"  onclick='bCalculation()'>15X15cm(0.14)
    <BR> <input type="radio" name="clothStyle"  onclick='bCalculation()'>KCT10X10cm(0.08)
    <input type="radio" name="clothStyle"  onclick='bCalculation()'>KCT15X15cm(0.1)
    <!--   <select id='clothStyle' name='clothStyle' style="width:75px;" onchange='bCalculation()'>
       <option value='0'>无</option><option value='0.09'>10mm</option><option value='0.15'>15mm</option></select>-->
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
  </tr>
    <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">除尘贴</td>
    <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
     <input type="radio" name="Deduster"  onclick='bCalculation()' checked>无(0)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <input type="radio" name="Deduster"  onclick='bCalculation()'>有(0.095)
     <!--  <select id='Deduster' name='Deduster' style="width:75px;" onchange='bCalculation()'>
       <option value='0'>无</option><option value='0.1'>有</option></select>-->
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">包装费</td>
    <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
	 <input type="radio" name="Packing"  onclick='bCalculation()' checked>无(0)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <input type="radio" name="Packing"  onclick='bCalculation()'>(0.047)
&nbsp;&nbsp;&nbsp;
     <input type="radio" name="Packing"  onclick='bCalculation()'>(0.028)
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">刮卡</td>
    <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
     <input type="radio" name="Kapian"  onclick='bCalculation()' checked>无(0)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	 <input type="radio" name="Kapian"  onclick='bCalculation()'>0.065&nbsp;&nbsp;&nbsp;
	<!-- <input type="radio" name="Kapian"  onclick='bCalculation()'>0.04(空白)&nbsp;-->
     <input type="radio" name="Kapian"  onclick='bCalculation()'>0.06(PP卡)&nbsp;&nbsp;&nbsp;
     <input type="radio" name="Kapian"  onclick='bCalculation()'>KCT0.05(PP卡)
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">说明书</td>
    <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
     <input type="radio" name="sBook"  onclick='bCalculation()' checked>无(0)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <input type="radio" name="sBook"  onclick='bCalculation()'>有(0.06)
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
  </tr>
    <tr bgcolor="#999999">
    <td style="width:100px;height:30px" align="right" class="A0110">计算结果</td>
     <td style="width:20px;" align="right" class="A0100">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
        <input name="bTotal" type="text" id="bTotal" size='8' class="I0000RB" style='width:75px;text-align:right;font-weight:bold;' readonly>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KCT
        <input name="kctTotal" type="text" id="kctTotal" size='8' class="I0000RB" style='width:75px;text-align:right;font-weight:bold;' readonly>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
  </table>
  <table id="ListTable" border="0" cellpadding="0" cellspacing="0" style="width:463px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
    <tr bgcolor="#CCCCCC">
    <td  style='height:55px;'  class="A0111"><font style='font-size:11px;color:#333;'><b>计算公式：</b><br />&nbsp;&nbsp;&nbsp;&nbsp;((((((长+10)*(宽+10)/1000000*公式分类)+0.1)*1.2*0.95*0.70)+印刷类型)*数量+眼镜布大小+除尘贴)*0.95+包装费+刮卡+说明书
    <br /><b>KCT公式：</b><br />&nbsp;&nbsp;&nbsp;&nbsp;(((((长+10)*(宽+10)/1000000*公式分类)+0.1)*1.2*0.8)+印刷类型)*数量+眼镜布大小+除尘贴+包装费+刮卡+说明书
    </td>
  </tr>
   <tr bgcolor="#CCCCCC">
     <td  style='height:25px;' align='center' class="A0111"> <input type='reset' name='reset' value='重  置'/></td>
   </tr>
  </table>
   </div>
  <div id="tab2" class="tab_content">
  <table id="ListTable" border="0" cellpadding="0" cellspacing="0" style="width:463px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
   <tr align="center" bgcolor="#999999">
      <td   height="35" class="A1111">纸箱计价</td>
    </tr>
  </table>
  
<table id="ListTable" border="0" cellpadding="0" cellspacing="0" style="width:463px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:13px;">
  <tr bgcolor="#CCCCCC">
    <td style="width: 100px;height:30px" align="right" class="A0110">材质类型</td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
      <input type="radio" name="xItemize"  onclick='xCalculation()' checked>K3K(2.17) 
      <input type="radio" name="xItemize"  onclick='xCalculation()'>K7K(2.7)
      <input type="radio" name="xItemize"  onclick='xCalculation()'>k535k(3.26)
	  <input type="radio" name="xItemize"  onclick='xCalculation()'>K=K(2.9)
	  <input type="radio" name="xItemize"  onclick='xCalculation()'>W+737K(3.4)
	  <input type="radio" name="xItemize"  onclick='xCalculation()'>F5F5F(3.92)
    <!--
       <select id='xItemize' name='xItemize' style="width:75px;" onchange='xCalculation()'>
       <option value='2.5'>K3K</option><option value='2.7'>K7K</option>
        <option value='3.27'>K=K</option><option value='3.65'>W=K</option></select>-->
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">长(cm)</td>
     <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
     <td style="width:320px;" align="right" class="A0100">
        <input name="xLen" type="text" id="xLen" style='width:72px;text-align:right' onfocus='toTempValue(this)' onchange='toCheck(this,2)'>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
   <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">宽(cm)</td>
     <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
     <td style="width:320px;" align="right" class="A0100">
        <input name="xWidth" type="text" id="xWidth"  style='width:72px;text-align:right' onfocus='toTempValue(this)' onchange='toCheck(this,2)'>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
   <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">高(cm)</td>
     <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="right" class="A0100">
        <input name="xHeight" type="text" id="xHeight" style='width:72px;text-align:right' onfocus='toTempValue(this)' onchange='toCheck(this,2)'>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
  </tr>
    <tr bgcolor="#999999">
    <td style="width:100px;height:30px" align="right" class="A0110">计算结果</td>
     <td style="width:20px;" align="right" class="A0100">&nbsp;</td>
     <td style="width:320px;" align="right" class="A0100">
        <input name="xTotal" type="text" id="xTotal" size='8' class="I0000RB" style='width:75px;text-align:right;font-weight:bold;' readonly>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
  </table>
    <table id="ListTable" border="0" cellpadding="0" cellspacing="0" style="width:463px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
    <tr bgcolor="#CCCCCC">
    <td  style='height:35px;' class="A0111"><font style='font-size:11px;color:#333;'><b>计算公式：</b><br />&nbsp;&nbsp;&nbsp;&nbsp;((长+宽)/2.54+2)*((宽+高)/2.54+1)*2/1000*材质价格</font></td>
  </tr>
   <tr bgcolor="#CCCCCC">
     <td  style='height:25px;' align='center' class="A0111"> <input type='reset' name='reset' value='重  置'/></td>
   </tr>
  </table>
  </div>
  
  <div id="tab3" class="tab_content">
  <table id="ListTable" border="0" cellpadding="0" cellspacing="0" style="width:463px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
   <tr align="center" bgcolor="#999999">
      <td   height="35" class="A1111">佑普发纸箱计价</td>
    </tr>
  </table>
  
<table id="ListTable" border="0" cellpadding="0" cellspacing="0" style="width:463px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word;font-size:13px;">
  <tr bgcolor="#CCCCCC">
    <td style="width: 100px;height:30px" align="right" class="A0110">材质类型</td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="left" class="A0100">
      <input type="radio" name="uItemize"  onclick='uCalculation()' checked>A=A200(4.45) 
      <input type="radio" name="uItemize"  onclick='uCalculation()'>W=A200(5.3)
      <input type="radio" name="uItemize"  onclick='uCalculation()'>K9K(2.3)
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">长(cm)</td>
     <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
     <td style="width:320px;" align="right" class="A0100">
        <input name="uLen" type="text" id="uLen" style='width:72px;text-align:right' onfocus='toTempValue(this)' onchange='toCheck(this,3)'>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
   <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">宽(cm)</td>
     <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
     <td style="width:320px;" align="right" class="A0100">
        <input name="uWidth" type="text" id="uWidth"  style='width:72px;text-align:right' onfocus='toTempValue(this)' onchange='toCheck(this,3)'>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
   <tr bgcolor="#CCCCCC">
    <td style="width:100px;height:30px" align="right" class="A0110">高(cm)</td>
     <td style="width:20px;" align="right" class="A0101">&nbsp;</td>
    <td style="width:320px;" align="right" class="A0100">
        <input name="uHeight" type="text" id="uHeight" style='width:72px;text-align:right' onfocus='toTempValue(this)' onchange='toCheck(this,3)'>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
  </tr>
    <tr bgcolor="#999999">
    <td style="width:100px;height:30px" align="right" class="A0110">计算结果</td>
     <td style="width:20px;" align="right" class="A0100">&nbsp;</td>
     <td style="width:320px;" align="right" class="A0100">
        <input name="uTotal" type="text" id="uTotal" size='8' class="I0000RB" style='width:75px;text-align:right;font-weight:bold;' readonly>
    </td>
    <td style="width: 20px;" align="right" class="A0101">&nbsp;</td>
   </tr>
  </table>
    <table id="ListTable" border="0" cellpadding="0" cellspacing="0" style="width:463px;TABLE-LAYOUT: fixed; WORD-WRAP: break-word">
    <tr bgcolor="#CCCCCC">
    <td  style='height:35px;' class="A0111"><font style='font-size:11px;color:#333;'><b>计算公式：</b><br />&nbsp;&nbsp;&nbsp;&nbsp;((长+宽+5)*(宽+高+3)*2*材质价格)/10000
    <br />K9K: (2*(长+宽)/2.54+1.5)*((1.5*宽+高+5)/2.54+1)*材质价格/1000 </font></td>
  </tr>
   <tr bgcolor="#CCCCCC">
     <td  style='height:25px;' align='center' class="A0111"> <input type='reset' name='reset' value='重  置'/></td>
   </tr>
  </table>
  </div>
  
 </div>
</div>
</form>
</body>
</html>
<script>
function toTempValue(e){
	document.getElementById("TempValue").value=e.value;
}

function toCheck(e,Flag){
	 var CheckNum=fucCheckNUM(e.value,"Price");
	 if (e.value=="") return false;
	 if(CheckNum==0 || e.value==0){
		alert("对应数量格式不规范！");
		e.value=document.getElementById("TempValue").value;
		return false;
	 }
   switch(Flag){
	   case 1:
	     bCalculation();
		 break;
	  case 2:
	     xCalculation();
		 break;
      case 3:
	     uCalculation();
		 break;
   }
}

function bCalculation(){
  var CheckNum=1;
  var Msg="";
  var p0=0.1;
  var p1=1.2*0.95*0.93;
  var p2=0.95;
  var kctp=1.2*0.8;
  var sLen=Number(document.getElementById("bLen").value);
  if (sLen=="" || sLen==0) return false;
	 
  var sWidth=Number(document.getElementById("bWidth").value);
  if (sWidth=="" || sWidth==0) return false;
	 
  var sNumber=Number(document.getElementById("bNumber").value);
  if (sNumber=="" || sNumber==0) return false;
  
  var Itemize=document.getElementsByName("Itemize");
  var s1=70;
  if (Itemize[1].checked) s1=100;
  if (Itemize[2].checked) {s1=130;p1=1*0.9;p2=1.1;}
  if (Itemize[3].checked) {s1=290,p0=0.15};
  
  var printStyle=document.getElementsByName("printStyle");
  var s2=0;
  if (printStyle[1].checked) s2=0.14;
  
  var clothStyle=document.getElementsByName("clothStyle");
  var s3=0;
  if (clothStyle[1].checked) s3=0.085;
  if (clothStyle[2].checked) s3=0.14;
  if (clothStyle[3].checked) s3=0.08;
  if (clothStyle[4].checked) s3=0.1;
  
  var Deduster=document.getElementsByName("Deduster");
  var s4=0;
  if (Deduster[1].checked) s4=0.095;
 
  var Packing=document.getElementsByName("Packing");
  var s5=0;
  if (Packing[1].checked) s5=0.047;
  if (Packing[2].checked) s5=0.028;
  
  var Kapian=document.getElementsByName("Kapian");
  var s6=0;
  if (Kapian[1].checked) s6=0.065;
  //if (Kapian[2].checked) s6=0.04;
  if (Kapian[2].checked) s6=0.06;
    if (Kapian[3].checked) s6=0.05;

  var sBook=document.getElementsByName("sBook");
  var s7=0;
  if (sBook[1].checked) s7=0.06;
  
  var sTotal=((((((sLen+10)*(sWidth+10)/1000000*s1)+p0)*p1*0.7)+s2)*sNumber+s3+s4)*p2+s5+s6+s7;
  sTotal=Math.floor(sTotal*1000*0.95*0.95);
  /*
if(clothStyle[1].checked || clothStyle[2].checked || Deduster[1].checked || Packing[1].checked || Packing[2].checked || Kapian[1].checked || Kapian[2].checked || sBook[1].checked)
  {
	  sTotal=Math.floor(sTotal*0.95);
  }
*/
  
  document.getElementById("bTotal").value=sTotal/1000;
  
  var kctTotal=(((((sLen+10)*(sWidth+10)/1000000*s1)+p0)*kctp)+s2)*sNumber+s3+s4+s5+s6+s7;
  kctTotal=Math.floor(kctTotal*1000);
  document.getElementById("kctTotal").value=kctTotal/1000;
}

function xCalculation(){
  var sLen=Number(document.getElementById("xLen").value);
   if (sLen=="" || sLen==0) return false;
	 
  var sWidth=Number(document.getElementById("xWidth").value);
  if (sWidth=="" || sWidth==0) return false;
	 
  var sHeight=Number(document.getElementById("xHeight").value);
   if (sHeight=="" || sHeight==0) return false;
 
  var xItemize=document.getElementsByName("xItemize");
  var s1=2.17;
  if (xItemize[1].checked) s1=2.7;
  if (xItemize[2].checked) s1=3.26;
  if (xItemize[3].checked) s1=2.9;
  if (xItemize[4].checked) s1=3.4;
  if (xItemize[5].checked) s1=3.92;
//((长+宽)/2.54+2)*((宽+高)/2.54+1)*2/1000*材质价格
  var xTotal=((sLen+sWidth)/2.54+2)*((sWidth+sHeight)/2.54+1)*2/1000*s1;
  document.getElementById("xTotal").value=xTotal.toFixed(2);
}

function uCalculation(){
  var sLen=Number(document.getElementById("uLen").value);
   if (sLen=="" || sLen==0) return false;
	 
  var sWidth=Number(document.getElementById("uWidth").value);
  if (sWidth=="" || sWidth==0) return false;
	 
  var sHeight=Number(document.getElementById("uHeight").value);
   if (sHeight=="" || sHeight==0) return false;
 
  var uItemize=document.getElementsByName("uItemize");
  if (uItemize[2].checked){
	  var s1=2.3;
	  var uTotal=(2*(sLen+sWidth)/2.54+1.5)*((1.5*sWidth+sHeight+5)/2.54+1)*s1/1000;
      document.getElementById("uTotal").value=uTotal.toFixed(2);
  }
  else{
     var s1=4.45;
     if (uItemize[1].checked) s1=5.3;
 // if (uItemize[2].checked) s1=3.26;
  //if (uItemize[3].checked) s1=2.9;
 // if (uItemize[4].checked) s1=3.4;
 // if (uItemize[5].checked) s1=3.92;
//((长+宽+5)*(宽+高+3)*2*材质价格)/10000
     var uTotal=((sLen+sWidth+5)*(sWidth+sHeight+3)*2*s1)/10000;
     document.getElementById("uTotal").value=uTotal.toFixed(2);
  }
}

</script>
