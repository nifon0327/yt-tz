<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
//步骤2：
$Log_Item="员工受训记录";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

//员工过滤
$NumberSTR="";
$InFo="全部";
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
		$InFo="职位ID为 $JobId的";
		}
	else{
		if($BranchId!=""){
			$NumberSTR="AND BranchId='$BranchId'";
			$InFo="部门ID为 $BranchId的";
			}
		}		
	}
//写入数据
if($DataIn !== 'ac'){
	$inRecode="INSERT INTO $DataPublic.aqsc08
	SELECT NULL,'$ItemId',Number,'$Exam','$DateTime','1','0','$Operator' 
	FROM $DataPublic.staffmain WHERE cSign='$Login_cSign' $NumberSTR AND Estate='1' 
	AND Number NOT IN(SELECT Number FROM $DataPublic.aqsc08 WHERE ItemId='$ItemId' ORDER BY Number)";
}else{
	$inRecode="INSERT INTO $DataPublic.aqsc08
	SELECT NULL,'$ItemId',Number,'$Exam','$DateTime','1','0','$Operator', 0, '$Operator', '$DateTime', '$Operator', '$DateTime'
	FROM $DataPublic.staffmain WHERE cSign='$Login_cSign' $NumberSTR AND Estate='1' 
	AND Number NOT IN(SELECT Number FROM $DataPublic.aqsc08 WHERE ItemId='$ItemId' ORDER BY Number)";
}
$inResult=@mysql_query($inRecode);
if($inResult){
	$Log.="&nbsp;&nbsp;$x-".$InFo."员工".$Log_Item."成功!</br>";
	}
else{
	$Log.="<div class='redB'>&nbsp;&nbsp;$x-".$InFo."员工".$Log_Item."的失败! $inRecode</div></br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
