<?php
//电信-zxq 2012-08-01
if ($CreateXmlFile!="SAVE_INVOICE"){
   //直接下载
      include "../basic/chksession.php" ;
      include "../basic/parameter.inc";
  }

$sheetResult = mysql_query("SELECT M.InvoiceNO,O.OrderPO,P.eCode,P.Description,S.Qty,S.Price,S.Type 
     FROM $DataIn.ch1_shipmain M 
     LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
     LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
     LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE M.Id='$Id' AND S.Type='1' 
 UNION ALL 
     SELECT M.InvoiceNO,'' AS OrderPO,O.SampName AS eCode,O.Description,S.Qty,S.Price,S.Type   
     FROM $DataIn.ch1_shipmain M 
     LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id  
     LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId WHERE M.Id='$Id' AND S.Type='2' AND O.Type='1' 
 UNION ALL 
     SELECT M.InvoiceNO,'' AS OrderPO,'' AS eCode,O.Description,S.Qty,S.Price,S.Type 
     FROM $DataIn.ch1_shipmain M 
     LEFT JOIN $DataIn.ch1_shipsheet S  ON S.Mid=M.Id  
     LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId WHERE S.Mid='$Id' AND S.Type='2' AND O.Type='0' 
",$link_id);

if($sheetRows = mysql_fetch_array($sheetResult)){
    $i=1;
    $InvoiceNO=$sheetRows["InvoiceNO"];
    do{
		$OrderPO=$sheetRows["OrderPO"];
		$Qty=$sheetRows["Qty"];
		$Price=sprintf("%.3f",$sheetRows["Price"]);
		$Amount=sprintf("%.2f",$Qty*$Price);
		$eCode=$sheetRows["eCode"];
		$Description=$sheetRows["Description"];
                $eCode=$eCode==""?$Description:$eCode;

                 //去除换行符
                $char_change = array("\r\n", "\n", "\r");
                $Description=str_replace($char_change,'', $Description);

                $arrdata[]=array(
                        'Ln.'=>$i,
                        'PO'=>$OrderPO,
                        'ProductCode'=>$eCode,
                        'Description'=>$Description,
                        'Qty'=>$Qty,
                        'UnitPrice'=>$Price,
                        'Amount'=>$Amount
                        );
                $i++;

        }while($sheetRows = mysql_fetch_array($sheetResult));

  if ($CreateXmlFile!="SAVE_INVOICE"){
      header("Content-Type:application/xml");
      header("Content-Disposition:attachment;filename=$InvoiceNO.xml");
  }

  $xmlDoc = new DOMDocument('1.0', 'utf-8');
  $xmlDoc->formatOutput = true;

  $r = $xmlDoc->createElement('INVOICE');
  $xmlDoc->appendChild( $r );

  foreach($arrdata as $data )
  {
     $b = $xmlDoc->createElement('Product');
      while(list($key,$value)=each($data)){
        $c=$xmlDoc->createElement($key);
        $c->appendChild($xmlDoc->createTextNode($value));
        $b->appendChild($c);
      }
     $r->appendChild( $b );
  }

  if ($CreateXmlFile=="SAVE_INVOICE"){
    $FilePath="../download/xmlfile/$CompanyId";
    if(!file_exists($FilePath)) dir_mkdir($FilePath);

    $wFile=$FilePath . "/" . $InvoiceNO . ".xml";
    $xmlDoc->save($wFile);
    if ($xmlDoc){
         $Log.="生成" . $InvoiceNO . ".xml文件成功！<br>";
         }
     else{
         $Log.="<div class='redB'>生成" . $InvoiceNO . ".xml文件失败！</div><br>" ;
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
