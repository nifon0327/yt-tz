<?php 
//电信-EWEN
include "../model/modelhead.php";
$Log_Item="忘签记录";			//需处理
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
$NumberSTR="";
$InFo="全部需要考勤的";
if($_POST['ListId']){//如果指定了配件
	$Counts=count($_POST['ListId']);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST[ListId][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$NumberSTR="AND Number IN ($Ids)";
	$InFo="员工ID在($Ids)的";
	}
else{
	if($BranchId!=""){
		$NumberSTR="AND BranchId='$BranchId'";
		$InFo="部门ID为 $BranchId 且需要考勤的";
		}
	}
$CheckTime=$CheckDate." ".$CheckTime.":00";		//签到时间
$theMonth=date("Y-m",strtotime($CheckTime));	//更新的月份,需检查该月份是否已经统计,如果是则不保存记录AND cSign='$Login_cSign'
$KrSign=0;
if($CheckType=="K"){
	$CheckType="O";
	$KrSign=1;
	}
$inRecode = "INSERT INTO $DataIn.checkinout 
SELECT NULL,BranchId,JobId,Number,'$CheckTime','$CheckType','1','1','1','0','$KrSign','$Operator',0,'$Operator',NOW(),'$Operator',NOW(),null FROM $DataPublic.staffmain WHERE 1 $NumberSTR 
AND KqSign!='3' AND Estate='1'  AND Number NOT IN(SELECT Number FROM $DataIn.kqdata WHERE Month='$theMonth')";

//echo $inRecode;

$inResult=@mysql_query($inRecode);
if($inResult){	
	$Log.="&nbsp;&nbsp;".$InFo."员工".$TitleSTR."成功!</br>";
	//$smsNote="$InFo 员工的考勤记录有更新，需重新考勤统计、重置工资以及审核! ";$smsfunId=2;include "subprogram/tosmsdata.php";
	}
else{
	$Log.="<div class='redB'>&nbsp;&nbsp;".$InFo."员工".$TitleSTR."失败!</div></br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
