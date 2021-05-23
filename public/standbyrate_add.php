<?php 
//电信---yang 20120801
//代码、数据共享-EWEN 2012-08-19
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增备品率");
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
         <td width="150" height="30" align="right" valign="middle" class='A0010'>备品率名称：</td>
    	<td class='A0001'><input name="uName" type="text"  id="uName" style="width:380px;" dataType="Require"  Msg="不能空"></td>
	</tr>
	<tr>
         <td width="150" height="30" align="right" valign="middle" class='A0010'>0~999：</td>
    	<td class='A0001'><input name="Rate1" type="text"  id="Rate1" style="width:380px;" dataType="Currency"  Msg="Rate1错误"></td>
	</tr>
	<tr>
         <td width="150" height="30" align="right" valign="middle" class='A0010'>1000~2000：</td>
    	<td class='A0001'><input name="RateA" type="text"  id="RateA" style="width:380px;" dataType="Currency"  Msg="RateA错误"></td>
	</tr>
    	<tr>
         <td width="150" height="30" align="right" valign="middle" class='A0010'>2001~4999：</td>
    	<td class='A0001'><input name="RateB" type="text"  id="RateB" style="width:380px;" dataType="Currency"  Msg="RateB错误"></td>
	</tr>
    	<tr>
         <td width="150" height="30" align="right" valign="middle" class='A0010'>5000以上：</td>
    	<td class='A0001'><input name="RateC" type="text"  id="RateC" style="width:380px;" dataType="Currency"  Msg="RateC错误"></td>
	</tr>
    <tr>
    	<td width="150" height="30" align="right" valign="middle" class='A0010'>备注：</td>
	 <td valign="middle" class='A0001'><input name="Remark" type="text" id="Remark"  style="width:380px;"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>