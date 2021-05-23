<?php  
//电信-zxq 2012-08-01
/*
$DataIn.ck5_llmain
$DataIn.ck5_llsheet
$DataIn.ck9_stocksheet
$DataIn.cg1_stocksheet
$DataIn.yw1_ordersheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="领料记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$chooseDate=substr($rkDate,0,7);
$ALType="chooseDate=$chooseDate";
$x=1;
//锁定表
//$LockSql=" LOCK TABLES $DataIn.ck5_llmain WRITE,$DataIn.ck5_llsheet WRITE,$DataIn.ck9_stocksheet K WRITE,$DataIn.cg1_stocksheet G WRITE,$DataIn.yw1_ordersheet WRITE,$DataIn.stuffdata D WRITE,$DataIn.stufftype T WRITE";$LockRes=@mysql_query($LockSql);
//保存主单资料
$inRecode="INSERT INTO $DataIn.ck5_llmain (Id,Materieler,Remark,Locks,Date,Operator) VALUES (NULL,'$Materieler','$Remark','0','$llDate','$Operator')";
$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
if($Mid!=0 && $Mid!=""){
	$Log.="主领料单新增成功:<br>";
	//分割字符串
	$valueArray=explode("|",$AddIds);
	$Count=count($valueArray);
	for($i=0;$i<$Count;$i++){
		$valueTemp=explode("!",$valueArray[$i]);
		$StockId=$valueTemp[0];
		$StuffId=$valueTemp[1];	
		$Qty=$valueTemp[2];	
		//检查在库是否足够
		$checkKc=mysql_query("SELECT K.Id FROM $DataIn.ck9_stocksheet K WHERE K.StuffId='$StuffId' AND K.tStockQty>=$Qty",$link_id);
		if($ckeckRows = mysql_fetch_array($checkKc) && mysql_affected_rows()>0){//有足够库存
			//1. 加入入库明细
			$addRecodes="INSERT INTO $DataIn.ck5_llsheet (Id,Mid,StockId,StuffId,Qty,Locks) VALUES (NULL,'$Mid','$StockId','$StuffId','$Qty','0')";
			$addAction=@mysql_query($addRecodes);
			if($addAction){
				$Log.="&nbsp;&nbsp;$x-1:$StockId 领料成功(领料数量 $Qty).<br>";
				////////////////////
				//2. 更新库存数量，更新需求单领料标记:0已全部领料，1非全部领料
				$POrderId=substr($StockId,0,-2);
				$upSign2="UPDATE $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
				SET K.tStockQty=K.tStockQty-$Qty,
					G.llSign=(CASE WHEN G.OrderQty=(SELECT SUM( Qty ) AS Qty FROM $DataIn.ck5_llsheet WHERE StockId = '$StockId' ) THEN 0 ELSE 1 END)
				WHERE G.StockId='$StockId' AND G.StuffId='$StuffId'";
				$upAction2=mysql_query($upSign2);	
				if($upAction2){
					$Log.="&nbsp;&nbsp;$x-2:需求单 $StockId 的领料标记更新成功.配件 $StuffId 在库更新成功(扣除领料数量 $Qty).<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;$x-2:需求单 $StockId 的领料标记更新失败.配件 $StuffId 在库更新失败(扣除领料数量 $Qty). $upSign2 </div><br>";
					$OperationResult="N";
					}
				//3.更新订单状态:2待出，1非待出
				$upSign3="UPDATE $DataIn.yw1_ordersheet SET Estate=(CASE WHEN(SELECT SUM(G.OrderQty) AS oQty 
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE 1 AND T.mainType AND G.POrderId='$POrderId')=(SELECT SUM(Qty) AS lQty FROM $DataIn.ck5_llsheet WHERE StockId LIKE '$POrderId%') THEN 2 ELSE 1 END) WHERE POrderId='$POrderId'";
				$upAction3=mysql_query($upSign3);	
				if($upAction3){
					$Log.="&nbsp;&nbsp;$x-3:订单 $POrderId 的状态更新成功.<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;$x-3:订单 $POrderId 的状态更新失败. $upSign3 </div><br>";
					$OperationResult="N";
					}
				///////////////////
				}
			else{
				$Log.="<div class='redB'>&nbsp;&nbsp;$x-1:$StockId 领料失败. $addRecodes </div><br>";
				$OperationResult="N";
				}
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;配件 $StuffId 在库不足.</div><br>";
			$OperationResult="N";
			}
		$x++;
		}//end for
	}//end if($Mid!=0 && $Mid!="")
//解锁
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";

?>
