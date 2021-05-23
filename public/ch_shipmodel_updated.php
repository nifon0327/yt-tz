<?php 
//电信-zxq 2012-08-01
//步骤1 $DataIn.ch8_shipmodel 二合一已更新
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="出货文档模板";			//需处理
$upDataSheet="$DataIn.ch8_shipmodel";	//需处理
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
		$Date=date("Y-m-d");

		$OutSign=$OutCheck==""?0:$OutCheck;
		$OutSign=intval($OutSign);
		
		$SetStr="CompanyId='$CompanyId',Title='$Title',Company='$Company',InvoiceModel='$InvoiceModel',LabelModel='$LabelModel',StartPlace='$StartPlace',EndPlace='$EndPlace'
		,Contact='$Contact',TEL='$TEL',SoldFrom='$SoldFrom',FromAddress='$FromAddress',FromFaxNo='$FromFaxNo'
		,SoldTo='$SoldTo',Address='$Address',FaxNo='$FaxNo',Wise='$Wise',PISign='$PISign',OutSign='$OutSign',Date='$Date',Locks='0',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		

		 
		$Mid=$Id;
		$clientResult = mysql_query("SELECT * FROM $DataIn.ch8_shipoutCompany WHERE MId='$Id' ORDER BY Id LIMIT 1 ",$link_id);
		if($clientRow = mysql_fetch_array($clientResult)) {
			$SetStrClient="OutCompanyName='$OutCompanyName',OutCurrency='$OutCurrency',OutAddress='$OutAddress',OutTel='$OutTel',OutFax='$OutFax',OutURL='$OutURL', OutRequistion='$OutRequistion',OutReqTel='$OutReqTel', OutBeneficiary='$OutBeneficiary',
			OutBeneficiaryCode='$OutBeneficiaryCode',OutSWIFTAddress='$OutSWIFTAddress'
			,OutAccountName='$OutAccountName',OutAccountNumber='$OutAccountNumber',OutBankAddress='$OutBankAddress'
			,OutRemark='$OutRemark',Date='$Date',Locks='0',Operator='$Operator'";	
			$updateSQLCli = "UPDATE $DataIn.ch8_shipoutCompany SET $SetStrClient WHERE MId='$Id' ";
			$updateCliResult = mysql_query($updateSQLCli);					
		}
		else{
			//echo "$Mid:  $OutCompanyName <br> ";
			if($Mid>0 &&  $OutCompanyName!=""){  //转外发公司
				$inRecodeOut="INSERT INTO $DataIn.ch8_shipoutCompany (Id,Mid,CompanyId,OutCompanyName,OutCurrency,OutAddress,OutTel,OutFax,OutURL,OutRequistion,OutReqTel,OutBeneficiary,OutBeneficiaryCode,OutSWIFTAddress,OutAccountName,OutAccountNumber,OutBankAddress,OutRemark,Date,Estate,Locks,Operator) 
			VALUES (NULL,'$Mid','$CompanyId','$OutCompanyName','$OutCurrency','$OutAddress','$OutTel','$OutFax','$OutURL','$OutRequistion','$OutReqTel','$OutBeneficiary','$OutBeneficiaryCode','$OutSWIFTAddress','$OutAccountName','$OutAccountNumber','$OutBankAddress','$OutRemark','$Date','1','0','$Operator')";	
				echo "$inRecodeOut";
				$inAction=@mysql_query($inRecodeOut);
			}			
		}
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>