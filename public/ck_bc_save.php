<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="补仓资料";			//需处理
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

//保存主单资料
$inRecode="INSERT INTO $DataIn.ck3_bcmain (Id,BillNumber,CompanyId,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
$Mid=mysql_insert_id();
if($Mid>0){
	$Lens=count($thQTY);
	for($i=0;$i<$Lens;$i++){
		$Id=$thQTY[$i];
		/*if($Id!=""){
			$StuffId=$thStuffId[$i];
			$Qty=$thQTY[$i];
			$Remark=$thRemark[$i];
			/////////////////////////////////////////////////////
			// 1 库存足够的情况下加入入库明细
			$addRecodes="INSERT INTO $DataIn.ck3_bcsheet (Id,Mid,StuffId,Qty,Remark,Locks) VALUES (NULL,'$Mid','$StuffId','$Qty','$Remark','0')";
			$addAction=@mysql_query($addRecodes);
			if($addAction){
				$Log.="$StuffId 补仓成功(补仓数量 $Qty).<br>";
				// 2 更新在库
				$upCk="UPDATE $DataIn.ck9_stocksheet SET tStockQty=tStockQty+$Qty WHERE StuffId='$StuffId' LIMIT 1";
				$upCkAction=mysql_query($upCk);		
				if($upCkAction){
					$Log.="&nbsp;&nbsp;配件 $StuffId 在库增加成功(数量 $Qty).<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;配件 $StuffId 在库扣增加失败(数量 $Qty).</div><br>";
					$OperationResult="N";
					}
				}
			else{
				$Log.="<div class='redB'>$StuffId 补仓失败(补仓数量 $Qty). </div><br>";
				$OperationResult="N";
				}		
			/////////////////////////////////////////////////////
			}*/
		}
	}
else{
	$Log.="<div class='redB'>补仓操作失败.</div><br>";
	$OperationResult="N";
	}
//解锁
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
