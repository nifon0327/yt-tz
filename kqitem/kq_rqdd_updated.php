<?php 
//电信-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="日期对调资料";		//需处理
$upDataSheet="$DataIn.kqrqdd";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		//检查原工作日和原休息日所在月份是否已经生成统计，是则不修改，   ??检查两个日期性质是否一样
		$GDateSTR=$GDate==""?"":",GDate='$GDate'";
		$XDateSTR=$XDate==""?"":",XDate='$XDate'";
		$SetStr="Locks='0',Operator='$Operator' $GDateSTR $XDateSTR";
		if($GDateSTR=="" && $XDateSTR==""){
			$Log="<div class='redB'>日期更改不符合条件,更新不成功.</div>";
			}
		else{		
			include "../model/subprogram/updated_model_3a.php";
			}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
