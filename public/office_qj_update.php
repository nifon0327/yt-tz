<?php 
//电信-joseph
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新请假资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT J.StartDate,J.EndDate,J.Reason,J.Proof,J.bcType,J.Date,M.Number,M.Name,J.Type
 FROM $DataPublic.kqqjsheet J 
 LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number 
 WHERE J.Id=$Id LIMIT 1",$link_id));
$Number=$upData["Number"];
$Name=$upData["Name"];
$StartDate=$upData["StartDate"];
$S_Date=date("Y-m-d",strtotime($StartDate));
$S_Time=date("H:i",strtotime($StartDate));
$EndDate=$upData["EndDate"];
$E_Date=date("Y-m-d",strtotime($EndDate));
$E_Time=date("H:i",strtotime($EndDate));
$Reason=$upData["Reason"];
$Type=$upData["Type"];
$bcType=$upData["bcType"];
$tempName="bcTypeSTR".strval($bcType);
$$tempName="selected";
$Proof=$upData["Proof"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
            <td width="149" height="21" align="right" scope="col">员工姓名：</td>
            <td width="582" scope="col"><?php  echo $Name?></td>
		</tr>
		<tr>
			<td height="23" align="right" scope="col">假期类别：</td>
			<td scope="col">
			<select name="Type" id="Type" style="width: 220px;">
			<?php 
				$qjtypeSql =  mysql_query("SELECT Id,Name FROM $DataPublic.qjtype WHERE Estate=1  AND Id IN (1,4,5)  ORDER BY Id",$link_id);
				while( $qjtypeRow = mysql_fetch_array($qjtypeSql)){
					$Id=$qjtypeRow["Id"];
					$Name=$qjtypeRow["Name"];
					if($Id==$Type){
						echo "<option value='$Id' selected>$Id - $Name</option>";
						}
					else{
						echo "<option value='$Id'>$Id - $Name</option>";
						}
					} 
			?>
            </select>
			</td>
		</tr>
		<tr>
			<td height="23" align="right" scope="col">班次类型：</td>
			<td scope="col">
			<select name="bcType" id="bcType" style="width: 220px;">
		  	<option value="0" <?php  echo $bcTypeSTR0?>>正常班次</option>
			</select>
			</td>
	    </tr>
          
          <tr>
            <td height="9" align="right">起始日期：</td>
            <td><input name="StartDate" type="text" id="StartDate" value="<?php  echo $S_Date?>" size="37" maxlength="10" onfocus="WdatePicker()" dataType="Date" formqt="ymd" Msg="未填写或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="13" align="right">时间：</td>
            <td><input name="StartTime" type="text" id="StartTime" value="<?php  echo $S_Time?>" size="37" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
          <tr>
            <td height="9" align="right">结束日期：</td>
            <td><input name="EndDate" type="text" id="EndDate" value="<?php  echo $E_Date?>" size="37" maxlength="10" onfocus="WdatePicker()" dataType="Date" formqt="ymd" Msg="未填写或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="13" align="right">时间：</td>
            <td><input name="EndTime" type="text" id="EndTime" value="<?php  echo $E_Time?>" size="37" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
          <tr>
            <td height="14" align="right" valign="top" >请假原因：</td>
            <td><textarea name="Reason" cols="50" rows="4" id="Reason" dataType="Require" Msg="未填写请假原因"><?php  echo $Reason?></textarea></td>
          </tr>
          <tr>
            <td height="4" align="right" valign="top">病历证明：</td>
            <td><input name="Proof" type="file" id="Proof" size="65" dataType="Filter" msg="非jpg文件格式" accept="jpg" Row="8" Cel="1"></td>
          </tr>
		  <?php 
          if($Proof==1){
          	echo"<tr><td height='9'>&nbsp;</td><td>
			  	<input type='checkbox' name='oldProof' id='oldProof' value='1'><LABEL for='oldProof'>删除已传病历证明</LABEL>			  	             
			  </td></tr>";
		  	}
		  ?>
          <tr>
            <td height="37" align="right" valign="top">注意事项：</td>
            <td>1、起始时间必须在签卡时间范围内(当天上班与下班之间)<br>
              2、请假时间以0.5小时为单位，向上取整。如实际请假时间4.1小时，将计为4.5小时。</td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>