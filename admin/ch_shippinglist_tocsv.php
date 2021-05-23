<?php   
//电信-zxq 2012-08-01

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
 
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
                
                $arrdata[]=array($i,$OrderPO,$eCode,$Description,$Qty,$Price,$Amount);
                    
                $i++;
		
        }while($sheetRows = mysql_fetch_array($sheetResult));      
}

 $keydata=array('Ln.','PO#','Product Code','Description','Qty','Unit Price','Amount');
 
 header("Content-Type:application/csv");
 header("Content-Disposition:attachment;filename=$InvoiceNO.csv"); 
  
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
