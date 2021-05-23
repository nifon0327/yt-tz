<?php   
/*订单数量减少的处理:$OldQty原订单数量>$Qty新的订单数量电信---yang 20120801
*/
$StockRead=mysql_query("SELECT G.Id,G.Mid,G.StuffId,G.StockId,G.OrderQty,G.StockQty,G.AddQty,G.FactualQty,D.TypeId,T.mainType 
FROM $DataIn.cg1_stocksheet G 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
WHERE G.POrderId='$POrderId'",$link_id);
if ($StockRow = mysql_fetch_array($StockRead)){
	do{
		$Sid=$StockRow["Id"];
		$sMid=$StockRow["Mid"];					//判断是否已下采购单
		$StuffId=$StockRow["StuffId"];
		$StockId=$StockRow["StockId"];
		$sOldOrderQty=$StockRow["OrderQty"];
		$sOldStockQty=$StockRow["StockQty"];
		$sOldAddQty=$StockRow["AddQty"];
		$sOldFactualQty=$StockRow["FactualQty"];
		$mainType=$StockRow["mainType"];
		$scTypeId=$StockRow["TypeId"];			//判断配件类型，是否需要生产记录处理
		$sTotalQTY=$sOldFactualQty+$sOldAddQty;
		
		//$newOrderQty=intval(($sOldOrderQty*$Qty)/$OldQty);	//配件新的订单数量
		
		$PandsResult =mysql_fetch_array(mysql_query("SELECT P.Relation  FROM $DataIn.pands P
				WHERE P.ProductId='$ProductId' AND P.StuffId='$StuffId' ",$link_id));
		if ($PandsResult["Relation"]==""){
			   $newOrderQty=intval(($sOldOrderQty*$Qty)/$OldQty);	//配件新的订单数量
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

		if($scTypeId<9000){//为统计配件:只需更新配件的订单数量，其它均为0，即维持原值
			$newStockQty=0;
			$newFactualQty=$newOrderQty;
			$newAddQty=0;
			$returnQty=0;
			}
		else{//非统计配件
			if($sMid==0){//未下单:两种情况：1、有需求；2、全部使用库存的
				if($sOldStockQty<$sOldAddQty){//如果原使用库存数少于增购数量，则增购数量保留，顺为原增购数量有可能已被使用
					$newAddQty=$sOldAddQty-$sOldStockQty;
					$newStockQty=0;
					$newFactualQty=$newOrderQty;  //2012-10-24更新
					$returnQty=0;
					}
				else{						//如果原本使用库存数量大于增购数量
					$newAddQty=0;			//则去掉增购
					if(($sOldStockQty-$sOldAddQty)<$newOrderQty){	//如果余下的库存数少于订单数，则有需求
						$newStockQty=$sOldStockQty-$sOldAddQty;
						$newFactualQty=$newOrderQty-$newStockQty;
						$returnQty=0;
						}
					else{											//如果库存仍然充足
						$newStockQty=$newOrderQty;								//使用库存数=订单数
						$newFactualQty=0;										//需求数=0
						$returnQty=$sOldStockQty-$sOldAddQty-$newOrderQty;		//退回库存的数量=原使用库存数-增购数量-新的订单数量
						}
					}
				}
			else{//已下单:主要是要保持采购的总数不变
				if($newOrderQty-$sOldFactualQty<=0){	//如果新的订单数量少于原采购数量
					$newFactualQty=$newOrderQty;							//新的需求数量=新的订单数量，多出部分作为增购
					$newAddQty=$sOldAddQty+($sOldFactualQty-$newOrderQty);	//新的增购=原增购+多出的需求
					//$returnQty=$sOldStockQty;								//退回库存数量=原使用库存数
					//$returnQty=$sOldFactualQty-$newOrderQty;
                                                                                $returnQty=$sOldOrderQty-$newOrderQty;//2012-10-24更新
					$newStockQty=0;											//新的使用库存数为0
					}
				else{									//如果新的订单数量大于原采购数量
					$newFactualQty=$sOldFactualQty;				//新的需求数量不变=原需求数量
					$newAddQty=$sOldAddQty;						//新的增购数量不变=原增购数量
					$newStockQty=$newOrderQty-$newFactualQty;	//新的使用库存数=新的订单数-新的需求数
					$returnQty=$sOldStockQty-$newStockQty;		//退回库存数量=原使用库存数-新的使用库存数
					}
				}
			}
		
		//更新需求单
		$StockUpSQL= "UPDATE $DataIn.cg1_stocksheet SET OrderQty='$newOrderQty',FactualQty='$newFactualQty',StockQty='$newStockQty',AddQty='$newAddQty' WHERE Id='$Sid'";
		$StockUp = mysql_query($StockUpSQL);
		if($StockUp){
			$Log.="第 $x 个配件需求单 $StockId 更新成功!<br>";
            //母配件更新子配件的需求数量
            $UpdateComboxSql = "UPDATE   $DataIn.cg1_stuffcombox  M   
             LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId =M.mStockId
             SET  M.OrderQty=M.Relation*($newFactualQty+$newAddQty)
             WHERE  G.StuffId=$StuffId AND G.StockId=$StockId";
            $UpdateComboxResult = @mysql_query($UpdateComboxSql);

			//更新生产记录
			if($mainType==3){//生产统计配件的记录处理
				$checkScRow=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS ScQtySUM FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId='$scTypeId'",$link_id));
				$scQtySUM=$checkScRow["ScQtySUM"];
				if($scQtySUM>$newOrderQty){//生产登记大于订单需求数量
					$checkScSql=mysql_query("SELECT Id,Qty FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId='$scTypeId' ORDER BY Date DESC,Id DESC",$link_id);
					if($checkScNow=mysql_fetch_array($checkScSql)){
						$scXqQty=$newOrderQty;
						do{
							$scId=$checkScNow["Id"];
							$scQty=$checkScNow["Qty"];
							if($scQty>$scXqQty && $scXqQty>0){//登记的数量大于需求的数量，则要更新数量
								$ScUpSQL= "UPDATE $DataIn.sc1_cjtj SET Qty='$scXqQty' WHERE Id='$scId'";
								$StockUp = mysql_query($ScUpSQL);
								$scXqQty=0;
								}
							else{//多余的登记数量，做删除处理
								if($scXqQty==0){//订单需求的数量已经登记完，则删除多余记录
									$DelSql="DELETE FROM $DataIn.sc1_cjtj WHERE Id='$scId'";
									$DelResult=mysql_query($DelSql);
									}
								else{//如果登记的数量少于需求的数量，则记录不变，但需求数量扣除
									$scXqQty=$scXqQty-$scQty;
									}
								}
							}while ($checkScNow=mysql_fetch_array($checkScSql));
						}
					}
				}//生产记录处理完毕
			//更新库存
			if($returnQty>0){
				$inSql = "UPDATE $DataIn.ck9_stocksheet SET oStockQty=oStockQty+'$returnQty' WHERE StuffId='$StuffId' LIMIT 1";
				$inRresult = mysql_query($inSql);
				if($inRresult){
					$Log.="&nbsp;&nbsp;配件 $StuffId 的可用库存数量更新成功.<br>";
					}
				else{
					$Log.="&nbsp;&nbsp;配件 $StuffId 的可用库存数量更新失败.<br>";
					$OperationResult="N";
					}
				}
			}
		else{
			$Log.="<div class=redB>第 $x 个配件需求单 $StockId 更新失败!</div><br>";
			$OperationResult="N";
			}
		$x++;
		
		}while ($StockRow = mysql_fetch_array($StockRead));
	}//end if ($StockRow = mysql_fetch_array($StockRead))
//订单生产状态更新
$UpdateSql="Update $DataIn.yw1_ordersheet Y
	LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId') A ON A.POrderId=Y.POrderId
	LEFT JOIN (
		SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
		FROM $DataIn.cg1_stocksheet G 
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		WHERE G.POrderId='$POrderId' AND T.mainType=3) B ON B.POrderId=Y.POrderId 
	SET Y.scFrom=0
	WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty AND Y.scFrom!=1";
$UpdateResult = mysql_query($UpdateSql);
?>