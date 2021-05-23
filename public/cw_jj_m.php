<?php 
include "../model/modelhead.php";
//需处理参数
$ColsNumber=13;
$sumCols="12";
$tableMenuS=600;
$From=$From==""?"m":$From;
ChangeWtitle("$SubCompany 奖金列表");
$funFrom="cw_jj";
$Th_Col="选项|50|序号|40|所属公司|60|奖金项目|120|部门|70|职位|60|员工ID|50|员工姓名|60|计算月份|110|比率参数|60|金额|80|个税|40|实付|80|状态|40|请款月份|80|操作员|60";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="17,15";
$nowWebPage=$funFrom."_m";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT Month FROM $DataIn.cw11_jjsheet WHERE Estate='2' GROUP BY Month order by Month DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		echo "<option value='' >请款月份</option>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==$dateValue){
			    $ChooseItemName=$dateValue;
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows="AND J.Month='$dateValue'";
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
$mySql="SELECT J.Id,J.ItemName,B.Name AS Branch,W.Name AS Job,J.Number,M.Name,M.Estate AS StaffEstate,J.Month,J.MonthS,J.MonthE,J.Divisor,J.Rate,J.Amount,J.Estate,J.Locks,J.Date,J.Operator,J.cSign,J.RandP,J.jjAmount  
FROM $DataIn.cw11_jjsheet J 
LEFT JOIN $DataPublic.branchdata B ON B.Id=J.BranchId 
LEFT JOIN $DataPublic.jobdata W ON W.Id=J.JobId 
LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number
WHERE 1 $SearchRows AND J.Estate='2' ORDER BY M.BranchId,M.JobId,M.Number";
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;
		$Id=$myRow["Id"];
		$ItemName=$myRow["ItemName"];
		$Branch=$myRow["Branch"];
		$Job=$myRow["Job"];
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$StaffEstate=$myRow["StaffEstate"];
		$Name = $StaffEstate==0?"<span class='yellowB'>$Name</span>":$Name;
		$Month=$myRow["Month"];
		$MonthS=$myRow["MonthS"];
		$MonthE=$myRow["MonthE"];
		$MonthSTR=$MonthS."~".$MonthE;
		$Divisor=$myRow["Divisor"];
		$Rate=$myRow["Rate"]*100/100;

		 $RandP = $myRow["RandP"];
		$Amount=$myRow["Amount"];
		$jjAmount=$myRow["jjAmount"];
		
		$Estate="<div align='center' class='yellowB' title='请款中...'>×.</div>";
		$Locks=$myRow["Locks"];
		$Date=$myRow["Date"];
		$Operator=$myRow["Operator"];
		include "../model/subprogram/staffname.php";
		$cSignFrom=$myRow["cSign"];
		include"../model/subselect/cSign.php";
		$ValueArray=array(
		    array(0=>$cSign, 1=>"align='center'"),
			array(0=>$ItemName, 1=>"align='center'"),
			array(0=>$Branch, 	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Number, 	1=>"align='center'"),
			array(0=>$Name, 	1=>"align='center'"),
			array(0=>$MonthSTR,	1=>"align='center'"),
			array(0=>$Rate."%", 	1=>"align='center'"),
			array(0=>$jjAmount, 	1=>"align='center'"),
			array(0=>$RandP, 	1=>"align='center'"),
			array(0=>$Amount, 	1=>"align='center'"),
			array(0=>$Estate,	1=>"align='center'"),
			array(0=>$Month,	 	1=>"align='center'"),
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