<?php 
//代码、数据共享-EWEN 2012-09-17
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新门禁指令");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Name,OpenKeys,CloseKeys FROM $DataPublic.accessguard_order WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
$OpenKeys=$upData["OpenKeys"];
$CloseKeys=$upData["CloseKeys"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../Admin/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
		<tr>
            <td width="100"  align="right" scope="col">名称</td>
            <td scope="col"><input name="PostValues[]" type="text" id="PostValues[]" style="width:380px" value="<?php  echo $Name?>" maxlength="20" dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围">
            </td>
          </tr>
          <tr>
            <td width="100" height="31" align="right" scope="col">开门指令</td>
            <td scope="col"><input name="PostValues[]" type="text" id="PostValues[]" style="width:380px" value="<?php  echo $OpenKeys?>" DataType="Require" Msg="没有填写"></td>
           </tr>
           <tr>
            <td width="100" height="31" align="right" scope="col">关门指令</td>
            <td scope="col"><input name="PostValues[]" type="text" id="PostValues[]" style="width:380px" value="<?php  echo $CloseKeys?>" DataType="Require" Msg="没有填写"></td>
           </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../Admin/subprogram/add_model_b.php";
?>