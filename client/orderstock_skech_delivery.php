<?php
   /*
   if($ModelId==0 && $EndPlace!="" && $Adress!=""){
                $Title="出Skech".$EndPlace;
                $inRecode="INSERT INTO $DataIn.ch8_shipmodel (Id,CompanyId,Title,InvoiceModel,LabelModel,StartPlace,EndPlace,SoldFrom,FromAddress,
                FromFaxNo,SoldTo,Address,FaxNo,PISign,Date,Estate,Locks,Operator) VALUES (NULL,'$CompanyId','$Title','1','1','Ash Cloud Co.,Ltd. Shenzhen','$EndPlace',
                '','','','','$Address','','0','$Date','1','0','10039')";
                $inAction=@mysql_query($inRecode);
                $ModelId=mysql_insert_id();
              }
	  */		  
      if($ModelId>0){
          $MainRecode="INSERT INTO $DataIn.ch1_deliverymain(Id, CompanyId, ModelId,ForwaderId,ForwaderRemark,DeliveryNumber,ShipType,Remark, DeliveryDate, Estate, Locks, Operator)
		  VALUES(NULL,'$CompanyId','$ModelId','$ForwaderId','$ForwaderRemark','$DeliveryNumber','$ShipType','$Remark','$DeliveryDate','2','0','$Operator')";
		 $MainAction=@mysql_query($MainRecode);
		 $Pid=mysql_insert_id();
         if ($sLen>0 && $Pid>0){
               for($i=0;$i<$sLen;$i++){
                               $tempArray=explode("^^",$ArrId[$i]);
                               $ProductId=$tempArray[0];
                               $SumQty=$tempArray[1];
                               $ShipResult=mysql_query("SELECT Mid,Date,POrderId,(Qty-DeliveryQty) AS unQty,Price FROM (
                                 SELECT M.Date,S.Mid,S.POrderId,S.Qty,IFNULL(D.DeliveryQty,0) AS  DeliveryQty,S.Price
                                 FROM $DataIn.ch1_shipsheet S 
                                 LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
                                 LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=M.Id
                                LEFT JOIN ( 
                                           SELECT POrderId,SUM(DeliveryQty) AS DeliveryQty  FROM $DataIn.ch1_deliverysheet  WHERE 1 GROUP BY  POrderId
                                        ) D ON D.POrderId=S.POrderId
                                   WHERE 1 AND O.Id IS NOT NULL  AND S.ProductId='$ProductId' AND M.CompanyId=$CompanyId 
                                 ) A  WHERE 1 AND (Qty-DeliveryQty)>0 ORDER BY Date ",$link_id);
                              while($ShipRow=mysql_fetch_array($ShipResult)){
                                          $ShipId=$ShipRow["Mid"];
                                          $POrderId=$ShipRow["POrderId"];
                                          $unQty=$ShipRow["unQty"];
                                          $Price=$ShipRow["Price"];
                                         if($SumQty>=$unQty){//全部发货
							                      	$SumQty-=$unQty;
		                                            $addRecodes="INSERT INTO $DataIn.ch1_deliverysheet (Id,Mid,ShipId,POrderId,DeliveryQty,Price,Type,Estate,Locks) VALUES (NULL,'$Pid','$ShipId','$POrderId','$unQty','$Price','1','1','0')";   
								                    $addAction=@mysql_query($addRecodes);
                                                    $Log.="the Qty saved....<br>";
                                             }
                                       else{//部分发货
		                                            $addRecodes="INSERT INTO $DataIn.ch1_deliverysheet (Id,Mid,ShipId,POrderId,DeliveryQty,Price,Type,Estate,Locks) VALUES (NULL,'$Pid','$ShipId','$POrderId','$SumQty','$Price','1','1','0')";   
								                    $addAction=@mysql_query($addRecodes);
                                                    $Log.="the Qty saved....<br>";
                                                     break;
                                                }
                                }//   while($ShipRow=mysql_fetch_array($ShipResult));
                      }//     for($i=0;$i<$sLen;$i++)
              }//if ($sLen>0 && $Mid>0)
			  $Id=$Pid;
			  include "../admin/billtopdf/ch_shipout_tobill.php";
  }
?>