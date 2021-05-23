<?php 
//EWEN 2013-04-19 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新非BOM采购员");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT A.Remark,B.Name 
										FROM $DataPublic.nonbom3_buyer A
										LEFT JOIN $DataPublic.staffmain B ON B.Number=A.BuyerId WHERE A.Id='$Id'",$link_id));
$Name=$upData["Name"];
$Remark=$upData["Remark"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
		<table width="760" border="0" align="center" cellspacing="5">
        	<tr>
          		<td width="150" height="40" align="right" scope="col">采购员</td>
          		<td scope="col"><?php  echo $Name?></td>
        	</tr>
            <tr>
          		<td height="40" align="right" valign="top" scope="col">备注</td>
          		<td scope="col"><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" msg="未填写或格式不对"><?php  echo $Remark?></textarea></td>
        	</tr>
      	</table>
        </td></tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>