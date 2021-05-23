<?php 
include "../model/modelhead.php";
//步骤2：
$Log_Item="出货文档模板设置";			//需处理
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
$Date=date("Y-m-d");
//步骤3：需处理
$Date=date("Y-m-d");

$OutSign=$OutCheck==""?0:$OutCheck;
$OutSign=intval($OutSign);

$inRecode="INSERT INTO $DataIn.ch8_shipmodel (Id,CompanyId,Title,Company,InvoiceModel,LabelModel,StartPlace,EndPlace,Contact,TEL,SoldFrom,FromAddress,FromFaxNo,SoldTo,Address,FaxNo,Wise,PISign,OutSign,Date,Estate,Locks,Operator) 
VALUES (NULL,'$CompanyId','$Title','$Company','$InvoiceModel','$LabelModel','$StartPlace','$EndPlace','$Contact','$TEL','$SoldFrom','$FromAddress','$FromFaxNo','$SoldTo','$Address','$FaxNo','$Wise','$PISign','$OutSign','$Date','1','0','$Operator')";

$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	    //echo "OutSign: $OutSign <br>";
		if($Mid>0 && $OutSign>0 && $OutCompanyName!="" ){  //转外发公司
			$inRecodeOut="INSERT INTO $DataIn.ch8_shipoutCompany (Id,Mid,CompanyId,OutCompanyName,OutCurrency,OutAddress,OutTel,OutFax,OutURL,OutRequistion,OutReqTel,OutBeneficiary,OutBeneficiaryCode,OutSWIFTAddress,OutAccountName,OutAccountNumber,OutBankAddress,OutRemark,Date,Estate,Locks,Operator) 
			VALUES (NULL,'$Mid','$CompanyId','$OutCompanyName','$OutCurrency','$OutAddress','$OutTel','$OutFax','$OutURL','$OutRequistion','$OutReqTel','$OutBeneficiary','$OutBeneficiaryCode','$OutSWIFTAddress','$OutAccountName','$OutAccountNumber','$OutBankAddress','$OutRemark','$Date','1','0','$Operator')";			
			$inAction=@mysql_query($inRecodeOut);
		}
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败 $inRecode !</div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
