<?php  
//电信-ZX  2012-08-01
/*
$DataPublic.redeployg
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="员工等级";			//需处理
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
$valueArray=explode("|",$AddIds);
$Count=count($valueArray);
$j=1;
for($i=0;$i<$Count;$i++){
	$valueTemp=explode("!",$valueArray[$i]);
	$Number=$valueTemp[0];		//员工ID
	$Grade=$valueTemp[1];		//新等级
	$inRecode = "INSERT INTO $DataPublic.redeployg SELECT NULL,'$Number',Grade,'$Grade','$Month','$Remark','$Date','0','$Operator' FROM $DataPublic.staffmain WHERE 1 AND Number='$Number' AND Grade!='$Grade'";
	$inAction=@mysql_query($inRecode);
	if($inAction && mysql_affected_rows()>0){ 
		$Log.="$j - 员工($Number) 的 $TitleSTR 成功!<br>";
		//更新人事表中的等级
		$upSql = "UPDATE $DataPublic.staffmain SET Grade='$Grade' WHERE Number='$Number' AND Grade!='$Grade' LIMIT 1";
		$upResult = mysql_query($upSql);
		if($upResult && mysql_affected_rows()>0){
			$Log.="&nbsp;&nbsp;员工($Number)的等级更新生效!<br>";
			//短消息通知
			//$smsNote="员工($Number) 等级更新生效,薪资按新的等级计算,请核查.";			//短消息内容
			//$smsfunId=4;			include "subprogram/tosmsdata.php";					//短消息通知编号：权限，经理
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;员工($Number)的等级更新未生效! $upSql <div><br>";
			$OperationResult="N";
			}
		} 
	else{
		$Log.="<div class=redB>$j - $Number $TitleSTR 失败! $inRecode </div><br>";
		$OperationResult="N";
		}
	$j++;
	}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
