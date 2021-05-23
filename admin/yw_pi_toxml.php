<?php
//电信-zxq 2012-08-01
if ($CreateXmlFile=="SAVE_PI"){
        $Id=$PI;
      }
  else{ //直接下载
    header("Content-Type:application/xml");
    header("Content-Disposition:attachment;filename=$Id.xml");
    include "../basic/chksession.php" ;
    include "../basic/parameter.inc";
}

$sheetResult = mysql_query("SELECT S.OrderPO,S.Qty,S.Price,S.ShipType,P.eCode,P.Description,I.CompanyId,I.Leadtime 
FROM $DataIn.yw3_pisheet I
LEFT JOIN $DataIn.yw1_ordersheet S ON I.oId=S.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
WHERE I.PI='$Id' ORDER BY S.OrderPO",$link_id);
if($sheetRows = mysql_fetch_array($sheetResult)){
    $i=1;
    do{
		$OrderPO=$sheetRows["OrderPO"];
		$Qty=$sheetRows["Qty"];
		$Price=sprintf("%.3f",$sheetRows["Price"]);
		$Amount=sprintf("%.2f",$Qty*$Price);
		$eCode=$sheetRows["eCode"];
		$Description=$sheetRows["Description"];
                 //去除换行符
                $char_change = array("\r\n", "\n", "\r");
                $Description=str_replace($char_change,'', $Description);

                $CompanyId=$sheetRows["CompanyId"];
		$ShipType=$sheetRows["ShipType"];
		if (is_numeric($ShipType)){
		   	$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE Id='$ShipType' LIMIT 1",$link_id);
		   	if($shipTypeRow = mysql_fetch_array($shipTypeResult)){
		   	   $ShipType=$shipTypeRow["Name"];
		   	}
	   }

		$Leadtime=$sheetRows["Leadtime"];
		if($Leadtime=="" || $Leadtime=="0000-00-00"){
                   $Leadtime="no delivery";
                 }
         else {
                    //$DeliveryDate=date("j/n/y" ,strtotime($DeliveryDate));
                     $Leadtime=str_replace("*", "", $Leadtime);
				      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek",$link_id));
			          $PIWeek=$dateResult["PIWeek"];
			          if ($PIWeek>0){
				          $week=substr($PIWeek, 4,2);
					      $Leadtime="Week " . $week;
				      }
         }

                if ($CompanyId==1018){  //Eurotek
                    $arrdata[]=array(
                        'Ln.'=>$i,
                        'PO'=>$OrderPO,
                        'ProductCode'=>$eCode,
                        'Description'=>$Description,
                        'UnitPrice'=>$Price,
                        'Qty'=>$Qty,
                        'Amount'=>$Amount,
                        'Leadtime'=>$Leadtime
                        );
                }else{
                    $arrdata[]=array(
                        'Ln.'=>$i,
                        'PO'=>$OrderPO,
                        'ProductCode'=>$eCode,
                        'UnitPrice'=>$Price,
                        'Qty'=>$Qty,
                        'Amount'=>$Amount,
                        'Leadtime'=>$Leadtime
                        );
                }
                $i++;

        }while($sheetRows = mysql_fetch_array($sheetResult));


  $xmlDoc = new DOMDocument('1.0', 'utf-8');
  $xmlDoc->formatOutput = true;

  $r = $xmlDoc->createElement('PI');
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

if ($CreateXmlFile=="SAVE_PI"){
    $FilePath="../download/xmlfile/$CompanyId";
    if(!file_exists($FilePath)) dir_mkdir($FilePath);

    $wFile=$FilePath . "/PI_" . $Id . ".xml";
    $xmlDoc->save($wFile);
    if ($xmlDoc){
         $Log.="<br>生成PI_" . $Id . ".xml文件成功！";
         }
     else{
         $Log.="<br><div class='redB'>生成PI_" . $Id . ".xml文件失败！</div>" ;
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
