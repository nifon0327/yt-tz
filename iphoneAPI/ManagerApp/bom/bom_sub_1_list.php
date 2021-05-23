<?php 
//BOM采购单详情
$dataArray=array();
include "../../basic/downloadFileIP.php";
 $Result=mysql_query("SELECT S.Id,S.POrderId,S.StockId,S.StuffId,S.AddQty,S.StockQty,S.OrderQty,S.FactualQty,A.Picture,Y.OrderPO,K.oStockQty,A.Price   
         FROM $DataIn.cg1_stocksheet S 
         LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
         LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
         LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId  
         WHERE  S.Id='$Id' LIMIT 1",$link_id);

 if($myRow = mysql_fetch_array($Result)) 
 {
     $StockId=$myRow["StockId"];
     $StuffId=$myRow["StuffId"];
     
     $POrderId=$myRow["POrderId"];
     $OrderPO=$POrderId==""?"特采单":$myRow["OrderPO"];
     $POSign=$POrderId==""?0:1;
     
     $OrderQty=$myRow["OrderQty"];    //订单数量
     $StockQty=$myRow["StockQty"];    //使用库存
     $oStockQty=$myRow["oStockQty"];   //订单库存
     $FactualQty=$myRow["FactualQty"];//需求数量
     $AddQty=$myRow["AddQty"];	//增购数量
     
     //取得历史最高价与最底价
     $historyPrice="";
     $CheckGSql=mysql_query("SELECT MAX(Price) AS hPirce,MIN(Price) AS lPrice FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId' AND Mid>0 ",$link_id);
      if($CheckGRow=mysql_fetch_array($CheckGSql)){
        $hPirce=$CheckGRow["hPirce"];
        $lPrice=$CheckGRow["lPrice"];
        if ($hPirce!="")
        {
           $historyPrice=" H:$hPirce  L:$lPrice";
        }
      }

     $Price=$myRow["Price"];
     
     $Picture=$myRow["Picture"];
     $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
   
     $listArray=array();
     $listArray[]=array("Cols"=>"1","Name"=>"采  购  ID:","Text"=>"$StockId");
     $listArray[]=array("Cols"=>"1","Name"=>"订 单 PO:","Text"=>"$OrderPO","onTap"=>"$POSign","ServerId"=>"$ServerId","Tag"=>"Order","Args"=>"$POrderId");
     $listArray[]=array("Cols"=>"1","Name"=>"订单数量:","Text"=>"$OrderQty");
     $listArray[]=array("Cols"=>"2","Name"=>"使用库存:","Text"=>"$StockQty","Text2"=>"可用库存: $oStockQty");
     //$listArray[]=array("Cols"=>"1","Name"=>"增购数量:","Text"=>"$AddQty");
     //$listArray[]=array("Cols"=>"1","Name"=>"需购数量:","Text"=>"$FactualQty");
     $listArray[]=array("Cols"=>"2","Name"=>"需购数量:","Text"=>"$FactualQty","Text2"=>"增购数量: $AddQty");
     //$listArray[]=array("Cols"=>"1","Name"=>"可用库存:","Text"=>"$oStockQty");
     $listArray[]=array("Cols"=>"1","Name"=>"默认单价:","Text"=>"$Price");
     $listArray[]=array("Cols"=>"1","Name"=>"历史单价:","Text"=>"$historyPrice","onTap"=>"1","ServerId"=>"$ServerId","Tag"=>"HistoryPrice","Args"=>"$StuffId");
     
    $jsonArray=array("Value"=>"$Picture","Type"=>"JPG","ImageFile"=>"$ImagePath","data"=>$listArray);
}
?>