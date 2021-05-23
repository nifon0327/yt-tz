<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新折旧期资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.nonbom6_depreciation WHERE Id='$Id' LIMIT 1",$link_id));
$Depreciation=$upData["Depreciation"];
$dValues=$upData["dValues"];
$Remark=$upData["Remark"];
$ListName=$upData["ListName"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="750" height="140" border="0" align="center" cellspacing="5">
        <tr>
          <td height="30" valign="middle" scope="col" align="right">折旧期</td>
          <td valign="middle" scope="col"><input name="Depreciation" type="text" id="Depreciation" value="<?php  echo $Depreciation?>" style="width:380px" maxlength="30" datatype="LimitB" max="30" min="1" msg="没有填写或超出2-30个字节的范围" title="必填项,2-30个字节的范围">
          </td>
        </tr>
        <tr>
			<td  height="30" valign="middle" scope="col" align="right">显示名称</td>
			<td valign="middle" scope="col"><input name="ListName" type="text" id="ListName" style="width:380px;"  value="<?php  echo $ListName?>" dataType="Require"  msg="未填写">
			</td>
		</tr>
		<tr>
			<td  height="30" valign="middle" scope="col" align="right">Keys</td>
			<td valign="middle" scope="col"><input name="dValues" type="text" id="dValues" value="<?php echo $dValues?>" style="width:380px;"  dataType="Number"  msg="天数不正确">
			</td>
		</tr>
		
		<tr>
		  <td height="30" valign="middle" scope="col" align="right">备注</td>
		  <td scope="col"><textarea name="Remark" cols="51" rows="3" id="Remark"  dataType="Require" Msg="未填写说明"><?php echo $Remark?></textarea></td>
		</tr>
              </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>