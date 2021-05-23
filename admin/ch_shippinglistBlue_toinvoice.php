<?php
//defined('IN_COMMON') || include '../basic/common.php';

include_once '../plugins/fpdf/chinese-unicode.php';
$pdf=new PDF_Unicode();

if ($DataIn==""){
     include "../basic/chksession.php" ;
     include "../basic/parameter.inc";
}

include "invoicetopdf_blue/config.php";  //字体颜色，行高等配置文件

$Commoditycode="";
$FromFunPos="CH";
//公司信息//
//include "../model/subprogram/mycompany_info.php";  //放在中间
$mySql1="SELECT M.CompanyId,M.InvoiceNO,M.Wise,M.Notes,M.Terms,M.PaymentTerm,M.Ship,M.Date,M.Operator,U.Symbol,I.Company,I.Fax,I.Address,D.InvoiceModel,D.SoldTo,D.Address AS ToAddress,D.FaxNo,D.SoldFrom,D.FromAddress,D.FromFaxNo,
B.Beneficary,B.Bank,B.BankAdd,B.SwiftID,B.ACNO,S.Nickname,S.Name as ZName,C.PriceTerm,F.Mobile,D.OutSign,
O.OutCompanyName,O.OutAddress 
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=8 
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId 
LEFT JOIN $DataIn.ch8_shipoutcompany O ON O.Mid=D.Id 
LEFT JOIN $DataIn.my2_bankinfo B ON B.Id=C.BankId
LEFT JOIN $DataIn.staffmain S ON S.Number=C.Staff_Number
LEFT JOIN $DataIn.staffsheet F ON F.Number=S.Number
WHERE M.Id=$Id LIMIT 1";
$mainResult = mysql_query($mySql1,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$CompanyId=$mainRows["CompanyId"];
    $Ship=$mainRows["Ship"];

	$Priceterm=$mainRows["PriceTerm"];

	include "invoicetopdf/company_info.php";  //相应公司附近加的信息

	$InvoiceNO=$mainRows["InvoiceNO"];
	//echo "$InvoiceNO";
	//$Invoice_PI="Invoice NO.:$InvoiceNO";
	$Invoice_PI="$InvoiceNO";
	$Wise=$mainRows["Wise"];
	$Operator= $mainRows["Operator"];
	$pResult = mysql_query("SELECT M.Name,M.Nickname,F.Mobile 
						    FROM $DataIn.staffmain M 
						    LEFT JOIN $DataIn.staffsheet F ON F.Number=M.Number
						    WHERE M.Number='$Operator'  LIMIT 1",$link_id);
	if($pRow = mysql_fetch_array($pResult)){
		   $Transactor=$pRow["Nickname"];
		   $ZTransactor=$pRow["Name"];
		   $TransMobile=$pRow["Mobile"];
	}


	$Notes=$mainRows["Notes"];
	$Terms=$mainRows["Terms"];

	$Date=date("d-M-y",strtotime($mainRows["Date"]));
	$ShipDate=$mainRows["Date"];

	$Symbol=$mainRows["Symbol"]=="USD"?"U.S.DOLLARS":$mainRows["Symbol"];
	$Company=$mainRows["Company"];
	$Fax=$mainRows["Fax"];
	$Address=$mainRows["Address"];
	$InvoiceModel=$mainRows["InvoiceModel"];
    if ($CompanyId!=1004 && $CompanyId!=1059 ){  //CEL-A,CEL-B
		$PriceTerm=$mainRows["PriceTerm"];
	}


	$SoldTo=$mainRows["SoldTo"]==""?$Company:$mainRows["SoldTo"];
	$ToAddress=$mainRows["ToAddress"]==""?$Address:$mainRows["ToAddress"];
	$FaxNo=$mainRows["FaxNo"]==""?$Fax:$mainRows["FaxNo"];

	//放在后面才行
	$Company=$mainRows["SoldFrom"]==""?$Company:$mainRows["SoldFrom"];
	$Address=$mainRows["FromAddress"]==""?$Address:$mainRows["FromAddress"];
	$Fax=$mainRows["FromFaxNo"]==""?$Fax:$mainRows["FromFaxNo"];

	$Beneficary=$mainRows["Beneficary"];
	$Bank=$mainRows["Bank"];
	$BankAdd=$mainRows["BankAdd"];
	$SwiftID=$mainRows["SwiftID"];
	$ACNO=$mainRows["ACNO"];
	$Nickname=$mainRows["Nickname"];
	$ZName=$mainRows["ZName"];
	$Mobile=$mainRows["Mobile"];

	$OutSign=$mainRows["OutSign"];

	}

	if($OutSign==9){  //需要转发公司相关资料，及价格等
			//include "ch_shippinglistOut_toinvoice.php";
			//return;
			$Company = $mainRows["OutCompanyName"];
			$Address = $mainRows["OutAddress"];
    }


$check_Id=$Id;

if ($CheckSign=='HK'){
    $InvoiceModel = 1;
	include "../model/subprogram/mycompany_hk_info.php";   //公司信息

	$addSign=0;
    if ($NewSoldTo!=''){
	    $NewSolds=explode('|', $NewSoldTo);
	    if (count($NewSolds)==2){
		    $Company=$NewSolds[0];
		    $Address=$NewSolds[1];
		    $addSign++;
	    }
    }

    if ($NewShipTo!=''){
	    $NewShips=explode('|', $NewShipTo);
	    if (count($NewShips)==2){
		    $SoldTo=$NewShips[0];
		    $ToAddress=$NewShips[1];
		    $addSign++;
	    }
    }

    if ($addSign==2){
	    $insertSql="INSERT INTO ch1_shipinfo(ShipMid,SoldCompany,SoldAddress,ShipCompany,ShipAddress,Operator,Date,creator,created) VALUE($Id,'$Company','$Address','$SoldTo','$ToAddress',$Login_P_Number,CURDATE(),$Login_P_Number,NOW())
	    ON DUPLICATE KEY UPDATE SoldCompany='$Company',SoldAddress='$Address',ShipCompany='$SoldTo',ShipAddress='$ToAddress',modified=NOW(),modifier=$Login_P_Number";

	    mysql_query($insertSql,$link_id);
    }


}else{
	include "../model/subprogram/mycompany_info.php";   //公司信息
}

//Invocse列表
$chSUMQty=0;
$orderSUMQty=0;
$unSendSUMQty=0;
$boxSUMQty=0;
$Total=0;
//$Id='184';
$mySql2="SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,P.Description,S.Qty,O.Qty as orderQty,S.Price,S.Type,S.YandN,S.ProductId  as ProductId,PI.PaymentTerm,P.bjRemark,P.TypeId,R.OrderPO as OutOrderPO,PT.TypeName,TD.Length,TD.Width,TD.Thick
FROM $DataIn.ch1_shipsheet S 
LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=O.Id 
LEFT JOIN $DataIn.ch1_shipsplit SP ON SP.ShipId=S.Id
LEFT JOIN $DataIn.yw7_clientOrderPo R ON R.Mid=SP.id
LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId
LEFT JOIN  $DataIn.producttype PT ON PT.TypeId=P.TypeId 
LEFT JOIN ac_cz.trade_drawing TD ON concat_ws('-',TD.BuildingNo,TD.FloorNo,TD.CmptNo,TD.SN)=P.cName
WHERE S.Mid='$Id' AND S.Type='1'
UNION ALL 
SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.SampName AS eCode,O.Description,
S.Qty,S.Qty as orderQty,S.Price,S.Type,S.YandN,'0' as ProductId,'' AS PaymentTerm ,'' AS bjRemark,'' AS TypeId,'' as OutOrderPO,'' as TypeName,'' as Length,'' as Width,'' as Thick
FROM $DataIn.ch1_shipsheet S 
LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId WHERE S.Mid='$Id' AND S.Type='2' AND O.Type='1'";

$sheetResult = mysql_query($mySql2,$link_id);

$i=1;
if($sheetRows = mysql_fetch_array($sheetResult)){

         $PaymentTerm=$sheetRows["PaymentTerm"];
	     $PaymentTerm=$PaymentTerm==""?"":"Payment term:$PaymentTerm<br>";

	do{

	    if($Id==1109){
		    $PaymentTerm="Payment term:TT at 30 days from AWB-B/L";
	    }
		$OrderPO=$sheetRows["OrderPO"];
		$OrderPO=$OrderPO==""?" ":$OrderPO;

		$OutOrderPO=$sheetRows["OutOrderPO"];
		if($OutOrderPO!=""){
			$OrderPO=$OutOrderPO;
		}

		if ($InvoiceNO=="IW 012") $OrderPO.="(22288)";
		$cName=$sheetRows["cName"];
		$eCode=$sheetRows["eCode"];
		$TypeName = $sheetRows["TypeName"];
		$Description=$sheetRows["Description"];
        $ShipType=$sheetRows["ShipType"];
		$Qty=$sheetRows["Qty"];
		$orderQty=$sheetRows["orderQty"];
		$unSendQty=$orderQty-$Qty;
		$Price=$sheetRows["Price"];

        $Length=$sheetRows['Length'];
		$Width=$sheetRows['Width'];
		$Thick=$sheetRows['Thick'];


		$Price=$CheckSign=='HK'?sprintf("%.4f",($Price*1.0000+0.0500)):$Price;

		$Amount=sprintf("%.2f",$Qty*$Price);
		$chSUMQty=$chSUMQty+$Qty;
		$orderSUMQty=$orderSUMQty+$orderQty;
		$unSendSUMQty=$unSendSUMQty+$unSendQty;

		$Total=sprintf("%.2f",$Total+$Amount);

		$bjRemark=$sheetRows["bjRemark"];
		$color="";
		$ProductId=$sheetRows["ProductId"];
		if($CompanyId==1064 && $ProductId!='0'){  //看是否出过货
			$Pre_Temp=mysql_query("SELECT ProductId  FROM $DataIn.ch1_shipsheet where ProductId='$ProductId' AND Mid!='$Id'   LIMIT 1",$link_id);  //看是否出过货

			if(!($MasterRow = mysql_fetch_array($Pre_Temp))){
				$color="color=#FF0000";
			}

		}
		//海关编码
		$TypeId=$sheetRows["TypeId"];

		$HSCodeArray=explode("HS CODE ", $Description);
		if (count($HSCodeArray)==2){
			 $Description=$HSCodeArray[0];
			 $HSCode=$HSCodeArray[1];
		}else $HSCode="";



		$AppFileJPGPath="../download/productIcon/" .$ProductId.".jpg";
		$AppFilePNGPath="../download/productIcon/" .$ProductId.".png";
		$AppFilePath ="";
		    if(file_exists($AppFileJPGPath)){
		       $AppFilePath  = $AppFileJPGPath;
		    }else{
		      if(file_exists($AppFilePNGPath)){
		         $im  = imagecreatefrompng($AppFilePNGPath);
		         imagejpeg($im, "../download/productIcon/" .$ProductId.'.jpg');
		         $AppFilePath =$AppFileJPGPath;
		      }
		      else{
			      $AppFilePath ="";
		      }
		    }
        $eurAppFileNo="eurAppFileNo".strval($i);
		$$eurAppFileNo  = $AppFilePath;


		$eurTableList.="<tr><td valign=middle align=center height=$RowsHight>$i</td><td valign=middle>$eCode</td><td valign=middle align=right>$Qty</td><td valign=middle align=right>$Price</td><td valign=middle align=right>$Amount</td></tr>";

		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格,184
		$ChinaTableNo="ChinaTableNo".strval($i);  //中文不带价格，数量
		if($InvoiceModel!=2){
		    $$eurTableNo="<table  border=1 ><tr> 
		    <td width=10 valign=middle ></td>
		    <td width=8 valign=middle align=center height=$RowsHight $color>$i</td>
		    <td width=20 valign=middle $color>$OrderPO</td>
		    <td width=30 valign=middle $color>$eCode</td>
		    <td width=52 valign=middle $color>$Description</td>
		    <td width=19 valign=middle $color>$HSCode</td>
		    <td width=14 valign=middle align=right $color>$Qty</td>
		    <td width=14 valign=middle align=right $color>$Price</td>
		    <td width=17 valign=middle align=right $color>$Amount</td>
		    </tr></table>";}
		else{
		    if($CompanyId=='1075')$Description=$cName;
		    $$eurTableNo="<table  border=1 ><tr>
		    <td width=10 valign=middle height=$RowsHight></td>
		    <td width=8 valign=middle align=center  $color>$i</td>
		    <td width=20 valign=middle $color>$OrderPO</td>
		    <td width=30 valign=middle $color>$eCode</td>
		    <td width=71 valign=middle $color>$Description</td>
		    <td width=14 valign=middle align=right $color>$Qty</td>
		    <td width=14 valign=middle align=right $color>$Price</td>
		    <td width=17 valign=middle align=right $color>$Amount</td>
		    </tr></table>";

		    $$ChinaTableNo="<table  border=1 ><tr>
		    <td width=1 valign=middle height=$RowsHight></td>
		    <td width=10 align=center valign=middle>$i</td>	
			<td width=18  align=center valign=middle >$TypeName</td>
			<td width=33 align=center valign=middle style=bold>$cName</td>	
			<td width=20 align=center valign=middle style=bold>$Length</td>
			<td width=20 align=center valign=middle style=bold>$Width</td>			
			<td width=20 align=center valign=middle style=bold>$Thick</td>
			<td width=15 align=center valign=middle style=bold>块</td>	
			<td width=15 align=center valign=middle style=bold>$Qty</td>
			<td width=30 align=center valign=middle style=bold></td>
		    </tr></table>";

		    }


		//****************************//mco报价规则
		$RemarkTableNo="RemarkTableNo".strval($i);
		if($CompanyId=='1066' && $bjRemark!=""){
		   $$RemarkTableNo="<table  border=1>
				<tr bgcolor=#cccccc>
				<td width=184 align=right color='#FFFFFF' style=bold height=$RowsHight valign=middle>$bjRemark</td>
				</tr></table>";

		    }
		else{
		    $$RemarkTableNo="";
		    }


		$i++;
		}while ($sheetRows = mysql_fetch_array($sheetResult));
	}



$boxSUMQty=$chSUMQty;
//非装箱项目
$unPackingSamp=mysql_query("SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,'' AS eCode,O.Description,S.Qty,S.Price,S.Type,S.YandN 
FROM $DataIn.ch1_shipsheet S 
LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId WHERE S.Mid='$Id' AND S.Type='2' AND O.Type='0'",$link_id);
if($unPackingRow=mysql_fetch_array($unPackingSamp)){
	do{
		$Description=$unPackingRow["Description"];
		$Qty=$unPackingRow["Qty"];
		//$chSUMQty=$chSUMQty+$Qty;				//将未装箱的计算在内
		$Price=sprintf("%.2f",$unPackingRow["Price"]);
		$Amount=sprintf("%.2f",$Qty*$Price);
		$Total=sprintf("%.2f",$Total+$Amount);
		$OrderPO=$unPackingRow["OrderPO"];
		$eCode="";
		$iTableList.="<tr>
		<td valign=middle align=center height=$RowsHight>$i</td>
		<td valign=middle>$OrderPO</td>
		<td valign=middle>$eCode</td>
		<td valign=middle>$Description</td>
		<td valign=middle align=right>$Qty</td>
		<td valign=middle align=right>$Price</td>
		<td valign=middle align=right>$Amount</td></tr>";
		$mcaTableList.="<tr><td valign=middle align=center height=$RowsHight>$i</td><td valign=middle>$OrderPO</td><td valign=middle>$eCode</td><td valign=middle>$Description</td><td valign=middle align=right></td></tr>";
		$eurTableList.="<tr><td valign=middle align=center height=$RowsHight>$i</td><td valign=middle>$Description</td><td valign=middle align=right>$Qty</td><td valign=middle align=right>$Price</td><td valign=middle align=right>$Amount</td></tr>";
		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格



		if($InvoiceModel!=2){
		    $$eurTableNo="<table  border=1 ><tr> 
		    <td width=8 valign=middle align=left height=$RowsHight $color>$i</td>
		    <td width=21 valign=middle $color>$OrderPO</td>
		    <td width=35 valign=middle $color>$eCode</td>
		    <td width=56 valign=middle $color>$Description</td>
		    <td width=19 valign=middle $color>$HSCode</td>
		    <td width=14 valign=middle align=right $color>$Qty</td>
		    <td width=14 valign=middle align=right $color>$Price</td>
		    <td width=17 valign=middle align=right $color>$Amount</td>
		    </tr></table>";}
		else{
		    $$eurTableNo="<table  border=1 ><tr>
		    <td width=8 valign=middle align=left height=$RowsHight $color>$i</td>
		    <td width=21 valign=middle $color>$OrderPO</td>
		    <td width=35 valign=middle $color>$eCode</td>
		    <td width=75 valign=middle $color>$Description</td>
		    <td width=14 valign=middle align=right $color>$Qty</td>
		    <td width=14 valign=middle align=right $color>$Price</td>
		    <td width=17 valign=middle align=right $color>$Amount</td>
		    </tr></table>";

			$ChinaTableNo="ChinaTableNo".strval($i);  //中文不带价格，数量
		    $$ChinaTableNo="<table  border=1 ><tr>
		    <td width=8 valign=middle align=left height=$RowsHight $color>$i</td>
		    <td width=30 valign=middle $color>$OrderPO</td>
		    <td width=35 valign=middle $color>$eCode</td>
		    <td width=66 valign=middle $color>$Description</td>
		    <td width=14 valign=middle align=right $color>$Qty</td>
		    <td width=14 valign=middle align=right $color>$Price</td>
		    <td width=17 valign=middle align=right $color>$Amount</td>
		    </tr></table>";

		    }

			$mcaTableNo="mcaTableNo".strval($i);   //不带价格的
			$$mcaTableNo="<table  border=1 ><tr>
			<td width=8 valign=middle align=center height=$RowsHight $color>$i</td>
			<td width=21 valign=middle $color>$OrderPO</td>
			<td width=35 valign=middle $color>$eCode</td>
			<td width=106 valign=middle $color>$Description</td>
			<td width=14 valign=middle align=right $color>$Qty</td>
			</tr></table>";

		$i++;
		}while ($unPackingRow=mysql_fetch_array($unPackingSamp));
	}

$Counts=$i;  //记录总条数

//合计
$iTableList.="<tr bgcolor=#CCCCCC><td colspan=4 height=$RowsHight valign=middle style=bold>Total</td><td align=right valign=middle style=bold>$chSUMQty</td><td></td><td align=right valign=middle style=bold>$Total</td></tr></table>";
$mcaTableList.="<tr bgcolor=#CCCCCC><td colspan=4 height=$RowsHight valign=middle style=bold>Total</td><td align=right valign=middle style=bold>$chSUMQty</td></tr></table>";
$eurTableList.="<tr bgcolor=#CCCCCC>
<td colspan=2 height=$RowsHight valign=middle style=bold>Total</td>
<td align=right valign=middle style=bold>$chSUMQty</td>
<td></td>
<td align=right valign=middle style=bold>$Total</td>
</tr></table>";


//加上总计
$eurTableNo="eurTableNo".strval($Counts);
$ChinaTableNo="ChinaTableNo".strval($Counts);
$Note_Bank_Height=44; //预留高度给尾页的Note,BANK  add by zx 2014-05-22
/*include "invoicetopdf_new/invoicepublicTotal.php";*/  //把每一项分离
include "invoicetopdf_blue/invoicepublicTotal.php";  //把每一项分离



//mca加上总计
$mcaTableNo="mcaTableNo".strval($Counts);

$$mcaTableNo=" 
<table  border=0 >
<tr  repeat>
<td width=29  align=left height=$RowsHight valign=middle >&nbsp;</td>
<td width=35 align=left valign=middle  ></td>
<td width=56 align=left valign=middle ></td>	
<td width=19 align=left valign=middle ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=17 align=right valign=middle ></td></tr>
<tr >
<td width=29  align=left height=14 valign=middle ></td>
<td width=35 align=left valign=middle  ></td>
<td width=56 align=left valign=middle ></td>	
<td width=19 align=left valign=middle ></td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle ></td>	
<td width=17 align=right valign=middle ></td></tr>
</table>
";
$mcaTableNoTotal=" 
<table  border=0 >
<tr bgcolor=#E8F5FC repeat>
<td width=29  align=left height=$RowsHight valign=middle >SUBTOTAL:</td>
<td width=35 align=left valign=middle  ></td>
<td width=56 align=left valign=middle >DELI VERYCOST:</td>	
<td width=19 align=left valign=middle >VAT:</td>			
<td width=14 align=right valign=middle ></td>	
<td width=14 align=right valign=middle >TOTAL:</td>	
<td width=17 align=right valign=middle ></td></tr></table>";



$BankTable ="
<table  border=0 >
<tr   >
<td width=18 align=left valign=middle >BENEFICIARY:</td>
<td width=166 align=left valign=middle  >$Beneficary</td>
</tr>
<tr >
<td width=18  align=left  valign=middle >BANK:</td>
<td width=166 align=left valign=middle  >$Bank</td>
</tr>
<tr >
<td width=18  align=left valign=middle >BANK Add:</td>
<td width=166 align=left valign=middle  >$BankAdd</td>
</tr>
<tr >
<td width=18  align=left valign=middle >SWIFT ID:</td>
<td width=166  align=left valign=middle  >$SwiftID</td>
</tr>
<tr >
<td width=18  align=left valign=middle >A/C NO:</td>
<td width=166 align=left valign=middle  >$ACNO</td>
</tr>
</table>";



$ChinaBankTable ="
<table  border=0 >
<tr>
<td width=20 height=10 align=center valign=middle >发货人：</td>
<td width=45 align=center valign=middle  ></td>
<td width=20 align=center valign=middle  >品质：</td>
<td width=45 align=center valign=middle  ></td>
<td width=20 align=center valign=middle  >司机：</td>
<td width=30 align=center valign=middle  ></td>
</tr>
<tr>
<td width=20 align=center valign=middle >项目收货人：</td>
<td width=45 align=center valign=middle  ></td>
<td width=20 align=center valign=middle  >监理：</td>
<td width=45 align=center valign=middle  ></td>
<td width=20 align=center valign=middle  ></td>
<td width=30 align=center valign=middle  ></td>
</tr>
</table>";


$smartsOrder="";
$tmpsOrder="";
$packingSUMQty=0;
$isFirst=0;  //0表示首行 则不持闭表。
$i=1;
$BoxSUM=0;
//装箱列表
if($toPackingList!="N"){	//装箱完整的情况下更新packinglist	判断来源：装箱页面		其它更新页面：需装箱总数与已装箱总数是否一致
$plResult = mysql_query("SELECT L.BoxRow,L.BoxPcs,L.BoxQty,L.FullQty,L.WG,L.POrderId,L.BoxSpec FROM $DataIn.ch2_packinglist L 
 WHERE L.Mid='$Id' ORDER BY L.Id",$link_id);
if ($plRows = mysql_fetch_array($plResult)){
	$j=1;
	do{
		$BoxRow=$plRows["BoxRow"];
		$BoxPcs=$plRows["BoxPcs"];
		$BoxQty=$plRows["BoxQty"];
		$POrderId=$plRows["POrderId"];
		$BoxSpec=$plRows["BoxSpec"];   //箱尺寸
		if(($strPos=strpos(strtoupper($BoxSpec),"CM"))>2)
		{
			$BoxSize=substr($BoxSpec,0,$strPos); //去掉CM
                        $BoxSize=str_replace( '×', 'x',$BoxSize);
		}
		$FullQty=$plRows["FullQty"];
		$WG=$plRows["WG"];

		$checkType=mysql_fetch_array(mysql_query("SELECT Type FROM $DataIn.ch1_shipsheet WHERE POrderId='$POrderId' LIMIT 1",$link_id));
		$Type=$checkType["Type"];
		switch($Type){
			case 1:	//产品
				$pSql = mysql_query("SELECT 
				S.OrderPO,P.cName,P.eCode,P.Description,P.Weight
				FROM $DataIn.yw1_ordersheet S 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
				WHERE S.POrderId='$POrderId' LIMIT 1",$link_id);
				if ($pRows = mysql_fetch_array($pSql)){
					$OrderPO=$pRows["OrderPO"];
					if ($InvoiceNO=="IW 012") $OrderPO.="(22288)";
					$cName=$pRows["cName"];
					$eCode=$pRows["eCode"];
					$Description=$pRows["Description"];
					$Weight=$pRows["Weight"];
					$NgWeight=round($Weight*$BoxPcs/1000,2);
					}
				break;
			case 2:	//样品
				$sSql = mysql_query("SELECT * FROM $DataIn.ch5_sampsheet WHERE SampId='$POrderId'",$link_id);
				if ($sRows = mysql_fetch_array($sSql)){
					//$OrderPO="";
                    $OrderPO=$sRows["SampPO"];
					$cName=$sRows["SampName"];
					$eCode=$cName;
					$Description=$sRows["Description"];
					$Weight=$sRows["Weight"];
					$NgWeight=round($Weight*$BoxPcs/1000,2);
					}
				break;
			}
			//if($BoxSizeSTR==""){$BoxSizeSTR=$BoxSpec;}else{if($BoxSizeSTR!=$BoxSpec){$BoxSizeSTR=$BoxSpec;}}
				$BoxRowSTR=$BoxRow>1?"rowspan=$BoxRow":"";//检查是否合并行
			//边线处理及合并列处理
			if($BoxRow==0){//并箱非首行
				$plTableList.="<tr>
					<td valign=middle height=$RowsHight>$OrderPO</td>			
					<td valign=middle>$eCode</td>
					<td valign=middle>$Description</td>
					<td valign=middle align=right>$BoxPcs</td>
					</tr>";
				$eurplList.="<tr><td valign=middle>$eCode</td><td valign=middle align=right>$BoxPcs</td></tr>";
				$$eurplNo.="<tr><td valign=middle>$eCode</td><td valign=middle align=right>$BoxPcs</td></tr>";

				//height=$RowsHight
				$$plTableNo.="<tr>
					<td valign=middle >$OrderPO</td>			
					<td valign=middle>$eCode</td>
					<td valign=middle>$Description</td>
					<td valign=middle align=right>$BoxPcs</td>
					</tr>";

				}
			else{
				$Sideline=1;
				$WgSUM=$WgSUM+$WG*$BoxQty;//毛重总计
				if ($Type==1 && $NgWeight>0){
					$NG=$NgWeight;//净重
				}
				else{
					$NG=$WG-1;//净重
				}

				if($NG<=0){
					$NG=round($WG*100/2)/100;
				}
				$NgSUM=$NgSUM+$NG*$BoxQty;//净重总计
				$packingSUMQty=$packingSUMQty+$FullQty;//装箱总数合计

				$Small=$BoxSUM+1;//起始箱号
				$Most=$BoxSUM+$BoxQty;//终止箱号
				$BoxSUM=$Most;
				if($Most!=$Small){
					$Most=$Small."-".$Most;}
				$plTableList.="<tr>
					<td valign=middle align=center $BoxRowSTR height=$RowsHight>$Most</td>
					<td valign=middle>$OrderPO</td>			
					<td valign=middle>$eCode</td>
					<td valign=middle>$Description</td>
					<td valign=middle align=right>$BoxPcs</td>
					<td valign=middle align=right $BoxRowSTR>$FullQty</td>
					<td valign=middle align=right $BoxRowSTR>$NG</td>
					<td valign=middle align=right $BoxRowSTR>$WG</td>
					</tr>";
				$eurplList.="<tr><td valign=middle align=center $BoxRowSTR height=$RowsHight>$Most</td><td valign=middle>$eCode</td><td valign=middle align=right>$BoxPcs</td><td valign=middle align=right $BoxRowSTR>$FullQty</td><td valign=middle align=right $BoxRowSTR>$NG</td><td valign=middle align=right $BoxRowSTR>$WG</td></tr>";

				if($isFirst==1 ){   //不是首行，则封闭上一行的
					$$eurplNo.="</table>";
					$$plTableNo.="</table>";
					$$smartsNo.="</table>";
					}
					$eurplNo="eurplNo".strval($i);   //每一条记录都是一个表格
					$$eurplNo="<table  border=1 ><tr>
					<td width=16 valign=middle align=center $BoxRowSTR height=$RowsHight>$Most</td>
					<td width=98 valign=middle>$eCode</td>
					<td width=15 valign=middle align=right>$BoxPcs</td>
					<td width=25 valign=middle align=center $BoxRowSTR>$BoxSize</td>
					<td width=15 valign=middle align=right $BoxRowSTR>$FullQty</td>
					<td width=13 valign=middle align=right $BoxRowSTR>$NG</td>
					<td width=13 valign=middle align=right $BoxRowSTR>$WG</td>
					</tr>";

					$plTableNo="plTableNo".strval($i);   //每一条记录都是一个表格
					//height=$RowsHight
					$$plTableNo="<table  border=1 ><tr>
					<td width=13 valign=middle $BoxRowSTR >$Most</td>
					<td width=21 valign=middle>$OrderPO</td>
					<td width=35 valign=middle>$eCode</td>
					<td width=42 valign=middle>$Description</td>
					<td width=11 valign=middle align=right>$BoxPcs</td>
					<td width=23 valign=middle align=center $BoxRowSTR>$BoxSize</td>
					<td width=13 valign=middle align=right $BoxRowSTR>$FullQty</td>
					<td width=13 valign=middle align=right $BoxRowSTR>$NG</td>
					<td width=13 valign=middle align=right $BoxRowSTR>$WG</td>
					</tr>";



					$smartsNo="smartsNo".strval($i);   //每一条记录都是一个表格
					//height=$RowsHight
					$$smartsNo="<table  border=1 ><tr>
					<td width=21 valign=middle $BoxRowSTR >R$Most</td>
					<td width=70 valign=middle>$Description</td>
					<td width=24 valign=middle align=right $BoxRowSTR>$FullQty</td>
					<td width=43 valign=middle align=center $BoxRowSTR>$BoxSize</td>					
					<td width=26 valign=middle align=right $BoxRowSTR>$WG</td>
					</tr>";
					if($tmpsOrder!=$OrderPO){
						$smartsOrder=$smartsOrder==""?$OrderPO:$smartsOrder.",$OrderPO";
						$tmpsOrder=$OrderPO;
					}

					$isFirst=1;
				    $i++;

				//计算体积
                                if (substr_count($BoxSpec,"*")>0){
				     $BoxSpec=explode("*",substr($BoxSpec,0,-2));
                                }else{
                                     $BoxSpec=explode("×",substr($BoxSpec,0,-2));

                                }
                                $ThisCube=$BoxSpec[0]*$BoxSpec[1]*$BoxSpec[2];
                                $CubeSUM=$CubeSUM+$ThisCube*$BoxQty;//总体积
				}
			$j++;
		}while ($plRows = mysql_fetch_array($plResult));

		if($i>1){   //说明有记录，要封掉最后一个表
			$$eurplNo.="</table>";
			$$plTableNo.="</table>";
			$$smartsNo.="</table>";
		}



		$CubeSUM=sprintf("%.2f",$CubeSUM/1000000);
		$plTableList.="<tr>
			<td valign=middle align=center height=$RowsHight></td>
			<td valign=middle align=center colspan=4> CUBE $CubeSUM M3</td>
			<td valign=middle></td>
			<td valign=middle></td>
			<td valign=middle></td>
			</tr>
			<tr bgcolor=#CCCCCC>
			<td valign=middle style=bold>Total</td>
			<td valign=middle colspan=4 height=$RowsHight></td>
			<td valign=middle align=right style=bold>$packingSUMQty</td>
			<td valign=middle align=right style=bold>$NgSUM</td>
			<td valign=middle align=right style=bold>$WgSUM</td>
			</tr></table>";

		$eurplList.="<tr>
			<td valign=middle align=center height=$RowsHight></td>
			<td valign=middle align=center> CUBE $CubeSUM M3</td>
			<td valign=middle></td>
			<td valign=middle></td>
			<td valign=middle></td>
			<td valign=middle></td>
			</tr>
			<tr bgcolor=#CCCCCC>
			<td valign=middle style=bold>Total</td>
			<td valign=middle colspan=2 height=$RowsHight></td>
			<td valign=middle align=right style=bold>$packingSUMQty</td>
			<td valign=middle align=right style=bold>$NgSUM</td>
			<td valign=middle align=right style=bold>$WgSUM</td>
			</tr></table>";



		$plCounts=$i;  //记录条数
		$eurplNo="eurplNo".strval($plCounts);   //每一条记录都是一个表格
		$$eurplNo="<table  border=1 ><tr>
			<td width=16 valign=middle align=center height=$RowsHight></td>
			<td width=98 valign=middle align=center> CUBE $CubeSUM M3</td>
			<td width=15 valign=middle></td>
			<td width=25 valign=middle></td>
			<td width=15 valign=middle></td>
			<td width=13 valign=middle></td>
			<td width=13 valign=middle></td>
			</tr></table>";

		$plTableNo="plTableNo".strval($plCounts);
		$$plTableNo="<table  border=0 >
					<tr bgcolor='' repeat>
					<td width=66 align=left height=$RowsHight valign=middle >&nbsp;</td>
					<td width=42 align=left valign=middle  > &nbsp;</td>	
					<td width=12 align=left valign=middle  >&nbsp;</td>	
					<td width=25 align=left valign=middle  ></td>
					<td width=13 align=left valign=middle  ></td>
					<td width=13 align=left valign=middle  ></td>
					<td width=13 align=left valign=middle  ></td>					
					</tr>
					<tr>
					<td width=66 valign=middle height=2 ></td>
					<td width=42 align=left valign=middle  >  </td>	
					<td width=12 align=left valign=middle  >  </td>	
					<td width=25 align=left valign=middle ></td>
					<td width=13 align=left valign=middle  ></td>
					<td width=13 align=left valign=middle  ></td>
					<td width=13 align=left valign=middle  ></td>					
					</tr>					
					</table>";

		$smartsNo="smartsNo".strval($plCounts);
		$$smartsNo="<table  border=0 >
					<tr bgcolor='' repeat>
					<td width=66 align=left height=$RowsHight valign=middle >&nbsp;</td>
					<td width=42 align=left valign=middle  > &nbsp;</td>	
					<td width=12 align=left valign=middle  >&nbsp;</td>	
					<td width=25 align=left valign=middle  ></td>
					<td width=13 align=left valign=middle  ></td>
					<td width=13 align=left valign=middle  ></td>
					<td width=13 align=left valign=middle  ></td>					
					</tr>
					<tr>
					<td width=66 valign=middle height=2 ></td>
					<td width=42 align=left valign=middle  >  </td>	
					<td width=12 align=left valign=middle  >  </td>	
					<td width=25 align=left valign=middle ></td>
					<td width=13 align=left valign=middle  ></td>
					<td width=13 align=left valign=middle  ></td>
					<td width=13 align=left valign=middle  ></td>					
					</tr>					
					</table>";

		$plTableNoTotal=" 
					<table  border=0 >
					<tr bgcolor=#E8F5FC repeat>
					<td width=66 align=left height=$RowsHight valign=middle > CUBE $CubeSUM (M3)</td>
					<td width=42 align=right valign=middle >TOTAL: </td>	
					<td width=12 align=center valign=middle >  </td>	
					<td width=25 align=right valign=middle >$BoxSUM</td>
					<td width=13 align=right valign=middle >$packingSUMQty </td>
					<td width=13 align=right valign=middle >$NgSUM</td>
					<td width=13 align=right valign=middle >$WgSUM</td>					
					</tr>
					</table>";

		$ChinaplTableNoTotal=" 
					<table  border=0 >
					<tr  repeat>
					<td width=66 align=left height=$RowsHight valign=middle > 体积 $CubeSUM (M3)</td>
					<td width=42 align=center valign=middle >  </td>	
					<td width=12 align=center valign=middle >  </td>	
					<td width=25 align=right valign=middle >合计: </td>
					<td width=13 align=right valign=middle >$packingSUMQty </td>
					<td width=13 align=right valign=middle >$NgSUM</td>
					<td width=13 align=right valign=middle >$WgSUM</td>					
					</tr>
					</table>";




		$plCounts=$plCounts+1;  //记录条数
		$eurplNo="eurplNo".strval($plCounts);   //每一条记录都是一个表格
		$$eurplNo="<table  border=1 ><tr>		
		<tr bgcolor=#CCCCCC>
		<td  width=16 valign=middle height=$RowsHight style=bold>Total</td>
		<td  width=138 valign=middle  ></td>
		<td  width=15 valign=middle align=right style=bold>$packingSUMQty</td>
		<td  width=13 valign=middle align=right style=bold>$NgSUM</td>
		<td  width=13 valign=middle align=right style=bold>$WgSUM</td>
		</tr>		  
		  <tr>
  		    <td colspan=5  height=23  align='left' valign='top'></td>
  		 </tr>
		
		</table>";





	}
}

if($boxSUMQty!=$packingSUMQty){	//需装箱数量与已装箱数量不一致时，即装箱不完整，不生成packinglist
	$plTableList="";
	$eurplList="";
	}


//include "invoicetopdf_blue/invoicemodel_".$InvoiceModel.".php";
switch ($InvoiceModel){
	case 2:   //中文
		include "invoicetopdf_blue/invoicemodel_".$InvoiceModel.".php"; break;
	case 6:
		include "invoicetopdf_blue/invoicemodel_smarts.php"; break;
	default:  //英文
	include "invoicetopdf_blue/invoicemodel_1.php"; break;
}

$FilePath="../download/invoice/";
if(!file_exists($FilePath)){
		dir_mkdir($FilePath);
}

if ($CheckSign=='HK'){
       ob_end_clean();
       $filename="../download/invoice/$InvoiceNO" . "_HK.pdf";
       $pdf->Output("$filename","D");

       //$pdf->Output();
}
else{
     //输出Invoice
	$filename="../download/invoice/$InvoiceNO.pdf";
	//echo "$filename";
	if(file_exists($filename)){unlink($filename);}

	 //附加图片处理
	 $chechPicture=mysql_query("SELECT Picture FROM $DataIn.ch7_shippicture WHERE Mid='$Id' ORDER BY Id",$link_id);
	 if($PrictureRow=mysql_fetch_array($chechPicture)){
		do{
			$Picture=$PrictureRow["Picture"];
			$pdf->AddPage();
			$this_Photo="../download/invoice/".$Picture;
			$pdf->Image($this_Photo,10,10,190,270,"JPG");
			}while($PrictureRow=mysql_fetch_array($chechPicture));
		}

		$pdf->Output("$filename","F");


		//include "invoicetopdf_blue/invoicemodel_mca.php";
		if(($CompanyId==1001)|| ($CompanyId==1064) || ($CompanyId==1071)){
			include "invoicetopdf_blue/invoicemodel_mca.php";
		}
		$Log.="Invoice $InvoiceNO 重置完毕!<br>";


		//输出财务使用Invoice
		$FilePath="../download/cw_invoice/";
		if(!file_exists($FilePath)){
				dir_mkdir($FilePath);
		}

		$cwfilename="../download/cw_invoice/$InvoiceNO.pdf";
		if(file_exists($cwfilename)){unlink($cwfilename);}

		switch ($InvoiceModel){
			case 2:   //中文
			include "invoicetopdf_blue/invoicemodel_cw_".$InvoiceModel.".php"; break;
			default:  //英文
			include "invoicetopdf_blue/invoicemodel_cw_1.php"; break;
		}

		$pdf->Output("$cwfilename","F");
		$Log.="财务使用Invoice $InvoiceNO 重置完毕!<br>";


		//生成XML文件
		$CreateXmlFile="SAVE_INVOICE";
		include "ch_shippinglist_toxml.php";
		 if($CompanyId==1074){
		            include "ch_shippinglist_toxml(strax).php"; // 新增的库存
		            include "ch_shippinglist_toxml(strax)_stock.php";//总的库存
		       }

 }
?>
