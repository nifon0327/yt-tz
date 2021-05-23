<?
 $Log_Item="采单删除审核"; 
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 
 switch($ActionId){
    case "PASS":
    /*
              $Stuff_Temp=mysql_query("SELECT G.Mid,G.StockId,G.POrderId,G.StuffId,G.BuyerId,G.OrderQty,G.StockQty,G.FactualQty,G.AddQty,D.TypeId,T.mainType 
		FROM $DataIn.cg1_stocksheet G
		LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
		WHERE G.Id=$Id ",$link_id); 
		if($Stuff_myrow = mysql_fetch_array($Stuff_Temp)){
				$StockId=$Stuff_myrow["StockId"];
				$Mid=$Stuff_myrow["Mid"];
				$StuffId=$Stuff_myrow["StuffId"];
				$BuyerId=$Stuff_myrow["BuyerId"];
				$OrderQty=$Stuff_myrow["OrderQty"];
				$StockQty=$Stuff_myrow["StockQty"];
				$FactualQty=$Stuff_myrow["FactualQty"];
				$AddQty=$Stuff_myrow["AddQty"];
				$POrderId=$Stuff_myrow["POrderId"];
				$TypeId=$Stuff_myrow["TypeId"];
				include "../../admin/subprogram/del_model_order.php";
				include "../../admin/subprogram/del_model_llqty.php";
				//更新订单状态
				if($mainType==3){//删除的是生产统计配件，则需要删除相应的生产记录，订单的状态也需要更新
					$DelSql="DELETE FROM $DataIn.sc1_cjtj WHERE TypeId='$TypeId' AND POrderId='$POrderId'";
					$DelResult=mysql_query($DelSql);
					//更新状态
					$UpdateSql="Update $DataIn.yw1_ordersheet Y
							LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' GROUP BY POrderId) A ON A.POrderId=Y.POrderId
							LEFT JOIN (
								SELECT SUM(G.OrderQty) AS Qty,G.POrderId 
								FROM $DataIn.cg1_stocksheet G 
								LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
								LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
								WHERE G.POrderId='$POrderId' AND T.mainType=3 GROUP BY G.POrderId) B ON B.POrderId=Y.POrderId 
							SET Y.scFrom=0,Y.Estate=1
							WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty";//该订单中，如果生产的数量和需求的数量一致，则生产状态为0，出货状态是不变的1，由品检审核后出货状态才改为待出2
						$UpdateResult = mysql_query($UpdateSql);
					}
			    $OperationResult="Y";
			}
			*/
			
			$insql="INSERT INTO  $DataIn.cg1_del_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,1,$Operator,$Operator,'$DateTime','$curDate')";
		         mysql_query($insql,$link_id);
              break;
              
    case "BACK":
            $updateSql="UPDATE $DataIn.cg1_stocksheet SET Estate=0 WHERE Id=$Id";
            $UpdateResult = mysql_query($updateSql,$link_id);
            if($UpdateResult){
	            
	            
	             $insql="INSERT INTO  $DataIn.cg1_del_audit (Sid,Estate,Checker,creator,created,Date) VALUES  ($Id,2,$Operator,$Operator,'$DateTime','$curDate')";
		         mysql_query($insql,$link_id);
         
         
                    $Log="<div class=greenB>ID($Id)的配件需求单退回删除申请成功.</div><br>";
                     $OperationResult="Y";
                    //$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw1_fkoutsheet");
                    }
            else{
                    $Log="<div class='redB'>ID($Id)的配件需求单退回删除申请失败.</div>";
                    $OperationResult="N";
                    }
            break;      
 }
 
?>