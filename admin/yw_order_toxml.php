<?php   
//电信-zxq 2012-08-01
if($CompanyId!=""){
	$ClientSTR="and M.CompanyId=\"$CompanyId\"";
	$OrderBY="order by M.OrderDate desc";
	}
else{
        $ClientSTR="";
	$OrderBY="order by M.CompanyId,M.OrderDate desc";
	}


include "../basic/chksession.php" ;
include "../basic/parameter.inc";

$mySql="SELECT S.OrderPO,M.OrderDate,S.ShipType,S.DeliveryDate,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,P.Unit,P.Description,C.Forshort  
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber  
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId  
LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
where 1 $ClientSTR  $SearchRows and S.Estate!='0' $OrderBY";

$result = mysql_query($mySql,$link_id);	

if($myrow = mysql_fetch_array($result)){
	$i=1;
        $Forshort=$myrow["Forshort"];
	do{
	 	$OrderPO=$myrow["OrderPO"];
		$OrderDate=$myrow["OrderDate"]."T00:00:00.000"; 
		$eCode=$myrow["eCode"];
		$Description=$myrow["Description"];
	  	$Qty=$myrow["Qty"];
		$ShipType=$myrow["ShipType"];
		if (is_numeric($ShipType)){
		   	$shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE Id='$ShipType' LIMIT 1",$link_id);
		   	if($shipTypeRow = mysql_fetch_array($shipTypeResult)){
		   	   $ShipType=$shipTypeRow["Name"];
		   	}
	   }
                $Price=$myrow["Price"];
                $cName=$myrow["cName"];
                
                //去除换行符
                $char_change = array("\r\n", "\n", "\r");   
                $Description=str_replace($char_change,'', $Description);
                
                //$cName=iconv( "UTF-8","gbk",$cName); 
                //echo $cName . "</br>";
		$DeliveryDate=$myrow["DeliveryDate"]=="0000-00-00"?"no delivery":$myrow["DeliveryDate"]."T00:00:00.000"; 
                if($CompanyId=="1032") {
                    $arrdata[]=array(
                        'PO'=>$OrderPO,
                        'Date'=>$OrderDate,
                        'ChineseName'=>$cName,
                        'ProductCode'=>$eCode,
                        'Description'=>$Description,
                        'Qty'=>$Qty,
                        'Price'=>$Price,
                        'Delivery'=>$DeliveryDate,
                        'Ship'=>$ShipType
                        );
                    }
                else{
                     $arrdata[]=array(
                        'PO'=>$OrderPO,
                        'Date'=>$OrderDate,
                        'ChineseName'=>$cName,
                        'ProductCode'=>$eCode,
                        'Description'=>$Description,
                        'Qty'=>$Qty,
                        'Delivery'=>$DeliveryDate,
                        'Ship'=>$ShipType
                        );
                    }
                $i++;	
        }while ($myrow = mysql_fetch_array($result));
}

$fileName=$Forshort . "_" . date('Ymd');

header("Content-Type:application/xml");
header("Content-Disposition:attachment;filename=$fileName.xml"); 

  $xmlDoc = new DOMDocument('1.0', 'utf-8');
  $xmlDoc->formatOutput = true;
  
  $r = $xmlDoc->createElement('OrderList');
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
  
  //在一字符串变量中建立XML结构
  $xmlString = $xmlDoc->saveXML();

  echo $xmlString;
?>
