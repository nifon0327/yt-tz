<?php 
//电信-EWEN
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新等级津贴");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Grade,Subsidy FROM $DataPublic.gradesubsidy WHERE Id=$Id order by Id LIMIT 1",$link_id));
$Grade=$upData["Grade"];
$Subsidy=$upData["Subsidy"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
    	<td width="150" height="35" valign="middle" class='A0010' align="right">等&nbsp;&nbsp;&nbsp;&nbsp;级：</td>
	    <td valign="middle" class='A0001'><?php  echo $Grade?></td>
    </tr>
    <tr>
    	<td height="43" valign="middle" class='A0010' align="right">津&nbsp;&nbsp;&nbsp;&nbsp;贴：</td>
	    <td valign="middle" class='A0001'><input name="Subsidy" type="text" id="Subsidy" value="<?php  echo $Subsidy?>" style="width:380px;" title="必选项,数值范围0-5000." maxlength="4" dataType="Range" msg="等级范围必须在0~5000之间" min="-1" max="5001"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>