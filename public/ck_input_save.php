<?php  
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";
//步骤2：
$Log_Item="入库资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$DateTime=date("Y-m-d H:i:s");
$OperationResult="Y";
//$Operator=$Login_P_Number;
//锁定表
//$LockSql=" LOCK TABLES $DataIn.ck1_rkmain WRITE,$DataIn.ck1_rksheet WRITE,$DataIn.ck9_stocksheet WRITE,$DataIn.cg1_stocksheet WRITE,$DataIn.gys_shsheet WRITE";$LockRes=@mysql_query($LockSql);
//保存主单资料
$inRecode="INSERT INTO $DataIn.ck1_rkmain (Id,BillNumber,CompanyId,BuyerId,Remark,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','$BuyerId','','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
if($Mid!=0 && $Mid!=""){
	//分割字符串
	$Count=count($CID);
	for($i=0;$i<$Count;$i++){
		$StockId=$CID[$i];
		$StuffId=$SID[$i];	
		$Qty=$QTY[$i];	
		if($StockId!="" || $StuffId!="" || $Qty>0){
			// 1 加入入库明细
			$addRecodes="INSERT INTO $DataIn.ck1_rksheet (Id,Mid,StockId,StuffId,Qty,Locks) VALUES (NULL,'$Mid','$StockId','$StuffId','$Qty','0')";
			$addAction=@mysql_query($addRecodes);
			if($addAction){
				$Log.="$StockId 入库成功(入库数量 $Qty).<br>";
				//锁定供应商送货资料
				if($shMid!=""){
					$upSH="UPDATE $DataIn.gys_shsheet SET Qty=$Qty,Locks=0 WHERE 1 AND Mid=$shMid AND StockId='$StockId' ANDStuffId='$StuffId' LIMIT 1";
					$upSHAction=mysql_query($upSH);
					}
				// 2 更新在库
				$upCk="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty+$Qty WHERE StuffId='$StuffId' LIMIT 1";
				$upCkAction=mysql_query($upCk);		
				if($upCkAction){
					$Log.="&nbsp;&nbsp;配件 $StuffId 在库入库成功(入库数量 $Qty).<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;配件 $StuffId 在库入库失败(入库数量 $Qty). $upCk </div><br>";
					
					}
				// 3 入库状态		
				$uprkSign="UPDATE $DataIn.cg1_stocksheet SET rkSign=(CASE 
					WHEN 
					FactualQty+AddQty>(
									SELECT SUM( Qty ) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId = '$StockId'
								 ) THEN 2
						ELSE 0 END) WHERE StockId='$StockId'";
				$upRkAction=mysql_query($uprkSign);	
				if($upRkAction){
					$Log.="&nbsp;&nbsp;&nbsp;&nbsp;需求单 $StockId 的入库标记更新成功.<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;&nbsp;&nbsp;需求单 $StockId 的入库标记更新失败. $uprkSign </div><br>";
					}
				}
			else{
				$Log.="<div class='redB'>$StockId 入库失败. $addRecodes </div><br>";
				$OperationResult="N";
				}
			}
		}
	}
//解锁
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
echo "<meta http-equiv=\"Refresh\" content='0;url=ck_input.php?Operator=$Operator'>";
?>
