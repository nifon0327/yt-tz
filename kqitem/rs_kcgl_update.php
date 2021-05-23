<?php   
//电信-ZX  2012-08-01
//已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新扣工龄记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT S.Month,S.Months,S.Remark,M.Name 
FROM $DataPublic.rs_kcgl S,$DataPublic.staffmain M 
WHERE 1 AND S.Number=M.Number AND S.Id=$Id LIMIT 1",$link_id));
$Months=$upData["Months"];
$Month=$upData["Month"];
$Name=$upData["Name"];
$Remark=$upData["Remark"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
//步骤5：//需处理

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
    	<td width="150" height="36" align="right" valign="middle" class='A0010'><p>员工姓名：<br> 
      </td>
	    <td valign="middle" class='A0001'><?php  echo $Name?>
		</td>
    </tr>
    <tr>
      <td height="29" align="right" valign="middle" class='A0010'>起效月份：</td>
      <td valign="middle" class='A0001'><input name="Month" type="text" id="Month" size="77" maxlength="7" dataType="Month" msg="月份格式不对"></td>
    </tr>
    <tr>
      <td height="29" align="right" valign="middle" class='A0010'>扣工龄月份数：</td>
      <td valign="middle" class='A0001'><input name="Months" type="text" id="Months" size="77" maxlength="6" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="47" valign="top" class='A0010' align="right">扣工龄原因：</td>
      <td valign="top" class='A0001'><textarea name="Remark" cols="50" rows="6" id="Remark" dataType="Require" Msg="未填写"><?php  echo $Remark?></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>