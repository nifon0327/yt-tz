<?php 
//电信-ZX  2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增工作模板文件");//需处理
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
<table width="750" id="NoteTable">
	<tr>
	  <td width="101" height="31" align="right">模板说明</td>
    <td width="637" height="31"><input name="Note" type="text" id="Note" value="" style="width:380px;" datatype="LimitB" min="2" max="30"  msg="必须在2-30个字节之内" title="必填项,2-30个字节内"></td>
	</tr>
	<tr>
	  <td height="37" align="right">模板文件</td>
    <td height="37"><input name="Attached" type="file" id="Attached" style="width:380px;" DataType="Filter" require="true"  Accept="jpg,gif,png,rar,zip,pdf,ai,psd,eps,titf,doc,xsl" Msg="文件格式不对,请重选" Row="1" Cel="1"></td>
	</tr>
</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>