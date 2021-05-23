<?php 
//电信-zxq 2012-08-01
//$DataIn.cw6_advancesreceived  二合一已更新
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="预收货款";		//需处理
$upDataSheet="$DataIn.cw6_advancesreceived";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=$Date==""?date("Y-m-d"):$Date;
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch ($ActionId){
	case 7:		//OK
		$Log_Funtion="锁定";		$SetStr="Locks=0";						include "../model/subprogram/updated_model_3d.php";		break;
	case 8:		//OK
		$Log_Funtion="解锁";		$SetStr="Locks=1";						include "../model/subprogram/updated_model_3d.php";		break;
	default://OK
     if($Attached!=""){//有上传文件
		   $FileType=".jpg";
		   $OldFile=$Attached;
		   $FilePath="../download/cwadvance/";
		   if(!file_exists($FilePath)){
			    makedir($FilePath);
			  }
		   $PreFileName="H".$Id.$FileType;
		   $Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
		}
        $AttachedStr=$Attached==""?"":",Attached='$Attached'";
		$Remark=FormatSTR($Remark);		
		$SetStr="BankId='$BankId',CompanyId='$CompanyId',Amount='$Amount',Remark='$Remark',PayDate='$PayDate' $AttachedStr";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Estate=$Estate&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>