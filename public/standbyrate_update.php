<?php 
//电信---yang 20120801
//代码、数据共享-EWEN 2012-08-19
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新配件备品率");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.standbyrate B WHERE B.Id=$Id ORDER BY B.Id",$link_id));
$uName=$upData["uName"];
$Rate1=$upData["Rate1"];
$RateA=$upData["RateA"];
$RateB=$upData["RateB"];
$Remark=$upData["Remark"];
$RateC=$upData["RateC"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr>
         <td width="150" height="30" align="right" valign="middle" class='A0010'>备品率名称：</td>
    	<td class='A0001'><input name="uName" type="text"  id="uName" style="width:380px;" value="<?php  echo $uName?>" dataType="Require"  Msg="不能空"></td>
	</tr>
	<tr>
         <td width="150" height="30" align="right" valign="middle" class='A0010'>0~999：</td>
    	<td class='A0001'><input name="Rate1" type="text"  id="Rate1" style="width:380px;" dataType="Currency"  value="<?php  echo $Rate1?>" Msg="RateA错误"></td>
	</tr>
	<tr>
         <td width="150" height="30" align="right" valign="middle" class='A0010'>1000~2000：</td>
    	<td class='A0001'><input name="RateA" type="text"  id="RateA" style="width:380px;" dataType="Currency"  value="<?php  echo $RateA?>" Msg="RateA错误"></td>
	</tr>
    	<tr>
         <td width="150" height="30" align="right" valign="middle" class='A0010'>2001~4999：</td>
    	<td class='A0001'><input name="RateB" type="text"  id="RateB" style="width:380px;" dataType="Currency" value="<?php  echo $RateB?>"  Msg="RateB错误"></td>
	</tr>
    	<tr>
         <td width="150" height="30" align="right" valign="middle" class='A0010'>5000以上：</td>
    	<td class='A0001'><input name="RateC" type="text"  id="RateC" style="width:380px;" dataType="Currency" value="<?php  echo $RateC?>"  Msg="RateC错误"></td>
	</tr>
    <tr>
    	<td width="150" height="30" align="right" valign="middle" class='A0010'>备注：</td>
	 <td valign="middle" class='A0001'><input name="Remark" type="text" id="Remark" value="<?php  echo $Remark?>"  style="width:380px;"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>