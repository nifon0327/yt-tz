<?php
defined('IN_COMMON') || include '../basic/common.php';

//电信-zxq 2012-08-01

//扣款单另行处理
$RowsHight=5;			//表格行高
$InvoiceHeadFontSize=8;	//头文字字体大小
$TableFontSize=8;		//表格字体大小
$Commoditycode="";
$FromFunPos="CH";
//公司信息//
//include "../model/subprogram/mycompany_info.php";  //放在中间
$mySql1="SELECT M.CompanyId,M.InvoiceNO,M.Wise,M.Notes,M.Terms,M.PaymentTerm,M.Ship,M.Date,U.Symbol,
I.Company,I.Fax,I.Address,D.InvoiceModel,D.SoldTo,D.Address AS ToAddress,D.FaxNo,D.SoldFrom,D.FromAddress,D.FromFaxNo,
B.Beneficary,B.Bank,B.BankAdd,B.SwiftID,B.ACNO,S.Nickname,S.Name as ZName,C.PriceTerm
FROM $DataIn.ch1_shipmain M 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.currencydata U ON C.Currency=U.Id 
LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=8
LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId 
LEFT JOIN $DataIn.my2_bankinfo B ON B.Id=C.BankId
LEFT JOIN $DataIn.staffmain S ON S.Number=C.Staff_Number
WHERE M.Id=$Id LIMIT 1";
//echo $mySql1;
$mainResult = mysql_query($mySql1,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$CompanyId=$mainRows["CompanyId"];
        $Ship=$mainRows["Ship"];
	include "invoicetopdf/company_info.php";  //相应公司附近加的信息

	$InvoiceNO=$mainRows["InvoiceNO"];
	//$Invoice_PI="Invoice NO.:$InvoiceNO";
	$Invoice_PI="$InvoiceNO";
	$Wise=$mainRows["Wise"];

	$Notes=$mainRows["Notes"];
	$Terms=$mainRows["Terms"];
	//$PaymentTerm=$mainRows["PaymentTerm"];
	//$PaymentTerm=$PaymentTerm==""?"":"Payment term:$PaymentTerm<br>";  //放在Terms里

	$Date=date("d-M-y",strtotime($mainRows["Date"]));
	$Symbol=$mainRows["Symbol"]=="USD"?"U.S.DOLLARS":$mainRows["Symbol"];
	$Company=$mainRows["Company"];
	$Fax=$mainRows["Fax"];
	$Address=$mainRows["Address"];
	$InvoiceModel=$mainRows["InvoiceModel"];
    if ($CompanyId!=1004 && $CompanyId!=1059 ){  //CEL-A,CEL-B
		$PriceTerm=$mainRows["PriceTerm"];
	}
    //include "invoicetopdf/company_info.php";  //相应公司附近加的信息

	$SoldTo=$mainRows["SoldTo"]==""?$Company:$mainRows["SoldTo"];
	$ToAddress=$mainRows["ToAddress"]==""?$Address:$mainRows["ToAddress"];
	$FaxNo=$mainRows["FaxNo"]==""?$Fax:$mainRows["FaxNo"];

	//放在后面才行 add by zx 2012-01-07
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

	}

$check_Id=$Id;

include "../model/subprogram/mycompany_info.php";
//Invocse列表
$chSUMQty=0;
$boxSUMQty=0;
$Total=0;
//$Id='184';
$mySql2="SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,P.Description,S.Qty,S.Price,S.Type,S.YandN,S.ProductId  as ProductId,PI.PaymentTerm,P.bjRemark,P.TypeId
FROM $DataIn.ch1_shipsheet S 
LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=O.Id 
LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE S.Mid='$Id' AND S.Type='1'
UNION ALL 
SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.SampName AS eCode,O.Description,
S.Qty,S.Price,S.Type,S.YandN,'0' as ProductId,'' AS PaymentTerm ,'' AS bjRemark,'' AS TypeId 
FROM $DataIn.ch1_shipsheet S 
LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId WHERE S.Mid='$Id' AND S.Type='2' AND O.Type='1'";
//echo $mySql2;
$sheetResult = mysql_query($mySql2,$link_id);
$i=1;
if($sheetRows = mysql_fetch_array($sheetResult)){
         $PaymentTerm=$sheetRows["PaymentTerm"];
	      $PaymentTerm=$PaymentTerm==""?"":"Payment term:$PaymentTerm<br>";  //放在Terms里
	do{
		$OrderPO=$sheetRows["OrderPO"];
		$OrderPO=$OrderPO==""?" ":$OrderPO;
		if ($InvoiceNO=="IW 012") $OrderPO.="(22288)";
		$cName=$sheetRows["cName"];
		$eCode=$sheetRows["eCode"];
		$Description=$sheetRows["Description"];
        $ShipType=$sheetRows["ShipType"];
		$Qty=$sheetRows["Qty"];
		$Price=$sheetRows["Price"];
		$Amount=sprintf("%.2f",$Qty*$Price);
		$chSUMQty=$chSUMQty+$Qty;
		$Total=sprintf("%.2f",$Total+$Amount);
		$bjRemark=$sheetRows["bjRemark"];
		$color="";
		$ProductId=$sheetRows["ProductId"];
		if($CompanyId==1064 && $ProductId!='0'){  //看是否出过货
			$Pre_Temp=mysql_query("SELECT ProductId  FROM $DataIn.ch1_shipsheet where ProductId='$ProductId' AND Mid!='$Id'   LIMIT 1",$link_id);  //看是否出过货
			//echo "SELECT ProductId  FROM $DataIn.ch1_shipsheet where ProductId='$ProductId' AND Mid!='$Id'   LIMIT 1 <br>";
			if(!($MasterRow = mysql_fetch_array($Pre_Temp))){
				$color="color=#FF0000";
			}

		}
		//海关编码
		$TypeId=$sheetRows["TypeId"];
		/*
		if($TypeId!=""){
		     $HSResult=mysql_query("SELECT HSCode FROM $DataIn.customscode WHERE CompanyId='$CompanyId' AND TypeId='$TypeId'",$link_id);
		    if (mysql_num_rows($HSResult)>0){
		            $HSCode=mysql_result($HSResult,0,"HSCode");
		     }
		}
		else $HSCode="";
		*/
		$HSCodeArray=explode("HS CODE ", $Description);
		if (count($HSCodeArray)==2){
			 $Description=$HSCodeArray[0];
			 $HSCode=$HSCodeArray[1];
		}else $HSCode="";

		$iTableList.="<tr><td valign=middle align=center height=$RowsHight $color >$i</td><td valign=middle $color >$OrderPO</td><td valign=middle $color >$eCode</td><td valign=middle $color>$Description</td><td valign=middle align=right $color>$Qty</td><td valign=middle align=right $color>$Price</td><td valign=middle align=right $color>$Amount</td></tr>";
		$mcaTableList.="<tr><td valign=middle align=center height=$RowsHight $color>$i</td><td valign=middle $color>$OrderPO</td><td valign=middle $color>$eCode</td><td valign=middle $color>$Description</td><td valign=middle align=right $color>$Qty</td></tr>";
		$eurTableList.="<tr><td valign=middle align=center height=$RowsHight>$i</td><td valign=middle>$eCode</td><td valign=middle align=right>$Qty</td><td valign=middle align=right>$Price</td><td valign=middle align=right>$Amount</td></tr>";

		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
		if($InvoiceModel!=2){
		    $$eurTableNo="<table  border=1 ><tr>
		    <td width=8 valign=middle align=center height=$RowsHight $color>$i</td>
		    <td width=21 valign=middle $color>$OrderPO</td>
		    <td width=35 valign=middle $color>$eCode</td>
		    <td width=67 valign=middle $color>$Description</td>
		    <td width=19 valign=middle $color>$HSCode</td>
		    <td width=12 valign=middle align=right $color>$Qty</td>
		    <td width=16 valign=middle align=right $color>$Price</td>
		    <td width=17 valign=middle align=right $color>$Amount</td>
		    </tr></table>";}
		else{
		    if($CompanyId=='1075')$Description=$cName;
		    $$eurTableNo="<table  border=1 ><tr>
		    <td width=8 valign=middle align=center height=$RowsHight $color>$i</td>
		    <td width=25 valign=middle $color>$OrderPO</td>
		    <td width=35 valign=middle $color>$eCode</td>
		    <td width=70 valign=middle $color>$Description</td>
		    <td width=19 valign=middle align=right $color>$Qty</td>
		    <td width=19 valign=middle align=right $color>$Price</td>
		    <td width=19 valign=middle align=right $color>$Amount</td>
		    </tr></table>";
		    }

		//MCA
		$mcaTableNo="mcaTableNo".strval($i);   //不带价格的
		$$mcaTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight $color>$i</td>
		<td width=25 valign=middle $color>$OrderPO</td>
		<td width=35 valign=middle $color>$eCode</td>
		<td width=108 valign=middle $color>$Description</td>
		<td width=19 valign=middle align=right $color>$Qty</td>
		</tr></table>";
		//****************************//mco报价规则
		$RemarkTableNo="RemarkTableNo".strval($i);
		if($CompanyId=='1066' && $bjRemark!=""){
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



$boxSUMQty=$chSUMQty;
//非装箱项目
$unPackingSamp=mysql_query("SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,'' AS eCode,O.Description,S.Qty,S.Price,S.Type,S.YandN 
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
		$$eurTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight>$i</td>
		<td width=21 valign=middle >$OrderPO</td>
		<td width=35 valign=middle >$eCode</td>
		<td width=67 valign=middle >$Description</td>
		<td width=19 valign=middle >$HSCode</td>
		<td width=12 valign=middle align=right >$Qty</td>
		<td width=16 valign=middle align=right >$Price</td>
		<td width=17 valign=middle align=right >$Amount</td>
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
  

  
  <tr>
  <td colspan=3  height=30  align='left' valign=middle >BANK:<br>Beneficiary: $Beneficary<br>Bank         : $Bank<br>Bank Add : $BankAdd<br>Swift ID    : $SwiftID<br>A/C NO    : $ACNO</td>
  </tr> 
  </table>
  ";

/*
  <tr  >
  <td  height=8 colspan=3 align='left' valign=middle >Currency  :$Symbol</td>
  </tr>
*/


$packingSUMQty=0;
$isFirst=0;  //0表示首行 则不持闭表。
$i=1;
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
			<td valign=middle colspan=4 height=$RowsHight> </td>
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

//输出Invoice
$filename="../download/invoice/$InvoiceNO.pdf";
if(file_exists($filename)){unlink($filename);}
if ($NewAndOld=="Old"){
	$E_Company="Middle Cloud Trading Ltd";
	$S_Company="上海市中云包装厂";
	$C_Company="上海市中雲包裝廠";
	include "invoicetopdf_old/invoicemodel_".$InvoiceModel.".php";
}
else {
	include "invoicetopdf/invoicemodel_".$InvoiceModel.".php";
}

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

if ($FromFunPage=='_tooldpdf'){
	$pdf->Output();
}
else{
		$pdf->Output("$filename","F");
		if(($CompanyId==1001)|| ($CompanyId==1064) || ($CompanyId==1071)){
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


		//输出财务使用Invoice
		$cwfilename="../download/cw_invoice/$InvoiceNO.pdf";
		if(file_exists($cwfilename)){unlink($cwfilename);}

		include "invoicetopdf/invoicemodel_cw_".$InvoiceModel.".php";
		$pdf->Output("$cwfilename","F");
		$Log.="<br>财务使用Invoice $InvoiceNO 重置完毕!";

		//生成XML文件
		$CreateXmlFile="SAVE_INVOICE";
		include "ch_shippinglist_toxml.php";

		if($CompanyId==1074){
		           include "ch_shippinglist_toxml(strax).php"; // 新增的库存
		           include "ch_shippinglist_toxml(strax)_stock.php";//总的库存
		   }
}
?>
