<?php 
//步骤1 $DataPublic.qjtype 二合一已更新
//电信-joseph
include "../model/modelhead.php";
include "../model/kq_YearHolday.php";
//步骤2：
ChangeWtitle("$SubCompany 新增请假记录");//需处理
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
		//入职当年
		$ComeInY=substr($ComeIn,0,4);
		//年份间隔:间隔在2以上的，全休，间隔1实计；间隔0无
		$ValueY=$chooseYear-$ComeInY;
				
		$DefaultLastM=$chooseYear."-12-01";
		$ThisEndDay=$chooseYear."-12-".(date("t",strtotime($DefaultLastM)));	//当年最后一天	
		$CountDays=date("z",strtotime($ThisEndDay));	//年假当年总天数
		
		
		//计算本年请假的时间(除年休)，超过15天以上的要扣除
		$sumQjTime=0;
		$qjTimeSql=mysql_query("SELECT StartDate,EndDate FROM $DataPublic.kqqjsheet WHERE Number=$Number  AND Type NOT IN (4,8) and (substring(StartDate,1,4)='$LastYear' OR substring(EndDate,1,4)='$LastYear')",$link_id);
		if($qjTimeRow=mysql_fetch_array($qjTimeSql)){//垮年处理
		    do{
		       $StartDate=$qjTimeRow["StartDate"];
		       $EndDate=$qjTimeRow["EndDate"];
			   $frist_Year=substr($StartDate,0,4);
			   $end_Year=substr($EndDate,0,4);
			   if($frist_Year<$LastYear)$StartDate=$LastYear."-01-01 08:00:00";
			   if($end_Year>$chooseYear)$EndDate=$chooseYear."-12-31 17:00:00";
			   $qjTime=abs(intval((strtotime($EndDate)-strtotime($StartDate))/3600/24));
			   $sumQjTime+=$qjTime;
		       }while($qjTimeRow=mysql_fetch_array($qjTimeSql));
		    }
			if($sumQjTime<15)$sumQjTime=0;//少于15天忽略。
			//echo $sumQjTime;
			if($ValueY>1){	//年份间隔在2以上的
				$inDays=$CountDays-$sumQjTime;
				$AnnualLeave=intval((5*8*$inDays)/$CountDays);
				if($ValueY>=10){
					$AnnualLeave=intval((10*8*$inDays)/$CountDays);
					}
				if($ValueY>=20){
					$AnnualLeave=intval((15*8*$inDays)/$CountDays);
					}					
				}
			else{
				if($ValueY==1){
					$inDays=abs(strtotime($ThisEndDay)-strtotime($ComeIn))/3600/24-$CountDays-$sumQjTime;
					$AnnualLeave=intval((5*8*$inDays)/$CountDays);
					}
				else{
					$AnnualLeave=0;
					$inDays=0;
					}
				}	
		$qjAllDays=HaveYearHolDayDays($Number,$chooseYear."-01-01 00:00:00",$chooseYear."-01-01 00:00:00",$DataIn,$DataPublic,$link_id)/8 ;		
		$AnnualLeave1=intval($AnnualLeave/8);
		$LastDay=$AnnualLeave1-$qjAllDays;
	}
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="600" border="0" align="center" cellspacing="5">
			<tr>
			  <td height="11" align="right" scope="col">假期类别：</td>
				<td scope="col">
				<select name="Type" id="Type" style="width: 220px;" onchange="ChangType()" dataType="Require"  msg="未选择假期类别">
				<option value="" selected>请选择</option>
				<?php 
				$qjtypeSql =  mysql_query("SELECT Id,Name FROM $DataPublic.qjtype WHERE Estate=1  AND Id IN (1,4,5) ORDER BY Id",$link_id);
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
				<option value="0" selected>默认班次</option>
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
<script language="javascript">
var NowToday="<?php  echo $NowToday?>";
var startYear="<?php  echo $startYear?>";
var LastDay="<?php  echo $LastDay?>";
function ChangType(){
    var qjType=document.getElementById("Type").value;
	   if(qjType=="")return false;
	   if(qjType==4){ 
	      if(startYear>=NowToday){
		      alert("还未到一年,不能请年休假!");
	          document.getElementById("Type").value="";
			  return false;}
		   if(LastDay<=0){
		      alert("可休年假为0,不能请年假");
	          document.getElementById("Type").value="";return false;}
			else alert("你还有"+ LastDay +"天年假可休");
	   }

      }

</script>