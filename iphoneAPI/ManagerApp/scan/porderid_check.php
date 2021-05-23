<?php
//订单资料查询
 $mySql="SELECT  M.CompanyId,M.OrderDate,M.Operator,M.ClientOrder,S.POrderId,S.OrderPO,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.TestStandard,P.bjRemark,P.Weight,U.Name AS Unit,C.Forshort,PI.Leadtime,PI.PI
                        FROM $DataIn.yw1_ordermain M
                        LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
                        LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
                        LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
                        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
                        WHERE  S.PorderId='$POrderId'  LIMIT 1";
//echo $mySql;
 $myResult = mysql_query($mySql);
if($myRow = mysql_fetch_array($myResult))
   {
        $ProductId=$myRow["ProductId"];
        $POrderId=$myRow["POrderId"];
        $eCode=$myRow["eCode"];
        $cName=$myRow["cName"];
        $OrderPO=$myRow["OrderPO"];
         $ClientOrder=$myRow["ClientOrder"];
        if($ClientOrder!=""){
              $OrderFile="download/clientorder/" . $ClientOrder;
               $ClientOrder=1;
        }
        else{
            $ClientOrder=0;
            $OrderFile="";
        }

        $Price=$myRow["Price"];
        $Qty=$myRow["Qty"];
        $Amount=sprintf("%.2f",$Qty*$Price);
        
        $BoxSUM=0;
        //出货信息
        $shipResult= mysql_query("SELECT S.Mid,M.InvoiceNO,M.InvoiceFile FROM $DataIn.ch1_shipsheet S 
              LEFT JOIN $DataIn.ch1_shipmain M   ON M.Id=S.Mid 
		       WHERE S.POrderId='$POrderId'",$link_id);
       if ($shipRows = mysql_fetch_array($shipResult)){
				$Mid=$shipRows["Mid"];
				$InvoiceNO=$shipRows["InvoiceNO"];
				$InvoiceFile=$shipRows["InvoiceFile"];
				if ($InvoiceFile==1){
					$InvoiceFilePath="download/invoice/$InvoiceNO.pdf";
				}
				$BoxSUM=0;
				$plResult = mysql_query("SELECT L.POrderId,L.BoxRow,L.BoxQty FROM $DataIn.ch2_packinglist L 
		 WHERE L.Mid='$Mid' ORDER BY L.Id",$link_id);	
			   while($plRows = mysql_fetch_array($plResult)){
					$BoxPcs=$plRows["BoxPcs"];
					$BoxQty=$plRows["BoxQty"];
					$BoxPOrderId=$plRows["POrderId"];
					if ($POrderId==$BoxPOrderId){
						$Small=$BoxSUM+1;//起始箱号
						$Most=$BoxSUM+$BoxQty;//终止箱号
					}
					else{
						$BoxSUM+=$BoxQty;
					}
			    }
	   }
	    
	     //已出货数量
		$checkShipQty= mysql_query("SELECT SUM(IF(Estate=0,Qty,0)) AS ShipQty,SUM(IF(Estate>0,Qty,0)) AS Qty FROM $DataIn.yw1_ordersheet   
		       WHERE ProductId='$ProductId' AND OrderPO='$OrderPO' ",$link_id);
		$ShipQty=mysql_result($checkShipQty,0,"ShipQty");
		$noShipQty=mysql_result($checkShipQty,0,"Qty");
		//$noShipQty=$Qty-$ShipQty;
		
        //$Unit=$myRow["Unit"]=="PCS"?"pcs":$myRow["Unit"];
        $Unit="pcs";
        $TestStandard=$myRow["TestStandard"];
     
        $CompanyId=$myRow["CompanyId"];
        include "../subprogram/currency_read.php";//$Rate、$PreChar
        
        $Weight=$myRow["Weight"];
        $TestStandard=$myRow["TestStandard"];
        if ($TestStandard*1>0){
            $ImagePath="download/teststandard/T$ProductId.jpg";
        }
        else{
           $ImagePath=""; 
        }
        
        $Qty=number_format($Qty);
        $Price=number_format($Price,2);
        $Amount="$PreChar" . "$Amount";
        if ($myRow["Estate"]>0){
	        $productId=$ProductId;
             include "../../model/subprogram/weightCalculate.php";
             $Qty=number_format($boxPcs);
             $Amount="";
               $dataArray= array(
              array("1","$eCode"),
              array("2","PO","$OrderPO","$ClientOrder","$OrderFile"),
              array("3","$PreChar$Price","$Qty$Unit","$Amount"),
              array("4","Delivered","$ShipQty","$ShipType"),
              array("5","Open order","$noShipQty")
         );
        }
       else{
        $dataArray= array(
              array("1","$eCode"),
              array("2","PO","$OrderPO","$ClientOrder","$OrderFile"),
              array("3","$PreChar$Price","$Qty$Unit","$Amount"),
              array("4","Delivered","$ShipQty","$ShipType"),
              array("5","Open order","$noShipQty"),
              array("6","CTN","$Small-$Most","/$BoxSUM"),
              array("7","Invoice / PL / AW / BL","$InvoiceNO","$InvoiceFile","$InvoiceFilePath")
         ); 
        } 
         $jsonArray= array("$POrderId","$TestStandard","$ImagePath", $dataArray);
 }
?>