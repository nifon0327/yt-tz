<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.sbdata
$DataIn.sbpaysheet
$DataOut.sbpaysheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="社保资料";		//需处理
$upDataSheet="$DataPublic.sbdata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
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
		//社保月份更新条件：更新起始月份：条件是没有缴费记录或加入月份少于最开始的缴费月份
		//					更新结束月份：条件是必须大于=加入月份，且须大于=最后缴费的月份
		//提出取缴费记录
		$payResult =mysql_query("
			SELECT MIN(minM) AS minTemp,MAX(minT) AS maxTemp FROM(
			SELECT MIN(Month) AS minM,MAX(Month) AS minT FROM $DataIn.sbpaysheet WHERE 1 AND TypeID=1  AND Number=$Number
			AND TypeId=1 ) M
			",$link_id);
		
		
		$minMonth=mysql_result($payResult,0,"minTemp");
		$maxMonth=mysql_result($payResult,0,"maxTemp");
		//检查起始月份
		if($minMonth!="" && $sMonth>$minMonth){
			$Log="<div class='redB'>起始月份不允许大于初始缴费的月份($minMonth),$TitleSTR 失败！<div><br>";
			$OperationResult="N";
			}
		else{
			$sMonthSTR=",sMonth='$sMonth'";
			$eMonthSTR=",eMonth='',Estate='1'";
			//检查结束月份
			if($eMonth!=""){
				if($maxMonth!="" && $eMonth<$maxMonth){
					$Log="<div class='redB'>结束月份不允许少于最后缴费的月份($maxMonth),$TitleSTR 失败！<div><br>";
					$OperationResult="N";
					}
				else{
					$eMonthSTR=",eMonth='$eMonth',Estate='0'";
					}
				}			
			}
		if($Log==""){
			$Sql = "UPDATE $DataPublic.sbdata SET Type='$Type',Locks='0',Note='$Note',Date='$Date',Operator='$Operator' $eMonthSTR  $sMonthSTR WHERE Id=$Id and Number=$Number LIMIT 1";
			$Result = mysql_query($Sql);
			if ($Result){
				$Log.="Number为 $Number 的 $TitleSTR 成功!</br>";
				}
			else{
				$Log.="<div class=redB>Number为 $Number 的 $TitleSTR 失败!</div></br>";
				$OperationResult="N";
				}
			}
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
  ?>