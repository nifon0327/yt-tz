<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增助学小孩年级");//需处理
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
			<td width="137" height="40" valign="middle" scope="col" align="right">年&nbsp;&nbsp;&nbsp;&nbsp;级</td>
			<td valign="middle" scope="col"><input name="Name" type="text" id="Name" style="width:380px;" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出2-30个字节的范围" title="必填项,2-30个字节的范围">
			</td>
		</tr>

	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>