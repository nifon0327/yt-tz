<?php   
//订单数量增加电信---yang 20120801
/*
			方法1
			1、需求单未下单
			计算:
				读取可用可用库存，如果有则使用，没有则增加采购数量
			2、需求单已下单：
				读取可用可用库存，如果有则使用，没有则分已结付和未结付处理
				未结付：追加采购数量
				已结付：新增需求单
			方法2：
			对新增的部分添加新的需求单，原单不变
$DataIn.cg1_stocksheet
$DataIn.ck9_stocksheet
已更新 2010.12.08
*/
$StockRead=mysql_query("SELECT * FROM $DataIn.cg1_stocksheet WHERE POrderId='$POrderId'",$link_id);
if($StockRow = mysql_fetch_array($StockRead)){
	do{
		$Sid=$StockRow["Id"];
		$sMid=$StockRow["Mid"];//是否已下采购单
		$StuffId=$StockRow["StuffId"];
		$StockId=$StockRow["StockId"];
		$AddPrice=$StockRow["Price"];
		$AddCompanyId=$StockRow["CompanyId"];
		$AddBuyerId=$StockRow["BuyerId"];
					
		$sOldOrderQty=$StockRow["OrderQty"];		//原订单需求数
		$sOldStockQty=$StockRow["StockQty"];		//原使用库存数
		$sOldAddQty=$StockRow["AddQty"];			//原增购数量
		$sOldFactualQty=$StockRow["FactualQty"];	//原需购数量
		$sTotalQTY=$sOldFactualQty+$sOldAddQty;		//原采购总数
		
		//$newOrderQty=intval(($sOldOrderQty*$Qty)/$OldQty);//需增加的配件数量
		
		$PandsResult =mysql_fetch_array(mysql_query("SELECT P.Relation  FROM $DataIn.pands P
				WHERE P.ProductId='$ProductId' AND P.StuffId='$StuffId' ",$link_id));
		if ($PandsResult["Relation"]==""){
			     $newOrderQty=intval(($sOldOrderQty*$Qty)/$OldQty);//需增加的配件数量
		}
		else{
				$Relation=explode("/",$PandsResult["Relation"]);
				if ($Relation[1]!=""){
				          $newOrderQty=ceil($Qty*$Relation[0]/$Relation[1]);
				  }
			      else{
				          $newOrderQty=ceil($Qty*$Relation[0]);
				    }
		}
		//读取库存
		$checkStock=mysql_fetch_array(mysql_query("SELECT oStockQty,mStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' LIMIT 1",$link_id));
		$oldoStockQty=$checkStock["oStockQty"];		//现有可用库存数量\
		$mStockQty=$checkStock["mStockQty"];
		$canUseQty=$oldoStockQty-$mStockQty*1;//可以使用的库存量=可用库存-最低库存下限
		$canUseQty=$canUseQty<0?0:$canUseQty;
		$SUMoStockQty=$canUseQty+$sOldStockQty;//全部可用的可用库存
		if($sMid==0){//未下采购单:
			if($SUMoStockQty<$newOrderQty){//可用库存不足
				$newFactualQty=$newOrderQty-$SUMoStockQty;	//新的需购数量
				$newStockQty=$SUMoStockQty;					//新的使用库存数
				$newAddQty=$sOldAddQty;						//新的增购数量（不变）
				$newoStockQty=0;							//新的库存数量
				$newEstate=1;
				}
			else{							//可用库存充足:需分有增购和没有增购两种情况
				if($sOldAddQty==0){//没有增购的情况
					$newFactualQty=0;						
					$newStockQty=$newOrderQty;					//新的使用库存数
					$newAddQty=$sOldAddQty;						//新的增购数量（不变）
					$newoStockQty=$SUMoStockQty-$newOrderQty;	//新的库存数量
					$newEstate=$newEstate<2?$newEstate:2;
					}
				else{//有增购的情况，保持采购数量不变的情况下，增加使用库存的数量
					$newFactualQty=$sOldFactualQty;				//新的需购数量（不变）
					$newStockQty=$newOrderQty-$newFactualQty;	//新的使用库存数
					$newAddQty=$sOldAddQty;						//新的增购数量（不变）
					$newoStockQty=$SUMoStockQty-$newStockQty;	//新的库存数量		
					$newEstate=1;
					}
				}
			//更新需求单
			$StockUpSQL= "UPDATE $DataIn.cg1_stocksheet SET OrderQty='$newOrderQty',FactualQty='$newFactualQty',StockQty='$newStockQty',AddQty='$newAddQty' WHERE Id='$Sid'";
			$StockUp = mysql_query($StockUpSQL);
			if($StockUp){
				$Log.="&nbsp;&nbsp;第 $x 个配件需求单 $StockId  更新成功!<br>";

                //母配件更新子配件的需求数量
                $UpdateComboxSql = "UPDATE   $DataIn.cg1_stuffcombox  M   
                LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId =M.mStockId
                SET  M.OrderQty=M.Relation*($newFactualQty+$newAddQty)
                WHERE  G.StuffId=$StuffId AND G.StockId=$StockId";
               $UpdateComboxResult = @mysql_query($UpdateComboxSql);
				//实物回库
				 $newoStockQty=$newoStockQty+$mStockQty;
				$upStock=@mysql_query("UPDATE $DataIn.ck9_stocksheet SET oStockQty='$newoStockQty' WHERE StuffId='$StuffId'");//2012-10-24更新
				if($upStock){
					$Log.="&nbsp;&nbsp;配件 $StuffId 的可用库存更新成功<br>";
					}
				else{
					$Log.="<div class=redB>&nbsp;&nbsp;配件 $StuffId 的可用库存更新失败</div><br>";
					$OperationResult="N";
					}
				}
			else{
				$Log.="<div class=redB>&nbsp;&nbsp;第 $x 个配件需求单 $StockId 更新失败!</div><br>";
				$OperationResult="N";
				}
			}
		else{//已下单		
						
			//A检查结付状态，如果没有结付，则数据合并处理（更新），否则新增需求单
			//或B当做新单处理，新增需求单
			$AddOrderQty=$newOrderQty-$sOldOrderQty;
			$AddStockQty=0;
			$AddFactualQty=0;
			$AddAddQty=0;
			//******************************
			if($oldoStockQty==0){
				$AddFactualQty=$AddOrderQty;						//没有可用库存,全数采购
				$newoStockQty=0;
				$newEstate=1;
				}
			else{
				if($AddOrderQty>$oldoStockQty){					//有部分可用库存
					$AddStockQty=$oldoStockQty;					//使用库存数=可用的可用库存数
					$AddFactualQty=$AddOrderQty-$oldoStockQty;	//实际需求=原需求数-可用的可用库存
					$newoStockQty=0;
					$newEstate=1;
					}
				else{//有足够库存
					$AddFactualQty=0;								//无需采购
					$AddStockQty=$AddOrderQty;						//全数使用库存
					$newoStockQty=$oldoStockQty-$AddOrderQty;		//新的可用库存
					$newEstate=$newEstate<2?$newEstate:2;
					}
				}
			//需求单号计算
			$checkMaxId=mysql_fetch_array(mysql_query("SELECT MAX(StockId)+1 AS maxId FROM $DataIn.cg1_stocksheet WHERE POrderId='$POrderId'",$link_id));
			$newStockId=$checkMaxId["maxId"];
			//配件需求单入库
			$IN_recode3="INSERT INTO $DataIn.cg1_stocksheet 
				(Id,Mid,StockId,POrderId,StuffId,Price,OrderQty,StockQty,AddQty,FactualQty,CompanyId,BuyerId,DeliveryDate,StockRemark,AddRemark,Estate,Locks)
				 VALUES (NULL,'0','$newStockId','$POrderId','$StuffId','$AddPrice','$AddOrderQty','$AddStockQty','0','$AddFactualQty','$AddCompanyId','$AddBuyerId','0000-00-00','','','1','1')";
					
			$res3=@mysql_query($IN_recode3);
			if($res3){								
				$Log.="&nbsp;&nbsp; 新加第 $x 个配件( $StuffId )的需求单 $newStockId 成功；";
				if($oldoStockQty>0){
				    $newoStockQty=$newoStockQty+$mStockQty;
					$Stockpile_SQL= "UPDATE $DataIn.ck9_stocksheet 
					SET oStockQty='$newoStockQty' WHERE StuffId='$StuffId'";
					$Stockpile_Result = mysql_query($Stockpile_SQL);
					if($Stockpile_Result){
						$Log.="并且可用库存已做更新( $oldoStockQty 更新为 $newoStockQty).<br>";
						}
					else{
						$Log.="<div class=redB>但可用库存更新失败.</div><br>";
						$OperationResult="N";
						}
					}
				else{
					$Log.="&nbsp;&nbsp; 可用库存已为0无需更新.<br>";
					}
				}
			else{
				$Log.="<div class=redB>&nbsp;&nbsp; 新加的第 $x 个配件( $StuffId )的需求单 $newStockId 失败.</div><br>";
				$OperationResult="N";
				}
			//******************************
			}
		$x++;
		}while ($StockRow = mysql_fetch_array($StockRead));
	}
//更新生产状态：生产中或生产完成状态改为生产中
$mainSql = "UPDATE $DataIn.yw1_ordersheet SET scFrom='2' WHERE POrderId='$POrderId' AND scFrom!=1";
$mainResult = mysql_query($mainSql);
?>