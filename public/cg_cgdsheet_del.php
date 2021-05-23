<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck9_stocksheet
$DataIn.cg1_stocksheet
*/
$MyPDOEnabled=1;

include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="特采单";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$myResult=$myPDO->query("CALL proc_cg1_stocksheet_delete('',$Id,$Operator);");
	    $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
	    $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;
	    $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
	    $Log.="</br>";
	
	    $myResult=null; $myRow=null;
	}
}

if ($Log==""){
    $OperationResult="N";
	$Log="<div class=redB>删除需求单失败</div> <br> CALL proc_cg1_stocksheet_delete('',$Id,$Operator);";
}


 /*
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
	
		$upSql="UPDATE $DataIn.ck9_stocksheet K,$DataIn.cg1_stocksheet S SET K.oStockQty=K.oStockQty-S.FactualQty,S.ywOrderDTime='$DateTime' WHERE S.Id=$Id AND S.cgSign=1 AND S.Mid='0' AND K.StuffId=S.StuffId AND K.oStockQty>=S.FactualQty";
		$upResult = mysql_query($upSql);
		if($upResult && mysql_affected_rows()>0){	//更新成功
			//删除特采单
			$delSql = "DELETE FROM $DataIn.cg1_stocksheet WHERE Id=$Id AND POrderId='' AND Mid='0' LIMIT 1"; 
			$delRresult = mysql_query($delSql);
			if ($delRresult && mysql_affected_rows()>0){
				     $Log.="$x- ID为 $Id 的需求单删除成功，配件库存已扣除.<br>";
				     /*
                      $DelComSql ="DELETE  FROM   $DataIn.cg1_stuffcombox   M 
                                                     LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId =M.mStockId WHERE G.Id=$Id";
                       $DelComResult =@mysql_query($DelComSql);
                       if($DelComResult){
				                   $Log.="$x- 1子配件的需求单删除成功<br>";
                            }
                       else{
							     $Log.="<div class='redB'>$x- 1子配件的需求单删除失败.</div><br>";
							     $OperationResult="N";
                          }
                     */
		/*		}
			else{
				$Log.="<div class='redB'>$x- ID为 $Id 的需求单删除成功，配件库存扣除失败.</div><br>";
				$OperationResult="N";
				}
			$y++;
			}
		else{
			//不是特采单或库存不足
			$Log.="<div class='redB'>$x- ID为 $Id 的需求单不是特采单或可用库存不足.</div><br>";
			$OperationResult="N";
			}
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)
	
//操作日志
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
*/
include "../model/logpage.php";
?>