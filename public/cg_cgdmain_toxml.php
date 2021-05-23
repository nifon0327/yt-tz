<?php 
//电信-zxq 2012-08-01
header("Content-Type:application/xml");
header("Content-Disposition:attachment;filename=$PurchaseID.xml");

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
 
$cgSql=mysql_query("SELECT M.PurchaseID,M.CompanyId,S.StockId,S.StuffId,F.StuffCname,P.ProductId AS Pid,P.cName AS Pname,S.FactualQty AS Qty,S.Price,A.Name AS Buyer,S.StockRemark AS Remark,M.Date   
            FROM $DataIn.cg1_stocksheet S 
            LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
            LEFT JOIN $DataIn.stuffdata F ON F.StuffId=S.StuffId 
            LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
            LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId 
            LEFT JOIN $DataIn.staffmain A ON A.Number=M.BuyerId  
            WHERE M.PurchaseID='$PurchaseID'",$link_id); 
 if ($cgRow=mysql_fetch_assoc($cgSql)){
      $arrdata=array();
       do{
          $arrdata[]=$cgRow;
    }while($cgRow=mysql_fetch_assoc($cgSql));
 }
 
  $xmlDoc = new DOMDocument('1.0', 'utf-8');
  $xmlDoc->formatOutput = true;
  
  $r = $xmlDoc->createElement('MC_ORDER');
  $xmlDoc->appendChild( $r );
  
  foreach($arrdata as $data )
  {
     $b = $xmlDoc->createElement('Order');
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
