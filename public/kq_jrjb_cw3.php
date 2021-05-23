<?php 
//电信-EWEN
$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
$Page_Size = 100;							//每页默认记录数量
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	//划分权限:如果没有最高权限，则只显示自己的记录
	$SearchRows="";
	$TempEstateSTR="EstateSTR".strval($Estate); 
	$$TempEstateSTR="selected";	
	
   $cSignResult = mysql_query("SELECT C.CShortName,S.cSign
	FROM $DataIn.hdjbsheet S 
	LEFT JOIN $DataIn.companys_group C ON C.cSign = S.cSign
	WHERE 1 and S.Estate='$Estate' $SearchRows  GROUP BY S.cSign ",$link_id);
	if($cSignRow = mysql_fetch_array($cSignResult)){
		$cSignSelect.="<select name='cSign' id='cSign' onchange='document.form1.submit()'>";
		do{
		    $cSignValue = $cSignRow["cSign"];
		    $CShortName = $cSignRow["CShortName"];
		    $cSign = $cSign==""?$cSignValue:$cSign;
			if($cSign==$cSignValue){
				$cSignSelect.="<option value='$cSignValue' selected>$CShortName</option>";
				$SearchRows.=" and  S.cSign ='$cSignValue'";
				}
			else{
				$cSignSelect.="<option value='$cSignValue'>$CShortName</option>";					
				}
			}while($cSignRow = mysql_fetch_array($cSignResult));
		$cSignSelect.="</select>&nbsp;";
		}
		
		
	$monthResult = mysql_query("SELECT S.Month FROM $DataIn.hdjbsheet S WHERE 1 and S.Estate='$Estate' $SearchRows group by S.Month order by S.Month DESC",$link_id);
	if($monthRow = mysql_fetch_array($monthResult)){
		$MonthSelect.="<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$monthRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				$MonthSelect.="<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and S.Month='$dateValue'";
				}
			else{
				$MonthSelect.="<option value='$dateValue'>$dateValue</option>";					
				}
			}while($monthRow = mysql_fetch_array($monthResult));
		$MonthSelect.="</select>&nbsp;";
		}
		
		
	echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
	<option value='3' $EstateSTR3>未结付</option>
	<option value='0' $EstateSTR0>已结付</option>
	</select>&nbsp;";	
	$SearchRows.="and S.Estate=3";
	echo $cSignSelect;
	echo $MonthSelect;
	
	//选择地点
	$SelectTB="M";$SelectFrom=1; 
    include "../model/subselect/WorkAdd.php"; 

	}

//结付的银行
include "../model/selectbank1.php";
echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo"$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i=1;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
S.Id,S.Number,S.Month,S.oHours,S.oWage,S.xHours,S.xWage,S.fHours,S.fWage,S.Amount,S.oRandP,S.Date,S.Estate,S.Locks,S.Operator,M.Name,J.Name AS Job,B.Name AS Branch,S.cSign
FROM $DataIn.hdjbsheet S
LEFT JOIN $DataPublic.staffmain M ON S.Number=M.Number
LEFT JOIN $DataPublic.jobdata J ON J.Id=S.JobId
LEFT JOIN $DataPublic.branchdata B ON B.Id=S.BranchId
WHERE 1 $SearchRows ORDER BY M.BranchId,M.JobId,M.Number";
//echo "$mySql";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];	
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$strName=$myRow["Name"];
        include "../model/subprogram/staff_qj_day.php";
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Month=$myRow["Month"];
		$oHours=$myRow["oHours"];
		$oWage=$myRow["oWage"];	
		$xHours=$myRow["xHours"];
		$fHours=$myRow["fHours"];
		$xWage=$myRow["xWage"];
		$fWage=$myRow["fWage"];
		$Amount=sprintf("%.0f",$myRow["Amount"]);
		$oRandP=$myRow["oRandP"]; 
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$Locks=1;
		$Estate="<div align='center' class='redB'>未付</div>";
		$ValueArray=array(
		    array(0=>$cSign,	1=>"align='center'"),
			array(0=>$Number,	1=>"align='center'"),
			array(0=>$Name,		1=>"align='center' $qjcolor"),
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Month,	1=>"align='center'"),
			array(0=>$oWage,	1=>"align='center'"),
			array(0=>$oHours,	1=>"align='center'"),
			array(0=>$xWage,	1=>"align='center'"),
			array(0=>$xHours,	1=>"align='center'"),
			array(0=>$fWage,	1=>"align='center'"),
			array(0=>$fHours, 	1=>"align='center'"),
			array(0=>$oRandP,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Date,		1=>"align='center'"),
			array(0=>$Operator,	1=>"align='center'")
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