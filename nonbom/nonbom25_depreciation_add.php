<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增折旧期");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" height="95" border="0" align="center" cellspacing="5">
		<tr>
			<td width="137" height="30" valign="middle" scope="col" align="right">折旧期</td>
			<td valign="middle" scope="col"><input name="Depreciation" type="text" id="Depreciation" style="width:380px;" maxlength="30" datatype="LimitB" max="30" min="1" msg="没有填写或超出2-30个字节的范围" title="必填项,2-30个字节的范围">
			</td>
		</tr>
		<tr>
			<td  height="30" valign="middle" scope="col" align="right">显示名称</td>
			<td valign="middle" scope="col"><input name="ListName" type="text" id="ListName" style="width:380px;"  dataType="Require"  msg="未填写">
			</td>
		</tr>
		<tr>
			<td  height="30" valign="middle" scope="col" align="right">Keys</td>
			<td valign="middle" scope="col"><input name="dValues" type="text" id="dValues" style="width:380px;"  dataType="Number"  msg="天数不正确">
			</td>
		</tr>

<tr>
		  <td height="30" valign="middle" scope="col" align="right">备注</td>
		  <td scope="col"><textarea name="Remark" cols="51" rows="3" id="Remark" dataType="Require" Msg="未填写说明"></textarea></td>
		</tr>
		
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>