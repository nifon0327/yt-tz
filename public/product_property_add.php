<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 新增产品购买属性");//需处理
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
            <td width="100" height="31" align="right" scope="col">属性名称</td>
            <td scope="col"><input name="Name" type="text" id="Name" style="width:380px;" maxlength="20" title="必选项,在20个汉字内." dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围"></td></tr>
            </tr>

		<tr>
            <td align="right" valign="top">参数值</td>
            <td><input id="pValue" name="pValue" value="0.00" type="text" style="width: 380px;" dataType="Double" Msg="未填写或格式不对"></td>
          </tr>
		
          <tr>
            <td align="right" valign="top">备 &nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" style="width:380px;" rows="6" id="Remark"></textarea></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>