<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工小孩助学补助费用申请");//需处理
$Log_Item="新增员工小孩助学补助费用 $chooseMonth 记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//追加情况，如果是当前月，只追加离职人员
$nowMonth=date("Y-m");

$myResult = mysql_query("SELECT  A.Id,A.Number,M.BranchId,M.JobId,A.ChildName,A.Sex,A.Amount,M.Name,M.cSign
FROM $DataPublic.childinfo A
LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Number
WHERE  A.Estate=1  AND A.Id NOT IN (SELECT   cId FROM $DataIn.cw19_studyfeesheet WHERE Month='$chooseMonth')",$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
          $Id=$myRow["Id"];
		  $Name=$myRow["Name"];	
		  $Number=$myRow["Number"];
          $BranchId = $myRow["BranchId"];
          $cSign = $myRow["cSign"];
		  $JobId = $myRow["JobId"];
		  $ChildName=$myRow["ChildName"];
		  $Sex=$myRow["Sex"]==1?"男":"女";
	      $Amount=$myRow["Amount"];

	            $inRecode="INSERT INTO $DataIn.cw19_studyfeesheet (Id,cSign,Mid,cId,Number,BranchId,JobId,Month,Amount,Remark,NowSchool,Attached,Date,Estate,Locks,Operator) 
	            VALUES (NULL,'$cSign','0','$Id','$Number','$BranchId','$JobId','$chooseMonth','$Amount', '','0','','$Date','1','0','$Operator')";
	            $inAction=@mysql_query($inRecode);
	            if ($inAction){
	            	$Log.="&nbsp;&nbsp;&nbsp;员工 $Name 的小孩  $ChildName 申请助学费用  $Amount 成功! <br>";
	            	}
	            else{
	            	$Log.="<div class=redB>&nbsp;&nbsp;&nbsp;员工 $Name 的小孩  $ChildName 申请助学费用  $Amount 失败! $inRecode</div><br>";
	            	$OperationResult="N";
	            	}
			}while($myRow = mysql_fetch_array($myResult));
		}
else{
	            	$Log.="已无记录可以添加<br>";
}
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&chooseMonth=$chooseMonth";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>