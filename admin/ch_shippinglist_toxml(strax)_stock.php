<?php   
$totalarrdata=array();
$Ids="9380,9370,9355";
$sheetResult = mysql_query("SELECT  SUM(Qty) AS TotalQty,eCode,Code   FROM (
        SELECT P.eCode,S.Qty,S.Price,P.Code,M.Date,P.ProductId
     FROM $DataIn.ch1_shipmain M 
     LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
     LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
     LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId WHERE M.Id IN ($Ids) AND S.Type='1') A  WHERE 1 GROUP BY A.ProductId",$link_id);
if($sheetRows = mysql_fetch_array($sheetResult)){
    $i=1;
    do{
		$Qty=$sheetRows["TotalQty"];
		$eCode=$sheetRows["eCode"];
		$Code=$sheetRows["Code"];
		$CodeArray=explode("|", $Code);
		$CodeSTR=count($CodeArray)==2?$CodeArray[1]:$CodeArray[0];
        $Date=$sheetRows["Date"];      
                 //去除换行符
                $char_change = array("\r\n", "\n", "\r");   
                $Description=str_replace($char_change,'', $Description);
                
                $totalarrdata[]=array(
                        'Ln.'=>$i,
                        'ProductCode'=>$eCode,
                        'EAN'=>$CodeSTR,
                        'Qty'=>$Qty
                        );
                $i++;
		
        }while($sheetRows = mysql_fetch_array($sheetResult));      

  
  $xmlDoc = new DOMDocument('1.0', 'utf-8');
  $xmlDoc->formatOutput = true;
  
  $r = $xmlDoc->createElement('STOCK');
  $xmlDoc->appendChild( $r );
  
  foreach($totalarrdata as $data ){
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
    
    $wFile=$FilePath . "/stocklist.xml";
    $xmlDoc->save($wFile);
    if ($xmlDoc){
         $Log.="<br>strax的库存文件生成成功！";
         }
     else{
         $Log.="<br><div class='redB'>strax的库存文件生成失败！</div>" ;
     }
    $xmlDoc=null;
}
?>
