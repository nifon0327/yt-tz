<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 每月汇率设置更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT M.Month,M.Rate,A.Name FROM $DataPublic.currencyrate M 
LEFT JOIN currencydata A ON A.Id=M.Currency WHERE M.Id='$Id'",$link_id));
$Month=$upData["Month"];
$Rate=$upData["Rate"];
$Name=$upData["Name"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Month,$Month,Name,$Name";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="544" border="0" align="center" cellspacing="5">
	   <tr>
			<td align="right" scope="col">所属月份</td>
			<td scope="col"><?php  echo $Month?></td>
		</tr>
	<tr>
			<td align="right" scope="col">货币名称</td>
			<td scope="col"><?php  echo $Name?></td>
		</tr>
		<tr>
			<td align="right" scope="col">汇率</td>
			<td scope="col"><input name="Rate" type="text" id="Rate" style="width:380px;" value="<?php  echo $Rate?>" title="必选项,至多可保留8位小数." dataType="Double" msg="没有填写或不合要求"></td>
		</tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>