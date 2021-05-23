<?php   
$newarrdata=array();
$sheetResult = mysql_query("SELECT M.InvoiceNO,O.OrderPO,P.eCode,P.Description,S.Qty,S.Price,S.Type,P.Code,M.Date
     FROM $DataIn.ch1_shipmain M 
     LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
     LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
     LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE M.Id='$Id' AND S.Type='1'",$link_id);
if($sheetRows = mysql_fetch_array($sheetResult)){
    $i=1;
    $InvoiceNO=$sheetRows["InvoiceNO"];
    do{
		$OrderPO=$sheetRows["OrderPO"];
		$Qty=$sheetRows["Qty"];
		$Price=sprintf("%.3f",$sheetRows["Price"]);
		$Amount=sprintf("%.2f",$Qty*$Price);
		$eCode=$sheetRows["eCode"];
        $eCode=$eCode==""?$Description:$eCode;
		$Code=$sheetRows["Code"];
		$CodeArray=explode("|", $Code);
		$CodeSTR=count($CodeArray)==2?$CodeArray[1]:$CodeArray[0];
        $Date=$sheetRows["Date"];      
                 //去除换行符
                $char_change = array("\r\n", "\n", "\r");   
                $Description=str_replace($char_change,'', $Description);
                
                $newarrdata[]=array(
                        'Ln.'=>$i,
                        'ProductCode'=>$eCode,
                        'EAN'=>$CodeSTR,
                        'Qty'=>$Qty,
                        'Date'=>$Date,
                        );
                $i++;
		
        }while($sheetRows = mysql_fetch_array($sheetResult));      

  
  $xmlDoc = new DOMDocument('1.0', 'utf-8');
  $xmlDoc->formatOutput = true;
  
  $r = $xmlDoc->createElement('INVOICE');
  $xmlDoc->appendChild( $r );
     $e = $xmlDoc->createElement('DocNumber');
        $e->appendChild($xmlDoc->createTextNode("$InvoiceNO"));
     $r->appendChild( $e );
  
  foreach($newarrdata as $data ){
     $b = $xmlDoc->createElement('Product');
      while(list($key,$value)=each($data)){ 
        $c=$xmlDoc->createElement($key);
        $c->appendChild($xmlDoc->createTextNode($value));
        $b->appendChild($c);
      }
     $r->appendChild( $b );
  }
 
    $FilePath="../client/strax";
    if(!file_exists($FilePath)) makedir($FilePath);
    
    $wFile=$FilePath . "/" . $InvoiceNO . ".xml";
    $xmlDoc->save($wFile);
    if ($xmlDoc){
         $Log.="<br>strax生成" . $InvoiceNO . ".xml文件成功！";
         }
     else{
         $Log.="<br><div class='redB'>strax生成" . $InvoiceNO . ".xml文件失败！</div>" ;
     }
    $xmlDoc=null;
}
?>
