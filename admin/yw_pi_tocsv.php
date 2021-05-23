<?php   
//电信-zxq 2012-08-01
header("Content-Type:application/csv");
header("Content-Disposition:attachment;filename=$Id.csv");

include "../basic/chksession.php" ;
include "../basic/parameter.inc";

$sheetResult = mysql_query("SELECT S.OrderPO,S.Id,S.Qty,S.Price,S.ShipType,P.eCode,P.Description,I.CompanyId,I.Leadtime,I.PaymentTerm,I.Notes,I.OtherNotes,I.Terms,P.bjRemark
FROM $DataIn.yw3_pisheet I
LEFT JOIN $DataIn.yw1_ordersheet S ON I.oId=S.Id
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
WHERE I.PI='$Id' ORDER BY S.OrderPO",$link_id);		
if($sheetRows = mysql_fetch_array($sheetResult)){
    $i=1;
    do{
		$OrderPO=$sheetRows["OrderPO"];
		$Qty=$sheetRows["Qty"];
		$QtySUM=$QtySUM+$Qty;
		$Price=sprintf("%.3f",$sheetRows["Price"]);
		$Amount=sprintf("%.2f",$Qty*$Price);
		$AmountSUM=sprintf("%.2f",$AmountSUM+$Amount);
		$eCode=$sheetRows["eCode"];
                $CompanyId=$sheetRows["CompanyId"];
		$Description=$sheetRows["Description"];
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

                if ($CompanyId==1018){
                    $arrdata[]=array($i,$OrderPO,$eCode,$Description,$Price,$Qty,$Amount,$Leadtime);
                }else{
                    $arrdata[]=array($i,$OrderPO,$eCode,$Price,$Qty,$Amount,$ShipType,$Leadtime);   
                }
                
             
                $i++;
		
        }while($sheetRows = mysql_fetch_array($sheetResult));      
}

if ($CompanyId==1018){
     $keydata=array('Ln.','PO#','Product Code','Description','Unit Price','Qty','Amount','Leadtime');
}else{
     $keydata=array('Ln.','PO#','Product Code','Unit Price','Qty','Amount','Air/Sea','Leadtime');
}

//输出表头
$keyCount=count($keydata)-1;
while(list($key,$value)=each($keydata)){ 
    if ($key<$keyCount){
         echo $value.",";
        }
    else{
         echo $value;
       }
}

echo "\n";  //换行
 
foreach($arrdata as $products)
  {  
    while(list($key,$value)=each($products)){
       if ($key<$keyCount){
           // echo $value.",";
            echo "\"".str_replace('"','""',$value)."\"".",";  
          }
       else{
            echo "\"".str_replace('"','""',$value)."\"";
         // echo $value;
       } 
    }  
    echo "\n";  
  } 
?>
