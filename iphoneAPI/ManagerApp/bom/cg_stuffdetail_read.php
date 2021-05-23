<?php 
//BOM采购单详情
$dataArray=array();
include "../../basic/downloadFileIP.php";
 $Result=mysql_query("SELECT S.Id,S.Mid,S.POrderId,S.StockId,S.StuffId,S.AddQty,S.BuyerId,S.OrderQty,S.FactualQty,S.Price,S.BuyerId,A.StuffCname,A.TypeId,A.Picture,U.Name AS UnitName,C.PreChar,P.Forshort,M.PurchaseID,Y.OrderPO  
         FROM $DataIn.cg1_stocksheet S 
         LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
         LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
         LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
         LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId  
         WHERE  S.StockId='$Id' LIMIT 1",$link_id);
 if (mysql_num_rows($Result)<=0){
	  $Result=mysql_query("SELECT S.Id,S.Mid,S.POrderId,S.StockId,S.StuffId,S.AddQty,S.BuyerId,S.OrderQty,S.FactualQty,S.Price,S.BuyerId,A.StuffCname,A.TypeId,A.Picture,U.Name AS UnitName,C.PreChar,P.Forshort,M.PurchaseID,Y.OrderPO  
         FROM  $DataIn.cg1_stuffcombox SG 
         LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=SG.mStockId 
         LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
         LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
         LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
         LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId  
         WHERE  SG.StockId='$Id' LIMIT 1",$link_id);
 }
 if($myRow = mysql_fetch_array($Result)) 
 {
     $Id=$myRow["Id"];
     $StockId=$myRow["StockId"];
     $StuffId=$myRow["StuffId"];
     $TypeId=$myRow["TypeId"];
     $Forshort=$myRow["Forshort"];
     $StuffCname=$myRow["StuffCname"];//配件名称
     
     $POrderId=$myRow["POrderId"];
     $OrderPO=$POrderId==""?"特采单":$myRow["OrderPO"];
     $POSign=$POrderId==""?0:1;
     
     $OrderQty=$myRow["OrderQty"];    //订单数量
     $FactualQty=$myRow["FactualQty"];//需求数量
     $AddQty=$myRow["AddQty"];	//增购数量
     $Price=$myRow["Price"];
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
      
     $UnitName=$myRow["UnitName"];
     $PreChar=$myRow["PreChar"];
     $Forshort=$myRow["Forshort"];
     
	$Qty=$FactualQty+$AddQty;

     $Amount=sprintf("%.2f",$Qty*$Price);
     
      $Mid=$myRow["Mid"];
     $PurchaseID=$myRow["PurchaseID"];
     $PurchasePath=$PurchaseID==""?"":"model/subprogram/purchaseorder_view.php?Id=$Mid&FromPage=iPhone";
     $Purchase=$PurchaseID==""?0:1;
     
     $Picture=$myRow["Picture"];
     $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
     //$PdfPath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. ".pdf":"";
     $Operator=$myRow["BuyerId"];
     include '../../model/subprogram/staffname.php';
     
     $rkResult=mysql_query("SELECT M.Id 
FROM $DataIn.ck1_rksheet R 
LEFT JOIN $DataIn.ck1_rkmain M ON R.Mid=M.Id WHERE R.StockId='$StockId'",$link_id);
   $rkSign=mysql_num_rows($rkResult)>0?1:0;
   
   $QtyArray=array(
                        array("Name"=>"Qty","Text"=>"$Qty$UnitName"),
                        array("Name"=>"Price","Text"=>"$PreChar$Price"),
                        array("Name"=>"Amount","Text"=>"$PreChar$Amount")
                       );
                      
   $PidArray=array(
                        array("Name"=>"PurchaseID","Text"=>"$PurchaseID"),
                        array("Name"=>"Operator","Text"=>"$Operator")
                       );
 
   $dataArray= array( 
                        array("Name"=>"StockId","Text"=>"$StockId","onTap"=>"$rkSign","Tag"=>"rkList","Args"=>"$StockId"),
                        //,"rtIcon"=>"stuff_Operator","rtText"=>"$Operator"
                        array("Name"=>"OrderPO","Text"=>"$OrderPO","onTap"=>"$POSign","Tag"=>"OrderDetail","Args"=>"$POrderId"),
                        array("Name"=>"Provider","Text"=>"$Forshort"),
                        array("Cols"=>"2","ColsData"=>$PidArray,"onTap"=>"$Purchase","Tag"=>"Web","URL"=>"$PurchasePath"),
                        //array("Name"=>"PurchaseID","Text"=>"$PurchaseID","onTap"=>"$Purchase","Tag"=>"Web","URL"=>"$PurchasePath"),
                        array("Name"=>"StuffId","Text"=>"$StuffId"),
                        array("Cols"=>"3","ColsData"=>$QtyArray),
                        array("Name"=>"HistoryPrice","Text"=>"$historyPrice","onTap"=>"1","Tag"=>"HistoryPrice","Args"=>"$StuffId")
                        ); 
      $jsonArray=array("NavTitle"=>"$StuffCname",
				                  "Image"=>array("Value"=>"$Picture","Tag"=>"Image","URL"=>"$ImagePath"),
				                 "data"=>$dataArray);
 }


?>