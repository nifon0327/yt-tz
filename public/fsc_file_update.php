<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：//需处理
ChangeWtitle("$SubCompany 更新FSC资料");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT * FROM $DataPublic.cg3_fscdata Where Id=$Id",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	$Id=$upRow["Id"];
	$Remark=$upRow["Remark"];
	$oldAttached=$upRow["Attached"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page,oldAttached,$oldAttached";

//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
<table width="800" border="0" align="center" cellspacing="5" id="NoteTable">
        <tr>
            <td width="149" align="right" valign="top">FSC资料备注</td>
            <td width="632"><textarea name="Remark" style="width:380px" rows="4" id="Remark" dataType="Require"  msg="未填写"><?php  echo $Remark?></textarea></td>
        </tr>
        <tr>
          <td height="35" align="right" valign="top">FSC资料源文件</td>
          <td><input name="Attached" type="file" id="Attached" style="width:380px" DataType="Filter" Accept="eml,pdf" Msg="文件格式不对,请重选" Row="1" Cel="1"></td>
        </tr>
	</table>
	</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>