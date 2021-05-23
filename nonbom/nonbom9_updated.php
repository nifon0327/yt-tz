<?php 
//EWEN 2013-02-26 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非bom配件转入";		//需处理
$upDataSheet="$DataIn.nonbom9_insheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$changeQty=$oldQty-$Qty;//如果是正数，则为减少数量，如果为负数则为增加
		$updateSQL = "UPDATE $upDataSheet A
		LEFT JOIN $DataPublic.nonbom5_goodsstock B ON A.GoodsId=B.GoodsId
		SET A.Qty='$Qty',A.Remark='$Remark',A.Date='$DateTime',A.Operator='$Operator',A.Locks='0',B.wStockQty=B.wStockQty-'$changeQty',B.oStockQty=B.oStockQty-'$changeQty' WHERE A.Id='$Id' AND A.Locks='1' AND B.wStockQty>'$changeQty' AND B.oStockQty>'$changeQty'";
		$updateResult = mysql_query($updateSQL);
		if ($updateResult && mysql_affected_rows()>0){
			$Log=$Log_Item.$Log_Funtion."成功.<br>";
			}
		else{
			$Log="<div class='redB'>".$Log_Item.$Log_Funtion."失败. $updateSQL</div><br>";
			$OperationResult="N";
			}
           $TypeSign=2;
           include "nonbom7_fixedupdated.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>