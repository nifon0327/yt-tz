<?php 
include "../model/modelhead.php";
//步骤2：
$Log_Item="意外险缴费记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");
	//指定部门或具体员工
	if($BranchId!=""){		//指定部门
		$BranchIdSTR="and M.BranchId='$BranchId'";
		}
	else{
		$BranchIdSTR="";//全部
		}
		
	if($_POST['ListId']){//如果指定了操作对象
		$Counts=count($_POST['ListId']);
		$Ids="";
		$tmpStr="";
		for($i=0;$i<$Counts;$i++){
			$thisId=$_POST[ListId][$i];
			$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
			
			$inRecode="INSERT INTO $DataIn.sbpaysheet 
			SELECT NULL,M.cSign,'3',M.BranchId,M.JobId,M.Number,'$newMonth','$ValidMonth','0','$cAmount','$Date','1','0',
			'$Operator','0', '0', '$Operator', NOW(), '$Operator', NOW()
			FROM $DataPublic.staffmain M
			WHERE 1 and M.Number IN ($thisId)   AND M.Estate=1
			AND M.Number NOT IN (SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$newMonth' and TypeId=3)";  
				$inResult=@mysql_query($inRecode);
				if($inResult){
					$Log.=" $thisId 成功.";
					}
				else{
					$Log.="<div class='redB'>$thisId  失败! $inRecode </div>";
					$OperationResult="N";
					}			
			
		}
		$Log.="&nbsp;&nbsp;所选员工 $Log .</br>";
		$BranchIdSTR="and M.Number IN ($Ids)";	//指定员工
	}else{
		
		$inRecode="INSERT INTO $DataIn.sbpaysheet 
		SELECT NULL,M.cSign,'3',M.BranchId,M.JobId,M.Number,'$newMonth','0','$cAmount','$Date','1','0',
		'$Operator','0', '0', '$Operator', NOW(), '$Operator', NOW()
		FROM $DataPublic.staffmain M
		WHERE 1 $BranchIdSTR  AND M.Estate=1
		AND M.Number NOT IN (SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$newMonth' and TypeId=3)";
			$inResult=@mysql_query($inRecode);
			if($inResult){
				$Log.="&nbsp;&nbsp;所选员工 $TitleSTR 成功.</br>";
				}
			else{
				$Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;所选员工 $TitleSTR 失败! $inRecode </div></br>";
				$OperationResult="N";
				}
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
