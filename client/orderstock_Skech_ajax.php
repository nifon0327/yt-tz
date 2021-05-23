<?php   
include "../model/modelhead.php";
$fromWebPage="orderstock_skech";
$nowWebPage="orderstock_skech_ajax";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="提货单";		//需处理
$upDataSheet="delivery_sheet";	//需处理
$Log_Funtion="生成";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
$ArrId=explode("|",$passData);
$sLen=count($ArrId); 	  
$Type=$ActionId;
$CompanyId=$myCompanyId;

switch($Type){
      case "2"://Reserve
       $ModelId=$ModelId==""?0:$ModelId;
       $mainInSql="INSERT INTO $DataIn.skech_deliverymain (Id,CompanyId,ModelId,ForwaderId,ForwaderRemark,DeliveryNumber,EndPlace,Adress,ShipType,Remark,DeliveryDate,Type,Estate,Locks,Operator) 
       VALUES (NULL,'$CompanyId','$ModelId','$ForwaderId','$ForwaderRemark','$DeliveryNumber','$newEndPlace','$newAddress','$ShipType','$Remark','$DeliveryDate','$Type','1','0','$Operator')";
	   //echo "$mainInSql <br>";	   
       $mainInAction=@mysql_query($mainInSql);
       $Mid=mysql_insert_id();
       if ($sLen>0 && $Mid>0){
               for($i=0;$i<$sLen;$i++){
                               $tempArray=explode("^^",$ArrId[$i]);
                               $ProductId=$tempArray[0];
                               $SumQty=$tempArray[1];
							   $shipSQL="SELECT Mid,Date,POrderId,(Qty-DeliveryQty) AS unQty,Price FROM (
                                 SELECT M.Date,S.Mid,S.POrderId,S.Qty,IFNULL(D.DeliveryQty,0) AS  DeliveryQty,S.Price
                                 FROM $DataIn.ch1_shipsheet S 
                                 LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
                                 LEFT JOIN $DataIn.ch1_shipout O ON O.ShipId=M.Id
                                LEFT JOIN ( 
                                           SELECT POrderId,SUM(DeliveryQty) AS DeliveryQty  FROM $DataIn.ch1_deliverysheet  WHERE 1 GROUP BY  POrderId
                                        ) D ON D.POrderId=S.POrderId
                                   WHERE 1 AND O.Id IS NOT NULL  AND S.ProductId='$ProductId' AND M.CompanyId=$CompanyId 
                                 ) A  WHERE 1 AND (Qty-DeliveryQty)>0 ORDER BY Date ";
                               $ShipResult=mysql_query($shipSQL,$link_id);
							  //echo "$shipSQL <br>";
                              while($ShipRow=mysql_fetch_array($ShipResult)){
                                          $ShipId=$ShipRow["Mid"];
                                          $POrderId=$ShipRow["POrderId"];
                                          $unQty=$ShipRow["unQty"];
                                          $Price=$ShipRow["Price"];
                                         if($SumQty>=$unQty){//全部发货
							                      	$SumQty-=$unQty;
		                                            //$addRecodes="INSERT INTO $DataIn.skech_deliverysheet (Id,Mid,ShipId,POrderId,Qty,Price,Type,Estate,Locks) VALUES (NULL,'$Mid','$ShipId','$POrderId','$unQty','$Price','2','1','0')";  
													//echo "1.INSERT INTO $DataIn.skech_deliverysheet (Id,Mid,ShipId,POrderId,Qty,Price,Type,Estate,Locks) VALUES (NULL,'$Mid','$ShipId','$POrderId','$unQty','$Price','2','1','0')";
													$addRecodes="INSERT INTO $DataIn.skech_deliverysheet (Id,Mid,ShipId,POrderId,Qty,Price,Type,Estate,Locks) VALUES (NULL,'$Mid','$ShipId','$POrderId','$unQty','$Price','2','1','0')";  
								                    $addAction=@mysql_query($addRecodes);
                                                    $Log.="the Qty saved....<br>";
                                             }
                                       else{//部分发货
		                                            //$addRecodes="INSERT INTO $DataIn.skech_deliverysheet (Id,Mid,ShipId,POrderId,ProductId,Qty,Price,Type,Estate,Locks) VALUES (NULL,'$Mid','$ShipId','$POrderId','$SumQty','$Price','2','1','0')";   
													//echo "2.INSERT INTO $DataIn.skech_deliverysheet (Id,Mid,ShipId,POrderId,ProductId,Qty,Price,Type,Estate,Locks) VALUES (NULL,'$Mid','$ShipId','$POrderId','$SumQty','$Price','2','1','0')";
													$addRecodes="INSERT INTO $DataIn.skech_deliverysheet (Id,Mid,ShipId,POrderId,Qty,Price,Type,Estate,Locks) VALUES (NULL,'$Mid','$ShipId','$POrderId','$SumQty','$Price','2','1','0')";   
								                    $addAction=@mysql_query($addRecodes);
                                                    $Log.="the Qty saved....<br>";
                                                     break;
                                                }
                                }//   while($ShipRow=mysql_fetch_array($ShipResult));
                  }//     for($i=0;$i<$sLen;$i++)
       }//if ($sLen>0 && $Mid>0)
     break;
      case "1"://Purchase
         include "orderstock_skech_delivery.php";
      break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>