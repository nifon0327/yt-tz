<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新财务基本参数");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT B.ValueCode,B.Remark,B.Value FROM $DataPublic.cw3_basevalue B WHERE B.Id=$Id ORDER BY B.Id",$link_id));
$ValueCode=$upData["ValueCode"];
$Remark=$upData["Remark"];
$Value=$upData["Value"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
    	<td width="150" height="35" align="right" valign="middle" class='A0010'><p>参数代码：<br> 
      </td>
	    <td valign="middle" class='A0001'>&nbsp;<?php  echo $ValueCode?>
		</td>
    </tr>
    <tr>
    	<td height="43" valign="middle" class='A0010' align="right">参数说明：</td>
	    <td valign="middle" class='A0001'><input name="Remark" type="text" id="Remark" title="必填项" value="<?php  echo $Remark?>" style="width:380px;" maxlength="30" DataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="47" valign="middle" class='A0010' align="right">参 数 值：</td>
      <td valign="middle" class='A0001'><input name="Value" type="text" id="Value" title="必填项,数值范围1-30.且必须不少于最低等级" value="<?php  echo $Value?>" style="width:380px;" maxlength="12" DataType="Currency" msg="未填写或数值不对"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>