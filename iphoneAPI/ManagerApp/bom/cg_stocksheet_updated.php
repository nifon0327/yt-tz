<?
 $Log_Item="待采购"; 
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
  $Log="";
  
switch($ActionId)
    {
    case "ADD":// 138931|2029|10161|123.00|0.900|Test
        $DateTemp=date("Ymd");
        $Bill_Temp=mysql_query("SELECT MAX(StockId) AS maxID FROM $DataIn.cg1_stocksheet WHERE POrderId='' and StockId LIKE '$DateTemp%'",$link_id); 
		$StockId =mysql_result($Bill_Temp,0,"maxID");
		if($StockId){
			$newStockId=$StockId+1;}
		else{
			$newStockId=$DateTemp."900001";
		}
			$newStuffId=$info[0];			//配件ID
			$CompanyId=$info[1];			//供应商
			$BuyerId=$info[2];         //采购
			$newFactualQty=round($info[3]);		//采购数量
			$newPrice=$info[4];		//采购价格
			$newAddRemark=$info[5];
			//计算特采单号
			if($newStuffId!=""){
					$addRecodes="INSERT INTO $DataIn.cg1_stocksheet (Id,Mid,StockId,POrderId,StuffId,Price,OrderQty,StockQty,AddQty,FactualQty,CompanyId,BuyerId,DeliveryDate,StockRemark,AddRemark,Estate,Locks) VALUES (NULL,'0','$newStockId','','$newStuffId','$newPrice','0','0','0','$newFactualQty','$CompanyId','$BuyerId','0000-00-00','','$newAddRemark','1','1')";
					$addResult = mysql_query($addRecodes);
					if($addResult && mysql_affected_rows()>0){
					       $OperationResult="Y";
					       $Log="新特采单( $newStockId )添加成功!<br>";
					       $upSql="UPDATE $DataIn.ck9_stocksheet SET oStockQty=oStockQty+$newFactualQty WHERE StuffId='$newStuffId'";
					       $upResult = mysql_query($upSql);
					       if($upResult && mysql_affected_rows()>0){
											$Log.="配件 $newStuffId 的可用库存(+$newFactualQty)更新成功. <br>";
											}
										else{
											$Log.="<div class='redB'>配件 $newStuffId 的可用库存(+$newoStockQty)更新失败</div><br>";
							}
					}
					else{
						 $Log.="<div class=redB>新特采单( $newStockId )添加失败! $addRecodes </div><br>";
					}
			}

        break;
    case "toMain":
             $Log_Funtion="生成采购单";
              $Ids=$info[0];$CompanyId="";
               $OperationResult="Y"; 
              ob_start();
              include "../../public/cg_cgdsheet_tomain_sub.php";
              require(dirname(__FILE__)."/../deskpath.php");
              ob_end_clean();
              break;
    case "RESET"://重置
             $Log_Funtion="重置采购单";
              $Id=$info[0];
              $OperationResult="Y"; 
              ob_start();
              include "../../public/cg_cgdsheet_reset.php";
              require(dirname(__FILE__)."/../deskpath.php");
              ob_end_clean();
              $Log="";
              break;
    case "UPDATE":
              $Log_Funtion="更新采单";
              $Id=$info[0];$newCompanyId=$info[1];$newBuyerId=$info[2];$newAddQty=$info[3];$newPrice=$info[4];$AddRemark=$info[5];
              //提取可用库存和原单数据，重新计算
		$checkSql=mysql_query("SELECT S.StockId,S.POrderId,S.StuffId,S.AddQty,S.FactualQty,S.Price,K.oStockQty 
				FROM $DataIn.cg1_stocksheet  S
				LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
				WHERE S.Id='$Id' LIMIT 1",$link_id);
		if($checkRow=mysql_fetch_array($checkSql)){
				$FactualQty=$checkRow["FactualQty"];
				$AddQty=$checkRow["AddQty"];
				$Price=$checkRow["Price"];
				$oStockQty=$checkRow["oStockQty"];
				$POrderId=$checkRow["POrderId"];
				$StuffId=$checkRow["StuffId"];
				$StockId=$checkRow["StockId"];
				
				if($POrderId==""){
				     $newFactualQty=$newAddQty;
				     if($FactualQty-$newFactualQty<=$oStockQty){//原采购数量-新的需购数量<=可用库存数量
				          $newoStockQty=$oStockQty-($FactualQty-$newFactualQty);	//新的可用库存=原可用库存-变化的数量
					      if($newFactualQty==0){//删除特采单
					            $delSql = "DELETE FROM $DataIn.cg1_stocksheet  WHERE Id='$Id' LIMIT 1"; 
						       $delRresult = mysql_query($delSql);
						       if($delRresult && mysql_affected_rows()>0){
										 $Log.="特采单 $StockId 重置后无需采购，资料删除成功<br>";
										 $OperationResult="Y";	
										 $upSql="UPDATE $DataIn.ck9_stocksheet K SET K.oStockQty='$newoStockQty' WHERE K.StuffId='$StuffId'";
										$upResult = mysql_query($upSql);
										if($upResult && mysql_affected_rows()>0){
											$Log.="配件 $StuffId 的可用库存($oStockQty->$newoStockQty)更新成功. <br>";
											}
										else{
											$Log.="<div class='redB'>配件 $StuffId 的可用库存($oStockQty->$newoStockQty)更新失败</div><br>";
										}
								}
								else{
										$Log.="<div class='redB'>特采单 $StockId 重置后无需采购，资料删除失败 $delSql </div><br>";
							    }
					      }//仍需采购
					      else{
					           $SetStr=$newCompanyId==""?"":"  S.CompanyId='$newCompanyId',";
					           $SetStr.=$newBuyerId==""?"":" S.BuyerId='$newBuyerId',";
					           $SetStr.=$newAddQty==""?"":" S.FactualQty='$newAddQty',";
					           $SetStr.=$newPrice==""?"":" S.Price='$newPrice',";
					           $SetStr.=" S.AddRemark='$AddRemark',S.Estate='1',S.Locks='1'  ";
					           $upSql="UPDATE $DataIn.cg1_stocksheet S SET $SetStr WHERE S.Id='$Id' ";
					           $upResult = mysql_query($upSql);
								if($upResult && mysql_affected_rows()>0){
									$Log.="需求单 $StockId 的资料更新成功 $upSql <br>";	
									
									//添加操作修改日志
             if($DataIn=="ac"){
				   $insertSql="INSERT INTO $DataIn.cg1_stocksheet_log SELECT NULL,StockId,'3',Estate,Locks,'$DateTime','$Operator','0','$Operator','$DateTime','$Operator','$DateTime'
                          FROM $DataIn.cg1_stocksheet WHERE Id='$Id'";
                  }else{
				   $insertSql="INSERT INTO $DataIn.cg1_stocksheet_log SELECT NULL,StockId,'3',Estate,Locks,'$DateTime','$Operator' FROM $DataIn.cg1_stocksheet WHERE Id='$Id'";
                }
									 $insertResult= mysql_query($insertSql);
									$OperationResult="Y";					
									}
								else{
									$Log.="需求单 $StockId 的资料更新失败 $upSql<br>";
									}
				        }
				     }
					else{//数量不满足条件
						 $Log.="<div class='redB'>可用库存不足，需求单 $StockId 的资料更新失败! </div>";
					}
				}
				else{//正常单
						$newoStockQty=$oStockQty-($AddQty-$newAddQty);
						if($AddQty-$newAddQty<=$oStockQty){	//数量满足条件
						        if($POrderId=="" && $newAddQty+$FactualQty<=0){//特采单重置
						               				        }
						        else{
							           $SetStr=$newCompanyId==""?"":"  S.CompanyId='$newCompanyId',";
							           $SetStr.=$newBuyerId==""?"":" S.BuyerId='$newBuyerId',";
							           $SetStr.=$newAddQty==""?"":" S.AddQty='$newAddQty',";
							           $SetStr.=$newPrice==""?"":" S.Price='$newPrice',";
							           
							           $SetStr.=" S.AddRemark='$AddRemark',S.Estate='1',S.Locks='1'  ";
							           
							           $upSql="UPDATE $DataIn.cg1_stocksheet S SET $SetStr WHERE S.Id='$Id'  ";
							           $upResult = mysql_query($upSql);
										if($upResult && mysql_affected_rows()>0){
											$Log.="需求单 $StockId 的资料更新成功 $upSql <br>";	
											
											//添加操作修改日志
             if($DataIn=="ac"){
				   $insertSql="INSERT INTO $DataIn.cg1_stocksheet_log SELECT NULL,StockId,'3',Estate,Locks,'$DateTime','$Operator','0','$Operator','$DateTime','$Operator','$DateTime'
                          FROM $DataIn.cg1_stocksheet WHERE Id='$Id'";
                  }else{
				   $insertSql="INSERT INTO $DataIn.cg1_stocksheet_log SELECT NULL,StockId,'3',Estate,Locks,'$DateTime','$Operator' FROM $DataIn.cg1_stocksheet WHERE Id='$Id'";
                }
										   $insertResult= mysql_query($insertSql);

											$OperationResult="Y";					
											}
										else{
											$Log.="需求单 $StockId 的资料更新失败 $upSql<br>";
											}
						        }
						}//数量满足条件
						else{
							 $Log.="<div class='redB'>可用库存不足，需求单 $StockId 的资料更新失败! </div>";
						}
				}
		  }
              break;
              
    case "Remark":
         	//添加备注信息
         	  $StockId=$info[0];
         	  $Remark=$info[1];
			 $insertSql="INSERT INTO $DataIn.cg_remark(Id,StockId,Remark,Date,Operator) VALUE(NULL,'$StockId','$Remark','$curDate','$Operator')";
			 $insertResult= mysql_query($insertSql);
		 	 if($insertResult && mysql_affected_rows()>0){
		 	      $Log="需求单 $StockId  添加备注信息成功<br>";	
		 	       $OperationResult="Y";
			 }
			 else{
				  $Log="<div class='redB'>需求单 $StockId  添加备注信息失败 $insertSql</div>";	
			 }
			
       break;
    }
   
if($OperationResult=="Y")
 {
      $Log.="<div class=greenB>ID:$Id-$Log_Item$Log_Funtion 成功!</div><br>";
      $infoSTR=$Log_Item . "成功";
 } 
else{
     $Log.="<div class=redB>ID:$Id-$Log_Item $Log_Funtion 失败! </div></br>"; 
      $infoSTR=$Log_Item . "失败";
 }
 
 $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
                 $IN_res=@mysql_query($IN_recode);
                 $jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR",'log'=>$Log);
?>