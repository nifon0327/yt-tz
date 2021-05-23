<?php
defined('IN_COMMON') || include '../../basic/common.php';

//电信-joseph
//扣款单另行处理
$RowsHight=5;			//表格行高
$InvoiceHeadFontSize=8;	//头文字字体大小
$TableFontSize=8;		//表格字体大小
$Commoditycode="";
$FromFunPos="CH";
//公司信息//
$mySql1="SELECT M.CompanyId,M.DeliveryDate,M.DeliveryNumber,U.Symbol,I.Company,I.Fax,
         I.Address,D.InvoiceModel,D.SoldTo,D.Address AS ToAddress,D.FaxNo,
         D.SoldFrom,D.FromAddress,D.FromFaxNo,M.Remark
         FROM $DataIn.ch1_deliverymain M 
         LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
		 LEFT JOIN $DataPublic.currencydata U ON C.Currency=U.Id 
         LEFT JOIN $DataIn.companyinfo I ON I.CompanyId=C.CompanyId and I.Type=2 
         LEFT JOIN $DataIn.ch8_shipmodel D ON D.Id=M.ModelId 
         WHERE M.Id=$Id LIMIT 1";
//echo $mySql1;
$mainResult = mysql_query($mySql1,$link_id);
if($mainRows = mysql_fetch_array($mainResult)){
	$CompanyId=$mainRows["CompanyId"];
	$DeliveryDate=date("d-M-y",strtotime($mainRows["DeliveryDate"]));
	$DeliveryNumber=$mainRows["DeliveryNumber"];
	$Company=$mainRows["Company"];
	$Symbol=$mainRows["Symbol"]=="USD"?"U.S.DOLLARS":$mainRows["Symbol"];
	$Fax=$mainRows["Fax"];
	$Address=$mainRows["Address"];
	$InvoiceModel=$mainRows["InvoiceModel"];
	$SoldTo=$mainRows["SoldTo"]==""?$Company:$mainRows["SoldTo"];
	$ToAddress=$mainRows["ToAddress"]==""?$Address:$mainRows["ToAddress"];
	$FaxNo=$mainRows["FaxNo"]==""?$Fax:$mainRows["FaxNo"];
	$Remark=$mainRows["Remark"];
	//放在后面才行
	$Company=$mainRows["SoldFrom"]==""?$Company:$mainRows["SoldFrom"];
	$Address=$mainRows["FromAddress"]==""?$Address:$mainRows["FromAddress"];
	$Fax=$mainRows["FromFaxNo"]==""?$Fax:$mainRows["FromFaxNo"];
	}
//Forwarder 信息
$ForwarderSql=mysql_query("SELECT C.Address,M.ForwaderRemark,M.ForwaderId 
          FROM $DataIn.ch1_deliverymain M 
		  LEFT JOIN $DataPublic.freightdata F ON F.CompanyId=M.ForwaderId
		  LEFT JOIN $DataIn.companyinfo C ON C.CompanyId=F.CompanyId
		  WHERE M.Id='$Id' AND C.Type='5'",$link_id);
if($ForwarderRow=mysql_fetch_array($ForwarderSql)){


	 $ForwaderRemark=$ForwarderRow["ForwaderRemark"];
	 $ForwaderId=$ForwarderRow["ForwaderId"];
	 if($ForwaderId!='3114'){  //3114是表示自已定义
     	$ForwarderAddress=$ForwarderRow["Address"];
	 }
	 else{
		$ForwarderAddress=$ForwaderRemark;
	 }
  }

include "mycompany_info.php";


$chSUMQty=0;
$boxSUMQty=0;
$Total=0;
$Amount=0;
$TotalQty=0;
$TotalAmount=0;
$mySql2="SELECT D.Id,D.POrderId,O.OrderPO,P.cName,P.eCode,P.Description,D.DeliveryQty,D.Price,
         D.Type ,M.InvoiceNO,P.Code,D.Mid,P.TypeId
         FROM $DataIn.ch1_deliverysheet D 
         LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=D.ShipId
         LEFT JOIN $DataIn.ch1_shipmain  M ON M.Id=S.Mid
         LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=D.POrderId 
         LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId
         WHERE D.Mid='$Id' AND D.Type='1' GROUP BY D.Id
         UNION ALL 
         SELECT S.Id,S.POrderId,O.SampPO AS OrderPO,O.SampName AS cName,O.SampName AS eCode,O.Description,
		 D.DeliveryQty,D.Price,D.Type,M.InvoiceNO,'' AS Code,D.Mid ,'' AS TypeId
         FROM $DataIn.ch1_deliverysheet D 
         LEFT JOIN $DataIn.ch1_shipsheet  S ON S.Mid=D.ShipId
         LEFT JOIN $DataIn.ch1_shipmain  M ON M.Id=S.Mid
         LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=D.POrderId 
         WHERE D.Mid='$Id' AND D.Type='2' AND O.Type='1' GROUP BY D.Id";
//echo $mySql2;
$i=1;
$AllPO="";
$AllInvoice="";
$TempMid="";
$sheetResult = mysql_query($mySql2,$link_id);
if($sheetRows = mysql_fetch_array($sheetResult)){
	do{
	    $Mid=$sheetRows["Mid"];
		$InvoiceNO=$sheetRows["InvoiceNO"];
		if($TempMid!=$Mid)$AllInvoice.=$InvoiceNO."/";
		$OrderPO=$sheetRows["OrderPO"];
		if($OrderPO!="")$TempPO.=$OrderPO."/";
		$cName=$sheetRows["cName"];
		$eCode=$sheetRows["eCode"];
		$Description=$sheetRows["Description"];
		$Price=sprintf("%.2f",$sheetRows["Price"]);
		$DeliveryQty=$sheetRows["DeliveryQty"];
		$TotalQty+=$DeliveryQty;
		$Amount=$Price*$DeliveryQty;
		$TotalAmount+=$Amount;
		$chSUMQty=$chSUMQty+$DeliveryQty;
		if($sheetRows["Code"]!=""){
		$CodeArray=explode("|",$sheetRows["Code"]);
		$Code=$CodeArray[1];}
		else $Code="";
		$TempMid=$Mid;
		//H.SCode

		$TypeId=$sheetRows["TypeId"];
		if($TypeId!=""){
		$HSResult=mysql_fetch_array(mysql_query("SELECT HSCode FROM $DataIn.customscode WHERE CompanyId='$CompanyId' AND TypeId='$TypeId'",$link_id));
		$HSCode=$HSResult["HSCode"];}
		else $HSCode="";

		$eurTableNo="eurTableNo".strval($i);   //每一条记录都是一个表格
		$$eurTableNo="<table  border=1 ><tr>
		<td width=8 valign=middle align=center height=$RowsHight $color>$i</td>
		<td width=18 valign=middle align=center $color>$InvoiceNO</td>
		<td width=18 valign=middle align=center $color>$OrderPO</td>
		<td width=32 valign=middle $color>$eCode</td>
		<td width=60 valign=middle $color>$Description</td>
		<td width=20 valign=middle $color>$HSCode</td>
		<td width=12 valign=middle align=right $color>$DeliveryQty<</td>
		<td width=12 valign=middle align=right $color>$Price</td>
		<td width=15 valign=middle align=right $color>$Amount</td>
		</tr></table>";
		$i++;
		}while ($sheetRows = mysql_fetch_array($sheetResult));
	}
$Counts=$i;
$AllInvoice=rtrim($AllInvoice, "/");
$poArray=explode("/",$TempPO);
$poArray=array_unique($poArray);//去掉重复项
$count=count($poArray);
for($n=0;$n<$count;$n++){if($poArray[$n]!="")$AllPO.=$poArray[$n]."/";}
$AllPO=rtrim($AllPO, "/");



//总计
$eurTableNo="eurTableNo".strval($Counts);
$$eurTableNo="<table  border=1 ><tr bgcolor='#CCCCCC'>
             <td width=26 valign=middle align=left height=$RowsHight style=bold>Total</td>
			 <td width=130 >&nbsp;</td>
			 <td width=12 valign=middle align=right style=bold>$TotalQty</td>
			 <td width=12 >&nbsp;</td>
			 <td width=15 valign=middle align=right style=bold>$TotalAmount</td>
             </tr><tr>
  		      <td colspan=5  height=23  align='right' valign='top'>Currency: $Symbol</td>
  		      </tr></table>";

$boxSUMQty=$chSUMQty;
$packingSUMQty=0;
$isFirst=0;  //0表示首行 则不持闭表。
$i=1;
//装箱列表
if($toPackingList!="N"){	//装箱完整的情况下更新packinglist	其它更新页面：需装箱总数与已装箱总数是否一致
$plResult = mysql_query("SELECT L.BoxRow,L.BoxPcs,L.BoxQty,L.FullQty,L.WG,L.POrderId,L.BoxSpec 
FROM $DataIn.ch1_deliverypacklist L 
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

		$checkType=mysql_fetch_array(mysql_query("SELECT Type FROM $DataIn.ch1_deliverysheet WHERE POrderId='$POrderId' LIMIT 1",$link_id));
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
                                }
					else{
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


//输出Invoice
$filename="../download/DeliveryNumber/$DeliveryNumber.pdf";
if(file_exists($filename)){unlink($filename);}
include "../admin/billtopdf/billmodel.php";

//附加图片处理
$chechPicture=mysql_query("SELECT Picture FROM $DataIn.ch7_deliverypicture WHERE Mid='$Id' ORDER BY Id",$link_id);
if($PrictureRow=mysql_fetch_array($chechPicture)){
	do{
		$Picture=$PrictureRow["Picture"];
		$pdf->AddPage();
		$this_Photo="../download/DeliveryNumber/".$Picture;
		$pdf->Image($this_Photo,10,10,190,270,"JPG");
		}while($PrictureRow=mysql_fetch_array($chechPicture));
	}
$pdf->Output("$filename","F");


if($ActionId==26){
$Log.="DeliveryNumber $DeliveryNumber 重置完毕!<br>";}
else{$Log.="DeliveryNumber $DeliveryNumber 生成成功!<br>";}
?>
