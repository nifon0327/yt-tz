<?php 
//电信-ZX
//代码、数据库合并后共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新外出记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.info1_business WHERE Id='$Id'",$link_id));
$Businesser=$upData["Businesser"];
$Date=$upData["Date"];
$StartTime=$upData["StartTime"];
$EndTime=$upData["EndTime"]=="0000-00-00 00:00:00"?"":$upData["EndTime"];
$CarId=$upData["CarId"];
$sCourses=$upData["sCourses"];
$eCourses=$upData["eCourses"];
$Drivers=$upData["Drivers"];
$Remark=$upData["Remark"];
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="0">
      <tr>
        <td width="101" align="right">外出人员</td>
        <td><input name="Businesser" type="text" id="Businesser" value="<?php  echo $Businesser?>" style="width:380px" maxlength="8"></td>
      </tr>
      <tr>
        <td align="right">起始时间</td>
        <td><input name="StartTime" type="text" id="StartTime" value="<?php  echo $StartTime?>" style="width:380px" maxlength="19" dataType="Require"  msg="未填写" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'EndTime\');}'})" readonly>
        </td>
      </tr>
      <tr>
        <td align="right">结束时间</td>
        <td><input name="EndTime" type="text" id="EndTime" style="width:380px" value="<?php  echo $EndTime?>" maxlength="19" dataType="Require"  msg="未填写" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'StartTime\');}'})" readonly>
        </td>
      </tr>
      <tr>
        <td align="right">用车情况</td>
        <td><select name="CarId" id="CarId" style="width:380px">
           <?php 
           $CarSql=mysql_query("SELECT C.Id,C.carListNo,T.Name,T.Color 
		   FROM $DataPublic.cardata C
		   LEFT JOIN $DataPublic.cartype T ON T.Id=C.TypeId
		   WHERE C.Estate=1 AND C.Locks=0 ORDER BY T.Id,C.Id",$link_id);
		 if($CarRow=mysql_fetch_array($CarSql)){
			do{
				$Number=$CarRow["Id"];
				$Name=$CarRow["carListNo"];
				$TypeName=$CarRow["Name"];
				$Color=$CarRow["Color"];
				if($Number==$CarId){
					echo"<option value='$Number' style='color:$Color;font-weight: bold' selected>($TypeName)$Name</option>";
					}
				else{
					echo"<option value='$Number' style='color:$Color;font-weight: bold'>($TypeName)$Name</option>";
					}
				}while($CarRow=mysql_fetch_array($CarSql));
			}
		  ?>
          </select></td>
      </tr>
      <tr>
        <td align="right">司&nbsp;&nbsp;&nbsp;&nbsp;机</td>
        <td><select name="Drivers" id="Drivers" style="width:380px">
		<?php 
		if($Drivers<2){
			if($Drivers==0){
				echo"<option value='1'>自驾</option>";
				echo"<option value='0'  selected>其他人</option>";
				}
			else{
				echo"<option value='1' selected>自驾</option>";
				echo"<option value='0'>其他人</option>";
				}
			}
		else{
			echo"<option value='1'>自驾</option>";
			echo"<option value='0'>其他人</option>";
			}
		$CheckSql=mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1 AND JobId='11'",$link_id);
		if($CheckRow=mysql_fetch_array($CheckSql)){
			do{
				$Number=$CheckRow["Number"];
				$Name=$CheckRow["Name"];
				if($Number==$Drivers){
					echo"<option value='$Number' selected>$Name</option>";
					}
				else{
					echo"<option value='$Number'>$Name</option>";
					}
				}while($CheckRow=mysql_fetch_array($CheckSql));
			}
		?>
        </select></td>
      </tr>
      <tr>
        <td valign="top" align="right">外出说明</td>
        <td><textarea name="Remark" style="width:380px" rows="8" id="Remark" dataType="Require"  msg="未填写"><?php  echo $Remark?></textarea></td>
      </tr>
      <tr>
        <td align="right">起始里程</td>
        <td><input name="sCourses" type="text" id="sCourses" value="<?php  echo $sCourses?>" style="width:380px" require="false" maxlength="10" dataType="Number"  msg="未填写或格式不对"></td>
      </tr>
      <tr>
        <td align="right">结束里程</td>
        <td><input name="eCourses" type="text" id="eCourses" value="<?php  echo $eCourses?>" style="width:380px" require="false" maxlength="10" dataType="Number"  msg="未填写或格式不对"></td>
      </tr>
    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>