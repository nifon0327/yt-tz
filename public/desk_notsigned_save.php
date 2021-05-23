<?php 
include "../model/modelhead.php";
$Log_Item="补签记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
session_register("nowWebPage");
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
$CheckType=$kqSignType;
$bcId=1;
$checkBCB=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.kqbcb WHERE Id='$bcId'",$link_id));
$CheckTime=$CheckType==1?$checkBCB["zb_InTime"]:$checkBCB["zb_OutTime"];
$inRecode = $DataIn !== 'ac' ? "INSERT INTO `$DataIn`.`checkinout` SELECT NULL,'$bcId',M.BranchId,M.JobId,M.Number,'$CheckDate','$CheckTime','$CheckType','1',M.KqSign,'1','1','$Operator' FROM $DataPublic.staffmain M WHERE M.Id IN ($Ids) AND M.KqSign!='3' AND M.Estate='1'" :
                               "INSERT INTO `$DataIn`.`checkinout` SELECT NULL,'$bcId',M.BranchId,M.JobId,M.Number,'$CheckDate','$CheckTime','$CheckType','1',M.KqSign,'1','1','$Operator',0,'$Operator','$DateTime','$Operator','$DateTime',null FROM $DataPublic.staffmain M WHERE M.Id IN ($Ids) AND M.KqSign!='3' AND M.Estate='1'";
$inResult=@mysql_query($inRecode);
if($inResult){	
	$Log.="&nbsp;&nbsp;".$InFo."员工".$TitleSTR."成功!</br>";
	}
else{
	$Log.="<div class='redB'>&nbsp;&nbsp;".$InFo."员工".$TitleSTR."失败! $inRecode </div></br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
