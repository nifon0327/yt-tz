<?php
//电信-zxq 2013-01-24
if ($CompanyId==1066 || $CompanyId==1064){
    $sheetResult = mysql_query("SELECT P.ProductId,P.eCode,P.Price,P.Code,P.Weight,P.Description,C.Forshort,D.PreChar,D.Symbol,U.Name AS PackingUnit 
					          FROM $DataIn.productdata P 
					          LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
						      LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
						      LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit  
						      WHERE P.CompanyId='$CompanyId' AND P.Estate=1",$link_id);
  $arrdata=array();
   if($sheetRows = mysql_fetch_array($sheetResult)){
		     $i=1;
		     $Forshort=$sheetRows["Forshort"];
		    do{
		        $ProductId=$sheetRows["ProductId"];
				$eCode=$sheetRows["eCode"];
				$Code=$sheetRows["Code"];
				$CodeArray=explode("|", $Code);
				$CodeSTR=count($CodeArray)==2?$CodeArray[1]:$CodeArray[0];

				$PreChar=$sheetRows["PreChar"];
				$Symbol=$sheetRows["Symbol"];
				$Price=sprintf("%.3f",$sheetRows["Price"]);

				$Weight=$sheetRows["Weight"];
				$Description=$sheetRows["Description"];

				$arrdata[]=array(
		                        'Ln.'=>$i,
		                        'ProductCode'=>$eCode,
		                         'Name'=>$Description,
		                        'Code'=>$CodeSTR,
		                        'WeightUnit'=>'g',
		                        'Weight'=>$Weight,
		                         'UnitCurrency'=>$Symbol,
		                        'UnitPrice'=>$Price
		                        );
		                $i++;
        }while($sheetRows = mysql_fetch_array($sheetResult));

   //生成XML文件
  $xmlDoc = new DOMDocument('1.0', 'utf-8');
  $xmlDoc->formatOutput = true;

  $r = $xmlDoc->createElement('MCCLOUD');
  $xmlDoc->appendChild( $r );
  foreach($arrdata as $data )
  {
     $b = $xmlDoc->createElement('PRODUCT');
      while(list($key,$value)=each($data)){
        $c=$xmlDoc->createElement($key);
        $c->appendChild($xmlDoc->createTextNode($value));
        $b->appendChild($c);
      }
       $r->appendChild( $b );
  }

    $FilePath="../download/xmlfile/$CompanyId";
    if(!file_exists($FilePath)) dir_mkdir($FilePath);

    $wFile=$FilePath . "/Product.xml";
    $xmlDoc->save($wFile);
    if ($xmlDoc){
         $Log.="<br>生成" . $CompanyId . "的产品xml文件成功！";
         }
     else{
         $Log.="<br><div class='redB'>生成" . $CompanyId . "的产品xml文件失败！</div>" ;
     }
    $xmlDoc=null;
    }
}
?>
