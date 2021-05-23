<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="领料记录";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$result= mysql_query("SELECT StuffId,Qty,StockId,POrderId,sPOrderId,RkId 
		FROM $DataIn.ck5_llsheet WHERE Id=$Id LIMIT 1",$link_id);
		if($myrow = mysql_fetch_array($result)) {
			$RkId=$myrow["RkId"];					
			$StuffId=$myrow["StuffId"];		
			$Qty=$myrow["Qty"];					
			$StockId=$myrow["StockId"];	
			$sPOrderId=$myrow["sPOrderId"];		
			$POrderId=$myrow["POrderId"];	
			
			/*$CheckResult=mysql_query("SELECT Id 
			FROM $DataIn.yw1_ordersheet WHERE POrderId='$POrderId' AND Estate=0",$link_id);
			if($CheckRow = mysql_fetch_array($CheckResult)){
				$Log.="<div class='redB'>&nbsp;&nbsp;相关的订单 $POrderId 已经出货，不能删除此领料记录!</div><br>";
				$OperationResult="N";
				}
			else{*/
				//1-删除领料记录
				$DelSql = "DELETE FROM $DataIn.ck5_llsheet WHERE Id=$Id"; 
				$DelResult = mysql_query($DelSql);
				if($DelResult && mysql_affected_rows()>0){
					$Log.="&nbsp;&nbsp;5-1:Id为 $Id 的领料记录删除成功.<br>";			
					//2-
					$UpSql2="UPDATE $DataIn.ck1_rksheet SET llQty = llQty-$Qty,llSign=2  WHERE Id='$RkId'";
					$UpResult2 = mysql_query($UpSql2);
					if($UpResult2){
						$Log.="&nbsp;&nbsp;5-2:配件ID:$StuffId 的库存领料数据还原成功.<br>";
						}
					else{
						$Log.="<div class='redB'>&nbsp;&nbsp;5-2:配件ID:$StuffId 的库存领料数据失败.$UpSql2 </div><br>";
						$OperationResult="N";
						}	
						
					$UpSql3="UPDATE $DataIn.cg1_stocksheet G
						SET G.llSign=(CASE WHEN G.OrderQty=(SELECT SUM( Qty ) AS Qty FROM $DataIn.ck5_llsheet WHERE StockId = '$StockId' ) THEN 0 ELSE 1 END)
						WHERE G.StockId='$StockId'";
					$UpResult3 = mysql_query($UpSql3);
					if($UpResult3){
						$Log.="&nbsp;&nbsp;5-3:相关的需求单 $StockId 的领料标记更新成功.<br>";
						}
					else{
						$Log.="<div class='redB'>&nbsp;&nbsp;5-3:相关的需求单 $StockId 的领料标记更新失败. $UpSql3 </div><br>";
						$OperationResult="N";
						}								
				
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;5-1:Id为 $Id 的领料记录删除失败.</div><br>";
					$OperationResult="N";
					}
				//}
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;领料ID为 $Id 的领料记录删除.</div><br>";
			$OperationResult="N";
			}//end if($myrow = mysql_fetch_array($result))
		$x++;
		}//end if($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
//操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>