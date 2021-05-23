<?php 
//电信---yang 20120801
//代码、数据库共享-EWEN 2012-08-15
include "../model/modelhead.php";
echo "<script src='../model/palette.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 损益表主项目");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.sys8_pandlmain WHERE Id='$Id'",$link_id));
$TypeName=$upData["ItemName"];
$TypeColor=$upData["ColorCode"];
$SortId=$upData["SortId"];
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

<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" cellspacing="5">
        <tr>
          <td width="100" height="40" align="right" scope="col">项目名称</td>
          <td scope="col"><input name="Name" type="text" id="Name" title="可输入2-16个字节(每1中文字占2个字节，每1英文字母占1个字节)" value="<?php  echo $TypeName?>" style="width:380px;" maxlength="16" DataType="LimitB"  Max="16" Min="2" Msg="没有填写或字符不在2-16个字节内"></td>
        </tr>
        <tr>
          <td width="100" height="40" align="right" scope="col">项目序号</td>
          <td scope="col"><input name="SortId" type="text" id="SortId" title="输入数字" value="<?php  echo $SortId?>" style="width:380px;" maxlength="16" DataType="Number"  Msg="没有填写或格式不对"></td>
        </tr>
      </table>
      </td>
	</tr>
</table>
	  
	  <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
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
<span class="gray f12">颜色代码：</span><br> 
<INPUT TYPE=TEXT class="colInp" name="SelColor" ID="SelColor" value="#FFFFFF" SIZE=7 onKeyUp="inpCol(this)">

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