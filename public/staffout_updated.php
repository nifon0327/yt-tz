<?php 
/**/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="离职员工资料";		//需处理
$upDataSheet="$DataPublic.dimissiondata";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
//echo "$ActionId: $NewIdcard : $Number";
//return false;
switch($ActionId){
	case '-99':
		$sql = "UPDATE $DataPublic.staffsheet SET Idcard='$NewIdcard'  WHERE Number='$Number' ";
		$result = mysql_query($sql);
		if($result){
			$Log="离职员工ID为 $Number 的记录更新身份证号码成功($NewIdcard) $Log_Funtion.</br>";
			}
		else{
			$Log="离职员工ID为 $Number 的记录$Log_Funtion 失败! $sql</br>";
			$OperationResult="N";
			}		
	
	break;	
		
	
	default://离职资料更新
		if($BackDate==""){			
			$Reason=FormatSTR($Reason);
			$SetStr="Type='$Type',outDate='$outDate',Reason='$Reason',Date='$Date',Operator='$Operator',Locks='0'";
			include "../model/subprogram/updated_model_3a.php";
			}
		else{//复职
			$Log_Funtion="复职";
			//更新原入职资料为在职状态
			$upSql = "UPDATE $DataPublic.staffmain M,$DataPublic.dimissiondata D SET M.Estate=1 WHERE D.Id=$Id AND D.Number=$Number AND M.Number=D.Number";
			$upResult = mysql_query($upSql);
			if($upResult){
				$Log="离职员工 $Number 复职设定成功。<br>";
				$Del = "DELETE FROM $DataPublic.dimissiondata WHERE Id=$Id AND Number=$Number LIMIT 1"; 
				$delResult = mysql_query($Del);
				if($delResult){
					$Log.="离职员工 $Number 的离职资料清除成功.<br>";
					}
				else{
					$Log.="<div class='redB'>离职员工 $Number 的离职资料清除失败.</div><br>";
					$OperationResult="N";
					}
				
				if($delM==1){
					$Log.="需扣除离职月份的工龄;";
					//离职月份计算
					$delGL=outDateDiff("month",$outDate,$BackDate);
					$inRecode="INSERT INTO $DataPublic.dimissiongl (Id, Number, outDate, BackDate, delGL, Date,Operator) VALUES (NULL,'$Number','$outDate','$BackDate','$delGL','$Date','$Operator')";
					$inAction=@mysql_query($inRecode);
					if($inAction){
						$Log.="离职期间的工龄记录成功.<br>";
						}
					else{
						$Log.="<div class='redB'>离职期间的工龄记录失败.</div><br>";
						$OperationResult="N";
						}
					}
				else{
					$Log.="不扣除离职月份的工龄.<br>";					
					}
					
				}
			else{
				$Log="<div class='redB'>离职员工 $Number 复职设定失败.</div><br>";
				$OperationResult="N";
				}
			}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>