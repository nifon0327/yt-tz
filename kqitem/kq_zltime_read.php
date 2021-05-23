<?php 
//电信-EWEN
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=400;
ChangeWtitle("$SubCompany 员工直落记录");
$funFrom="kq_zltime";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|日期|100|星期|60|员工ID|60|姓名|80|起始时间|100|结束时间|100|直落工时|80|操作员|80";
$Pagination=$Pagination==""?1:$Pagination;
$Page_Size = 200;
$ActioToS="1,2,3,4,7,8";
$sumCols=8;
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT DATE_FORMAT(Date,'%Y-%m') AS Month FROM $DataPublic.kqzltime WHERE 1 group by DATE_FORMAT(Date,'%Y-%m') order by Date DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
		echo"<select name='chooseMonth' id='chooseMonth' onchange='ResetPage(this.name)'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;
				}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="and DATE_FORMAT(Z.Date,'%Y-%m')='$dateValue'";
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
List_Title($Th_Col,"1",0);
$Darray=array (0=>"日",1=>"一",2=>'二',3=>"三",4=>"四",5=>"五",6=>"六");
$mySql="SELECT Z.Id,Z.Number,Z.Stime,Z.Etime,Z.Hours,Z.Date,Z.Locks,Z.Operator,M.Name
FROM $DataPublic.kqzltime Z
LEFT JOIN $DataPublic.staffmain M ON M.Number=Z.Number 
WHERE 1 $SearchRows  and M.cSign='$Login_cSign' order by Z.Date DESC,Z.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$Number=$myRow["Number"];
		$Stime=$myRow["Stime"];		
		$Etime=$myRow["Etime"];
		$Hours=$myRow["Hours"];
		$Name=$myRow["Name"];
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$MonthTemp=substr($Date,0,7);	
		//星期计算
		$weekTemp="星期".$Darray[date("w",strtotime($Date))];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$LockRemark="";
		$checkMonth=mysql_query("SELECT Id FROM $DataIn.kqdata WHERE Month='$MonthTemp' and Number='$Number' ORDER BY Month LIMIT 1",$link_id);
		if($checkRow = mysql_fetch_array($checkMonth)){
			$LockRemark="该月考勤统计已生成,禁止修改.";
			}
		$ValueArray=array(
			array(0=>$Date,1=>"align='center'"),
			array(0=>$weekTemp, 1=>"align='center'"),
			array(0=>$Number,1=>"align='center'"),
			array(0=>$Name,1=>"align='center'"),
			array(0=>$Stime,1=>"align='center'"),
			array(0=>$Etime,1=>"align='center'"),
			array(0=>$Hours,1=>"align='center'"),
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