<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=600;
$sumCols="6,7";		//求和列
ChangeWtitle("$SubCompany 员工抵扣工时");
$funFrom="rs_staffDK";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|员工姓名|60|部门|60|职位|60|抵扣日期|80|时长(H)|60|未抵扣<br>时长(H)|60|备注|250|可用状态|30|登记日期|70|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT DISTINCT DATE_FORMAT(dkDate,'%Y-%m') as Month FROM $DataPublic.staff_dkdate WHERE 1  order by dkDate DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows=" and DATE_FORMAT(S.dkDate,'%Y-%m')='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}

	}
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
	S.Id,S.Number,S.Locks,S.Date,S.Operator,S.Estate,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,P.ComeIn,S.dkDate,S.dkHour,S.RemainHour
	 FROM $DataPublic.staff_dkdate S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	WHERE 1 $SearchRows ";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
			$m=1;
			$Id=$myRow["Id"];			
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
			$Amount =$myRow["Amount"];
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
            $dkDate=$myRow["dkDate"];
			$dkHour=$myRow["dkHour"];
			$RemainHour=$myRow["RemainHour"];
			$BranchName=$myRow["BranchName"];				
			$JobName=$myRow["JobName"];
			$Mid=$myRow["Mid"];
            $ComeIn=$myRow["ComeIn"];
			$Operator=$myRow["Operator"];
            $Remark=$myRow["Remark"];

			include "../model/subprogram/staffname.php";


			$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";

		$ValueArray=array(
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$BranchName, 	1=>"align='center'"),
			array(0=>$JobName,	1=>"align='center'"),
			array(0=>$dkDate,	1=>"align='center'"),
			array(0=>$dkHour,	1=>"align='right'"),
			array(0=>$RemainHour,	1=>"align='right'"),
			array(0=>$Remark, 	1=>"align='left'"),
			array(0=>$Estate, 	1=>"align='center'"),
			array(0=>$Date, 	1=>"align='center'"),
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
List_Title($Th_Col,"0",0);
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>