<?php   
//电信-zxq 2012-08-01
/*
$DataIn.ch6_creditnote
$DataIn.ch1_shipmain
$DataIn.ch1_shipsheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="扣款资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_topdf";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$chooseDate=substr($rkDate,0,7);
$ALType="CompanyId=$CompanyId";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
//保存主单资料
$checkNumber=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataIn.ch1_shipmain",$link_id));
$Number=$checkNumber["Number"]+1;

	$Ustr=str_replace("*",'', $Forshort);
	$Ustr=str_replace("¥", "(RMB)", $Ustr);
	$Ustr=str_replace("￥", "(RMB)", $Ustr);
	
	include "../model/zh2py.php";  //中文转拼单
	$py=CUtf8_PY::encode($Ustr);
	$Ustr=strtoupper($py); 
	if(strtoupper(substr($Ustr,0,2))!='CG'){
		$Field=explode(" ",$Ustr);  //AF Muvit, AF Brand 只要AF
		$Ustr=$Field[0];
	}
	
//echo "Forshort:  $Forshort";
/*
$Ustr=str_replace("*",'', $Forshort);
$Ustr=str_replace("¥", "(RMB)", $Ustr);

$strA = trim($Forshort);
$lenA = strlen($strA);
$lenB = mb_strlen($strA, "utf-8");
if (($lenA != $lenB) && ($lenA % $lenB == 0)){
	$chinese=new chinese;
	$ULetter=$chinese->c($Forshort);
	//$Ustr=preg_replace('/[a-z]/','',$ULetter);
	$Ustr=$ULetter;
}
if(strtoupper(substr($Ustr,0,2))!='CG'){
	$Field=explode(" ",$Ustr);  //AF Muvit, AF Brand 只要AF
	$Ustr=$Field[0];
}
*/

//$InvoiceNO=$Forshort." ".$ShipType." ".$InvoiceNO;  //更改
$InvoiceNO=$Ustr." ".$ShipType." ".$InvoiceNO;  //更改


if ($ShipType=='debit') { //向客户要钱
	$Sign=1; //收钱
}
else 
{
	$Sign=-1;  //扣钱
}

$mainInSql="INSERT INTO $DataIn.ch1_shipmain (Id,CompanyId,ModelId,BankId,Number,InvoiceNO,InvoiceFile,Wise,Notes,Terms,PaymentTerm,PreSymbol,Date,Estate,Locks,Sign,Ship,ShipType,cwSign,Remark,Operator) 
VALUES (NULL,'$CompanyId','$ModelId','$BankId','$Number','$InvoiceNO','1','$Wise','$Notes','','','','$ShipDate','0','1','$Sign','-1','$ShipType','1','','$Operator')";


$mainInAction=@mysql_query($mainInSql);
$Mid=mysql_insert_id();
if($mainInAction){
	$Log.="扣款主单($Mid)创建成功.<br>";
	$sheetInSql="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,'$Mid','0',Number,'0',Qty,Price,'1','3','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM ch6_creditnote WHERE Id IN ($Ids)";

	$sheetInAction=@mysql_query($sheetInSql);
	if($sheetInAction && mysql_affected_rows()>0){
		$Log.="扣款项目($Ids)加入扣款单明细表成功.<br>";
		//更新状态
		$pUpSql=mysql_query("UPDATE $DataIn.ch6_creditnote SET Estate='0' WHERE Id IN ($Ids)");
		if($pUpSql && mysql_affected_rows()>0){
			$Log.="扣款项目($Ids)的已出状态更新成功.<br>";
			}
		else{
			$Log.="<div class='redB'>扣款项目($Ids)的已出状态更新失败. $pUpSql </div><br>";
			$OperationResult="N";
			}
		}
	else{
		$Log.="<div class='redB'>扣款项目($Ids)加入扣款单明细表失败. $sheetInSql </div><br>";
		$OperationResult="N";
		}
	$Id=$Mid;
	include "ch_creditnoteBlue_topdf.php";
	}
else{
	$Log.="<div class='redB'>扣款主单($Mid)创建失败. $mainInSql </div><br>";
	$OperationResult="N";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>