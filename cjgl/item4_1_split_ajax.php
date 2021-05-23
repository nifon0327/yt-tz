<?php   
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取产品资料
include "cj_chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$llSign=1;
if($llSign==1){//有备料的订单拆分 先不做拆分处理，需业务主管审核方可拆单。不审核做不处理状态
		     $OrderSplit="INSERT $DataIn.yw10_ordersplit(Id, POrderId, Qty, Qty1, Qty2,Remark,Estate, Date, Operator)VALUES(NULL,'$POrderId','$Qty','$Qty1','$Qty2','$SpiltRemark','0','$Date','$Operator')";
			 $OrderResult=mysql_query($OrderSplit);
			 if($OrderResult){
			       $Log.="订单流水号为 $POrderId 添加表中成功,等待业务主管审核才能拆分!";
				   echo "Y";
			         }
		        else{
				    $Log.="<div class='redB'>订单流水号为 $POrderId 添加表中失败,拆分失败! $OrderSplit </div>";
					$OperationResult="N";
				    }
					//echo $OrderSplit;
		      }
else{
	    //订单列表页面本身已做即出状态Estate=4和已出状态限制Estate=0，为防止操作状态过程中状态发生变化，1、锁定订单明细表 2、或再加入限制，原则上需要锁定表的操作，这里暂使用2
		$Log_Funtion="拆分订单";
		$sheetResult = mysql_query("SELECT OrderNumber,POrderId,Qty,Price,scFrom,Estate FROM $DataIn.yw1_ordersheet WHERE Id='$Id' AND Estate>0 AND Estate<4 ORDER BY Id DESC",$link_id);
		if($sheetRow = mysql_fetch_array($sheetResult)){
			///////读取原订单资料///////////
			$OrderNumber=$sheetRow["OrderNumber"];	//原订单主单编号
			$POrderId=$sheetRow["POrderId"];		//原订单流水号
			$OrderNumTemp=substr($POrderId,0,10);	//取订单的主单编号，为防止主单有拆分的情况，所以不直接取OrderNumber
			$Qty=$sheetRow["Qty"];					//原订单数量
			$Price=$sheetRow["Price"];				//原订单售价
			$scFrom=$sheetRow["scFrom"];			//原订单生产状态
			$Estate=$sheetRow["Estate"];			//原订单出货状态

			//第一步:原订单更新:生产状态不改变，由第四步自动判断，出货状态一律改为未出状态1，如果已经登记完毕，则需重新审核
			$sql1 = "UPDATE $DataIn.yw1_ordersheet SET Estate='1',Qty='$Qty1',PackRemark=concat(PackRemark,'(拆分的订单数量:$Qty1)') WHERE Id='$Id' AND POrderId='$POrderId'";
			$result1 = mysql_query($sql1);
			if($result1){
				$Log.="1、子单1( $POrderId )已加入(主订单编号：$OrderNumber 订单数量：$Qty1)<br>";
				//第二步:计算新的POrderId号,并加入新的订单?????????部分手动改PO的会出错??????????
				/*
				$maxSql=mysql_query("select MAX(POrderId) AS maxPid FROM $DataIn.yw1_ordersheet WHERE POrderId LIKE '$OrderNumTemp%' ORDER BY POrderId DESC LIMIT 1",$link_id);
				$max_POrderId=mysql_result($maxSql,0,"maxPid");
				$newPOrderId=$max_POrderId+1;
				*/
				$OrderNum_New=$OrderNumTemp;  //刚开始时，两个值相等
				do{
					$OrderNumTemp=$OrderNum_New;
					$maxSql=mysql_query("select MAX(POrderId) AS maxPid FROM $DataIn.yw1_ordersheet WHERE POrderId LIKE '$OrderNumTemp%' ORDER BY POrderId DESC LIMIT 1",$link_id);
					$max_POrderId=mysql_result($maxSql,0,"maxPid");
					
					//当超过100时，会出问题 modify by zx 20110704 ,所以要检查，如果没有才能用，否则继续加到没有为止
					$newPOrderId=$max_POrderId+1;
					if($newPOrderId==1){  //说明新的不存在类似的PorderID,则需要重新给值
						$newPOrderId=$OrderNumTemp.'01';  
					}
					$OrderNum_New=substr($newPOrderId,0,10);  //新的前十位，
				} while($OrderNum_New!=$OrderNumTemp); //如果两者不相等，说明超过100,则需要重新				
				
				
				//生产状态与原单状态一致，出货状态初始状态为1，由第四步重新判断生产状态。订单锁定
			   if ($DataIn=='ac'){
				   $new_Recode="INSERT INTO $DataIn.yw1_ordersheet SELECT NULL,OrderNumber,OrderPO,'$newPOrderId',ProductId,'$Qty2',Price,concat(PackRemark,'(拆分的订单数量:$Qty2)'),cgRemark,sgRemark,dcRemark,'0000-00-00',ShipType,'$scFrom','1','0','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.yw1_ordersheet WHERE Id='$Id' and POrderId='$POrderId'";
				}
				else{
					$new_Recode="INSERT INTO $DataIn.yw1_ordersheet SELECT NULL,OrderNumber,OrderPO,'$newPOrderId',ProductId,'$Qty2',Price,concat(PackRemark,'(拆分的订单数量:$Qty2)'),cgRemark,sgRemark,dcRemark,'0000-00-00',ShipType,'$scFrom','1','0' FROM $DataIn.yw1_ordersheet WHERE Id='$Id' and POrderId='$POrderId'";
				}
				$new_Result = mysql_query($new_Recode);
                $new_Id=mysql_insert_id();
				if($new_Result && mysql_affected_rows()>0){
					$Log.="*******2、子单2( $newPOrderId )已加入(主订单编号：$OrderNumber 订单数量：$Qty2)<br>";
					}
				else{
					$Log.="<div class='redB'>*******2、子单2( $newPOrderId )加入(主订单编号：$OrderNumber 订单数量：$Qty2)失败.</div><br>";
					}
				
				//第三步：读取原配件需求单资料，开始处理配件需求单
				$oldStockResult = mysql_query("SELECT * FROM $DataIn.cg1_stocksheet WHERE POrderId='$POrderId' ORDER BY Id DESC",$link_id);
				$newEstate2=2;
				if($oldStockRow = mysql_fetch_array($oldStockResult)){
					$k=1;
					do{
						$Mid=$oldStockRow["Mid"];
						$StuffId=$oldStockRow["StuffId"];
						$TypeSql=mysql_query("SELECT TypeId  FROM $DataIn.stuffdata WHERE StuffId='$StuffId'",$link_id);
						$TypeId=mysql_result($TypeSql,0,"TypeId");
						$oldStockId=$oldStockRow["StockId"];
						$Price=$oldStockRow["Price"];
						$BuyerId=$oldStockRow["BuyerId"];
						$TempCompanyId=$oldStockRow["CompanyId"];					
						$oldOrderQty=$oldStockRow["OrderQty"];			//原配件订单需求数
						$oldStockQty=$oldStockRow["StockQty"];		//原使用库存数
						$oldAddQty=$oldStockRow["AddQty"];			//原增购数量
						$oldFactualQty=$oldStockRow["FactualQty"];	//原需采购数量
//子单1的数据分析
						//计算拆分单1的配件订单数=(原配件订单需求数*拆分子单1的产品订单数)/原产品订单数
						$newOrderQTY1=intval(($oldOrderQty*$Qty1)/$Qty);//取整
						$newOrderQTY2=$oldOrderQty-$newOrderQTY1;
//①	全部使用库存，直接拆分;子单1的使用库存数=子单1的订单需求数
						if($oldStockQty==$oldOrderQty){	
							$newStockQty1=$newOrderQTY1;		$newFactualQty1=0;		$newAddQty1=0;
							$newStockQty2=$newOrderQTY2;		$newFactualQty2=0;		$newAddQty2=0;
							}
						else{	
//②	部分或没有使用库存:分未下单和已下单处理
							if($Mid==0){//未下单:	方法A,采购数量分开,使用此方法，以便精准计算毛利	(方法B，仍由子单1承担采购)
								$newFactualQty1=intval(($oldFactualQty*$Qty1)/$Qty);		//取整
								$newFactualQty1=$newFactualQty1==0?1:$newFactualQty1;
								$newStockQty1=$newOrderQTY1-$newFactualQty1;
								$newAddQty1=$oldAddQty;
								$newFactualQty2=$oldFactualQty-$newFactualQty1;
								$newStockQty2=$newOrderQTY2-$newFactualQty2;
								$newAddQty2=0;
								$newEstate2=1;
								}
							else{		//已下单,则维持原单采购数量,新单使用库存（不平分是避免已入库，不方便拆分）
								if($oldFactualQty>$newOrderQTY1){	//原采购数大于子单1的订单数
									       $newStockQty1=0;	$newFactualQty1=$newOrderQTY1;	$newAddQty1=$oldAddQty+($oldFactualQty-$newFactualQty1);
									       $newStockQty2=$newOrderQTY2; $newFactualQty2=0; $newAddQty2=0;
									    }
								else{
									     $newStockQty1=$newOrderQTY1-$oldFactualQty;		$newFactualQty1=$oldFactualQty;	$newAddQty1=$oldAddQty;
									     $newStockQty2=$newOrderQTY2; $newFactualQty2=0; $newAddQty2=0;
									  }								
								}
							}
//子单1的数据分析完毕
						//子单1入库(更新)
						if($TypeId=='9104**'){//客户退款配件 不用增购。实际数量=订单数量
						$subStockSQL1= "UPDATE $DataIn.cg1_stocksheet SET OrderQty='$newOrderQTY1',FactualQty='$newOrderQTY1',StockQty='0',AddQty='0',llSign='1' WHERE StockId='$oldStockId'";
						      }
						else {
						$subStockSQL1= "UPDATE $DataIn.cg1_stocksheet SET OrderQty='$newOrderQTY1',FactualQty='$newFactualQty1',StockQty='$newStockQty1',AddQty='$newAddQty1',llSign='1' WHERE StockId='$oldStockId'";
						     }
						$subStockResult1 = mysql_query($subStockSQL1);
						if($subStockResult1){
							$Log.="3、子单1 第 $k 个配件需求单( $oldStockId )已加入.<br>";
							//子单2入库（新增）
							$newStockId2=$newPOrderId.substr($oldStockId,-2);
							if($TypeId=='9104**'){
							$IN_recode2="INSERT INTO $DataIn.cg1_stocksheet (Id,Mid,StockId,POrderId,StuffId,Price,OrderQty,StockQty,AddQty,FactualQty,CompanyId,BuyerId,DeliveryDate,StockRemark,AddRemark,Estate,rkSign,llSign,Locks) VALUES (NULL,'$Mid','$newStockId2','$newPOrderId',	'$StuffId','$Price','$newOrderQTY2','0','0','$newOrderQTY2','$TempCompanyId','$BuyerId','0000-00-00','','','0','1','1','0')";
							   }
						   else{
						      $IN_recode2="INSERT INTO $DataIn.cg1_stocksheet (Id,Mid,StockId,POrderId,StuffId,Price,OrderQty,StockQty,AddQty,FactualQty,CompanyId,BuyerId,DeliveryDate,StockRemark,AddRemark,Estate,rkSign,llSign,Locks) VALUES 					(NULL,'0','$newStockId2','$newPOrderId','$StuffId','$Price','$newOrderQTY2','$newStockQty2','$newAddQty2','$newFactualQty2','$TempCompanyId','$BuyerId','0000-00-00','','','1','1','1','0')";
						       }
							$InRes2=@mysql_query($IN_recode2);
							if($InRes2){
								$Log.="*******3、子单2 第 $k 个配件需求单( $newStockId2 )已加入.<br>";
								}
							else{
								$Log.="<div class='redB'>*******3、子单2 第 $k 个配件需求单( $newStockId2 )新增失败. $IN_recode2 </div><br>";$OperationResult="N";
								}
							}
						else{
							$Log.="<div class='redB'>3、子单1 第 $k 个配件需求单( $oldStockId )更新失败. $subStockSQL1 </div><br>";$OperationResult="N";
							}
///////////////////////////////领料单拆分（更新）///////////////////////////////////////////
			                
					     $oldllResult = mysql_query("SELECT Id,Qty FROM $DataIn.ck5_llsheet WHERE  StockId='$oldStockId' order by Qty",$link_id);
					     if($oldllRow = mysql_fetch_array($oldllResult)){
							   $oldllQty=0;$upId=1;
							 do {
							   $llId=$oldllRow["Id"];
						       $oldllQty+=$oldllRow["Qty"];
						       if ($oldllQty>$newOrderQTY1 && $upId>0){
							      $changeQty=$oldllQty-$newOrderQTY1;
								  $UpllSql="UPDATE $DataIn.ck5_llsheet SET Qty=Qty-$changeQty   WHERE Id='$llId'"; 
								  $UpllResult=mysql_query($UpllSql);
								  if($UpllResult && mysql_affected_rows()>0){
									//追加新领料记录
									if ($DataIn=='ac'){
								         $InllRecode="INSERT INTO $DataIn.ck5_llsheet SELECT NULL,Pid,Mid,'$newStockId2',StuffId,'$changeQty',Locks,Estate,'0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.ck5_llsheet WHERE Id='$llId'";
								    }
								    else{
									     $InllRecode="INSERT INTO $DataIn.ck5_llsheet SELECT NULL,Pid,Mid,'$newStockId2',StuffId,'$changeQty',Locks,Estate FROM $DataIn.ck5_llsheet WHERE Id='$llId'";
								    }
								    $InllRes=@mysql_query($InllRecode);
									if($InllRes){
								             $Log.="<div class='greenB'>子单2 第 $k 个配件领料单( $newStockId2 )已加入.</div><br>";
								            }
							        else{
								              $Log.="<div class='redB'>4、子单2 第 $k 个配件领料单( $newStockId2 )新增失败. $IN_recode2 </div><br>";
									          $OperationResult="N";
								           }
								    }//if($UpllResult && mysql_affected_rows()>0)
									 $upId=0;
						         }//if ($oldllQty>$newOrderQTY1 && $upId>0)
							    else{//原单已领料完毕，领料数据增回到新单
								if ($upId==0){
								   $UpllSql="UPDATE $DataIn.ck5_llsheet SET StockId=$newStockId2   WHERE Id='$llId'";	
								   $UpllResult=mysql_query($UpllSql);
								 }
							   }
							 }while($oldllRow = mysql_fetch_array($oldllResult));
					         }
			    ////////////////////////////////////////////////////////////////
						$k++;
						}while($oldStockRow = mysql_fetch_array($oldStockResult));
					}//end if($oldStockRow = mysql_fetch_array($oldStockResult))
					//第三步处理完毕
				//第四步：拆分生产记录:取生产记录进行处理
				$checkScTypeSql=mysql_query("SELECT TypeId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' GROUP BY TypeId ORDER BY TypeId",$link_id);
				if($checkScTypeRow=mysql_fetch_array($checkScTypeSql)){
					do{
						$scTypeId=$checkScTypeRow["TypeId"];
						//读取此分类的生产记录//原单Qty1 新子单Qty2
						$checkScRecordSql=mysql_query("SELECT Id,Qty FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId='$scTypeId' ORDER BY Id");
						if($checkScRecordRow=mysql_fetch_array($checkScRecordSql)){
							$scQtySum=$Qty1;//原单需要生产的总数
							do{
								$scRecordId=$checkScRecordRow["Id"];
								$scRecordQty=$checkScRecordRow["Qty"];
								if($scRecordQty>$scQtySum && $scQtySum>0){//如果生产登记的数量大于需要生产的数量，则要拆分
									//更新原登记记录
									$UpLlSql="UPDATE $DataIn.sc1_cjtj SET Qty=$scQtySum,Remark='随订单拆分的生产记录' WHERE Id='$scRecordId'";
									$UpLlResult=mysql_query($UpLlSql);
									if($UpLlResult && mysql_affected_rows()>0){
										//追加新的生产记录
										$NewScQty=$scRecordQty-$scQtySum;
										if ($DataIn=='ac'){
										     $InScRecode="INSERT INTO $DataIn.sc1_cjtj SELECT NULL,GroupId,TypeId,'$newPOrderId','$NewScQty','生产记录随订单拆分',Date,Estate,Locks,Leader,'0','$Operator',NOW(),'$Operator',NOW(),'$Operator' FROM $DataIn.sc1_cjtj WHERE Id='$scRecordId'";
										}
										else{
											$InScRecode="INSERT INTO $DataIn.sc1_cjtj SELECT NULL,GroupId,TypeId,'$newPOrderId','$NewScQty','生产记录随订单拆分',Date,Estate,Locks,Leader FROM $DataIn.sc1_cjtj WHERE Id='$scRecordId'";
										}
										$InScRes=@mysql_query($InScRecode);
										$scQtySum=0;
										}
									}
								else{//登记的数量少于需要生产的数量或需要生产的数量已经登记完毕
									if($scQtySum==0){//原单已经登记完毕，则生产订单号直接改为新的子单
										$UpLlSql="UPDATE $DataIn.sc1_cjtj SET POrderId='$newPOrderId',Remark='随订单拆分的生产记录' WHERE Id='$scRecordId'";
										$UpLlResult=mysql_query($UpLlSql);
										}
									else{//如果是登记的数量少于生产的数量，则不更新记录，但需登记的数量做扣除
										$scQtySum=$scQtySum-$scRecordQty;//剩余的生产数量
										}
									}
								
								}while ($checkScRecordRow=mysql_fetch_array($checkScRecordSql));
							}
							
						}while($checkScTypeRow=mysql_fetch_array($checkScTypeSql));
					//生产状态更新：1、生产状态自动判断，出货状态重置为1，即订单需要重新审核
					if($scFrom==2){//当原单生产状态为2时，需重置生产状态，因为拆分后，登记的数量已经达到要求
						$UpdateSql="Update $DataIn.yw1_ordersheet Y
							LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' GROUP BY POrderId) A ON A.POrderId=Y.POrderId
							LEFT JOIN (
								SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
								FROM $DataIn.cg1_stocksheet G 
								LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
								LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
								WHERE G.POrderId='$POrderId' AND T.mainType=3 GROUP BY G.POrderId) B ON B.POrderId=Y.POrderId 
							SET Y.scFrom=0 
							WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty";//该订单中，如果生产的数量和需求的数量一致，则生产状态为0，出货状态是不变的1，由品检审核后出货状态才改为待出2
						$UpdateResult = mysql_query($UpdateSql);
						}
						
				       //更新生产状态:当生产状态为0时，刷新状态，其它状态下不做变更
		                 $UpdateSql="Update $DataIn.yw1_ordersheet Y
			LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$newPOrderId' GROUP BY POrderId) A ON A.POrderId=Y.POrderId
			LEFT JOIN (
				SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$newPOrderId' AND T.mainType=3 GROUP BY G.POrderId) B ON B.POrderId=Y.POrderId 
			SET Y.scFrom=2,Y.Estate=1
			WHERE Y.POrderId='$newPOrderId' AND IFNULL(A.Qty,0)!=B.Qty  AND Y.scFrom='0'";  
			             $UpdateResult = mysql_query($UpdateSql);
					}
				//第四步：生产记录处理完毕$new_Id
				
                          // ----------拆单PI交期-------
               if ($DataIn=='ac'){
		               $new_PIRecode="INSERT INTO $DataIn.yw3_pisheet 
		               SELECT NULL, CompanyId,'$new_Id', PI, Leadtime, Paymentterm, Notes, OtherNotes, Terms,ShipTo,`condition`,Remark,Date, Operator,'1','1','0','$Operator',NOW(),'$Operator',NOW()  
		               FROM $DataIn.yw3_pisheet WHERE oId='$Id'";
               }
               else{
	               $new_PIRecode="INSERT INTO $DataIn.yw3_pisheet 
		               SELECT NULL, CompanyId,'$new_Id', PI, Leadtime, Paymentterm, Notes, OtherNotes, Terms,ShipTo,`condition`,Remark,Date, Operator 
		               FROM $DataIn.yw3_pisheet WHERE oId='$Id'";
               }
			  $new_PIResult = mysql_query($new_PIRecode); 
                          if ($new_PIResult && mysql_affected_rows()){
                              $Log.="4、新单的PI交期已自动生成<br>";
                          }
                          
                          //新增拆单记录登记
                          $tmpCheck=mysql_query("SELECT SPOrderId FROM $DataIn.yw1_ordersplit  WHERE OPOrderId='$POrderId' LIMIT 1",$link_id);
			   if($temprow = mysql_fetch_array($tmpCheck)) {
                                 $SPOrderId=$temprow["SPOrderId"];
                            }
                            else{
                                $SPOrderId=$POrderId;
                            }
                          $split_Recode="INSERT INTO $DataIn.yw1_ordersplit (Id, SPOrderId, OPOrderId, Date, Operator) VALUES (NULL,'$SPOrderId', '$newPOrderId', '$Date','$Operator')";
			  $split_Result = mysql_query($split_Recode); 
                          if ($split_Result && mysql_affected_rows()){
                              $Log.="5、拆单记录登记成功.<br>";
                          }
					echo "Y";
			   }//end  if($result1) 原单更新完毕（子单1）
		     }//end if($sheetRow = mysql_fetch_array($sheetResult))
		else{//订单状态即出或已出，不允许拆分
			$Log.="<div class='redB'>订单序号为 $Id 的订单，目前状态不允许拆分动作.</div>";
			$OperationResult="N";
			}
  }
?>