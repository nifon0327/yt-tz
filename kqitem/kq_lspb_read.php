<?php 
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=500;
ChangeWtitle("$SubCompany 临时班次设定");
$funFrom="kq_lspb";
$nowWebPage=$funFrom."_read";
$sumCols="";		//求和列
$Th_Col="选项|40|序号|40|员工ID|60|员工姓名|60|临时签到时间|140|临时签退时间|140|计迟到时间<bt>(分钟)|80|计早退时间<bt>(分钟)|80|中途休息<br>(分钟)|60|时间段|60|工时|40|操作员|50";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,2,3,4,7,8";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";
	
	$date_Result = mysql_query("SELECT DATE_FORMAT(InTime,'%Y-%m') AS Month FROM $DataIn.kqlspbb WHERE 1 group by DATE_FORMAT(InTime,'%Y-%m') order by InTime DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and DATE_FORMAT(L.InTime,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
			echo"</select>&nbsp;";
		}	
		$FormalSign=$FormalSign==""?0:$FormalSign;
		$selStr="selFlag" . $FormalSign;
		$$selStr="selected";
		echo"<select name='FormalSign' id='FormalSign' onchange='RefreshPage(\"$nowWebPage\")'>
		     <option value='0' $selFlag0>全部</option>
			 <option value='1' $selFlag1>正式工</option>
			 <option value='2' $selFlag2>试用期</option>";
		echo "</select>&nbsp;";
		if($FormalSign>0)$SearchRows.=" AND M.FormalSign='$FormalSign'"; 	
	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",1);
$mySql="SELECT L.Id,L.Number,L.InTime,L.OutTime,L.InLate,L.OutEarly,L.TimeType,L.RestTime,L.Locks,L.Operator,M.Name 
FROM $DataIn.kqlspbb L
LEFT JOIN $DataPublic.staffmain M ON L.Number=M.Number
WHERE 1 $SearchRows order by L.InTime DESC,L.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Name=$myRow["Name"];
		$Number=$myRow["Number"];		
		$InTime=$myRow["InTime"];
		$MonthTemp=substr($InTime,0,7);
		$OutTime=$myRow["OutTime"];
		$InLate=$myRow["InLate"];
		$OutEarly=$myRow["OutEarly"];
		$TimeType=$myRow["TimeType"];
		$RestTime=$myRow["RestTime"];
		$Locks=$myRow["Locks"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$TimeType=$TimeType==0?"夜班":"日班";
		$GTime=sprintf("%.1f",abs(strtotime($OutTime)-strtotime($InTime)-$RestTime*60)/3600);//向上取整
		$GTime=$GTime*1;		
		$LockRemark="";
		$checkMonth=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Month='$MonthTemp' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
		if($checkRow = mysql_fetch_array($checkMonth)){
			$LockRemark="该月考勤统计已生成,禁止修改.";
			}
		$ValueArray=array(
			array(0=>$Number,1=>"align='center'"),
			array(0=>$Name, 1=>"align='center'"),
			array(0=>$InTime, 1=>"align='center'"),
			array(0=>$OutTime,1=>"align='center'"),
			array(0=>$InLate,1=>"align='center'"),
			array(0=>$OutEarly,1=>"align='center'"),
			array(0=>$RestTime, 1=>"align='center'"),
			array(0=>$TimeType,1=>"align='center'"),
			array(0=>$GTime, 1=>"align='center'"),
			array(0=>$Operator, 1=>"align='center'")
			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
		}while ($myRow = mysql_fetch_array($myResult));
	}
else{
	noRowInfo($tableWidth);
	}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>