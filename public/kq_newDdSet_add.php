<?php
	
	include "../model/modelhead.php";
	//步骤2：
	ChangeWtitle("$SubCompany 新增调班记录");//需处理
	$nowWebPage =$funFrom."_add";	
	$toWebPage  =$funFrom."_save";	
	$_SESSION["nowWebPage"]=$nowWebPage; 
	$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
	//步骤3：
	$tableWidth=850;$tableMenuS=500;
	include "../model/subprogram/add_model_t.php";
?>

	<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
		<table width="600" border="0" align="center" cellspacing="0">
			<tr>
				<td width="104" align="right">原工作日</td>
				<td width="492"><input name="GDate" type="text" id="GDate" size="30" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly>
				</td>
			</tr>
			<tr>
				<td width="104" align="right">原工作日时间</td>
				<td width="492"><input name="GTime" type="text" id="GTime" size="30" maxlength="11">格式(08:00-12:00)</td>
			</tr>
			<tr>
				<td align="right">原休息日</td>
				<td><input name="XDate" type="text" id="XDate" size="30" maxlength="10" onfocus="WdatePicker()" readonly>
				</td>
			</tr>
			<tr>
				<td align="right">原休息日时间</td>
				<td><input name="XTime" type="text" id="XTime" size="30" maxlength="11">格式(08:00-12:00)</td>
			</tr>
      <tr>
	</table></td></tr></table>
	
<?php 
	//步骤5：
	include "../model/subprogram/add_model_b.php";
?>