<?php
     include "../../model/stuffcombox_function.php";
     
     $Log_Item="补货审核"; 
     $curDate=date("Y-m-d");
	 $DateTime=date("Y-m-d H:i:s");
	 $OperationResult="N";
	 $Operator=$LoginNumber;
 
     $NoEchoSign=1;
      switch($ActionId){
			case "PASS":
			      header("location:http://www.ashcloud.com/appAPI/web.php/audit/audit_ckreplenish?Action=$ActionId&Id=$Id&Operator=$Operator");
			    break;
			case "BACK":
			      header("location:http://www.ashcloud.com/appAPI/web.php/audit/audit_ckreplenish?Action=$ActionId&Id=$Id&Operator=$Operator&Reasons=$ReturnReasons");
			    break;
	  } 
 /*
   switch($ActionId){
			case "PASS":
			          
			            $updateSql="UPDATE $DataIn.ck13_replenish SET Estate=1,modified='$DateTime' WHERE Id=$Id";
			            $UpdateResult = mysql_query($updateSql,$link_id);
			            if($UpdateResult)
			            {
			                    $Log="<div class=greenB>补货单($Id)审核成功!</div><br>";
				                $OperationResult="Y";
				                
			                    $checkResult=mysql_query("SELECT S.POrderId,S.StockId,S.StuffId,S.Remark,S.Qty,G.Price,G.CompanyId,G.BuyerId,K.oStockQty
			                    FROM $DataIn.ck13_replenish S 
			                    LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
			                    LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
			                    WHERE S.Estate=1 and S.Id=$Id",$link_id);
			                    if ($checkRow = mysql_fetch_array($checkResult)){
				                        $POrderId=$checkRow["POrderId"];
				                        $StockId=$checkRow["StockId"];
				                        $StuffId=$checkRow["StuffId"];
				                        $Qty=$checkRow["Qty"];
				                        $oStockQty=$checkRow["oStockQty"];
				                        
				                        $CompanyId=$checkRow["CompanyId"];
				                        $Price=$checkRow["Price"];
				                        $BuyerId=$checkRow["BuyerId"];
				                        
				                        $Remark=$checkRow["Remark"];
				                        
				                        //1.领料退料
                                 if($DataIn=="ac"){
				                        $IN_Recode="INSERT INTO $DataIn.ck5_llsheet SELECT NULL,Pid,Mid,POrderId,StockId,StuffId,'-$Qty','1','0','0','$Operator','$DateTime','$Operator','$DateTime','$DateTime','$Operator'
                                        FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' LIMIT 1";
                                     }else{
				                        $IN_Recode="INSERT INTO $DataIn.ck5_llsheet SELECT NULL,Pid,Mid,POrderId,StockId,StuffId,'-$Qty','1','0' 
                                        FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' LIMIT 1";
                                         }
				                       $IN_Result= mysql_query($IN_Recode,$link_id);
				                       $LL_Id=mysql_insert_id();
				                       $newoStockQty=$oStockQty;
				                       $upSign=0; $errorSign=0;
				                       if ($IN_Result){
					                         if ($oStockQty<$Qty){
						                          //2.生成特采单
						                          $DateTemp=date("Ymd"); 
												  $Bill_Temp=mysql_query("SELECT MAX(StockId) AS maxID FROM $DataIn.cg1_stocksheet WHERE POrderId='' and StockId LIKE '$DateTemp%'",$link_id); 
												$maxID =mysql_result($Bill_Temp,0,"maxID");
												$newStockId=$maxID==""?$DateTemp."900001":$maxID+1;
												$FactualQty=$Qty-$oStockQty;
												$newoStockQty=0;
												
												if (check_stuffbox_sub("",$StuffId,$DataIn,$link_id)){
								                   $comResult=mysql_query("SELECT S.mStuffId,S.Relation,G.Price,G.CompanyId,G.BuyerId 
														FROM $DataIn.cg1_stuffcombox S
														left join $DataIn.cg1_stocksheet G ON G.StockId=S.mStockId
														WHERE  S.Estate=1 and S.StockId='$StockId' ",$link_id);
							                       if ($comRow = mysql_fetch_array($comResult)){
							                             $mStuffId=$comRow["mStuffId"];
							                             $CompanyId=$comRow["CompanyId"];
				                                         $Price=$comRow["Price"];
				                                         $BuyerId=$comRow["BuyerId"];
				                                         $Relation=$comRow["Relation"]==""?1:$comRow["Relation"];
				                                         
				                                         $FactualQty=$FactualQty/$Relation;
				                                         
				                                         $addRecodes="INSERT INTO $DataIn.cg1_stocksheet (Id,Mid,StockId,POrderId,StuffId,Price,OrderQty,StockQty,AddQty,FactualQty,CompanyId,BuyerId,DeliveryDate,StockRemark,AddRemark,Estate,Locks) VALUES (NULL,'0','$newStockId','','$mStuffId','$Price','0','0','0','$FactualQty','$CompanyId','$BuyerId','0000-00-00','','车间补料:$Remark','1','1')";	                                  
							                       }
												}
												else{
						                          $addRecodes="INSERT INTO $DataIn.cg1_stocksheet (Id,Mid,StockId,POrderId,StuffId,Price,OrderQty,StockQty,AddQty,FactualQty,CompanyId,BuyerId,DeliveryDate,StockRemark,AddRemark,Estate,Locks) VALUES (NULL,'0','$newStockId','','$StuffId','$Price','0','0','0','$FactualQty','$CompanyId','$BuyerId','0000-00-00','','车间补料:$Remark','1','1')";	                         }
						                         $addResult= mysql_query($addRecodes,$link_id);
						                         $CG_Id=mysql_insert_id();
						                         if ($addResult){
							                           $upSign=1;
							                           $Log.="<div class=greenB>配件 $StuffId 生成新的特采单($newStockId)!</div><br>";
							                           if ($mStuffId!=""){
							                               //添加子配件bom
								                           addCg_StuffComBox_data($newStockId,$mStuffId,$DataIn,$link_id,$Operator,$Log);
							                           }
						                         }
						                         else{
							                           $errorSign++;
							                           $Log.="<div class=redB>配件 $StuffId 生成新的特采单($newStockId)失败!</div><br>";
						                         }
					                         }
					                         else{
						                            $upSign=1;
						                            $newoStockQty=$oStockQty-$Qty;
					                         }
					                         
					                       if ($upSign==1){
						                      //3.生成报废单
						                     $IN_Recode2="INSERT INTO $DataIn.ck8_bfsheet (Id,ProposerId,StuffId,Qty,Remark,Type,Bill,DealResult,Date,Estate,Locks,Operator) VALUES(NULL,'$Operator','$StuffId','$Qty','订单补料:$POrderId,原因:$Remark','7','0','','$curDate','0','0','$Operator')";
				                                     $IN_Result2= mysql_query($IN_Recode2,$link_id);
				                                     $BF_Id=mysql_insert_id();
				                                     if ($IN_Result2){
				                                            $Log.="<div class=greenB>配件 $StuffId 生成新的报废单($BF_Id)!</div><br>";
					                                      //4.更新库存表
					                                      if ($newoStockQty!=$oStockQty){
						                                         $updateSql2="UPDATE $DataIn.ck9_stocksheet SET oStockQty='$newoStockQty' WHERE StuffId='$StuffId'";
			                                                     $updateResult2 = mysql_query($updateSql2,$link_id);
					                                      }
					                                      
				                                     }else{
					                                      $errorSign++;
					                                       $Log.="<div class=greenB>配件 $StuffId 生成新的报废单失败!</div><br>";
				                                     }
					                         }
				                       }
				                       //错误处理
				                      if ($errorSign>0){
					                       if ($CG_Id>0) {
						                       $delResult=mysql_query("DELETE FROM $DataIn.cg1_stocksheet WHERE Id='$CG_Id' ",$link_id);
					                       }
					                       
					                      if ($LL_Id>0){
						                        //删除领料记录
					                          $delResult=mysql_query("DELETE FROM $DataIn.ck5_llsheet WHERE Id='$LL_Id' ",$link_id);
					                      }
					                      
					                      //还原未审核状态
					                       $updateSql2="UPDATE $DataIn.ck13_replenish SET Estate=2 WHERE Id=$Id";
			                               $UpdateResult = mysql_query($updateSql2,$link_id);
			                                
			                                $OperationResult="N";
			                                $Log.="<div class=redB>补货单($Id)审核失败! </div><br>$updateSql</br>";   
				                      }
			                    }
			                } 
			            else{
			                $Log="<div class=redB>补货单($Id)审核失败! </div><br>$updateSql</br>";   
			                }
			           
			 break;
		case "BACK":
		   $delRecode="update  $DataIn.ck13_replenish set Estate=3,ReturnReasons='$ReturnReasons' WHERE Id='$Id'";
           $delAction = mysql_query($delRecode);
            if($delAction && mysql_affected_rows()>0){
                    $Log="<div class=greenB>ID( $Id )的补料单退回(删除)成功.</div><br>";
                     $OperationResult="Y";
               }
            else{
                    $Log="<div class='redB'>ID( $Id )的补料单退回(删除)失败.</div>";
                    $OperationResult="N";
                    }
            break;    
      }
       */
?> 