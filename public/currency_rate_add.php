<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增每月汇率设置");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="5">
			<tr>
				<td align="right" scope="col">所属月份</td>
				<td width="660" scope="col"><input name="Month" type="text" id="Month" style="width:180px;" value='<?php echo date('Y-m') ?>' DataType="Require"  Msg="未填写" onfocus="WdatePicker({dateFmt: 'yyyy-MM'})" maxlength='7' readonly></td>
			</tr> 
		   </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>