<?php
defined('IN_COMMON') || include '../basic/common.php';

//电信-zxq 2012-08-01
//扣款单另行处理
$RowsHight=5;			//表格行高
$InvoiceHeadFontSize=9;	//头文字字体大小
$TableFontSize=8;		//表格字体大小
$Commoditycode="";
//公司信息//
include "../model/subprogram/mycompany_info.php";
$mainResult = mysql_query("SELECT M.CompanyId,M.InvoiceNO,M.Wise,M.Notes,M.Terms,M.PaymentTerm,M.Date,U.Symbol,I.Company,I.Fax,I.Address,D.InvoiceModel,D.SoldTo,D.Address AS ToAddress,D.FaxNo,
B.Beneficary,B.Bank,B.BankAdd,B.SwiftID,B.ACNO,S.Nickname,S.Name as ZName 
FROM $DataIn.ch0_shipmain M 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId  and I.Type=8 
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId 
LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=M.BankId
LEFT JOIN $DataPublic.staffmain S ON S.Number=C.Staff_Number
WHERE M.Id=$Id LIMIT 1",$link_id);

if($mainRows = mysql_fetch_array($mainResult)){
	$CompanyId=$mainRows["CompanyId"];
	include "invoicetopdf/company_info.php";  //相应公司附近加的信息

	$InvoiceNO=$mainRows["InvoiceNO"];
	//$Invoice_PI="Invoice NO.:$InvoiceNO";
	$Invoice_PI="$InvoiceNO";
	$Wise=$mainRows["Wise"];

	$Notes=$mainRows["Notes"];
	$Terms=$mainRows["Terms"];
	$PaymentTerm=$mainRows["PaymentTerm"];
	$PaymentTerm=$PaymentTerm==""?"":"Payment term:$PaymentTerm<br>";  //放在Terms里

	$Date=date("d-M-y",strtotime($mainRows["Date"]));
	$Symbol=$mainRows["Symbol"]=="USD"?"U.S.DOLLARS":$mainRows["Symbol"];
	$Company=$mainRows["Company"];
	$Fax=$mainRows["Fax"];
	$Address=$mainRows["Address"];
	$InvoiceModel=$mainRows["InvoiceModel"];
	$SoldTo=$mainRows["SoldTo"]==""?$Company:$mainRows["SoldTo"];
	//$SoldTo=$Address;
	$ToAddress=$mainRows["ToAddress"]==""?$Address:$mainRows["ToAddress"];
	//$ToAddress=$Address;
	$FaxNo=$mainRows["FaxNo"]==""?$Fax:$mainRows["FaxNo"];
	$Beneficary=$mainRows["Beneficary"];
	$Bank=$mainRows["Bank"];
	$BankAdd=$mainRows["BankAdd"];
	$SwiftID=$mainRows["SwiftID"];
	$ACNO=$mainRows["ACNO"];
	$Nickname=$mainRows["Nickname"];
	$ZName=$mainRows["ZName"];
	}


//Invocse列表
$chSUMQty=0;
$boxSUMQty=0;
$Total=0;
//$Id='184';
$sheetResult = mysql_query("SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,P.Description,S.Qty,S.Price,S.Type,S.YandN 
FROM $DataIn.ch0_shipsheet S 
LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1' order by O.OrderPO 
",$link_id);

$i=1;
if($sheetRows = mysql_fetch_array($sheetResult)){
	do{
		$OrderPO=$sheetRows["OrderPO"];
		$OrderPO=$OrderPO==""?" ":$OrderPO;
		$cName=$sheetRows["cName"];
		$eCode=$sheetRows["eCode"];
		$Description=$sheetRows["Description"];
		$Qty=$sheetRows["Qty"];
		$Price=$sheetRows["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$chSUMQty=$chSUMQty+$Qty;
		$Total=sprintf("%.2f",$Total+$Amount);
		$iTableList.="<tr><td valign=middle align=center height=$RowsHight>$i</td><td valign=middle>$OrderPO</td><td valign=middle>$eCode</td><td valign=middle>$Description</td><td valign=middle align=right>$Qty</td><td valign=middle align=right>$Price</td><td valign=middle align=right>$Amount</td></tr>";
		$mcaTableList.="<tr><td valign=middle align=center height=$RowsHight>$i</td><td valign=middle>$OrderPO</td><td valign=middle>$eCode</td><td valign=middle>$Description</td><td valign=middle align=right>$Qty</td></tr>";
		$eurTableList.="<tr><td valign=middle align=center height=$RowsHight>$i</td><td valign=middle>$eCode</td><td valign=middle align=right>$Qty</td><td valign=middle align=right>$Price</td><td valign=middle align=right>$Amount</td></tr>";


		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
		/*
		$$eurTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight>$i</td>
		<td width=25 valign=middle>$OrderPO</td>
		<td width=35 valign=middle>$eCode</td>
		<td width=70 valign=middle>$Description</td>
		<td width=19 valign=middle align=right>$Qty</td>
		<td width=19 valign=middle align=right>$Price</td>
		<td width=19 valign=middle align=right>$Amount</td>
		</tr></table>";
		*/
		$$eurTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight>$i</td>
		<td width=21 valign=middle>$OrderPO</td>
		<td width=35 valign=middle>$eCode</td>
		<td width=67 valign=middle>$Description</td>
		<td width=19 valign=middle align=right> &nbsp; </td>
		<td width=12 valign=middle align=right>$Qty</td>
		<td width=16 valign=middle align=right>$Price</td>
		<td width=17 valign=middle align=right>$Amount</td>
		</tr></table>";


		//MCA
		$mcaTableNo="mcaTableNo".strval($i);   //不带价格的
		$$mcaTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight>$i</td>
		<td width=25 valign=middle>$OrderPO</td>
		<td width=35 valign=middle>$eCode</td>
		<td width=108 valign=middle>$Description</td>
		<td width=19 valign=middle align=right>$Qty</td>
		</tr></table>";



		$i++;
		}while ($sheetRows = mysql_fetch_array($sheetResult));
	}



$boxSUMQty=$chSUMQty;
//非装箱项目
/*
$unPackingSamp=mysql_query("SELECT S.Id,S.POrderId,'' AS OrderPO,O.SampName AS cName,'' AS eCode,O.Description,S.Qty,S.Price,S.Type,S.YandN
FROM $DataIn.ch1_shipsheet S
LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId WHERE S.Mid='$Id' AND S.Type='2' AND O.Type='0'",$link_id);
if($unPackingRow=mysql_fetch_array($unPackingSamp)){
	do{
		//$Description=$unPackingRow["Description"];
		$Description=$unPackingRow["Description"];
		$Qty=$unPackingRow["Qty"];
		//$chSUMQty=$chSUMQty+$Qty;				//将未装箱的计算在内
		$Price=sprintf("%.2f",$unPackingRow["Price"]);
		$Amount=sprintf("%.2f",$Qty*$Price);
		$Total=sprintf("%.2f",$Total+$Amount);
		$OrderPO="";
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
		$$eurTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight>$i</td>
		<td width=25 valign=middle>$OrderPO</td>
		<td width=35 valign=middle>$eCode</td>
		<td width=70 valign=middle>$Description</td>
		<td width=19 valign=middle align=right>$Qty</td>
		<td width=19 valign=middle align=right>$Price</td>
		<td width=19 valign=middle align=right>$Amount</td>
		</tr></table>";

		//MCA
		$mcaTableNo="mcaTableNo".strval($i);   //不带价格的
		$$mcaTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight>$i</td>
		<td width=25 valign=middle>$OrderPO</td>
		<td width=35 valign=middle>$eCode</td>
		<td width=108 valign=middle>$Description</td>
		<td width=19 valign=middle align=right>$Qty</td>
		</tr></table>";

		$i++;
		}while ($unPackingRow=mysql_fetch_array($unPackingSamp));
	}
*/
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
include "invoicetopdf/invoicepublicTotal.php";  //把每一项分离
/*
$$eurTableNo="<table  border=1 > <tr>
    <td  width=138 rowspan='4' align='left' valign='top'>Notes:<br>$Commoditycode$StableNote$Notes </td>
    <td  width=38 bgcolor='#999999' >SUBTOTAL</td>
    <td  width=19 align='right'>$Total</td>
  </tr>
  <tr>
    <td width=38 bgcolor='#999999'>DELIVERY COST</td>
    <td width=19 align='right'></td>
  </tr>
  <tr>
    <td width=38 bgcolor='#999999'>VAT</td>
    <td width=19 align='right'></td>
  </tr>
  <tr>
    <td  width=38 bgcolor='#999999'>TOTAL</td>
    <td  width=19 align='right'>$Total</td>
  </tr>
   <tr>
    <td colspan=3  height=17  align='left' valign='top'>Terms:<br>$PaymentTerm$Priceterm$Terms  </td>
  </tr>

  <tr  >
  <td  height=8 colspan=3 align='left' valign=middle >Currency  :$Symbol</td>
  </tr>

  <tr>
  <td colspan=3  height=30  align='left' valign=middle >BANK:<br>Beneficary: $Beneficary<br>Bank         : $Bank<br>Bank Add : $BankAdd<br>Swift ID    : $SwiftID<br>A/C NO    : $ACNO</td>
  </tr>
  </table>
  ";
*/

//mca加上总计
$mcaTableNo="mcaTableNo".strval($Counts);
$$mcaTableNo="<table  border=1 > <tr>
    <td  width=138 rowspan='4' align='left' valign='top'>Notes:<br>$Commoditycode$StableNote$Notes </td>
    <td  width=38 bgcolor='#999999' >SUBTOTAL</td>
    <td  width=19 align='right'>$Total</td>
  </tr>
  <tr>
    <td width=38 bgcolor='#999999'>DELIVERY COST</td>
    <td width=19 align='right'></td>
  </tr>
  <tr>
    <td width=38 bgcolor='#999999'>VAT</td>
    <td width=19 align='right'></td>
  </tr>
  <tr>
    <td  width=38 bgcolor='#999999'>TOTAL</td>
    <td  width=19 align='right'>$Total</td>
  </tr>
   <tr>
    <td colspan=3  height=17  align='left' valign='top'>Terms:<br>$PaymentTerm$Priceterm$Terms  </td>
  </tr>
  
  <tr  >
  <td  height=8 colspan=3 align='left' valign=middle >Currency  :$Symbol</td>
  </tr>
  
  <tr>
  <td colspan=3  height=30  align='left' valign=middle >BANK:<br>Beneficiary: $Beneficary<br>Bank         : $Bank<br>Bank Add : $BankAdd<br>Swift ID    : $SwiftID<br>A/C NO    : $ACNO</td>
  </tr> 
  </table>
  ";




$packingSUMQty=0;
$isFirst=0;  //0表示首行 则不持闭表。
$i=1;
//装箱列表
if($toPackingList!="N"){	//装箱完整的情况下更新packinglist	判断来源：装箱页面		其它更新页面：需装箱总数与已装箱总数是否一致
$plResult = mysql_query("SELECT L.Id,L.BoxRow,L.BoxPcs,L.BoxQty,L.FullQty,L.WG,L.POrderId,L.BoxSpec,e.OrderPO,e.eCode,e.BoxCode 
						FROM $DataIn.ch0_packinglist L 
						LEFT JOIN $DataIn.ch0_packpoecodebar e ON e.Mid=L.id 
                        WHERE L.Mid='$Id' ORDER BY L.Id",$link_id);

if ($plRows = mysql_fetch_array($plResult)){
	$j=1;
	do{
	    $BoxListId=$plRows["Id"];
		$BoxRow=$plRows["BoxRow"];
		$BoxPcs=$plRows["BoxPcs"];
		$BoxQty=$plRows["BoxQty"];
		$POrderId=$plRows["POrderId"];
		$BoxSpec=$plRows["BoxSpec"];   //箱尺寸

		$newPo=$plRows["OrderPO"];
		$newEcode=$plRows["eCode"];
		$newBarCode=$plRows["BoxCode"];

		if(($strPos=strpos(strtoupper($BoxSpec),"CM"))>2)
		{
			$BoxSize=substr($BoxSpec,0,$strPos); //去掉CM
                        $BoxSize=str_replace( '×', 'x',$BoxSize);
		}
		$FullQty=$plRows["FullQty"];
		$WG=$plRows["WG"];
		$NgWeight=0;

		$checkType=mysql_fetch_array(mysql_query("SELECT Type FROM $DataIn.ch0_shipsheet WHERE POrderId='$POrderId' LIMIT 1",$link_id));
		//echo "SELECT Type FROM $DataIn.ch0_shipsheet WHERE POrderId='$POrderId' LIMIT 1 <br>";
		$Type=$checkType["Type"];
		switch($Type){
			case 1:	//产品
			    if ($BoxRow>1){
			        //并箱时计算产品净重
				    $pSql = mysql_query("SELECT 
					S.OrderPO,P.cName,P.eCode,P.Description,P.Weight,L.BoxPcs 
					FROM $DataIn.ch0_packinglist L  
					LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=L.POrderId 
					LEFT JOIN  $DataIn.productdata P ON P.ProductId=S.ProductId 
					WHERE L.Mid='$Id' AND L.Id>='$BoxListId' ORDER BY L.Id  LIMIT $BoxRow",$link_id);
					$pCount=1;$pNgWeight=0;
					while ($pRows = mysql_fetch_array($pSql)){
					   $Weight=$pRows["Weight"];
					   if ($pCount==1){
						 $OrderPO=$pRows["OrderPO"];
						 $cName=$pRows["cName"];
						 $eCode=$pRows["eCode"];
						 $Description=$pRows["Description"];
						 $pNgWeight=$Weight*$BoxPcs;
					   }
					   else{
						  $L_BoxPcs=$pRows["BoxPcs"];
						  $pNgWeight+=$Weight*$L_BoxPcs;
					   }
					   $pCount++;
					}
					$NgWeight=round($pNgWeight/1000,2);
			    }
			    else{
					$pSql = mysql_query("SELECT 
					S.OrderPO,P.cName,P.eCode,P.Description,P.Weight  
					FROM $DataIn.yw1_ordersheet S 
					LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
					WHERE S.POrderId='$POrderId' LIMIT 1",$link_id);
					if ($pRows = mysql_fetch_array($pSql)){
						$OrderPO=$pRows["OrderPO"];
						$cName=$pRows["cName"];
						$eCode=$pRows["eCode"];
						$Description=$pRows["Description"];

						$Weight=$pRows["Weight"];
						$NgWeight=round($Weight*$BoxPcs/1000,2);
						}
				}
				break;
			/*
			case 2:	//样品
				$sSql = mysql_query("SELECT * FROM $DataIn.ch5_sampsheet WHERE SampId='$POrderId'",$link_id);
				if ($sRows = mysql_fetch_array($sSql)){
					$OrderPO="";
					$cName=$sRows["SampName"];
					$eCode=$cName;
					$Description=$sRows["Description"];
					}
				break;
			*/
			}

			if($newPo!=""){
				$OrderPO=$newPo;
			}
			if($newEcode!=""){
				$eCode=$newEcode;
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

				$$plTableNo.="<tr>
					<td valign=middle height=$RowsHight>$OrderPO</td>			
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
					$$plTableNo="<table  border=1 ><tr>
					<td width=16 valign=middle align=center $BoxRowSTR height=$RowsHight>$Most</td>
					<td width=25 valign=middle>$OrderPO</td>
					<td width=35 valign=middle>$eCode</td>
					<td width=38 valign=middle>$Description</td>
					<td width=15 valign=middle align=right>$BoxPcs</td>
					<td width=25 valign=middle align=center $BoxRowSTR>$BoxSize</td>
					<td width=15 valign=middle align=right $BoxRowSTR>$FullQty</td>
					<td width=13 valign=middle align=right $BoxRowSTR>$NG</td>
					<td width=13 valign=middle align=right $BoxRowSTR>$WG</td>
					</tr>";




					$isFirst=1;
				    $i++;

				//计算体积
                                if (preg_match('/×/',$BoxSpec)){
				      $BoxSpec=explode("×",substr($BoxSpec,0,-2));
                                      }
                                  else{
                                      $BoxSpec=explode("*",substr($BoxSpec,0,-2));
                                     }
                                   $ThisCube=$BoxSpec[0]*$BoxSpec[1]*$BoxSpec[2];$CubeSUM=$CubeSUM+$ThisCube*$BoxQty;//总体积
				}
			$j++;
		}while ($plRows = mysql_fetch_array($plResult));

		if($i>1){   //说明有记录，要封掉最后一个表
			$$eurplNo.="</table>";
			$$plTableNo.="</table>";
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

		$plTableNo="plTableNo".strval($plCounts);   //每一条记录都是一个表格
		$$plTableNo="<table  border=1 ><tr>
					<td width=16 valign=middle align=center $BoxRowSTR height=$RowsHight></td>
					<td width=98 valign=middle align=center>CUBE $CubeSUM M3</td>
					<td width=15 valign=middle align=right></td>
					<td width=25 valign=middle align=right></td>
					<td width=15 valign=middle align=right ></td>
					<td width=13 valign=middle align=right ></td>
					<td width=13 valign=middle align=right ></td>
			</tr></table>";


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

		$plTableNo="plTableNo".strval($plCounts);   //每一条记录都是一个表格
		$$plTableNo="<table  border=1 ><tr>		
		<tr bgcolor=#CCCCCC>
		<td  width=16 valign=middle height=$RowsHight style=bold>Total</td>
		<td  width=138 valign=middle  ></td>		
		<td  width=15 valign=middle align=right style=bold>$packingSUMQty</td>
		<td  width=13 valign=middle align=right style=bold>$NgSUM</td>
		<td  width=13 valign=middle align=right style=bold>$WgSUM</td>
		</tr>		  
		  <tr>
  		    <td colspan=6  height=23  align='left' valign='top'></td>
  		 </tr>
		
		</table>";


	}
}

if($boxSUMQty!=$packingSUMQty){	//需装箱数量与已装箱数量不一致时，即装箱不完整，不生成packinglist
	$plTableList="";
	$eurplList="";
	}
/*
//输出Invoice
$filename="../download/invoice/$InvoiceNO.pdf";
if(file_exists($filename)){unlink($filename);}


//$NewAndOld=$NewAndOld==""?"Old":$NewAndOld;
//echo "NewAndOld:$NewAndOld";
if ($NewAndOld=="Old"){
	$E_Company="Middle Cloud Trading Ltd";
	$S_Company="上海市中云包装厂";
	$C_Company="上海市中雲包裝廠";
	include "invoicetopdf_old/invoicemodel_".$InvoiceModel.".php";
}
else {
	include "invoicetopdf/invoicemodel_".$InvoiceModel.".php";
	//include "invoicetopdf/invoicemodel_3.php";
}
//include "invoicetopdf/invoicemodel_2test.php";
//include "invoicetopdf/invoicemodel_1.php";

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
if($CompanyId==1001){
	if ($NewAndOld=="Old"){
		$E_Company="Middle Cloud Trading Ltd";
		$S_Company="上海市中云包装厂";
		$C_Company="上海市中雲包裝廠";
		include "invoicetopdf_old/invoicemodel_mca.php";
	}
	else{
		include "invoicetopdf/invoicemodel_mca.php";
	}
}

$Log.="Invoice $InvoiceNO 重置完毕!";
*/
//输出Invoice
$FilePath="../download/invoice0/";
if(!file_exists($FilePath)){
	makedir($FilePath);
	}
$filename=$FilePath.$InvoiceNO.".pdf";
if(file_exists($filename)){unlink($filename);}
//echo "Model:$InvoiceModel";
include "invoicetopdf/invoicemodel_1.php";
//include "invoicetopdf/invoicemodel_".$InvoiceModel.".php";
$pdf->Output("$filename","F");
//$pdf->Close();

$Packfilename=$FilePath.$InvoiceNO."_P.pdf";
if(file_exists($Packfilename)){unlink($Packfilename);}
include "invoicetopdf/packinglist.php";
/*
if ($InvoiceModel==2){
	include "invoicetopdf/packinglist_2.php";
}
else{
   include "invoicetopdf/packinglist.php";
}
*/
$pdf->Output("$Packfilename","F");



$Log.="Invoice $InvoiceNO 重置完毕!";
?>
