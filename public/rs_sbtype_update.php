<?php 
//电信-ZX  2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新社保参数");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT B.Name,B.cAmount,B.mAmount,B.Remark FROM $DataIn.rs_sbtype B WHERE B.Id=$Id ORDER BY B.Id",$link_id));

$cAmount=$upData["cAmount"];
$mAmount=$upData["mAmount"];
$Remark=$upData["Remark"];
$Name=$upData["Name"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
         <td width="150" height="43" align="right" valign="middle" class='A0010'>社保类型：</td>
    	<td class='A0001'><input name="Name" type="text"  id="Name" style="width:380px;" value="<?php  echo $Name?>" maxlength="30"  DataType="Require" Msg="类型名称不能为空"></td>
	</tr>
    <tr>
    	 <td width="150" height="43" align="right" valign="middle" class='A0010'>公司缴费：</td>
	 <td valign="middle" class='A0001'><input name="cAmount" type="text" id="cAmount" value="<?php  echo $cAmount?>" style="width:380px;" maxlength="10" dataType="Currency" msg="未填写或格式不对"></td>
    </tr>
    <tr>
    	 <td width="150" height="43" align="right" valign="middle" class='A0010'>个人缴费：</td>
	 <td valign="middle" class='A0001'><input name="mAmount" type="text" id="mAmount"  value="<?php  echo $mAmount?>" style="width:380px;" maxlength="10" dataType="Currency" msg="未填写或格式不对"></td>
    </tr>
    <tr>
    	<td width="150" height="43" align="right" valign="middle" class='A0010'>备注：</td>
	 <td valign="middle" class='A0001'><input name="Remark" type="text" id="Remark"  value="<?php  echo $Remark?>" style="width:380px;"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>