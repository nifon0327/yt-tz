<?php 
//ewen 2013-03-18 OK
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM采购预付订金";		//需处理
$upDataSheet="$DataIn.nonbom11_djsheet";	//需处理
$upDataMain="$DataIn.nonbom11_djmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=$Date==""?date("Y-m-d"):$Date;
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$FileDir="/nonbomdj/";	
switch ($ActionId){
	case 7:		//OK
		$Log_Funtion="锁定";		$SetStr="Locks=0";						include "../model/subprogram/updated_model_3d.php";		break;
	case 8:		//OK
		$Log_Funtion="解锁";		$SetStr="Locks=1";						include "../model/subprogram/updated_model_3d.php";		break;
	case 14:	//OK
		$Log_Funtion="请款";		$SetStr="Estate=2,Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 15://OK
		$OtherWhere=" AND Did='0' ";
		$Log_Funtion="退回";
		if($fromWebPage==$funFrom."_m"){	//审核退回OK
			$Log_Funtion="审核退回";			$SetStr="Estate=4,Locks=1,ReturnReasons='审核退回: $ReturnReasons'";		include "../model/subprogram/updated_model_3b.php";			
			}
		else{							//财务退回
			if($Estate==3){					//未结付退回OK
				$Log_Funtion="未结付退回";		$SetStr="Estate=4,Locks=1,ReturnReasons='未结付退回: $ReturnReasons'";		include "../model/subprogram/updated_model_3b.php";
				}
			else{							//已结付退回，要处理现金流水帐
				$Log_Funtion="已结付退回";		$SetStr="Mid=0,Estate=4,Locks=1,ReturnReasons='已结付退回: $ReturnReasons'";		include "../model/subprogram/updated_model_3c.php";
				}			
			}
		break;
	case 16://OK
		$Log_Funtion="取消结付";	$SetStr="Mid=0,Estate=3,Locks=0";		include "../model/subprogram/updated_model_3c.php";		break;
	case 17:// OK
		$Log_Funtion="审核";		$SetStr="Estate=3,Locks=0";				include "../model/subprogram/updated_model_3d.php";		$fromWebPage=$funFrom."_m"; break;
	case 18://OK
		$Log_Funtion="结付";		include "../model/subprogram/updated_model_3pay.php";
		$Estate=0;$fromWebPage=$funFrom."_cw";
		break;
	case 20://OK
		$Log_Funtion="主结付单资料更新";		
		include "../model/subprogram/updated_model_cw.php";
		$Estate=0;
		break;
     case 84://补传发票文件
	     $Log_Funtion="上传凭证";	
         $FilePath="../download/nonbomht/";
         if(!file_exists($FilePath)){
				makedir($FilePath);
		}
		$PreFileName1="C".$Id.".jpg";
		if($Attached!=""){
			$OldFile1=$Attached;
			
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);
			//$InvoiceFileSTR=$uploadInfo1==""?",ContractFile=1";
			if ($uploadInfo1){
			    $Log="";
				$SetStr="ContractFile=1";
				include "../model/subprogram/updated_model_3a.php";   
			}
		}
       // $InvoiceNUM=FormatSTR($InvoiceNUM);
		//$SetStr="`InvoiceNUM`='$InvoiceNUM' $InvoiceFileSTR";
		
      break;		
	default://OK
		$Remark=FormatSTR($Remark);		
		$SetStr="CompanyId='$CompanyId',PurchaseID='$PurchaseID',Amount='$Amount',Remark='$Remark',Date='$Date'";
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