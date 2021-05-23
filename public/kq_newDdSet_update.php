<?php

	//电信-EWEN
	include "../model/modelhead.php";
	//步骤2：
	ChangeWtitle("$SubCompany 更新排班设定资料");//需处理
	$fromWebPage=$funFrom."_read";		
	$nowWebPage =$funFrom."_update";	
	$toWebPage  =$funFrom."_updated";	
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤3：//需处理
	$upData =mysql_fetch_array(mysql_query("SELECT D.GDate,D.GTime,D.XDate,D.XTime,D.Operator FROM $DtaIn.kq_rqddnew D 
 	WHERE D.Id=$Id LIMIT 1",$link_id));

	$gDate = $upData["GDate"];
	$gTime = $upData["GTime"];
	$xDate = ($upData["XDate"]=="")?"待定":$upData["XDate"];
	$xTime = ($upData["XTime"]=="")?"待定":$upData["XTime"];
	$operator = $upData["Operator"];

	$tableWidth=850;$tableMenuS=500;
	include "../model/subprogram/add_model_t.php";
	$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
?>

	<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
		<table width="600" border="0" align="center" cellspacing="0">
			<tr>
				<td width="104" align="right">原工作日</td>
				<td width="492"><input name="GDate" type="text" id="GDate" size="30" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" value="<?php echo $gDate?>" readonly>
				</td>
			</tr>
			<tr>
				<td width="104" align="right">原工作日时间</td>
				<td width="492"><input name="GTime" type="text" id="GTime" size="30" maxlength="11" value="<?php echo $gTime?>">格式(08:00-12:00)</td>
			</tr>
			<tr>
				<td align="right">原休息日</td>
				<td><input name="XDate" type="text" id="XDate" size="30" maxlength="10" onfocus="WdatePicker()" value="<?php echo $xDate?>" readonly>
				</td>
			</tr>
			<tr>
				<td align="right">原休息日时间</td>
				<td><input name="XTime" type="text" id="XTime" size="30" maxlength="11" value="<?php echo $xTime?>">格式(08:00-12:00)</td>
			</tr>
      <tr>
	</table></td></tr></table>

	<?php 
	//步骤5：
	include "../model/subprogram/add_model_b.php";
	?>



