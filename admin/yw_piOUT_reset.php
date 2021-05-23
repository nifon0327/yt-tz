<?php
defined('IN_COMMON') || include '../basic/common.php';

//电信-zxq 2012-08-01
$RowsHight=5;
$InvoiceHeadFontSize=9;
$TableFontSize=8;

$QtySUM=0;
$AmountSUM=0;
$oldPO="";
$OrderPOs="";
//公司信息
//include "../model/subprogram/mycompany_info.php";
/*
$clientResult = mysql_query("SELECT
C.Forshort,U.Symbol,
I.Company,I.Fax,I.Address,D.SoldTo,D.Address AS ToAddress,S.Nickname
FROM $DataIn.trade_object C
LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=1
LEFT JOIN $DataIn.ch8_shipmodel D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain S ON S.Number=C.Staff_Number
WHERE C.CompanyId=$CompanyId LIMIT 1",$link_id);
*/

$clientResult = mysql_query("
SELECT 
C.Forshort,U.Symbol,C.CompanyId,
I.Company,I.Fax,I.Address,D.SoldTo,D.Address AS ToAddress,D.EndPlace AS EndPlace,S.Nickname,P.ShipTo,C.PriceTerm,
D.SoldFrom,D.FromAddress,
O.OutCompanyName,O.OutAddress,O.OutTel,O.OutFax,O.OutURL,O.OutRequistion,O.OutReqTel,O.OutBeneficiary,O.OutBeneficiaryCode,O.OutSWIFTAddress,O.OutAccountName,O.OutAccountNumber,O.OutBankAddress,O.OutRemark,D.Id as ModelId
FROM $DataIn.yw3_pisheet P
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=8
LEFT JOIN $DataIn.ch8_shipmodel D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataIn.ch8_shipoutcompany O ON O.Mid=D.Id
LEFT JOIN $DataPublic.staffmain S ON S.Number=P.Operator
WHERE P.PI='$PI' AND  D.PiSign=1  LIMIT 1
UNION
SELECT 
C.Forshort,U.Symbol,C.CompanyId,
I.Company,I.Fax,I.Address,D.SoldTo,D.Address AS ToAddress,D.EndPlace AS EndPlace,S.Nickname,P.ShipTo,C.PriceTerm,  
D.SoldFrom,D.FromAddress,
O.OutCompanyName,O.OutAddress,O.OutTel,O.OutFax,O.OutURL,O.OutRequistion,O.OutReqTel,O.OutBeneficiary,O.OutBeneficiaryCode,O.OutSWIFTAddress,O.OutAccountName,O.OutAccountNumber,O.OutBankAddress,O.OutRemark,D.Id as ModelId
FROM  $DataIn.yw3_pisheet P
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=8 
LEFT JOIN $DataIn.ch8_shipmodel D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataIn.ch8_shipoutcompany O ON O.Mid=D.Id
LEFT JOIN $DataPublic.staffmain S ON S.Number=P.Operator
WHERE P.PI='$PI' LIMIT 1",$link_id);


if($clientRows = mysql_fetch_array($clientResult)){
	$Symbol=$clientRows["Symbol"]=="USD"?"U.S.DOLLARS":$clientRows["Symbol"];
	$Forshort=$clientRows["Forshort"];
	//SOLD TO:  $Company, $Address
	$Company=$clientRows["Company"];
    $CompanyId=$clientRows["CompanyId"];
	include "invoicetopdf/company_info.php";  //相应公司附近加的信息
	$FaxNo=$clientRows["Fax"];
	$Address=$clientRows["Address"];
	//$SoldTo=$clientRows["SoldTo"]==""?$Company:$clientRows["SoldTo"];
	//$ToAddress=$clientRows["ToAddress"]==""?$Address:$clientRows["ToAddress"];

	//SHIP TO: $SoldTo, $ToAddress
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
	$Nickname=$clientRows["Nickname"];

	//**********************************

	$OutCompanyName=$clientRows["OutCompanyName"];
	$OutAddress=$clientRows["OutAddress"];
	$OutRequistion=$clientRows["OutRequistion"];
	$OutBeneficiary=$clientRows["OutBeneficiary"];
	$OutBeneficiaryCode=$clientRows["OutBeneficiaryCode"];
	$OutSWIFTAddress=$clientRows["OutSWIFTAddress"];
	$OutAccountName=$clientRows["OutAccountName"];
	$OutAccountNumber=$clientRows["OutAccountNumber"];
	$OutBankAddress=$clientRows["OutBankAddress"];
	$OutRemark=$clientRows["OutRemark"];

	$OutTel=$clientRows["OutTel"];
	$OutFax=$clientRows["OutFax"];
	$OutURL=$clientRows["OutURL"];
	$OutReqTel=$clientRows["OutReqTel"];

	$SoldFrom=$clientRows["SoldFrom"];
	$FromAddress=$clientRows["FromAddress"];
	$SoldTo=$clientRows["SoldTo"];
	$Address=$clientRows["ToAddress"];
	$ModelId=$clientRows["ModelId"];  //ModelId=482

	}

$sheetResult = mysql_query("SELECT S.OrderPO,S.Id,S.Qty,P.OutPrice,S.ShipType,P.eCode,P.Description,I.Leadtime,I.PaymentTerm,I.Notes,I.OtherNotes,I.Terms,P.bjRemark,I.Remark 
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
		//$Price=sprintf("%.3f",$sheetRows["Price"]);
		$Price=sprintf("%.3f",$sheetRows["OutPrice"]);

		$Amount=sprintf("%.2f",$Qty*$Price);
		$AmountSUM=sprintf("%.2f",$AmountSUM+$Amount);
		$eCode=$sheetRows["eCode"];
		$Description=$sheetRows["Description"];
		$ShipType=$sheetRows["ShipType"];
		if (is_numeric($ShipType)){
		   	$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE Id='$ShipType' LIMIT 1",$link_id);
		   	if($shipTypeRow = mysql_fetch_array($shipTypeResult)){
		   	   $ShipType=$shipTypeRow["Name"];
		   	}
		   	else $ShipType="&nbsp;";
	   }
	   else{
		   $ShipType="&nbsp;";
	   }
		$Leadtime=$sheetRows["Leadtime"];
		/*
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
		*/
		$PaymentTerm=$sheetRows["PaymentTerm"];
		$PaymentTerm=$PaymentTerm==""?"":"Payment term:$PaymentTerm <br>";  //放在Terms里
		$Notes=$sheetRows["Notes"];
        $OtherNotes=$sheetRows["OtherNotes"];
		$Terms=$sheetRows["Terms"];
		$bjRemark=$sheetRows["bjRemark"];

		$Remark =$sheetRows["Remark"];
		$pimodel="pimodel";
		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
		/*
		if($CompanyId==1018){
			$iTableList.="
				<tr>
				<td valign=middle align=center height=$RowsHight>$i</td>
				<td valign=middle>$eCode</td>
				<td valign=middle>$Description</td>
				<td valign=middle align=right>$Price</td>
				<td valign=middle align=right>$Qty</td>
				<td valign=middle align=right>$Amount</td>
				<td valign=middle>$Leadtime</td>
				</tr>";

				//每一条记录都是一个表格
				$$eurTableNo="<table  border=1 >
				<tr >
				<td width=15 align=center height=$RowsHight valign=middle >$i</td>
				<td width=30 align=center valign=middle >$eCode</td>
				<td width=63 align=Left valign=middle >$Description</td>
				<td width=19 valign=middle align=right>$Price</td>
				<td width=19 valign=middle align=right>$Qty</td>
				<td width=19 valign=middle align=right>$Amount</td>
				<td width=30 align=center valign=middle >$Leadtime</td>
				</tr></table>";
		}
		else{
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

				//每一条记录都是一个表格

				$$eurTableNo="<table  border=1 >
				<tr >
				<td width=10 align=center height=$RowsHight valign=middle >$i</td>
				<td width=30 align=center valign=middle >$OrderPO</td>
				<td width=57 align=left valign=middle >$eCode</td>
				<td width=18 valign=middle align=right>$Price</td>
				<td width=16 valign=middle align=right>$Qty</td>
				<td width=18 valign=middle align=right>$Amount</td>
				<td width=18 align=center valign=middle>$ShipType</td>
				<td width=28 align=center valign=middle>$Leadtime</td>
				</tr></table>";

			}
			*/
		switch ($CompanyId) {
			case 1018:
		//if($CompanyId==1018){
			$iTableList.="
				<tr>
				<td valign=middle align=center height=$RowsHight>$i</td>
				<td valign=middle>$eCode</td>
				<td valign=middle>$Description</td>
				<td valign=middle align=right>$Price</td>
				<td valign=middle align=right>$Qty</td>
				<td valign=middle align=right>$Amount</td>
				<td valign=middle>$Leadtime</td>
				</tr>";

				//每一条记录都是一个表格
				$$eurTableNo="<table  border=1 >
				<tr >
				<td width=15 align=center height=$RowsHight valign=middle >$i</td>
				<td width=30 align=center valign=middle >$eCode</td>
				<td width=63 align=Left valign=middle >$Description</td>
				<td width=19 align=center valign=middle align=right>$Price</td>
				<td width=19 align=center valign=middle align=right>$Qty</td>
				<td width=19 align=center valign=middle align=right>$Amount</td>
				<td width=30 align=center valign=middle >$Leadtime</td>
				</tr></table>";
		//}
			break;
			case 1088:	//diesel
			case 1090:  // aAveur
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

				//每一条记录都是一个表格
				$$eurTableNo="<table  border=1 >
				<tr >
				<td width=10 align=center height=$RowsHight valign=middle >$i</td>
				<td width=20 align=center valign=middle >$OrderPO</td>
				<td width=30 align=left valign=middle >$eCode</td>	
				<td width=45 align=left valign=middle >$Description</td>
				<td width=18 align=center valign=middle align=right>$Price</td>
				<td width=16 align=center valign=middle align=right>$Qty</td>
				<td width=18 align=center valign=middle align=right>$Amount</td>
				<td width=16 align=Left valign=middle >$ShipType</td>
				<td width=22 align=center valign=middle >$Leadtime</td>
				
				</tr></table>";
			break;
			default:
			//else{
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

				//每一条记录都是一个表格
				$$eurTableNo="<table  border=1 >
				<tr >
				<td width=10 align=center height=$RowsHight valign=middle >$i</td>
				<td width=20 align=center valign=middle >$OrderPO</td>
				<td width=47 align=left valign=middle >$eCode</td>				
				<td width=18 align=center valign=middle align=right>$Price</td>
				<td width=16 align=center valign=middle align=right>$Qty</td>
				<td width=18 align=center valign=middle align=right>$Amount</td>
				<td width=16 align=Left valign=middle >$ShipType</td>
				<td width=22 align=center valign=middle >$Leadtime</td>
				<td width=28 align=center valign=middle >$Remark</td>
				</tr></table>";

			//}
				break;
		}

			//<td width=10 height=$RowsHight valign=middle>&nbsp;</td>
			$RemarkTableNo="RemarkTableNo".strval($i);
			$companyResult=mysql_query("SELECT * FROM yw3_pirules WHERE CompanyId='$CompanyId'",$link_id);
		    if(mysql_num_rows($companyResult)>0){
			    $$RemarkTableNo="<table  border=1>
				<tr bgcolor=#cccccc>
				<td width=195 align=right color='#FFFFFF' style=bold height=$RowsHight valign=middle>$bjRemark</td>
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
/*
//加上银行账号
switch($CompanyId){
	case 1024:$BankId=3;break;//KON使用阿香帐号
	case 1050:$BankId=2;break;//PGD使用台北帐号
	default:$BankId=1;break;
	}
*/

include "subprogram/mybank_info.php";//银行卡信息

$bankResult = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.my2_bankinfo WHERE Id='$BankId' LIMIT 1",$link_id));
$Beneficary=$bankResult["Beneficary"];
$Bank=$bankResult["Bank"];
$BankAdd=$bankResult["BankAdd"];
$SwiftID=$bankResult["SwiftID"];
$ACNO=$bankResult["ACNO"];


$eurTableNo="eurTableNo".strval($Counts);
/*
if($CompanyId==1018){
	include "pimodel/PipublicTotal.php";

	$eurTableField="<table  border=1 >
	<tr bgcolor=#CCCCCC repeat>
	<td width=15 align=center height=$RowsHight valign=middle style=bold>Ln.</td>
	<td width=30 align=center valign=middle style=bold>Product Code</td>
	<td width=63 align=center valign=middle style=bold>Description</td>
	<td width=19 align=center valign=middle style=bold>Unit Price</td>
	<td width=19 align=center valign=middle style=bold>Qty</td>
	<td width=19 align=center valign=middle style=bold>Amount</td>
	<td width=30 align=center valign=middle style=bold>Leadtime</td>
	</tr></table>" ;
	$eurEmptyField="<table  border=1 >
	<tr >
	<td width=15 align=center height=$RowsHight valign=middle style=bold> </td>
	<td width=30 align=center valign=middle style=bold> </td>
	<td width=63 align=center valign=middle style=bold> </td>
	<td width=19 align=center valign=middle style=bold> </td>
	<td width=19 align=center valign=middle style=bold> </td>
	<td width=19 align=center valign=middle style=bold> </td>
	<td width=30 align=center valign=middle style=bold> </td>
	</tr></table>" ;//$eurTableList;


}
else{
	include "pimodel/PipublicTotal.php";

	$eurTableField="<table  border=1 >
	<tr bgcolor=#CCCCCC repeat>
	<td width=10 align=center height=$RowsHight valign=middle style=bold>Ln.</td>
	<td width=30 align=center valign=middle style=bold>PO#</td>
	<td width=57 align=center valign=middle style=bold>Product Code</td>
	<td width=18 align=center valign=middle style=bold>Unit Price</td>
    <td width=16 align=center valign=middle style=bold>Qty</td>
	<td width=18 align=center valign=middle style=bold>Amount</td>
	<td width=18 align=center valign=middle style=bold>Air/Sea</td>
	<td width=28 align=center valign=middle style=bold>Leadtime</td>
	</tr></table>" ;

	$eurEmptyField="<table  border=1 >
	<tr >
	<td width=10 align=center height=$RowsHight valign=middle style=bold> </td>
	<td width=30 valign=middle> </td>
	<td width=57 align=center valign=middle > </td>
	<td width=18 align=center valign=middle > </td>
	<td width=16 align=center valign=middle > </td>
	<td width=18 align=center valign=middle > </td>
	<td width=18 align=center valign=middle > </td>
	<td width=28 align=center valign=middle > </td>
	</tr></table>" ;//$eurTableList;

}
*/
switch ($CompanyId) {
	case 1018:
	//if($CompanyId==1018){
	include "pimodel/PipublicTotalOUT.php";
	$eurTableField="<table  border=1 >
	<tr bgcolor=#CCCCCC repeat>
	<td width=15 align=center height=$RowsHight valign=middle style=bold>Ln.</td>
	<td width=30 align=center valign=middle style=bold>Product Code</td>
	<td width=63 align=center valign=middle style=bold>Description</td>
	<td width=19 align=center valign=middle style=bold>Unit Price</td>
	<td width=19 align=center valign=middle style=bold>Qty</td>
	<td width=19 align=center valign=middle style=bold>Amount</td>
	<td width=30 align=center valign=middle style=bold>Leadtime</td>
	</tr></table>" ;
	$eurEmptyField="<table  border=1 >
	<tr >
	<td width=15 align=center height=$RowsHight valign=middle style=bold> </td>
	<td width=30 align=center valign=middle style=bold> </td>
	<td width=63 align=center valign=middle style=bold> </td>
	<td width=19 align=center valign=middle style=bold> </td>
	<td width=19 align=center valign=middle style=bold> </td>
	<td width=19 align=center valign=middle style=bold> </td>
	<td width=30 align=center valign=middle style=bold> </td>
	</tr></table>" ;//$eurTableList;
	//}
	break;

	case 1088:	//diesel
	case 1090:  // aAveur
	include "pimodel/PipublicTotalOUT.php";

	$eurTableField="<table  border=1 >
	<tr bgcolor=#CCCCCC repeat>
	<td width=10 align=center height=$RowsHight valign=middle style=bold>Ln.</td>
	<td width=20 align=center valign=middle style=bold>PO#</td>
	<td width=30 align=center valign=middle style=bold>Product Code</td>
	<td width=45 align=center valign=middle style=bold>Description</td>
	<td width=18 align=center valign=middle style=bold>Unit Price</td>	
	<td width=16 align=center valign=middle style=bold>Qty</td>	
	<td width=18 align=center valign=middle style=bold>Amount</td>
	<td width=16 align=center valign=middle style=bold>Air/Sea</td>
	<td width=22 align=center valign=middle style=bold>Leadtime</td>
	</tr></table>" ;

	$eurEmptyField="<table  border=1 >
	<tr >
	<td width=10 align=center height=$RowsHight valign=middle style=bold> </td>
	<td width=20 valign=middle> </td>
	<td width=30 align=center valign=middle > </td>
	<td width=45 align=center valign=middle > </td>	
	<td width=18 align=center valign=middle > </td>
	<td width=16 align=center valign=middle > </td>		
	<td width=18 align=center valign=middle > </td>
	<td width=16 align=center valign=middle > </td>
	<td width=22 align=center valign=middle > </td>
	</tr></table>" ;//$eurTableList;

	break;


	default:
	//else{
	include "pimodel/PipublicTotalOUT.php";

	$eurTableField="<table  border=1 >
	<tr bgcolor=#CCCCCC repeat>
	<td width=10 align=center height=$RowsHight valign=middle style=bold>Ln.</td>
	<td width=20 align=center valign=middle style=bold>PO#</td>
	<td width=47 align=center valign=middle style=bold>Product Code</td>
	<td width=18 align=center valign=middle style=bold>Unit Price</td>	
	<td width=16 align=center valign=middle style=bold>Qty</td>	
	<td width=18 align=center valign=middle style=bold>Amount</td>
	<td width=16 align=center valign=middle style=bold>Air/Sea</td>
	<td width=22 align=center valign=middle style=bold>Leadtime</td>
	<td width=28 align=center valign=middle style=bold>Remark</td>
	</tr></table>" ;

	$eurEmptyField="<table  border=1 >
	<tr >
	<td width=10 align=center height=$RowsHight valign=middle style=bold> </td>
	<td width=20 valign=middle> </td>
	<td width=47 align=center valign=middle > </td>
	<td width=18 align=center valign=middle > </td>
	<td width=16 align=center valign=middle > </td>		
	<td width=18 align=center valign=middle > </td>
	<td width=16 align=center valign=middle > </td>
	<td width=22 align=center valign=middle > </td>
	<td width=28 align=center valign=middle > </td>
	</tr></table>" ;//$eurTableList;
	//}
	break;
}


//
$filename="../download/pipdf/".$PI.".pdf";
if(file_exists($filename)){unlink($filename);}
//-----include "pimodel/pimodel.php";
include "pimodel/pimodelOUT.php";

$pdf->Output("$filename","F");
$Log.="<br>PI $PI 重置完成!";
?>