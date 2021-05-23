<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工购房补助费用申请");//需处理
$Log_Item="新增员工购房补助费用 $chooseMonth 记录";			//需处理
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
$mySql = "SELECT  A.Id,A.Number,M.BranchId,M.JobId,A.Amount,M.Name,A.Attached,A.cSign
FROM $DataIn.staff_housesubsidy A
LEFT JOIN $DataIn.staffmain M ON M.Number=A.Number
WHERE  A.Estate=1  AND A.Number NOT IN (SELECT Number FROM $DataIn.cw21_housefeesheet WHERE Month='$chooseMonth')";
//echo $mySql;
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
          $Id=$myRow["Id"];
		  $Name=$myRow["Name"];	
		  $Number=$myRow["Number"];
          $BranchId = $myRow["BranchId"];
		  $JobId = $myRow["JobId"];
	      $Amount=$myRow["Amount"];
	      $cSign=$myRow["cSign"];
	      $Attached=$myRow["Attached"];
          $inRecode="INSERT INTO $DataIn.cw21_housefeesheet 
            (Id,cSign,Mid,Number,BranchId,JobId,Month,Amount,Remark,Attached,Date,Estate,Locks,Operator) 
VALUES (NULL,'$cSign','0','$Number','$BranchId','$JobId','$chooseMonth','$Amount','','$Attached','$Date','1','0','$Operator')";
            $inAction=@mysql_query($inRecode);
            if ($inAction){
            	$Log.="&nbsp;&nbsp;&nbsp;员工 $Name 的购房补助费用:$Amount 成功! <br>";
            	}
            else{
            	$Log.="<div class=redB>&nbsp;&nbsp;&nbsp;员工 $Name 的购房补助费用:$Amount 失败! $inRecode</div><br>";
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