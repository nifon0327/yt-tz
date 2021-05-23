<?php 
//电信-ZX  2012-08-01
/*
$DataIn.sbpaysheet
$DataPublic.staffmain
$DataPublic.jobdata
$DataPublic.branchdata
二合一已更新
*/
include "../model/modelhead.php";
$From=$From==""?"read":$From;
//需处理参数
$ColsNumber=12;				
$tableMenuS=600;
$sumCols="6,7,8";		//求和列
ChangeWtitle("$SubCompany 意外险缴费记录");
$funFrom="Rs_Casualty";
$nowWebPage=$funFrom."_read";
$Th_Col="选项|40|序号|40|类型|60|员工姓名|60|部门|60|职位|60|缴费月份|70|个人缴费|60|公司缴费|60|小计|60|结付|80|单据|60|登记日期|100|操作员|80";
$Pagination=$Pagination==""?0:$Pagination;
$Page_Size = 100;
$ActioToS="1,2,3,4,14,13";
//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
$SearchRows.=" AND S.TypeId=3";	
if($From!="slist"){
	$date_Result = mysql_query("SELECT S.Month FROM $DataIn.sbpaysheet S WHERE 1 $SearchRows GROUP BY S.Month order by S.Month DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)) {
		echo"<select name='chooseMonth' id='chooseMonth' onchange='document.form1.submit()'>";
		do{
			$dateValue=$dateRow["Month"];
			if($chooseMonth==""){
				$chooseMonth=$dateValue;}
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				$SearchRows.=" and S.Month='$dateValue'";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";					
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		echo"</select>&nbsp;";
		}

	$B_Result=mysql_query("SELECT B.Id,B.Name FROM $DataIn.sbpaysheet S
     LEFT JOIN   $DataPublic.branchdata B ON B.Id=S.BranchId
     WHERE 1 $SearchRows GROUP BY B.Id ORDER BY B.Id",$link_id);
	if($B_Row = mysql_fetch_array($B_Result)) {
	  echo"<select name='BranchId' id='BranchId' onchange='document.form1.submit()'>";
		echo "<option value='' selected>全部</option>";
		do{
			$B_Id=$B_Row["Id"];
			$B_Name=$B_Row["Name"];
			if($BranchId==$B_Id){
				echo "<option value='$B_Id' selected>$B_Name</option>";
				$SearchRows.=" AND S.BranchId='$BranchId'";
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
	S.Id,S.BranchId,S.JobId,S.Number,S.Month,S.mAmount,S.cAmount,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,P.Name,S.TypeId,P.Picture
	 FROM $DataIn.sbpaysheet S
    LEFT JOIN $DataIn.rs_casualty_picture P ON P.Mid=S.Id
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
	WHERE 1 $SearchRows ";
//echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
			$m=1;
			$Id=$myRow["Id"];			
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
			$Month =$myRow["Month"];
			$mAmount =$myRow["mAmount"];
			$cAmount =$myRow["cAmount"];
			$Amount=sprintf("%.2f",$mAmount +$cAmount);
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
			$BranchId=$myRow["BranchId"];				
			$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata WHERE 1 AND Id=$BranchId LIMIT 1",$link_id));
			$Branch=$B_Result["Name"];				
			$JobId=$myRow["JobId"];
			$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata WHERE 1 AND Id=$JobId LIMIT 1",$link_id));
			$Job=$J_Result["Name"];
			$Mid=$myRow["Mid"];
			$Operator=$myRow["Operator"];
			include "../model/subprogram/staffname.php";
            //$TypeName=$myRow["TypeId"]==1?"社保":"公积金";
            switch($myRow["TypeId"]){
                    case 1: $TypeName="社保";break;
                    case 2: $TypeName="公积金";break;
                    case 3: $TypeName="意外险";break;
                   }
			$Estate=$myRow["Estate"];
			switch($Estate){
				case 1:
					if($Mid==0){
						$Estate="<span class='redB'>未处理</span>";$LockRemark="";}
					else{
						$Estate="<span class='redB'>错误(状态为未处理但已付)</span>";
						$LockRemark="错误,需核查并请IT处理.";
						}
				break;
				case 2:
					$Estate="<span class='yellowB'>请款中</span>";
					$LockRemark="记录已经请款，强制锁定操作！修改需退回。";
					$Locks=0;
				break;
				case 3:
				$Estate="<span class='yellowB'>请款通过</span>";
					$LockRemark="记录已经请款通过，强制锁定操作！修改需退回。";
					$Locks=0;
				break;
				case 0:
					$Estate="<span class='greenB'>已结付</span>";
					$LockRemark="记录已经结付，强制锁定！修改需取消结付。";
					$Locks=0;
				break;
				}
        $changeResult=mysql_fetch_array(mysql_query("SELECT R.OldNumber,M.Name FROM $DataIn.rs_casualty R
LEFT JOIN $DataPublic.staffmain M ON M.Number=R.OldNumber
WHERE R.NewNumber='$Number' AND R.Month='$chooseMonth'",$link_id));
         $OldName=$changeResult["Name"];
           if($OldName!=""){
                  $Name="<div class='greenB' title='原缴费员工:$OldName'>$Name</div>";
                }
        $Picture=$myRow["Picture"];
        if($Picture!=""){
		    $Dir=anmaIn("download/Casualty/",$SinkOrder,$motherSTR);
			$Bill=anmaIn($Picture,$SinkOrder,$motherSTR);
			$Picture="<span onClick='OpenOrLoad(\"$Dir\",\"$Bill\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
            }
       else $Picture="";
		$ValueArray=array(
			array(0=>$TypeName,		1=>"align='center'"),
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$Branch,	1=>"align='center'"),
			array(0=>$Job,		1=>"align='center'"),
			array(0=>$Month, 	1=>"align='center'"),
			array(0=>$mAmount,	1=>"align='center'"),
			array(0=>$cAmount,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Estate, 	1=>"align='center'"),
			array(0=>$Picture, 	1=>"align='center'"),
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