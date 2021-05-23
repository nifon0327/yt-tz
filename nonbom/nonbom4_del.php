<?php 
//EWEN 2013-02-20 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非bom配件资料";//需处理
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
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	
	
//删除数据库记录：如果已经有采购单或转入，则不能删
$DelSql = "DELETE A ,B 
FROM $DataPublic.nonbom4_goodsdata A 
LEFT JOIN $DataPublic.nonbom4_defaultcompany  B ON B.GoodsId=A.GoodsId
LEFT JOIN (
	SELECT GoodsId FROM (
		SELECT GoodsId FROM $DataIn.nonbom6_cgsheet
		UNION ALL
		SELECT GoodsId FROM $DataPublic.nonbom5_goodsstock WHERE wStockQty>0 OR oStockQty>0 
		) Y GROUP BY GoodsId
	)Z ON Z.GoodsId=A.GoodsId
WHERE Z.GoodsId IS NULL AND A.Id IN ($Ids) "; 
$DelResult = mysql_query($DelSql);
if($DelResult ){
	$Log="&nbsp;&nbsp; ID号在( $Ids )的 $Log_Item 删除操作成功.<br>";
	}
else{
	$OperationResult="N";
	$Log="<div class='redB'>ID号在( $Ids )的 $Log_Item 删除操作失败. $DelSql </div><br>";
	}//end if ($Del_result)
$Page=$IdCount==$x?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
