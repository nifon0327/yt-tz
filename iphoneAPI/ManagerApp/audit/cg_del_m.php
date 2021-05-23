<?php 
//统计类配件异动审核
 $mySql="SELECT S.Id,S.Mid,S.POrderId,S.StockId,S.StuffId,S.AddQty,S.BuyerId,S.OrderQty,S.StockQty,S.FactualQty,S.Price,S.BuyerId,IFNULL(S.modified,S.ywOrderDTime) AS modified,S.StockRemark,
 A.StuffCname,A.TypeId,A.Picture,C.PreChar,U.Name AS UnitName,P.Forshort   
         FROM $DataIn.cg1_stocksheet S 
         LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
         LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
         LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit 
         LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
         LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency 
         WHERE  S.Estate=4  AND T.mainType>1 ORDER BY modified";
 $Result=mysql_query($mySql,$link_id);
 while($myRow = mysql_fetch_array($Result)) 
 {
	        $Id=$myRow["Id"];
	        $TypeId=$myRow["TypeId"];
	        $Forshort=$myRow["Forshort"];
	        $StuffCname=$myRow["StuffCname"];//配件名称
	        $OrderQty=$myRow["OrderQty"];    //订单数量
	        $FactualQty=$myRow["FactualQty"];//需求数量
	        $AddQty=$myRow["AddQty"];	//增购数量
	        $Price=$myRow["Price"];
	        $PreChar=$myRow["PreChar"];
	        
           $StockId=$myRow["StockId"];
           $StockQty=number_format($myRow["StockQty"]);//使用库存
           $StockRemark=$myRow["StockRemark"];//更新备注
     
         $StuffId=$myRow["StuffId"];
         $Picture=$myRow["Picture"];
         include "submodel/stuffname_color.php";
       
        $xdColor=$myRow["Mid"]>0?"#FF0000":"";
	     
	    if($TypeId=='9104'){//如果是客户退款，请款总额为订单数*价格
	        $Qty=$OrderQty;	//采购总数		
	    }
	    else{
	        $Qty=$FactualQty+$AddQty;
	    }
	    $Amount=sprintf("%.2f",$Qty*$Price);
	    $Amount=number_format($Amount,2);

      $OperateArray=getCgOperateDate($StockId,'4',$DataIn,$link_id);
      if (count($OperateArray)>0){
	        $ywOrderDTime=$OperateArray["Date"];
	        $Operator=$OperateArray["Operator"];
      }
     else{
	       $ywOrderDTime=$myRow["modified"];
	       $Operator=$myRow["BuyerId"];
            include "../../model/subprogram/staffname.php";
     }

    //$Date=date("m-d H:i",strtotime($ywOrderDTime));
	$Date=GetDateTimeOutString($ywOrderDTime,'');
	
    $opHours= geDifferDateTimeNum($ywOrderDTime,"",1);
    if ($opHours>=$timeOut[0]) {$OverNums++;$DateColor="#FF0000";} else $DateColor="";
     
     
     $FactualQty=number_format($FactualQty);
     $AddQty=number_format($AddQty);
     $Qty=number_format($Qty);
     $Price=number_format($Price,3);
     
     $POrderId=$myRow["POrderId"];
     
     $listArray=array();
     $listArray[]=array("Cols"=>"1","Name"=>"采  购  ID:","Text"=>"$StockId","onTap"=>"1","ServerId"=>"$ServerId","Tag"=>"StuffDetail","Args"=>"$StockId");
     if (strlen($POrderId)==12){
	       $checkOrder=mysql_fetch_array(mysql_query("SELECT S.OrderPO,P.cName,P.TestStandard 
										        FROM  $DataIn.yw1_ordersheet S 
										        LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
										        WHERE S.POrderId='$POrderId' ",$link_id));
		  $cName=$checkOrder["cName"];
		  $OrderPO=$checkOrder["OrderPO"];
		  $TestStandard=$checkOrder["TestStandard"];	
		  include "order/order_TestStandard.php";	
		  $listArray[]=array("Cols"=>"1","Name"=>"产品名称:","Text"=>"$cName","Color"=>"$TestStandardColor");
          $listArray[]=array("Cols"=>"1","Name"=>"PO:","Text"=>"$OrderPO");					        
     } 
     $listArray[]=array("Cols"=>"1","Name"=>"使用库存:","Text"=>"$StockQty");
     $listArray[]=array("Cols"=>"3","Name"=>"需购:","Text"=>"$FactualQty","Text2"=>"增购: $AddQty","Text3"=>"实购: $Qty");
     
     $dataArray[]=array(
	                     "Id"=>"$Id",
	                     "onTap"=>array("Value"=>"1","hidden"=>"1","Args"=>"$Id","Audit"=>"$AuditSign"),
	                     "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor"),
	                     "Col1"=>array("Text"=>"$Forshort","Color"=>"$xdColor"),
	                     "Col2"=>array("Text"=>"$Qty"),
	                     "Col3"=>array("Text"=>"$PreChar$Price"),
	                     "Col4"=>array("Text"=>"$PreChar$Amount"),
	                     "Remark"=>array("Text"=>"$StockRemark"),
	                     "Date"=>array("Text"=>"$Date","Color"=>"$DateColor"),
	                     "Operator"=>array("Text"=>"$Operator"),
	                     "List"=>array("Value"=>"0","data"=>$listArray)
                     );
 }

?>