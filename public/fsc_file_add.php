<?php 
//电信-EWEN
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 添加FSC资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
	<table width="800" border="0" align="center" cellspacing="5" id="NoteTable">
        <tr>
            <td width="149" align="right" valign="top">FSC资料备注</td>
            <td width="632"><textarea name="Remark" style="width:380px" rows="4" id="Remark" dataType="Require"  msg="未填写"></textarea></td>
        </tr>
        <tr>
          <td height="35" align="right">FSC资料源文件</td>
          <td><input name="Attached" type="file" id="Attached" style="width:380px" DataType="Filter" Accept="eml,pdf" Msg="文件格式不对,请重选" Row="1" Cel="1"></td>
        </tr>
	</table>
	</td>
</tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>