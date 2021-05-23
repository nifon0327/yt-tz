<?
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="订单整箱备料或者判断是否够料";			//需处理
$Log_Funtion="数据检查";
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$CheckRow=mysql_fetch_array(mysql_query("SELECT S.OrderPO,S.Qty,S.ProductId,P.cName,P.eCode,S.POrderId
FROM $DataIn.yw1_ordersheet S
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
WHERE 1 AND S.POrderId=$POrderId",$link_id));
$ProductId=$CheckRow["ProductId"];
$Qty=$CheckRow["Qty"];

$BoxResult = mysql_query("SELECT P.Relation FROM $DataIn.pands P LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 and T.TypeId='9040'",$link_id);
if($BoxRows = mysql_fetch_array($BoxResult)){
   $Relation=$BoxRows["Relation"];
   $RelationArray=explode("/",$Relation);
   if($RelationArray[1]!="")$Relation=$RelationArray[1];
   else $Relation=$RelationArray[0];
  }
/*先再检查该生产数量下料库存是否足够。其次检查该订单是否有外箱,有按整箱备料，若没有，则随即备料。*/
$TotalResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS TotalQty FROM $DataIn.yw1_scsheet WHERE POrderId=$POrderId AND Estate=0",$link_id));
$TotalQty=$TotalResult["TotalQty"]==""?0:$TotalResult["TotalQty"];
$TotalQty=$TotalQty+$thisQty;
//1.检查料够不够。
$checkStuff=mysql_query("SELECT  SUM(G.OrderQty) AS OrderQty,K.tStockQty,SUM(L.Qty) AS llQty  FROM $DataIn.cg1_stocksheet G 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId
LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId=G.StockId
WHERE 1 AND G.POrderId='$POrderId' AND T.mainType<=1 GROUP BY G.StuffId",$link_id);
$unNum=0;
if($CheckStuffRow=mysql_fetch_array($checkStuff)){
     do{
           $stuffOrderQty=$CheckStuffRow["OrderQty"];
           $tStockQty=$CheckStuffRow["tStockQty"];
           $llQty=$CheckStuffRow["llQty"];
           $needQty=ceil($stuffOrderQty*($TotalQty/$Qty));
           if($tStockQty<$needQty-$llQty)$unNum++;
          }while($CheckStuffRow=mysql_fetch_array($checkStuff));
}
if($unNum==0){
         if($Relation!=""){//料够并且是有外箱则必须按整箱备料
                      if($TotalQty>=$Qty)echo "0";//最后一次登记则允许。
                       else{
                               if($thisQty%$Relation==0)echo "0";
                               else echo "2";
                              }
                 }
          }
else{
          echo "1";
       }
?>