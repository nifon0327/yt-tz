<?php 
//EWEN 2013-02-27 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM配件报废记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);

//步骤3：需处理，执行动作
$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$checkQty=mysql_fetch_array(mysql_query("SELECT IFNULL(Qty,0) AS Qty,GoodsId,Locks FROM $DataIn.nonbom10_outsheet WHERE Id='$Id'",$link_id));
		$bfQty=$checkQty["Qty"];
		$GoodsId=$checkQty["GoodsId"];
		if($checkQty["Locks"]==1){
			$DelSql = "DELETE FROM $DataIn.nonbom10_outsheet WHERE Id='$Id' AND Locks='1'";
			$DelResult = mysql_query($DelSql);
			if($DelResult && mysql_affected_rows()>0){
			     	$Log.="ID/编号为 $Id/$GoodsId 的 $Log_Item 删除操作成功.<br>";
			     	//更新库存
				    $updateSQL = "UPDATE $DataPublic.nonbom5_goodsstock SET wStockQty=wStockQty+'$bfQty',oStockQty=oStockQty+'$bfQty' WHERE GoodsId='$GoodsId'";
				     $updateResult = mysql_query($updateSQL);
            		//2，固定资产报废信息删除，资产状态返回在库状态
                     $UpdateSql1="UPDATE  $DataIn.nonbom7_code C 
                     LEFT JOIN nonbom10_bffixed F ON F.BarCode=C.BarCode
                     SET C.Estate=1  WHERE  F.BfId='$Id'";
                    $UpdateResult1=@mysql_query($UpdateSql1);
					$delCodeSql = "DELETE FROM $DataIn.nonbom10_bffixed WHERE BfId='$Id'"; 
					$delCodeRresult = mysql_query($delCodeSql);
					if($delCodeRresult && mysql_affected_rows()>0){
                               $Log.="&nbsp;&nbsp;2.固定资产相关报废信息清除成功!<br>";
                            }
				}
			else{
				$Log.="<div class='redB'>ID/编号为 $Id/$GoodsId 的 $Log_Item 删除操作失败! $DelSql</div><br>";
				$OperationResult="N";
				}
			}
		else{
			$Log.="<div class='yellowB'>ID/编号为 $Id/$GoodsId 的 $Log_Item 锁定中，请主管解锁后再操作! $DelSql</div><br>";
			$OperationResult="N";
			}
		}
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>