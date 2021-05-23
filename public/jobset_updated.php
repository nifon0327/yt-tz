<?php 
//电信-EWEN
//代码共享-EWEN 2012-08-14
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="职位资料";		//需处理
$upDataSheet="$DataPublic.jobdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
       if($LeaderNumber!=""){
			$checkSql=mysql_query("SELECT Id FROM $upDataSheet WHERE JobId='$Id'",$link_id);
			if($checkRow=mysql_fetch_array($checkSql)){
				$inRecode = "UPDATE $upDataSheet SET LeaderNumber='$LeaderNumber' WHERE JobId='$Id'";
				}
			else{
				 $inRecode="INSERT INTO $upDataSheet (Id,JobId,LeaderNumber)values(NULL,$Id,$LeaderNumber)";
				}
			 $inRes=mysql_query($inRecode);	
          }
		$WorkNote=FormatSTR($WorkNote);
		$WorkTime=FormatSTR($WorkTime);
		$Date=date("Y-m-d");
		$SetStr="Name='$Name',WorkNote='$WorkNote',WorkTime='$WorkTime',Date='$Date',Operator='$Operator',Locks='0'";
		include "../model/subprogram/updated_model_3a.php";
		 break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>