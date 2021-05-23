<?php
$Log_Item = "非bom配件申领记录";        //需处理
$upDataSheet = "$DataIn.nonbom8_outsheet";    //需处理
$Log_Funtion = "发放";
$TitleSTR = $SubCompany . " " . $Log_Item . $Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime = date("Y-m-d H:i:s");
$Operator = $Login_P_Number;
$OperationResult = "Y";

if ($Ids != "") {
    $Field = explode(",", $Ids);
    $Lens = count($Field);
    for ($i = 0; $i < $Lens; $i++) {
        $Id = $Field[$i];

        $updateSQL = "UPDATE $upDataSheet A
				LEFT JOIN $DataPublic.nonbom5_goodsstock B ON A.GoodsId=B.GoodsId
				SET A.Estate=0,A.OutOperator='$Operator',A.OutDate='$DateTime',
				B.wStockQty=B.wStockQty-A.Qty,B.oStockQty=B.oStockQty-A.Qty,B.lStockQty=B.lStockQty+A.Qty
				WHERE A.Id = $Id AND A.Estate='1' AND B.wStockQty>=A.Qty AND B.oStockQty>=A.Qty";
        $updateResult = mysql_query($updateSQL);
        if ($updateResult && mysql_affected_rows() > 0) {
            $Log .= $Log_Item . $Log_Funtion . "成功.<br>";
        } else {
            $Log .= "<div class='redB'>" . $Log_Item . $Log_Funtion . "失败,库存可能不足,请检查.</div><br>$updateSQL <br>";
            $OperationResult = "N";
        }
    }
}
$ALType = "From=$From&Pagination=$Pagination&Page=$Page&Estate=$Estate";
$IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res = @mysql_query($IN_recode);
include "../model/logpage.php";
?>