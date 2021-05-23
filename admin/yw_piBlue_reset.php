<?php
defined('IN_COMMON') || include '../basic/common.php';

include "invoicetopdf_blue/config.php";  //字体颜色，行高等配置文件
$Commoditycode="";
$FromFunPos="CH";

$QtySUM=0;
$AmountSUM=0;
$oldPO="";
$OrderPOs="";
//公司信息

$clientResult = mysql_query("
SELECT 
C.Forshort,U.Symbol,C.CompanyId,
I.Company,I.Fax,I.Address,D.InvoiceModel,D.Wise,D.SoldTo,D.Address AS ToAddress,D.EndPlace AS EndPlace,S.Nickname,S.Name as ZName,P.ShipTo,P.SoldTo as ClientSoldTo,C.PriceTerm,F.Mobile,P.Operator
FROM $DataIn.yw3_pisheet P
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=8
LEFT JOIN $DataIn.ch8_shipmodel D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataIn.staffmain S ON S.Number=P.Operator
LEFT JOIN $DataIn.staffsheet F ON F.Number=S.Number
WHERE P.PI='$PI' AND  D.PiSign=1  
UNION
SELECT 
C.Forshort,U.Symbol,C.CompanyId,
I.Company,I.Fax,I.Address,D.InvoiceModel,D.Wise,D.SoldTo,D.Address AS ToAddress,D.EndPlace AS EndPlace,S.Nickname,S.Name as ZName,P.ShipTo,P.SoldTo as ClientSoldTo,C.PriceTerm,F.Mobile,P.Operator   
FROM  $DataIn.yw3_pisheet P
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=8
LEFT JOIN $DataIn.ch8_shipmodel D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataIn.staffmain S ON S.Number=P.Operator
LEFT JOIN $DataIn.staffsheet F ON F.Number=S.Number
WHERE P.PI='$PI' ",$link_id);

if($clientRows = mysql_fetch_array($clientResult)){
	$Symbol=$clientRows["Symbol"]=="USD"?"U.S.DOLLARS":$clientRows["Symbol"];
	$Forshort=$clientRows["Forshort"];
	$Company=$clientRows["Company"];
        $CompanyId=$clientRows["CompanyId"];
	include "invoicetopdf/company_info.php";  //相应公司附近加的信息
	$FaxNo=$clientRows["Fax"];
	$Address=$clientRows["Address"];
	if($InvoiceModel==""){
		$InvoiceModel=$clientRows["InvoiceModel"];
	}
	$Wise=$clientRows["Wise"];
	$ShipTo=$clientRows["ShipTo"];
	$Priceterm=$clientRows["Priceterm"];
    $SoldTo=$Company;
	$ToAddress=$Address;
	switch ($CompanyId) {
		case 1090:
		    $SoldTo=$clientRows["EndPlace"];
			$ToAddress=$clientRows["ToAddress"];
			break;
	}

	$ClientSoldTo=$clientRows["ClientSoldTo"];

	$Nickname=$clientRows["Nickname"];
	$ZName=$clientRows["ZName"];
	$Mobile=$clientRows["Mobile"];

	$Operator= $clientRows["Operator"];
	$pResult = mysql_query("SELECT M.Name,M.Nickname,F.Mobile 
						    FROM $DataIn.staffmain M 
						    LEFT JOIN $DataIn.staffsheet F ON F.Number=M.Number
						    WHERE M.Number='$Operator'  LIMIT 1",$link_id);
	if($pRow = mysql_fetch_array($pResult)){
		   $Transactor=$pRow["Nickname"];
		   $ZTransactor=$pRow["Name"];
		   $TransMobile=$pRow["Mobile"];
	}

}

$sheetResult = mysql_query("SELECT S.OrderPO,S.Id,S.Qty,S.Price,S.ShipType,P.eCode,P.Description,I.Leadtime,I.PaymentTerm,I.Notes,I.OtherNotes,I.Terms,P.bjRemark,I.Remark 
FROM $DataIn.yw3_pisheet I
LEFT JOIN $DataIn.yw1_ordersheet S ON I.oId=S.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
WHERE I.PI='$PI' ORDER BY I.Id",$link_id);
if($sheetRows = mysql_fetch_array($sheetResult)){
	$i=1;
	do{
		$OrderPO=$sheetRows["OrderPO"];
		if($OrderPO!="" && $oldPO!=$OrderPO){
			$OrderPOs=$OrderPOs==""?"PO#".$OrderPO:($OrderPOs."/".$OrderPO);
			$oldPO=$OrderPO;
			}
		$Id=$sheetRows["Id"];
		$Qty=$sheetRows["Qty"];
		$QtySUM=$QtySUM+$Qty;
		$Price=sprintf("%.3f",$sheetRows["Price"]);
		$Amount=sprintf("%.2f",$Qty*$Price);
		$AmountSUM=sprintf("%.2f",$AmountSUM+$Amount);
		$eCode=$sheetRows["eCode"];
		$Description=$sheetRows["Description"];
		$ShipType=$sheetRows["ShipType"];
		$eShipType="&nbsp;";
		if (is_numeric($ShipType)){
		   	$shipTypeResult = mysql_query("SELECT Id,Name,eName FROM $DataPublic.ch_shiptype WHERE Id='$ShipType' LIMIT 1",$link_id);
		   	if($shipTypeRow = mysql_fetch_array($shipTypeResult)){
		   	   $ShipType=$shipTypeRow["Name"];
			   $eShipType=$shipTypeRow["eName"];
			   if($InvoiceModel!=2){
				  $ShipType=$eShipType;
			   }
		   	}
		   	else $ShipType="&nbsp;";
	   }
	   else{
		   $ShipType="&nbsp;";
	   }
		$Leadtime=$sheetRows["Leadtime"];

		$Leadtime=str_replace("*", "", $Leadtime);
		 if ($Leadtime!=""){
				$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek",$link_id));
		        $PIWeek=$dateResult["PIWeek"];
		        $week=substr($PIWeek, 4,2);
			    //$Leadtime="Week " . $week;
			    if ($CompanyId==1090){
				     $dateArray= GetWeekToDate($PIWeek,"jS M",2);
			    }
			    else{
			        $dateArray= GetWeekToDate($PIWeek,"jS M");
			     }
		        $Leadtime="W" . $week . "." . $dateArray[1];
        }
        else{
              $Leadtime="TBC";
        }
		$PaymentTerm=$sheetRows["PaymentTerm"];
		$PaymentTerm=$PaymentTerm==""?"":"Payment term:$PaymentTerm <br>";  //放在Terms里
		$Notes=$sheetRows["Notes"];
        $OtherNotes=$sheetRows["OtherNotes"];
		$Terms=$sheetRows["Terms"];
		$bjRemark=$sheetRows["bjRemark"];

		$Remark =$sheetRows["Remark"];
		$pimodel="pimodel";
		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格

        if ($CompanyId=="100155"){
	        $eCode.=" - $Description";
        }
			//新版只一种格式,没有多个 2014-10-10
			$iTableList.="
				<tr>
				<td valign=middle align=center height=$RowsHight>$i</td>
				<td valign=middle>$OrderPO</td>
				<td valign=middle>$eCode</td>
				<td valign=middle align=right>$Price</td>
				<td valign=middle align=right>$Qty</td>
				<td valign=middle align=right>$Amount</td>
				<td valign=middle>$ShipType</td>
				<td valign=middle>$Leadtime</td>
				</tr>";

				$remarkSign=0;
				switch ($CompanyId) {
					case 1024: //Kondor
					case 1031: //Elite
					case 1101: //XS Mobile
					case 1073: //smart mobile
					case 1090: //Avenir
						//每一条记录都是一个表格
						$remarkSign=1;
						$$eurTableNo="<table  border=1 >
						<tr >
						<td width=8 align=left height=$RowsHight valign=middle >$i</td>
						<td width=30 align=left valign=middle >$OrderPO</td>
						<td width=44 align=left valign=middle >$eCode</td>				
						<td width=14 align=center valign=middle align=right>$Price</td>
						<td width=14 align=center valign=middle align=right>$Qty</td>
						<td width=17 align=center valign=middle align=right>$Amount</td>
						<td width=17 align=center valign=middle >$ShipType</td>
						<td width=22 align=center valign=middle >$Leadtime</td>
						<td width=18 align=center valign=middle >$Remark</td>
						</tr></table>";

						break;
					default:
						//每一条记录都是一个表格
						$$eurTableNo="<table  border=1 >
						<tr >
						<td width=8 align=left height=$RowsHight valign=middle >$i</td>
						<td width=30 align=left valign=middle >$OrderPO</td>
						<td width=62 align=left valign=middle >$eCode</td>				
						<td width=14 align=center valign=middle align=right>$Price</td>
						<td width=14 align=center valign=middle align=right>$Qty</td>
						<td width=17 align=center valign=middle align=right>$Amount</td>
						<td width=17 align=center valign=middle >$ShipType</td>
						<td width=22 align=center valign=middle >$Leadtime</td>
						</tr></table>";
						break;
				}

			$RemarkTableNo="RemarkTableNo".strval($i);
			$companyResult=mysql_query("SELECT * FROM yw3_pirules WHERE CompanyId='$CompanyId'",$link_id);
		    if(mysql_num_rows($companyResult)>0){
			    $$RemarkTableNo="<table  border=0>
				<tr >
				<td width=181 align=right   height=$RowsHight valign=middle>$bjRemark</td>
				<td width=1 align=right  ></td>
				</tr></table>";

			    }
			else{
				$$RemarkTableNo="";
			}
		 $i++;
		}while ($sheetRows = mysql_fetch_array($sheetResult));
	}

$Counts=$i;  //记录条数
//加上总计

$info_comefrom="PI";  //表示来自PI
include "../model/subprogram/mycompany_info.php";   //公司信息

include "subprogram/mybank_info.php";//银行卡信息

$bankResult = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.my2_bankinfo WHERE Id='$BankId' LIMIT 1",$link_id));
$Beneficary=$bankResult["Beneficary"];
$Bank=$bankResult["Bank"];
$BankAdd=$bankResult["BankAdd"];
$SwiftID=$bankResult["SwiftID"];
$ACNO=$bankResult["ACNO"];

$BankTable ="
<table  border=0 >
<tr >
<td width=18 align=left valign=middle >BENEFICIARY:</td>
<td width=166 align=left valign=middle  colspan='2'>$Beneficary</td>
</tr>
<tr >
<td width=18  align=left valign=middle >BANK:</td>
<td width=166 align=left valign=middle  colspan='2'>$Bank</td>
</tr>
<tr >
<td width=18  align=left valign=middle >BANK Add:</td>
<td width=166 align=left valign=middle  colspan='2'>$BankAdd</td>
</tr>
<tr >
<td width=18  align=left valign=middle >SWIFT ID:</td>
<td width=166  align=left valign=middle  colspan='2'>$SwiftID</td>
</tr>
<tr >
<td width=18  align=left valign=middle >A/C NO:</td>
<td width=66  align=left valign=middle  >$ACNO</td>
<td width=102 align=right valign=middle >This document applies to Ash Cloud Co., Ltd. Shenzhen and all its subsidiaries.</td>
</tr>
</table>";


$ChinaBankTable ="
<table  border=0 >
<tr   >
<td width=18 align=left valign=middle >开户名:</td>
<td width=166 align=left valign=middle  colspan='2'>$Beneficary</td>
</tr>
<tr >
<td width=18  align=left  valign=middle >开户行:</td>
<td width=166 align=left valign=middle colspan='2' >$Bank</td>
</tr>
<tr >
<td width=18  align=left valign=middle >银行地址:</td>
<td width=166 align=left valign=middle colspan='2' >$BankAdd</td>
</tr>
<tr >
<td width=18  align=left valign=middle >SWIFT ID:</td>
<td width=166  align=left valign=middle colspan='2' >$SwiftID</td>
</tr>
<tr >
<td width=18  align=left valign=middle >A/C NO:</td>
<td width=66  align=left valign=middle  >$ACNO</td>
<td width=102 align=right valign=middle >本PI内容适用于上海研砼治筑建筑科技有限公司及其控股公司</td>
</tr>
</table>";

$eurTableNo="eurTableNo".strval($Counts);

include "pimodel_Blue/PipublicTotal.php";

$FilePath="../download/pipdf/";
if(!file_exists($FilePath)){
		makedir($FilePath);
}

$filename="../download/pipdf/".$PI.".pdf";
if(file_exists($filename)){unlink($filename);}

switch ($InvoiceModel){
   case 3:  //中文繁体
       $InvoiceModel =2;
	case 2:   //中文
	include "pimodel_Blue/pimodel_".$InvoiceModel.".php"; break;
	default:  //英文
	include "pimodel_Blue/pimodel_1.php"; break;
}
$pdf->Output("$filename","F");
$Log.="<br>PI $PI 新Pi重置完成!";
?>