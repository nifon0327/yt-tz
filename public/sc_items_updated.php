<?php 
//电信---yang 20120801
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Funtion="保存";
$upDataSheet="$DataIn.sc1_sctj";	//需处理
$TitleSTR=$Log_Item . $Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Number=$Operator;
$GroupResult=mysql_query("select GroupId From $DataPublic.staffmain where Number='$Number'",$link_id);
if($GroupRow=mysql_fetch_array($GroupResult)){
       $GroupId=$GroupRow["GroupId"];
     }
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 84:
		$Log_Item="生产登记";		//需处理
		//$LockSql=" LOCK TABLES $DataIn.sc1_cjtj WRITE";$LockRes=@mysql_query($LockSql);
		$inRecode="INSERT INTO $DataIn.sc1_cjtj (Id,GroupId,TypeId,POrderId,Qty,Remark,Date,Estate,Locks,Leader) VALUES (NULL,'$GroupId','$ItemId','$POrderId','$NowQty','$Remark','$DateTime','1','0','$Operator')";
		//echo "$inRecode <br>";
		$inAction=@mysql_query($inRecode);
		//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
		if ($inAction){ 
			$Log="$TitleSTR 成功!<br>";
			//检查该订单是否已经生产完，是则更新订单状态Y.Estate=2,
			$UpdateSql="Update $DataIn.yw1_ordersheet Y
				LEFT JOIN(SELECT SUM(Qty) AS Qty,POrderId FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' GROUP BY POrderId) A ON A.POrderId=Y.POrderId
				LEFT JOIN (SELECT SUM(G.OrderQty) AS Qty,G.POrderId FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId WHERE G.POrderId='$POrderId' AND D.TypeId<8000 GROUP BY G.POrderId) B ON B.POrderId=Y.POrderId 
				SET Y.scFrom=0 
				WHERE Y.POrderId='$POrderId' AND A.Qty=B.Qty";//该订单中，如果生产的数量和需求的数量一致，则生产状态为0，出货状态是不变的1，由品检审核后出货状态才改为待出2
			$UpdateResult = mysql_query($UpdateSql);
			if($UpdateResult && mysql_affected_rows()>0){
				$Log="$Operator"."生产登记".$POrderId."( $ItemId )数量".$NowQty.";订单生产完毕,生产状态更新成功.";
				}
			else{
				$Log="$Operator"."生产登记".$POrderId."( $ItemId )数量".$NowQty.";订单生产未完成.";
				}
			} 
		else{
			$Log=$Log."<div class=redB>$TitleSTR 失败! " . $inRecode ."</h4></div><br>";
			$OperationResult="N";
			} 
		break;
	case 85:
		$Log_Item="打印任务";		//需处理
		$inRecode="INSERT INTO $DataIn.sc3_printtasks (Id,CodeType,POrderId,Qty,Estate,Date,Operator) VALUES";
		if($Qty1!="" && $Qty1>0){
			$inRecode1=" (NULL,'1','$POrderId','$Qty1','1','$DateTime','$Operator')";
			}
		if($Qty2!="" && $Qty2>0){
			$inRecode1.=$inRecode1==""?"(NULL,'2','$POrderId','$Qty2','1','$DateTime','$Operator')":",(NULL,'2','$POrderId','$Qty2','1','$DateTime','$Operator')";
			}
		if($Qty3!="" && $Qty3>0){
			$inRecode1.=$inRecode1==""?"(NULL,'3','$POrderId','$Qty3','1','$DateTime','$Operator')":",(NULL,'3','$POrderId','$Qty3','1','$DateTime','$Operator')";
                        }
                if($Qty4!="" && $Qty4>0){
			$inRecode1.=$inRecode1==""?"(NULL,'4','$POrderId','$Qty4','1','$DateTime','$Operator')":",(NULL,'4','$POrderId','$Qty4','1','$DateTime','$Operator')";
			}        
			
		//步骤3：需处理
		$inAction=@mysql_query($inRecode.$inRecode1);
		if ($inAction){ 
			$Log="$Log_Item 添加成功!<br>";
			} 
		else{
			$Log=$Log."<div class=redB>$Log_Item 添加失败!</div><br>";
			$OperationResult="N";
			} 
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>