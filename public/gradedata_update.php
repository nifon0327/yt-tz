<?php 
//电信-EWEN
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新职位等级范围");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT G.Low,G.Hight,J.Name FROM $DataPublic.gradedata G LEFT JOIN $DataPublic.jobdata J ON G.JobId=J.Id WHERE G.Id=$Id order by J.Id",$link_id));
$Low=$upData["Low"];
$Hight=$upData["Hight"];
$Job=$upData["Name"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
    	<td width="150" height="35" valign="middle" class='A0010'><p align="right">职&nbsp;&nbsp;&nbsp;&nbsp;位：<br> 
      </td>
	    <td valign="middle" class='A0001'>&nbsp;<?php  echo $Job?>
		</td>
    </tr>
    <tr>
    	<td height="43" valign="middle" class='A0010' align="right">最低等级：</td>
	    <td valign="middle" class='A0001'><input name="Low" type="text" id="Low" style="width:380px;" value="<?php  echo $Low?>" title="必选项,数值范围1-30." dataType="Range" msg="等级范围必须在1~30之间" min="0" max="31"></td>
    </tr>
    <tr>
      <td height="47" valign="middle" class='A0010' align="right">最高等级：</td>
      <td valign="middle" class='A0001'><input name="Hight" type="text" id="Hight" style="width:380px;" value="<?php  echo $Hight?>" title="必选项,数值范围1-30.且必须不少于最低等级" DataType="Range" min="0" max="31" msg="不满足数值条件"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>