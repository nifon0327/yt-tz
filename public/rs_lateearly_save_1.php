<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployb
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="员工津贴扣款";			//需处理
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
if($_POST['ListId']){//如果指定了操作对象
	$Counts=count($_POST['ListId']);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST[ListId][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$NumberSTR="AND Number IN ($Ids)";	//指定员工的编号字串
	}
$cs=1;
$Remark=FormatSTR($Remark);
$inRecode = "INSERT INTO $DataPublic.staff_lateearly SELECT NULL,Number,'$Month','$cs','$Amount','$Remark','$Date','0','$Operator', '1', '0', '$Operator','$DateTime' ,'$Operator', '$DateTime' FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
//echo "$inRecode";
$inResult=@mysql_query($inRecode);
if($inResult){	
	$Log="&nbsp;&nbsp; $TitleSTR 成功. ($Ids)</br>";//更新
	}
else{
	$Log="<div class='redB'>&nbsp;&nbsp; $TitleSTR 失败! ($Ids)</div></br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
