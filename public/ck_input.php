<?php 
//电信-zxq 2012-08-01
session_start();
if($BillNumber!=""){
	include "../basic/parameter.inc";
	$checkGysRow=mysql_fetch_array(mysql_query("SELECT M.Id,M.CompanyId,P.Forshort FROM $DataIn.gys_shmain M,$DataIn.trade_object P WHERE 1 AND M.BillNumber='$BillNumber' AND M.CompanyId=P.CompanyId LIMIT 1",$link_id));
	$CompanyId=$checkGysRow["CompanyId"];
	$Forshort=$checkGysRow["Forshort"];
	$shMid=$checkGysRow["Id"];
	}
?>
<html><head>
<META HTTP-EQUIV="pragma">
<META HTTP-EQUIV="Cache-Control">
<META HTTP-EQUIV="expires">
<META HTTP-EQUIV="expires">
<META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<link rel="stylesheet" href="../model/pos/pos1.css">
<LINK href="../model/mask.css" rel=stylesheet>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style></head>
<script src="../model/pagefun.js" type=text/javascript></script>
<script language="javascript" event="onkeydown" for="document">  
if(event.keyCode==13)event.keyCode=9;
</script>
<script LANGUAGE="JavaScript">
<!-- 
var record = 13;//每页显示多少条记录 
var count = 25;//记录总数 
function showhiddenRecord(pagenum){ 
    if(pagenum<=1){ 
        theFirstPage.innerHTML="↑";} //当前为第一页时，不响应鼠标事件
	else{ 
        theFirstPage.innerHTML="<a href=\"javascript:ToPage(1)\">↑</a>";} 
    if(pagenum>=2){ 
        theLastPage.innerHTML="↓";} 
	else{ 
        theLastPage.innerHTML="<a href=\"javascript:ToPage(2)\">↓</a>";}
    	 
    pageBegin = (record*(pagenum-1)+1)|0;	//首行
    pageEnd = record*pagenum; 				//尾行
    for(var i=2;i<=count;i++){ 
        if(i>=pageBegin && i<=pageEnd){ 
            mytable.rows[i].style.display=""; 
        	}
		else{ 
            mytable.rows[i].style.display="none"; 
        	} 
    	} 
	}
function ToPage(pagenum){showhiddenRecord(pagenum);}
//--> 
</script>
<body scroll=no onLoad="showhiddenRecord(1);">
<form name="form1" method="post" action="">
<table id="mytable" border="1" cellspacing="0" style="height:480px;width:272px;">
  <tr bgcolor="#CCCCCC">
    <td align="center" class="A1111" style="width:26px;height:25px;" onDblClick="javascript:window.close();">&nbsp;</td>
	<td colspan="2" align="center" class="A1101" style="width:164px"><input name="BillNumber" id="BillNumber" type="text" size="20" class="BillNum" onchange="checkBill()" value="<?php  echo $BillNumber?>"><input name="shMid" type="hidden" id="shMid" value="<?php  echo $shMid?>"></td>
    <td align="center" class="A1101"><input name="Forshort" type="text" size="8" class="BillNum" value="<?php  echo $Forshort?>"></td>
  </tr>
  <tr bgcolor="#999999">
    <td align="center" class="A0111" style="height:25px;">ID</td>
	<td colspan="2" align="center" class="A0101">配件需求流水号</td>
    <td align="center" class="A0101">入库数量</td>
  </tr>
	<?php 
	if($BillNumber!=""){
		//流水号，未收数量，配件ID，配件名称，采购ID，采购名称，供应商ID，供应商名称
		$mySql=mysql_query("SELECT 
		S.StockId,S.Qty,S.StuffId,D.StuffCname,G.BuyerId,(G.AddQty+G.FactualQty) AS cgQty
		FROM $DataIn.gys_shsheet S
		LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
		WHERE 1 AND S.Mid=$shMid AND S.Locks=1 ORDER BY S.Id",$link_id);
		$i=1;
		if($myRow= mysql_fetch_array($mySql)){
			do{
				$Id=$myRow["Id"];
				$StockId=$myRow["StockId"];
				$cgQty=$myRow["cgQty"];
				$Qty=$myRow["Qty"];
				$StuffId=$myRow["StuffId"];
				$StuffCname=$myRow["StuffCname"];
				$BuyerId=$myRow["BuyerId"];
				//实际未收货数量
				//已收货总数
				$rkTemp=mysql_query("SELECT SUM(R.Qty) AS Qty FROM $DataIn.ck1_rksheet R 
				LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
				WHERE R.StockId='$StockId'",$link_id);
				$rkQty=mysql_result($rkTemp,0,"Qty");
				$rkQty=$rkQty==""?0:$rkQty;
				//待送货数量
				$shSql=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.gys_shsheet WHERE 1 AND Locks=1 AND StockId=$StockId AND Id!=$Id",$link_id);
				$shQty=mysql_result($shSql,0,"Qty");
				$shQty=$shQty==""?0:$shQty;
				$noQty=$cgQty-$rkQty-$shQty;
				if($noQty>0){
					$Qty=$Qty>$noQty?$noQty:$Qty;
					echo"<tr><input name='SID[$i]' type='hidden' id='SID$i' value='$StuffId'>
					<td align='center' class='A0111' style='height:25px' onclick='ViewName($i)'>$i<input name='SName[$i]' type='hidden' id='SName$i' value='$StuffCname'></td>
					<td colspan='2' class='A0101'><input name='CID[$i]' type='text' id='CID$i' value='$StockId' size='22' maxlength='14' class='StockId' onfocus='this.select();' onchange='ReadCgQty($i)'></td>
					<td class='A0101'><input name='QTY[$i]' type='text' id='QTY$i' value='$Qty' size='9' class='QtyRight' onfocus='this.select()'></td>
					</tr>";
					$i++;
					}
				}while ($myRow= mysql_fetch_array($mySql));
			}
		if($i<25){
			for($i=$i;$i<25;$i++){
				echo"<tr><input name='SID[$i]' type='hidden' id='SID$i'>
				<td align='center' class='A0111' style='height:25px' onclick='ViewName($i)'>$i<input name='SName[$i]' type='hidden' id='SName$i' value=''></td>
				<td colspan='2' class='A0101'><input name='CID[$i]' type='text' id='CID$i' value='' size='22' maxlength='14' class='StockId' onfocus='this.select();' onchange='ReadCgQty($i)'></td>
				<td class='A0101'><input name='QTY[$i]' type='text' id='QTY$i' value='' size='9' class='QtyRight' onfocus='this.select()'></td>
				</tr>";
				}
			}
		}
	else{
		for($i=1;$i<25;$i++){
			echo"<tr><input name='SID[$i]' type='hidden' id='SID$i'>
			<td align='center' class='A0111' style='height:25px' onclick='ViewName($i)'>$i<input name='SName[$i]' type='hidden' id='SName$i' value=''></td>
			<td colspan='2' class='A0101'><input name='CID[$i]' type='text' id='CID$i' value='' size='22' maxlength='14' class='StockId' onfocus='this.select();' onchange='ReadCgQty($i)'></td>
			<td class='A0101'><input name='QTY[$i]' type='text' id='QTY$i' value='' size='9' class='QtyRight' onfocus='this.select()'></td>
			</tr>";
			}
		}
	?>
  <tr bgcolor="#999999">
    <td class="A0011" align="center"><span id="theFirstPage"><a href="javascript:ToPage(1)">↑</a></span></td>
  <td colspan="3" rowspan="2" class="A0101" style="height:25px;"><div id="ViewName">&nbsp;</div></td>
    </tr>
  <tr bgcolor="#999999">
    <td class="A0111" style="height:25px;" align="center"><span id="theLastPage"><a href="javascript:ToPage(2)">↓</a></span></td>
  </tr>
  <tr bgcolor="#CCCCCC">
  	<td align="center" class="A0111" onClick="javascript:showMaskDiv()">◎</td>
 	<td align="center" class="A0101"><span id="Name"><?php  echo $Login_Name?></span><input name="Operator" type="hidden" id="Operator" value="<?php  echo $Operator?>"></td>
	<td align="center" class="A0101" onclick="ClearData()">清空</td>
	<td align="center" class="A0101"><div onclick="javascript:ToSave()">提交</div></td>
  </tr>
</table>
<input name="CompanyId" type="hidden" id="CompanyId" value="<?php  echo $CompanyId?>"><input name="BuyerId" type="hidden" id="BuyerId" value="<?php  echo $BuyerId?>">

<div id='divShadow' class="divShadow" style="display:none;">
	<div class='divInfo' id='divInfo'>
	<table width="250">
		<tr><td align="center">登录<input name="LoginN" type="password" id="LoginN" onblur="closeMaskDiv()"></td></tr>
	</table>
	</div>
</div>
<div id="divPageMask" class="divPageMask" style="display:none;">
	<iframe scrolling="no" height="100%" width="100%" marginwidth="-5" marginheight="-5" src="MaskBgColor.htm"></iframe>
</div>
</form>
</body>
</html>
<script>
function ClearData(){//清空列表数据：只保留操作员ID
	for (var i=0;i<form1.elements.length;i++){
		var e=form1.elements[i];
		if (e.name!="Operator"){
			e.value="";
			}
		}
	}
function checkBill(){//搜索供应商送货单
	document.form1.submit();
	}
function ToSave(){	//确认和保存收货记录
	document.form1.action="ck_input_save.php";
	document.form1.submit();
	}
function ReadCgQty(textId){//读取扫描到的需求单资料，如果有符合条件的记录，则在列表中列出
	var num=Math.random();  
	var CID=eval("document.form1.CID"+textId).value;
	if(CID!=""){
		BackData=window.showModalDialog("ck_input_ajax.php?r="+num+"&CID="+CID,"BackData","dialogHeight =650px;dialogWidth=980px;center=yes;scroll=yes");
		//流水号，未收数量，配件ID，配件名称，采购ID，采购名称，供应商ID，供应商名称
		if (BackData){
			var reValueStr=BackData.split("");
			//检查资料是否已经存在，存在则提示;
			var CheckRow=0;
			for(var i=1;i<13;i++){
				if(eval("document.form1.SID"+i).value==reValueStr[2]){
					CheckRow++;
					}
				}
			if(CheckRow>1){
				alert("该流水号已在列表");
				eval("document.form1.CID"+textId).value="";
				eval("document.form1.CID"+textId).focus();
				return false;
				}
			else{
				document.form1.BuyerId.value=reValueStr[4];
				document.form1.CompanyId.value=reValueStr[6];
				document.form1.Forshort.value=reValueStr[7];
				eval("document.form1.SName"+textId).value=reValueStr[3];
				eval("document.form1.SID"+textId).value=reValueStr[2];
				eval("document.form1.QTY"+textId).value=reValueStr[1];
				}
			}
		else{
			alert("流水号出错，读不到未收货资料!");
			eval("document.form1.CID"+textId).value="";
			eval("document.form1.SName"+textId).value="";
			eval("document.form1.SID"+textId).value="";
			eval("document.form1.QTY"+textId).value="";
			}
		}
	else{//如果流水号为空，则清空相应资料
		eval("document.form1.CID"+textId).value="";
		eval("document.form1.SName"+textId).value="";
		eval("document.form1.SID"+textId).value="";
		eval("document.form1.QTY"+textId).value="";
		}
	}
function ViewName(textId){	//点击行号时，在信息提示框显示配件名称
	var SName=eval("document.form1.SName"+textId).value;
	if(SName!=""){
		document.getElementById("ViewName").innerHTML=textId+":"+SName;
		}
	else{
		document.getElementById("ViewName").innerHTML="&nbsp;";
		}
	}
function showMaskDiv(){		//待机或登录时，显示遮罩对话框,数据初始化
	//清除上次登记信息
	//检查是否有选取记录
	document.getElementById('divShadow').style.display='block';
	divPageMask.style.width = document.body.scrollWidth;
	divPageMask.style.height = document.body.scrollHeight;
	document.getElementById('divPageMask').style.display='block';
	//初始化设计
	document.getElementById("Name").innerHTML="";
	document.form1.Operator.value="";
	document.form1.LoginN.value="";
	document.all['LoginN'].focus();				//获得焦点
	}

function closeMaskDiv(){	//隐藏遮罩对话框,检查输入的操作员帐号，如果正确则隐藏对话框，并设置操作员信息，注：操作员ID可不要；如果没有，则返回对话框
	var num=Math.random();
	var LoginN=document.form1.LoginN.value;
	BackData=window.showModalDialog("ck_input_login.php?r="+num+"&LoginN="+LoginN,"BackData","dialogTop =900px;dialogLeft =900px;dialogHeight =-10px;dialogWidth=-10px;scroll=no");
	//流水号，未收数量，配件ID，配件名称，采购ID，采购名称，供应商ID，供应商名称
	if (BackData){
		var reValueStr=BackData.split("");
		var NameTemp=reValueStr[1];
		var NumberTemp=reValueStr[0];
		if(NameTemp!=""){
			document.getElementById('divShadow').style.display='none';
			document.getElementById('divPageMask').style.display='none';
			document.form1.Operator.value=NumberTemp;
			document.getElementById("Name").innerHTML=NameTemp;
			}
		else{
			document.form1.LoginN.value="";
			document.form1.LoginN.focus();
			return false;
			}
		}
	else{
		document.form1.LoginN.value="";
		document.form1.LoginN.focus();
		return false;
		}
	}
if(document.form1.Operator.value==""){//如果操作员为空时，则显示对话框需做登录操作，如果已登录则忽略
	showMaskDiv();
	}
</script>
