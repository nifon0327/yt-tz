<?php 
//电信-ZX  2012-08-01
//代码、数据库共享-EWEN
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 更新模板文件");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.workmodelfile WHERE Id=$Id ORDER BY Id",$link_id));
$Note=$upData["Note"];
$Attached=$upData["Attached"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,OldFile,$Attached";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
<table width="750">
	<tr>
    	<td colspan="2" >&nbsp;
		</td>
	</tr>
	<tr>
	  <td width="121" height="31" align="right">模板说明</td>
    <td width="617" height="31"><input name="Note" type="text" id="Note" style="width:380px;" value="<?php  echo $Note?>" datatype="LimitB" min="3" max="30"  msg="必须在2-30个字节之内" title="必填项,2-30个字节内"></td>
	</tr>
	<tr>
	  <td height="37" align="right">模板文件</td>
    <td height="37"><input name="Attached" type="file" id="Attached" style="width:380px;" DataType="Filter" require="true"  Accept="jpg,gif,png,rar,zip,pdf,ai,psd,eps,titf,doc,xsl" Msg="文件格式不对,请重选" Row="1" Cel="1"></td>
	</tr>
	<?php 
	if($Attached!=""){
	echo"<tr>
	  <td height='21'>&nbsp;</td>
	  <td height='21'><input name='oldAttached' type='checkbox' id='oldAttached' value='1'><label for='oldAttached'>删除已传附件</label></td>
	</tr>";
	}
	?>
</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>