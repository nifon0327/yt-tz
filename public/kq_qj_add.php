<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增请假记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="600" border="0" align="center" cellspacing="5">
		<tr>
          <td height="27" align="right" scope="col">员 工 ID：</td>
            <td scope="col">          
			<input name="Number" type="text" id="Number" size="38" onclick="SearchRecord('staff','<?php  echo $funFrom?>',1,2)" title="必选项，点击在查询窗口选取" DataType="Require" Msg="没有选取用户" readonly></td>
		</tr>
			<tr>
			  <td height="11" align="right" scope="col">假期类别：</td>
				<td scope="col">
				<select name="Type" id="Type" style="width: 220px;" dataType="Require"  msg="未选择假期类别">
				<option value="" selected>请选择</option>
				<?php 
				$qjtypeSql =  mysql_query("SELECT Id,Name FROM $DataPublic.qjtype WHERE Estate=1  ORDER BY Id",$link_id);
				while( $qjtypeRow = mysql_fetch_array($qjtypeSql)){
					$Id=$qjtypeRow["Id"];
					$Name=$qjtypeRow["Name"];
					echo "<option value='$Id'>$Id - $Name</option>";
					} 
				?>
				</select></td>
			</tr>
			<tr>
			  <td height="16" align="right" scope="col">班次类型：</td>
				<td scope="col">
				<select name="bcType" id="bcType" style="width: 220px;" dataType="Require"  msg="未选择班次类型">
				<option value="" selected>请选择</option>
				<option value="0">默认班次</option>
				<option value="1">临时班次</option>
			    </select></td>
			</tr>          
          <tr>
            <td height="9" align="right">起始日期：</td>
            <td width="520"><input name="StartDate" type="text" id="StartDate" value="<?php  echo date("Y-m-d")?>" size="38" maxlength="10" onfocus="WdatePicker()" dataType="Date" formqt="ymd" Msg="未填或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="13" align="right">时间：</td>
            <td><input name="StartTime" type="text" id="StartTime" value="08:00" size="38" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
          <tr>
            <td height="9" align="right">结束日期：</td>
            <td><input name="EndDate" type="text" id="EndDate" value="<?php  echo date("Y-m-d")?>" size="38" maxlength="10" onfocus="WdatePicker()" dataType="Date" formqt="ymd" Msg="未填或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="13" align="right">时间：</td>
            <td><input name="EndTime" type="text" id="EndTime" value="17:00" size="38" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
          <tr>
            <td height="37" align="right" valign="top">请假原因：</td>
            <td><textarea name="Reason" cols="50" rows="5" id="Reason" dataType="Require" Msg="未填写请假原因"></textarea></td>
          </tr>
          <tr>
            <td height="37" align="right" valign="top"><div align="right">
              <input name="uType" type="hidden" id="uType">
            注意事项：</div></td>
            <td>1、起始时间必须在签卡时间范围内(当天上班与下班之间)<br>
              2、请假时间以0.5小时为单位，向上取整。如实际请假时间4.1小时，将计为4.5小时。</td>
          </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>