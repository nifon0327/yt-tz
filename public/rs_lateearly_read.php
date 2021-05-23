<?php 
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=10;				
$tableMenuS=600;
$sumCols="6";		//求和列
ChangeWtitle("$SubCompany 员工津贴扣款");
$funFrom="Rs_lateearly";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|员工姓名|60|部门|60|职位|60|入职日期|70|金额|60|备注|250|状态|60|登记日期|70|操作员|120";//迟到早退<br>次数|80|
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,7,8";

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if($From!="slist"){
	$SearchRows ="";	
	$date_Result = mysql_query("SELECT Month FROM $DataIn.staff_lateearly WHERE 1 GROUP BY Month order by Month DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows=" and S.Month='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}
	$B_Result=mysql_query("SELECT B.Id,B.Name FROM $DataIn.staff_lateearly S 
	      LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
          LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
          WHERE 1 $SearchRows  GROUP BY B.Id",$link_id);
	if($B_Row = mysql_fetch_array($B_Result)) {
	     echo"<select name='BranchId' id='BranchId' onchange='document.form1.submit()'>";
		echo "<option value='' selected>全部</option>";
		do{
			$B_Id=$B_Row["Id"];
			$B_Name=$B_Row["Name"];
			if($BranchId==$B_Id){
				echo "<option value='$B_Id' selected>$B_Name</option>";
				$SearchRows.=" AND P.BranchId='$BranchId'";
				}
			else{
				echo "<option value='$B_Id'>$B_Name</option>";
				}
			}while ($B_Row = mysql_fetch_array($B_Result));
		}
	echo"</select>&nbsp;";
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
	S.Id,S.Number,S.Month,S.Amount,S.cs,S.Remark,S.Locks,S.Date,S.Operator,S.Estate,P.Name,B.Name AS BranchName,J.Name AS JobName,P.ComeIn
	 FROM $DataIn.staff_lateearly S
	LEFT JOIN $DataIn.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataIn.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataIn.jobdata J ON J.Id=P.JobId
	WHERE 1 $SearchRows ";
//echo "$mySql";	
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
//$GDnumber= array("①","①","②","③","④","⑤","⑥","⑦","⑧","⑨","⑩");
	do{
			//S.Id,S.Number,S.Month,S.Amount,S.cs,S.Remark,S.Locks,S.Date,S.Operator,S.Estate
			$m=1;
			$Id=$myRow["Id"];			
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
			$Month =$myRow["Month"];
			$Amount =$myRow["Amount"]; //公司需要报销的费用
			$cs =$myRow["cs"];  //社保局支付费用
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
			$BranchName=$myRow["BranchName"];				
			$JobName=$myRow["JobName"];
            $ComeIn=$myRow["ComeIn"];
			$Operator=$myRow["Operator"];
			include "../model/subprogram/staffname.php";	
            $Remark=$myRow["Remark"];
			$Estate=$myRow["Estate"];
			switch($Estate){
				case 0:
					$Estate="<div class='redB'>×</div>";
					break;
				case 1:
					$Estate="<div class='greenB'>√</div>";
					break;
			}
			
			

		$ValueArray=array(
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$BranchName, 	1=>"align='center'"),
			array(0=>$JobName,	1=>"align='center'"),
		    array(0=>$ComeIn, 	1=>"align='center'"),
			//array(0=>$cs,	    1=>"align='right'"),			
			array(0=>$Amount,	1=>"align='right'"),
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
echo '</div>';
$myResult = mysql_query($mySql,$link_id);
$RecordToTal= mysql_num_rows($myResult);
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
include "../model/subprogram/read_model_menu.php";
?>