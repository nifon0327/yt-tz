<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="新增直落记录";			//需处理
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
//员工过滤
$NumberSTR="";
$InFo="全部需要考勤的";
if($_POST["ListId"]){//如果指定
	$Counts=count($_POST["ListId"]);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST["ListId"][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$NumberSTR="AND Number IN ($Ids)";
	$InFo="员工ID在($Ids)的";
	}
else{
	if($JobId!=""){
		$NumberSTR="AND JobId='$JobId'";
		$InFo="职位ID为 $JobId 且需要考勤的";
		}
	else{
		if($BranchId!=""){
			$NumberSTR="AND BranchId='$BranchId'";
			$InFo="部门ID为 $BranchId 且需要考勤的";
			}
		}		
	}
//工时计算
$Hours=WorkTime($Stime,$Etime,$Action);
$thisMonth=substr($Date,0,7);
//写入数据的条件：该日期大于入职日期，且未生成月统计资料
$inRecode="INSERT INTO $DataPublic.kqzltime
	SELECT NULL,BranchId,JobId,Number,'$Stime','$Etime','$Hours','$Date','0','$Operator', 1,0,'$Operator',NOW(),'$Operator',NOW() 
	FROM $DataPublic.staffmain WHERE 1 $NumberSTR AND KqSign!='3' AND Estate='1' AND ComeIn<='$Date'
	AND Number NOT IN (SELECT Number FROM $DataIn.kqdata WHERE Month='$thisMonth' ORDER BY Number)
	AND Number NOT IN (SELECT Number FROM $DataPublic.kqzltime WHERE Date = '$Date' AND (Stime='$Stime' OR Etime='$Etime'))";
$inResult=@mysql_query($inRecode);
if($inResult){
	$Log.="&nbsp;&nbsp;".$InFo."员工".$DayTemp."的".$TitleSTR."成功!</br>";
	}
else{
	$Log.="<div class='redB'>&nbsp;&nbsp;".$InFo."员工".$DayTemp."的".$TitleSTR."失败!</div></br>";
	$OperationResult="N";
	}
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
