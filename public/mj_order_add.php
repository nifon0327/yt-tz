<?php 
//代码、数据共享-EWEN 2012-09-17
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增门禁指令");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">
		<tr>
            <td width="100"  align="right" scope="col">名称</td>
            <td scope="col"><input name="PostValues[]" type="text" id="PostValues[]" style="width:380px" maxlength="20" dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围">
            </td>
          </tr>
          <tr>
            <td width="100" height="31" align="right" scope="col">开门指令</td>
            <td scope="col"><input name="PostValues[]" type="text" id="PostValues[]" style="width:380px" DataType="Require" Msg="没有填写"></td>
           </tr>
           <tr>
            <td width="100" height="31" align="right" scope="col">关门指令</td>
            <td scope="col"><input name="PostValues[]" type="text" id="PostValues[]" style="width:380px" DataType="Require" Msg="没有填写"></td>
           </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>