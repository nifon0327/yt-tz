<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.redeployb
$DataPublic.staffmain
$DataIn.cwxzsheet
二合一已更新
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工津贴扣款";		//需处理
$upDataSheet="$DataPublic.staff_lateearly";	//需处理
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
	default://起效的月份不能少于上一次的月份,如果月份已生成薪资，也不能更新
		$Date=date("Y-m-d");
		$Remark=FormatSTR($Remark);
		//更新
		$UpSql="UPDATE $upDataSheet A 
				LEFT JOIN $DataIn.cwxzsheet B ON A.Month=B.Month AND A.Number=B.Number
		    SET 
			A.Month='$Month',A.Amount='$Amount',A.Remark='$Remark',A.Date='$Date',A.Operator='$Operator'
			WHERE A.Id='$Id' AND B.Month IS NULL";
		$UpResult=mysql_query($UpSql);
		if($UpResult && mysql_affected_rows()>0){
			$Log="员工 $Number 的 $Log_Item$Log_Funtion 更新成功.";
			}
		else{
			$Log="<div class='redB'>员工 $Number 的 $Log_Item$Log_Funtion更新失败,检查月份是否符合条件,已生成工资？. $UpSql </div>";
			$OperationResult="N";
			}
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>