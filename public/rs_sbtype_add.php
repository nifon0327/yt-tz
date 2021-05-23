<?php 
//电信-ZX  2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增社保类型");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
         <td width="150" height="43" align="right" valign="middle" class='A0010'>社保类型：</td>
    	<td class='A0001'><input name="Name" type="text"  id="Name" style="width:380px;" maxlength="30"  DataType="Require" Msg="类型名称不能为空"></td>
	</tr>
    <tr>
    	 <td width="150" height="43" align="right" valign="middle" class='A0010'>公司缴费：</td>
	 <td valign="middle" class='A0001'><input name="cAmount" type="text" id="cAmount"  style="width:380px;" maxlength="10" dataType="Currency" msg="未填写或格式不对"></td>
    </tr>
    <tr>
    	 <td width="150" height="43" align="right" valign="middle" class='A0010'>个人缴费：</td>
	 <td valign="middle" class='A0001'><input name="mAmount" type="text" id="mAmount"  style="width:380px;" maxlength="10" dataType="Currency" msg="未填写或格式不对"></td>
    </tr>
    <tr>
    	<td width="150" height="43" align="right" valign="middle" class='A0010'>备注：</td>
	 <td valign="middle" class='A0001'><input name="Remark" type="text" id="Remark"  style="width:380px;"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>