<?php 
//电信---yang 20120801
//代码、数据库合并后共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="公司收款帐号资料";		//需处理
$upDataSheet="$DataPublic.my2_bankinfo";	//需处理
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
		$Log_Funtion="可用";	$SetStr="Estate=1";				include "../model/subprogram/updated_model_3b.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0";				include "../model/subprogram/updated_model_3b.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
	    $bankLogoPath ="../download/banklogo/";
		if(!file_exists($bankLogoPath)){
				makedir($bankLogoPath);
		}
		 //上传公司logo
		if($Logo!=""){
			$newLogoFileName="newbank_".$Id.".png";
			$uploadInfo=UploadPictures($Logo,$newLogoFileName,$bankLogoPath);
			if ($uploadInfo!=''){
				$Log="上传logo图片成功. $uploadInfo<br>";
			}
		}


		$Title=FormatSTR($Title);
		$Beneficary=FormatSTR($Beneficary);
		$Bank=FormatSTR($Bank);
		$BankAdd=FormatSTR($BankAdd);
		$SwiftID=FormatSTR($SwiftID);
		$ACNO=FormatSTR($ACNO);
		$CnapsCode=FormatSTR($CnapsCode);
		$SetStr="cSign='$cSign',Title='$Title',Beneficary='$Beneficary',Bank='$Bank',BankAdd='$BankAdd',SwiftID='$SwiftID',
		ACNO='$ACNO',Locks='0',CnapsCode='$CnapsCode',modifier='$Operator',modified='$DateTime'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>