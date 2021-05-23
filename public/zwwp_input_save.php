<?php  
//
include "../model/modelhead.php";
//步骤2：
$Log_Item="入库资料";			//需处理
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
$ALType="CompanyId=$CompanyId";

//锁定表
//$LockSql=" LOCK TABLES $DataIn.ck1_rkmain WRITE,$DataIn.ck1_rksheet WRITE,$DataIn.ck9_stocksheet WRITE,$DataIn.cg1_stocksheet WRITE";$LockRes=@mysql_query($LockSql);
//保存主单资料
$inRecode="INSERT INTO $DataIn.ck1_rkmain (Id,BillNumber,CompanyId,BuyerId,Remark,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','$BuyerId','$Remark','0','$rkDate','$Operator')";
$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
//分割字符串
$valueArray=explode("|",$AddIds);
$Count=count($valueArray);
for($i=0;$i<$Count;$i++){
	$valueTemp=explode("!",$valueArray[$i]);
	$StockId=$valueTemp[0];
	$StuffId=$valueTemp[1];	
	$Qty=$valueTemp[2];	
	// 1 加入入库明细
	$addRecodes="INSERT INTO $DataIn.ck1_rksheet (Id,Mid,StockId,StuffId,Qty,Locks) VALUES (NULL,'$Mid','$StockId','$StuffId','$Qty','0')";
	$addAction=@mysql_query($addRecodes);
	if($addAction){
		$Log.="$StockId 入库成功(入库数量 $Qty).<br>";
		// 2 更新在库
		$upCk="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty+$Qty WHERE StuffId='$StuffId' LIMIT 1";
		$upCkAction=mysql_query($upCk);		
		if($upCkAction){
			$Log.="&nbsp;&nbsp;配件 $StuffId 在库入库成功(入库数量 $Qty).<br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;配件 $StuffId 在库入库失败(入库数量 $Qty). $upCk </div><br>";
			
			}
		// 3 入库状态:有入库则2，最后才统一更新状态?
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
//解锁
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
//?????全部状态更新
//$checkSign="UPDATE $DataIn.cg1_stocksheet G LEFT JOIN (SELECT SUM( Qty ) AS Qty,StockId FROM $DataIn.ck1_rksheet WHERE 1 GROUP BY StockId) R ON R.StockId=G.StockId SET G.rkSign=0 WHERE G.rkSign=2 AND R.Qty=G.FactualQty+G.AddQty";

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
