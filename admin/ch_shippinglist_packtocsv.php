<?php   
//电信-zxq 2012-08-01

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
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
                    $Small=$BoxSUM+1;//起始箱号
		    $Most=$BoxSUM+$BoxQty;//终止箱号 
                    $BoxSUM=$Most;
                    if($Most!=$Small){$Most=$Small."-".$Most;}  
                 }
                                        
                  //去除换行符
                $char_change = array("\r\n", "\n", "\r");   
                $Description=str_replace($char_change,'', $Description);
                
                $arrdata[]=array($Most,$OrderPO,$eCode,$Description,$BoxPcs,$BoxSpec,$FullQty,$NG,$WG);         
  	$i++;
      }while ($plRows = mysql_fetch_array($plResult));
}

 $keydata=array('No.','PO#','Product Code','Product Description','Unit/Carton','Carton Size(CM)','Quantity','NW(KG)','GW(KG)');
 
 header("Content-Type:application/csv");
 header("Content-Disposition:attachment;filename=" . $InvoiceNO . "_PackingList.csv"); 
  
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
