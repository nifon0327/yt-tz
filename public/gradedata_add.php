<?php 
//电信-EWEN
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增职位等级范围");//需处理
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
	<tr>
    	<td colspan="2" class='A0011'><input name="Id" type="hidden" id="Id" value=""><input name="AttachedName" type="hidden" id="AttachedName" value="<?php  echo $BulletinRows["Attached"]?>"></td>
	</tr>
    <tr>
    	<td width="150" height="35" align="right" valign="middle" class='A0010'><p>职&nbsp;&nbsp;&nbsp;&nbsp;位：<br> 
      </td>
	    <td valign="middle" class='A0001'>
			<select name="JobId" id="JobId" style="width:380px;">
			<?php 
			$checkSql = "SELECT Id,Name FROM $DataPublic.jobdata WHERE Id NOT IN(SELECT JobId FROM $DataPublic.gradedata ORDER BY Id) ORDER BY Id";
			$checkResult = mysql_query($checkSql); 
			while( $checkRow = mysql_fetch_array($checkResult)){
				$Id=$checkRow["Id"];
				$Name=$checkRow["Name"];					
				echo "<option value='$Id'>$Name</option>";
				} 
			?>		 
		  </select>
		</td>
    </tr>
    <tr>
    	<td height="43" valign="middle" class='A0010' align="right">最低等级：</td>
	    <td valign="middle" class='A0001'><input name="Low" type="text" id="Low" style="width:380px;" title="必选项,数值范围1-30." dataType="Range" msg="等级范围必须在1~30之间" min="0" max="31"></td>
    </tr>
    <tr>
      <td height="47" valign="middle" class='A0010' align="right">最高等级：</td>
      <td valign="middle" class='A0001'><input name="Hight" type="text" id="Hight" style="width:380px;" title="必选项,数值范围1-30.且必须不少于最低等级" DataType="Range" min="0" max="31" msg="不满足数值条件"></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>