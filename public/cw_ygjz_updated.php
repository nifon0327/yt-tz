<?php 
//电信-zxq 2012-08-01
//$DataIn.cwygjz 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="员工借支记录";		//需处理
$upDataSheet="$DataIn.cwygjz";	//需处理
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
		$FilePath="../download/cwygjz/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$PreFileName1="J".$Id.".jpg";
		if($Attached!=""){
			$OldFile1=$Attached;
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);				
			$BillSTR=$uploadInfo1==""?",Payee='0'":",Payee='1'";
			}
		if($BillSTR=="" && $oldAttached!=""){//没有上传文件并且已选取删除原文件
			$FilePath1=$FilePath.$PreFileName1;
			if(file_exists($FilePath1)){
				unlink($FilePath1);
				}
			$BillSTR=",Payee='0'";
			}
		$Remark=FormatSTR($Remark);
		$thisMonth=substr($PayDate,0,7);
		//$OtherWhere="and $upDataSheet.Number NOT IN (SELECT cwxzsheet.Number From cwxzsheet WHERE cwxzsheet.Month='$thisMonth' ORDER BY cwxzsheet.Number)";
		if($Mid==0){
			$SetStr="BankId='$BankId',Amount='$Amount',PayDate='$PayDate',Remark='$Remark',Operator='$Operator',Locks='0' $BillSTR";
			}
		else{
			$SetStr="BankId='$BankId',Remark='$Remark',Operator='$Operator',Locks='0' $BillSTR";
			}
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>