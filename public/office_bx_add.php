<?php 
//步骤1 $DataPublic.qjtype 二合一已更新
//电信-joseph
include "../model/modelhead.php";
include "../model/kq_YearHolday.php";
//步骤2：
ChangeWtitle("$SubCompany 新增补休记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$ComeInResult=mysql_fetch_array(mysql_query("SELECT ComeIn FROM $DataPublic.staffmain 
WHERE Number='$Login_P_Number'",$link_id));
$ComeIn=$ComeInResult["ComeIn"];
$startYear=date("Y-m-d",strtotime("1 year",strtotime($ComeIn)));
$NowToday=date("Y-m-d");


$chooseYear=date("Y");
$NextYear=$chooseYear+1;
$LastYear=$chooseYear-1;
$mySql="SELECT M.Number,M.Name,M.BranchId,M.JobId,M.ComeIn,M.KqSign,B.Name AS Branch,J.Name AS Job,G.GroupName
	FROM $DataPublic.staffmain M
    LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
	LEFT JOIN $DataPublic.branchdata B ON M.BranchId=B.Id
	LEFT JOIN $DataPublic.jobdata J ON M.JobId=J.Id
	WHERE 1 AND M.Estate=1 AND M.Number='$Login_P_Number'";
$myResult = mysql_query($mySql."",$link_id);
if($myRow = mysql_fetch_array($myResult)){
		$KqSign=$myRow["KqSign"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$ComeIn=$myRow["ComeIn"];
		$GroupName=$myRow["GroupName"];
			
		//$AnnualLeave1=intval($AnnualLeave/8);
		//$LastDay=$AnnualLeave1-$qjAllDays;
	}
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="600" border="0" align="center" cellspacing="5">
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
            <td height="13" align="right">计算方式：</td>
            <td><select id="CalculateType" name="CalculateType">
	            <option value="0" selected>按小时计算</option>
	            <option value="1">按天计算</option>
            </select></td>
          </tr>
          <tr>
            <td height="37" align="right" valign="top">补休原因：</td>
            <td><textarea name="note" cols="50" rows="5" id="note" dataType="Require" Msg="未填写请假原因"></textarea></td>
          </tr>
          <tr>
            <td height="37" align="right" valign="top"><div align="right">
              <input name="uType" type="hidden" id="uType">
            注意事项：</div></td>
            <td>补休时间以0.5小时为单位，向上取整。如实际请假时间4.1小时，将计为4小时。</td>
          </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
