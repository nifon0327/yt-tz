<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.staffmain
$DataPublic.sbdata
$DataIn.sbpaysheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item=$TypeId==1?"社保缴费记录":"公积金缴费记录";			//需处理
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
		for($i=0;$i<$Counts;$i++){
			$thisId=$_POST[ListId][$i];
			$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
			}
		$BranchIdSTR="and M.Number IN ($Ids)";	//指定员工
		}


switch($TypeId){
            case 1://社保
            if($DataIn !== 'ac'){
	            $inRecode = "INSERT INTO $DataIn.sbpaysheet
				SELECT NULL,'$TypeId',M.BranchId,M.JobId,M.Number,'$newMonth',T.mAmount,T.cAmount,'$Date','1','0','$Operator','0' FROM $DataPublic.staffmain M,$DataPublic.sbdata S,$DataIn.rs_sbtype T
				WHERE 1 $BranchIdSTR 
				AND S.Number=M.Number
				AND T.Id=S.Type
				AND M.cSign='$Login_cSign'
				AND M.Number IN (SELECT Number FROM $DataPublic.sbdata WHERE sMonth<='$newMonth' AND Estate='1')
				AND M.Number NOT IN (SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$newMonth' and TypeId='$TypeId')";
			}else{
				$inRecode = "INSERT INTO $DataIn.sbpaysheet
				SELECT NULL,'$TypeId',M.BranchId,M.JobId,M.Number,'$newMonth',T.mAmount,T.cAmount,'$Date','1','0','$Operator','0', 0, '$Operator', '$Date', '$Operator', '$Date' FROM $DataPublic.staffmain M,$DataPublic.sbdata S,$DataIn.rs_sbtype T
				WHERE 1 $BranchIdSTR 
				AND S.Number=M.Number
				AND T.Id=S.Type
				AND M.cSign='$Login_cSign'
				AND M.Number IN (SELECT Number FROM $DataPublic.sbdata WHERE sMonth<='$newMonth' AND Estate='1')
				AND M.Number NOT IN (SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$newMonth' and TypeId='$TypeId')";
			}
            break;
           case 2://住房公积金
            $AmonutResult=mysql_fetch_array(mysql_query("SELECT mAmount,cAmount FROM $DataIn.rs_sbtype WHERE Id=4",$link_id));//住房公积金的金额
            $mAmount=$AmonutResult["mAmount"];
            $cAmount=$AmonutResult["cAmount"];

            if($DataIn !== 'ac'){
	            $inRecode="INSERT INTO $DataIn.sbpaysheet 
	            SELECT NULL,'$TypeId',M.BranchId,M.JobId,M.Number,'$newMonth','$mAmount','$cAmount','$Date','1','0','$Operator','0' 
	            FROM $DataPublic.staffmain M
	            WHERE 1 $BranchIdSTR  AND M.cSign='$Login_cSign' AND M.Estate=1
	            AND M.Number NOT IN (SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$newMonth' and TypeId='$TypeId')";
        	}else{
        		$inRecode="INSERT INTO $DataIn.sbpaysheet 
	            SELECT NULL,'$TypeId',M.BranchId,M.JobId,M.Number,'$newMonth','$mAmount','$cAmount','$Date','1','0','$Operator','0', 0, '$Operator', '$Date', '$Operator', '$Date' 
	            FROM $DataPublic.staffmain M
	            WHERE 1 $BranchIdSTR  AND M.cSign='$Login_cSign' AND M.Estate=1
	            AND M.Number NOT IN (SELECT Number FROM $DataIn.sbpaysheet WHERE Month='$newMonth' and TypeId='$TypeId')";
        	}
            break;
        }

	$inResult=@mysql_query($inRecode);
	if($inResult){
		$Log.="&nbsp;&nbsp;所选员工 $TitleSTR 成功.</br>";
		}
	else{
		$Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;所选员工 $TitleSTR 失败! $inRecode </div></br>";
		$OperationResult="N";
		}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
