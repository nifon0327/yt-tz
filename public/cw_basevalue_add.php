<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增财务基本参数");//需处理
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
    	<td width="150" height="43" align="right" valign="middle" class='A0010'>参数说明：</td>
	    <td valign="middle" class='A0001'><input name="Remark" type="text" id="Remark" title="必填项" style="width:380px;" maxlength="30" DataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="47" valign="middle" class='A0010' align="right">参 数 值：</td>
      <td valign="middle" class='A0001'><input name="Value" type="text" id="Value" title="必填项,数值范围1-30.且必须不少于最低等级" style="width:380px;" maxlength="12" DataType="Currency" msg="未填写或数值不对"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>