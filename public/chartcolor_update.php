<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新统计图例颜色资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT B.Forshort,A.ColorCode 
FROM $DataIn.chart2_color A
LEFT JOIN $DataIn.trade_object B ON B.CompanyId=A.CompanyId
WHERE A.Id='$Id'",$link_id));
$Forshort=$upData["Forshort"];
$ColorCode=$upData["ColorCode"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<style type="text/css">
<!--
a.g:link {
	text-decoration: none;
	color: #0000FF;
	font-size: 13px;
}
a.g:visited {
	text-decoration: none;
	color: #0000FF;
	font-size: 13px;
}
a.g:hover {
	text-decoration: none;
	color: #FF0000;
	font-size: 13px;
}

.gray{color:#666666}
.f12{font-size:12px}
.box{padding:2px;border:1px solid #CCC}
-->
</style>
<script language="javascript">
<!--
function h(obj,url){
obj.style.behavior='url(#default#homepage)';
obj.setHomePage(url);
}
function $(id){
    obj=document.getElementById(id);
	if (obj==null) obj=document.all.id;
	return obj;
	}
//检查颜色值-Begin
function isNum16(ch){
	if (ch >= '0' && ch <= '9')return true;
		if (ch >= 'A' && ch <= 'F')return true;
		if (ch >= 'a' && ch <= 'f')return true;
		return false;
	}
	function isAllNum16(str1)
	{//判断颜色值。除第一个字符#外的任一个值是否大于等a,A,0,小于等于f,F,9，否则报错。
		for (i=1; i<str1.length; i++) {
			if (!isNum16(str1.charAt(i)))
			{
				return false;
			}
		}
		return true;
	}

function checkCol(myColor)
{   //made by jiarry,input color value to change background
if(myColor!="")
 {
  if(myColor.length !=7 || myColor.charAt(0)!="#")
   {
   alert("颜色值加#至少7位，请检查！");
   $("SelColor").value="";
   }
  else if(!isAllNum16(myColor))
  {
  alert("颜色代码错误，请检查\n 颜色代码示例:#ff6600");
  $("SelColor").value="";
  }
  else{
   return myColor;
   }
  }
}

//检查颜色值-END
var SelRGB = '#808080';
var DrRGB = "";
var SelGRAY = '120';
var SelCol="";
var baseCol="#808080";
var light="120";
var RGB=$("RGB");
var hexch = new Array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
//add innerText to FireFox Begin
if(!document.all){
HTMLElement.prototype.__defineGetter__ 
( 
"innerText", 
function () 
{ 
var anyString = ""; 
var childS = this.childNodes; 
for(var i=0; i<childS.length; i++) 
{ 
if(childS[i].nodeType==1) 
anyString += childS[i].tagName=="BR" ? '\n' : childS[i].innerText; 
else if(childS[i].nodeType==3) 
anyString += childS[i].nodeValue; 
} 
return anyString; 
} 
); 
}

////add innerText to FireFox End

function ToHex(n){	var h, l;
	n = Math.round(n);
	l = n % 16;
	h = Math.floor((n / 16)) % 16;
	return (hexch[h] + hexch[l]);
	}

function DoColor(c, l){ 
	var r, g, b;
  	r = '0x' + c.substring(1, 3);
  	g = '0x' + c.substring(3, 5);
  	b = '0x' + c.substring(5, 7);
  	if(l > 120){
		l = l - 120;
		r = (r * (120 - l) + 255 * l) / 120;
		g = (g * (120 - l) + 255 * l) / 120;
		b = (b * (120 - l) + 255 * l) / 120;
		}
	else{
    	r = (r * l) / 120;
    	g = (g * l) / 120;
    	b = (b * l) / 120;
  		}
  	return '#' + ToHex(r) + ToHex(g) + ToHex(b);
	}

function EndColor(){ 
	var i;
	var GrayTable=$("GrayTable");
  	if(DrRGB != SelRGB){
		DrRGB = SelRGB;
   		for(i = 0; i <= 30; i ++)
		GrayTable.rows[i].bgColor = DoColor(SelRGB, 240 - i * 8);
		}
	var SelColor=$("SelColor");
  	var RGB=baseCol;
  	var GRAY=light;
  	var ShowColor=$("ShowColor");
  	SelColor.value = DoColor(baseCol, light);
  	ShowColor.bgColor = SelColor.value;
  	document.getElementById('copytip').innerHTML='';
	}

function ctOut(e) {
	baseCol=SelRGB;
  	EndColor(baseCol);
	}

function ctClick(e) {
  	SelRGB = e.bgColor;
  	EndColor();
	}

function ctOver(e){
  	baseCol = e.bgColor.toUpperCase();
  	EndColor();
	}

function gtOver(e){
	light = e.title;
  	EndColor();
 	}

function gtOut() {
	light = SelGRAY;
	EndColor();
  	}

function gtClick(e){
	SelGRAY = e.title;
  	EndColor();
 	}

function okClick(){
	var SelColor=$("SelColor");
 	self.parent.setColor(SelColor.value);
 	}

function inpCol(o){
	var l=o.value;
	if (l.length==7){
		$('ShowColor').bgColor=checkCol(o.value);
		}
	else if(l.length>7){
		o.value=l.substring(0,7);
 		alert("颜色代码加#不能超过7位");
 		}
	}
-->
</script>

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
        <tr>
          <td width="150" height="40" align="right" scope="col">客户名称</td>
          <td scope="col"><?php  echo $Forshort?></td>
        </tr>
        <tr><td colspan="2">
        <body bgcolor="#ffffff" text="#000000" vlink="#0033CC" alink="#800080"  link="#0033cc" topmargin="0">
	<table width="720" border="0" cellpadding="0" cellspacing="0" class="colTab">
		<tr align="left" valign="top">
  			<td width=515>
  				<table border="0" cellspacing="0" cellpadding="0">
  					<tr>
  						<td>
  						<span class="gray f12">颜色：</span>
  						<div class="box" style="padding:0;width:422px !important;width:424px">
							<TABLE ID=ColorTable BORDER=0 CELLSPACING=2 CELLPADDING=0 style='cursor:pointer'>
							<SCRIPT LANGUAGE=JavaScript>
								function wc(r, g, b, n){
									r = ((r * 16 + r) * 3 * (15 - n) + 0x80 * n) / 15;
									g = ((g * 16 + g) * 3 * (15 - n) + 0x80 * n) / 15;
									b = ((b * 16 + b) * 3 * (15 - n) + 0x80 * n) / 15;
									document.write('<TD BGCOLOR=#' + ToHex(r) + ToHex(g) + ToHex(b) + ' height=8 width=12 onmouseover="ctOver(this)" onmouseout="ctOut(this)" onmousedown="ctClick(this)"></TD>');
									}
								var cnum = new Array(1, 0, 0, 1, 1, 0, 0, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1, 1, 0, 0);
								for(i = 0; i < 16; i ++){
									document.write('<TR>');
    								for(j = 0; j < 30; j ++){
    									n1 = j % 5;
     									n2 = Math.floor(j / 5) * 3;
     									n3 = n2 + 3;
     									wc((cnum[n3] * n1 + cnum[n2] * (5 - n1)),
     									(cnum[n3 + 1] * n1 + cnum[n2 + 1] * (5 - n1)),
     									(cnum[n3 + 2] * n1 + cnum[n2 + 2] * (5 - n1)), i);
     									}
     							document.writeln('</TR>');
  								}
							</script>
							</TABLE>
						</div>
					</td>
					<td valign="top" style="padding-left:30px ">
					<span class="gray f12">亮度</span>
					<div class="box" style="width:20px !important;width:26px;">
						<TABLE ID=GrayTable BORDER=0 CELLSPACING=0 CELLPADDING=0 style='cursor:pointer'>
						<SCRIPT LANGUAGE=JavaScript>
  							for(i = 255; i >= 0; i -= 8.5) {
	 							document.write('<TR BGCOLOR=#' + ToHex(i) + ToHex(i) + ToHex(i) + '><TD TITLE=' + Math.floor(i * 16 / 17) + ' height=5 width=20 onmouseover="gtOver(this)" onmouseout="gtOut()" onmousedown="gtClick(this)"></TD></TR>');
	 							}
						</script>
						</TABLE>
					</div>
				</td>
			</tr>
		</table>
	</td>
	<td width=87 valign="top">
	<span class="gray f12">选中颜色：</span>
	<div class="box" style="width:50px !important;width:54px ">
	<table ID=ShowColor width="50" height="24" cellspacing="0" cellpadding="0">
	<tr><td></td></tr>
	</table>
</div>
</td>
<td width="128" valign="top">
<span class="gray f12">代码：</span><br> 
<INPUT TYPE=TEXT class="colInp" id='SelColor' name='SelColor' value="#FFFFFF" SIZE=7 onKeyUp="inpCol(this)">

<div id="copytip" class="gray f12" style="margin-top:5px"></div></div><div style="visibility:hidden"></div></td>
</tr>
</table>
<script>
EndColor();
</script>

        </td></tr>
      </table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>