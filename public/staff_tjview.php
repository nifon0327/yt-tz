<?php 
/*代码、数据库合并后共享-ZXQ 2012-08-08
$DataPublic.sbdata
$DataPublic.staffmain
$DataIn.sbpaysheet
*/
//步骤1
include "../model/modelhead.php";
include "../model/subprogram/read_datain.php";
$fArray=explode("|",$f);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Number=anmaOut($RuleStr1,$EncryptStr1,"f");

//步骤2：需处理
$ColsNumber=16;
$tableMenuS=500;
$sumCols="3";		//求和列
$From=$From==""?"read":$From;
ChangeWtitle("$SubCompany 体检费用及体检报告");
$funFrom="staff";
$Th_Col="序号|40|员工姓名|60|部门|60|职位|60|金额|60|结付|80|备注|200|凭证|40|体检日期|100|登记日期|100|操作员|80";
//必选，分页默认值
$Pagination=$Pagination==""?0:$Pagination;	
$Page_Size = 100;						
$ActioToS="";	

//步骤3：
$nowWebPage=$funFrom."_tjview";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项

$checkSql = "SELECT M.Name,M.Estate FROM $DataPublic.staffmain M 
	WHERE M.Number=$Number  LIMIT 1";
//echo $checkSql ;
$checkResult = mysql_query($checkSql." $PageSTR",$link_id);
if($checkRow = mysql_fetch_array($checkResult)){
	$Name=$checkRow["Name"];
	$Estate=$checkRow["Estate"]==1?"在职员工：":"离职员工：";
	echo $Estate.$Name;
	}
//体检报告

$reportResult=mysql_query("SELECT Attached FROM $DataPublic.staff_tj WHERE Number='$Number'",$link_id);
$report="";
while($reportRow=mysql_fetch_array($reportResult)){
     $reportAttached=$reportRow["Attached"];
       if($reportAttached!="" && $reportAttached!=0){
       $f1=anmaIn($reportAttached,$SinkOrder,$motherSTR);
       $d1=anmaIn("download/tjfile/",$SinkOrder,$motherSTR);
                $Attached="<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>view</a>";
               }
            else $Attached="&nbsp;";
       $report=$report."&nbsp;".$Attached;
        }
//步骤5：
include "../model/subprogram/read_model_5.php";
 echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP:       break-word' bgcolor='#FFFFFF'><tr ><td height='20' class='A0011' >
       <span style='color:red'>体检报告:</span>$report
  </td></tr></table>";
$ChooseOut="N";
//步骤6：需处理数据记录处理
$i=1;$sumM=0;$sumC=0;$sumA=0;
$j=($Page-1)*$Page_Size+1;
List_Title($Th_Col,"1",0);
$mySql="SELECT 
	S.Id,S.Number,S.Month,S.Amount,S.Locks,S.Date,S.Operator,S.Estate,S.Mid,P.Name,B.Name AS BranchName,J.Name AS JobName,S.Remark,S.Attached,S.tjDate
	 FROM $DataIn.cw17_tjsheet S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number
    LEFT JOIN $DataPublic.branchdata B ON B.Id=P.BranchId
    LEFT JOIN $DataPublic.jobdata J ON J.Id=P.JobId
	WHERE 1 AND P.Number=$Number";
 // echo $mySql;
$myResult = mysql_query($mySql." $PageSTR",$link_id);
if( $myRow = mysql_fetch_array($myResult)){
	do{
            $m=1;
			$Id=$myRow["Id"];			
			$Number=$myRow["Number"];		
			$Name=$myRow["Name"];
			$Month =$myRow["Month"];
			$Amount =$myRow["Amount"];
			$Locks=$myRow["Locks"];
			$Date=$myRow["Date"];
			$tjDate=$myRow["tjDate"];
			$BranchName=$myRow["BranchName"];				
			$JobName=$myRow["JobName"];
			$Mid=$myRow["Mid"];
			$Operator=$myRow["Operator"];
            $Remark=$myRow["Remark"];
			include "../model/subprogram/staffname.php";
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
          $Attached=$myRow["Attached"];
        if($Attached!="" && $Attached!=0){
		     $f2=anmaIn($Attached,$SinkOrder,$motherSTR);
		     $d2=anmaIn("download/tjfile/",$SinkOrder,$motherSTR);		
             $Attached="<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>View</a>";
          }
        else $Attached="&nbsp;";
		$ValueArray=array(
			array(0=>$Name,		1=>"align='center'"),
			array(0=>$BranchName, 	1=>"align='center'"),
			array(0=>$JobName,	1=>"align='center'"),
			array(0=>$Amount,	1=>"align='center'"),
			array(0=>$Estate, 	1=>"align='center'"),
			array(0=>$Remark, 	1=>"align='left'"),
		    array(0=>$Attached, 	1=>"align='center'"),
		    array(0=>$tjDate, 	1=>"align='center'"),
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
$RecordToTal= $myResult!=""?mysql_num_rows($myResult):0;
pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
?>