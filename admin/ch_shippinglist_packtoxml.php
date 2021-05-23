<?php
//电信-zxq 2012-08-01
if ($CreateXmlFile!="SAVE_PACK"){
   //直接下载
      include "../basic/chksession.php" ;
      include "../basic/parameter.inc";
  }

$plResult = mysql_query("SELECT M.InvoiceNO,L.POrderId,L.BoxRow,L.BoxPcs,L.BoxQty,L.FullQty,L.WG,L.BoxSpec,S.Type  
          FROM $DataIn.ch1_shipmain M 
          LEFT JOIN $DataIn.ch2_packinglist L ON L.Mid=M.Id  
          LEFT JOIN $DataIn.ch1_shipsheet S ON S.POrderId=L.POrderId 
 WHERE M.Id='$Id' ORDER BY L.Id",$link_id);

if ($plRows = mysql_fetch_array($plResult)){
	$i=1;
        $InvoiceNO=$plRows["InvoiceNO"];
	do{
		$BoxRow=$plRows["BoxRow"];  //是否并箱
		$BoxPcs=$plRows["BoxPcs"];
		$BoxQty=$plRows["BoxQty"];
		$POrderId=$plRows["POrderId"];
		$BoxSpec=$plRows["BoxSpec"];   //箱尺寸
                $BoxSpec=str_replace( '×', '*',$BoxSpec);

		$FullQty=$plRows["FullQty"];
		$WG=$plRows["WG"];
		$Type=$plRows["Type"];

		switch($Type){
			case 1:	//产品
				$pSql = mysql_query("SELECT 
				S.OrderPO,P.cName,P.eCode,P.Description 
				FROM $DataIn.yw1_ordersheet S 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
				WHERE S.POrderId='$POrderId' LIMIT 1",$link_id);
				if ($pRows = mysql_fetch_array($pSql)){
					$OrderPO=$pRows["OrderPO"];
					$cName=$pRows["cName"];
					$eCode=$pRows["eCode"];
					$Description=$pRows["Description"];

					}
				break;
			case 2:	//样品
				$sSql = mysql_query("SELECT * FROM $DataIn.ch5_sampsheet WHERE SampId='$POrderId'",$link_id);
				if ($sRows = mysql_fetch_array($sSql)){
					$OrderPO="";
					$cName=$sRows["SampName"];
					$eCode=$cName;
					$Description=$sRows["Description"];
					}
				break;
			}

                 $WgSUM=$WgSUM+$WG*$BoxQty;//毛重总计
		 $NG=$WG-1;//净重
		 if($NG<=0){
		     $NG=round($WG*100/2)/100;
		 }
		 $NgSUM=$NgSUM+$NG*$BoxQty;//净重总计
		 $packingSUMQty=$packingSUMQty+$FullQty;//装箱总数合计

		 if($BoxRow==0){
                  $FullQty="";
                  $NG="";
                  $WG="";
                 }
                 else{
                    $NG.=" KG";
                    $WG.=" KG";
                    $Small=$BoxSUM+1;//起始箱号
		    $Most=$BoxSUM+$BoxQty;//终止箱号
                    $BoxSUM=$Most;
                    if($Most!=$Small){$Most=$Small."-".$Most;}
                 }

                  //去除换行符
                $char_change = array("\r\n", "\n", "\r");
                $Description=str_replace($char_change,'', $Description);

                $arrdata[]=array(
                        'No.'=>$Most,
                        'PO'=>$OrderPO,
                        'ProductCode'=>$eCode,
                        'ProductDescription'=>$Description,
                        'Unit'=>$BoxPcs,
                        'CartonSize'=>$BoxSpec,
                        'Quantity'=>$FullQty,
                        'NW'=>$NG,
                        'GW'=>$WG
                        );
  	$i++;
      }while ($plRows = mysql_fetch_array($plResult));

   if ($CreateXmlFile!="SAVE_PACK"){
       header("Content-Type:application/xml");
       header("Content-Disposition:attachment;filename=" . $InvoiceNO . "_PackingList.xml");
   }

  $xmlDoc = new DOMDocument('1.0', 'utf-8');
  $xmlDoc->formatOutput = true;

  $r = $xmlDoc->createElement('PackingList');
  $xmlDoc->appendChild( $r );

  foreach($arrdata as $data )
  {
     $b = $xmlDoc->createElement('Carton');
      while(list($key,$value)=each($data)){
        $c=$xmlDoc->createElement($key);
        $c->appendChild($xmlDoc->createTextNode($value));
        $b->appendChild($c);
      }
     $r->appendChild( $b );
  }
  if ($CreateXmlFile=="SAVE_PACK"){
    $FilePath="../download/xmlfile/$CompanyId";
    if(!file_exists($FilePath)) dir_mkdir($FilePath);

    $wFile=$FilePath . "/Packing_" . $InvoiceNO . ".xml";
    $xmlDoc->save($wFile);
    if ($xmlDoc){
         $Log.="<br>生成Packing_" . $InvoiceNO . ".xml文件成功！";
         }
     else{
         $Log.="<br><div class='redB'>生成" . $InvoiceNO . ".xml文件失败！</div>" ;
     }
    $xmlDoc=null;
    }
else{  //直接下载
       //在一字符串变量中建立XML结构
      $xmlString = $xmlDoc->saveXML();
      echo $xmlString;
   }
}

?>
