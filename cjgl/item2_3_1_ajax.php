<?php
//电信-zxq 2012-08-01
session_start();
$MyPDOEnabled = 1;
include "../basic/parameter.inc";
include "../model/modelfunction.php";


$FromqcCause = 1;
$Date = date("Y-m-d");
$DateTime = date("Y-m-d H:i:s");
$Operator = $Login_P_Number;

$IdArr = explode(",", $Ids);
if ($ActionId == 19) {
    foreach ($IdArr as $Id) {

        $result = $myPDO->query("SELECT S.Id,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname,D.TypeId,D.CheckSign,T.AQL,
                (G.AddQty+G.FactualQty) AS cgQty
                FROM $DataIn.gys_shsheet S
                LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
                LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
                WHERE S.Id='$Id' LIMIT 1");

        if ($upData = $result->fetch()) {
            $StuffId = $upData["StuffId"];
            $StockId = $upData["StockId"];
            $Qty = $upData["Qty"];
            $cgQty = $upData["cgQty"];
            $StuffCname = $upData["StuffCname"];
            $TypeId = $upData["TypeId"];
            $CheckSign = $upData["CheckSign"];
            $AQL = $upData["AQL"];
            if ($CheckSign == 0) {
                $CheckSignStr = "抽检";
                $CheckQtyStr = "抽样数量";
                $AQL = "";
                $ReQty = 1;
            }
            else {
                $AQL = "";
                $CheckSignStr = "全检";
                $CheckQtyStr = "全检数量";
            }
            $CheckQty = $Qty;
            $SendSign = $upData["SendSign"];

            $sumQty = 0;

            $Estate = $sumQty == 0 ? 0 : 1;
            if ($CheckSign == 0) { //抽检
                $Estate = 0;  // 全部入库，不良品不做退换操作
            }

            $CheckAQL = $AQL;
        }


        $qcRes = $myPDO->query("SELECT S.Id FROM $DataIn.qc_badrecord S WHERE S.Sid='$Id'");

        if ($qcData = $qcRes->fetch()) {
            $qcResult = "来料品检不良记录主表已存在！";
        }else{

            $inSql = "INSERT INTO $DataIn.qc_badrecord (Id,shMid,Sid,StockId,StuffId,shQty,checkQty,Qty,AQL,Remark,Estate,Locks,Date,Operator,creator,created)
        SELECT NULL,Mid,'$Id',StockId,StuffId,Qty,
        '$CheckQty','$sumQty','$CheckAQL','','$Estate','0','$Date','$Operator','$Operator',NOW()
        FROM  $DataIn.gys_shsheet WHERE Id = '$Id' LIMIT 1";
//        echo $inSql;
            $inAction = $myPDO->exec($inSql);

            $Mid = $myPDO->lastInsertId();
        }

        if ($Mid > 0) {
            $qcResult = "来料品检不良记录主表保存成功！";
        }
        else {
            $qcError = 1;
            $qcResult = "来料品检不良记录保存失败！";
        }


        if ($CheckSign == 1) {       //全检审核，记录入库

            $ActionId = 17;
        }
        else {    //抽检，要分允收？拒收？
            if ($sumQty == 0 || $sumQty < $ReQty) {
                $ActionId = 17;  //允收
            }
            else {
                $ActionId = 15; //抽检拒收，全部退回不入库。
            }
        }

        switch ($ActionId) {
            case 15://退回
                $updateSQL = "UPDATE $DataIn.gys_shsheet Q SET Estate=1,Locks=1 WHERE Q.Id='$Id'";
                $updateResult = $myPDO->exec($updateSQL);
                if ($updateResult) {
                    if ($FromqcCause == 1) {
                        echo "<SCRIPT LANGUAGE=JavaScript>alert('记录已退回 $qcResult');</script>";
                    }
                    else {
                        echo "记录已退回";
                    }

                }
                break;


            case 17://审核通过，记录入库
                if ($FromqcCause == 1) {
                    include "item2_3_shrk.php";
                    echo "<SCRIPT LANGUAGE=JavaScript>
                    var host = window.parent.selObj;
                    host.parentNode.removeChild(host);
                    alert('$OperResult $qcResult');
                    </script>
                    <script language=JavaScript>
                    parent.location.reload();
                    </script>";
                    //by.lwh 页面刷新


                }
                else {
                    echo $OperResult;
                }
                break;
        }

        $sql = "SELECT G.stockId, G.POrderId, G.stuffId, SC.sPOrderId, SC.ActionId, SC.WorkshopId, 
round(G.OrderQty*(SC.Qty/O.Qty)) AS Qty1 
FROM gys_shsheet GS LEFT JOIN cg1_stocksheet G ON GS.stockId = G.StockId 
LEFT JOIN yw1_scsheet SC ON SC.POrderId = G.POrderId 
LEFT JOIN yw1_ordersheet O ON SC.POrderId = O.POrderId 
WHERE GS.id = $Id and ActionId = 101";
        $myResult = $myPDO->query($sql);
        $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
        $StockId = $myRow['stockId'];
        $POrderId = $myRow['POrderId'];
        $StuffId = $myRow['stuffId'];
        $sPOrderId = $myRow['sPOrderId'];
        $ActionId = $myRow['ActionId'];
        $WorkShopId = $myRow['WorkshopId'];
        $llQty = $myRow['Qty1'];

        $myResult = $myPDO->query("CALL proc_ck5_llsheet_save('$POrderId','$sPOrderId','$StockId','$StuffId','$llQty',$Operator,'$fromPage');");
        //echo "CALL proc_ck5_llsheet_save('$POrderId','$sPOrderId','$StockId','$StuffId','$llQty',$Operator,'$fromPage');";
        $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
        $OperationResult = $myRow['OperationResult'] != "Y" ? $myRow['OperationResult'] : $OperationResult;
        $myResult = null;
        $myRow = null;

        $tempCG_mStockId[] = $mStockId;
        $tempPrice_mStockId[] = $mStockId;
        $tempbl_sPOrderId[] = $sPOrderId;

        //去掉重复值
        $tempCG_mStockId = array_unique($tempCG_mStockId);
        $tempPrice_mStockId = array_unique($tempPrice_mStockId);
        $tempbl_sPOrderId = array_unique($tempbl_sPOrderId);

        $CG_mStockId = implode(",", $tempCG_mStockId);
        $Price_mStockId = implode(",", $tempPrice_mStockId);
        $bl_sPOrderId = implode(",", $tempbl_sPOrderId);

        $cgCount = count($tempCG_mStockId);
        if ($cgCount > 0 && in_array($ActionId, $ActionIdArray) && $OperationResult == "Y") {
            //内部加工单自动下采购单,外发发料必须两个料都发完再下采购单
            $bl_mStockId = "";
            if ($fromPage == "3") {
                $blSql = "SELECT SC.POrderId,SC.sPOrderId,SC.mStockId,SC.Qty,(CG.addQty+CG.FactualQty) AS xdQty
		             FROM $DataIn.yw1_scsheet SC 
	                 LEFT  JOIN $DataIn.cg1_stocksheet    CG ON CG.StockId = SC.mStockId
	                 WHERE SC.sPOrderId IN ($bl_sPOrderId) ";

                $blResult = $myPDO->query($blSql);
                while ($blRow = $blResult->fetch(PDO::FETCH_ASSOC)) {
                    $blPOrderId = $blRow["POrderId"];
                    $blsPOrderId = $blRow["sPOrderId"];
                    $blmStockId = $blRow["mStockId"];
                    $blQty = $blRow["Qty"];
                    $blxdQty = $blRow["xdQty"];
                    $blRelation = $blQty / $blxdQty;


                    $checkOrderQtyResult = $myPDO->query("SELECT SUM(ROUND(A.OrderQty*$blRelation,U.Decimals)) AS OrderQty
						 FROM  $DataIn.cg1_semifinished   A 
		                 INNER JOIN $DataIn.cg1_stocksheet G  ON G.StockId = A.StockId
		                 LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
                         LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
						 WHERE  A.POrderId='$blPOrderId' AND A.mStockId='$blmStockId' AND G.blSign=1");

                    $checkOrderQtyRow = $checkOrderQtyResult->fetch(PDO::FETCH_ASSOC);
                    $blOrderQty = $checkOrderQtyRow["OrderQty"];
                    $checkOrderQtyResult = null;
                    $checkOrderQtyRow = null;

                    $checkllResult = $myPDO->query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet 
		                 WHERE sPOrderId = '$blsPOrderId'");
                    $checkllRow = $checkllResult->fetch(PDO::FETCH_ASSOC);
                    $llQty = $checkllRow["llQty"];
                    $checkllResult = null;
                    $checkllRow = null;

                    if ($blOrderQty == $llQty) {
                        $bl_mStockId .= $bl_mStockId == "" ? $blmStockId : "," . $blmStockId;
                    }
                }
                $blResult = null;
                $blRow = null;
                //料齐才能够自动下采购单
                if ($bl_mStockId == "") $allblSign = 0;
                else {
                    $CG_mStockId = $bl_mStockId;
                }
            }

            if ($allblSign == 1) include "item5_3_auto_cg.php";

        }

        $priceCount = count($tempPrice_mStockId);
        if ($priceCount > 0 && in_array($ActionId, $ActionIdArray) && $OperationResult == "Y") {
            //自动更新半成品价格
            include "item5_3_updateprice.php";
        }

        $upSql = "UPDATE $DataIn.ck5_llsheet SET Estate=0,Receiver='$Operator',Received='$DateTime'
    WHERE sPOrderId='$sPOrderId' AND StockId='$StockId' ";
        $upResult = $myPDO->exec($upSql);

        if ($upResult) {
            //echo "Y";
            $UpdateComboxSql = "UPDATE $DataIn.ck5_llsheet  L
        LEFT JOIN $DataIn.cg1_stuffcombox C ON C.StockId = L.StockId
        SET L.Estate = 0,L.Receiver='$Operator',L.Received='$DateTime'
        WHERE C.mStockId = '$StockId' AND L.sPOrderId = '$sPOrderId'";
            $UpdateComboxResult = $myPDO->exec($UpdateComboxSql);
            $Log .= "<div class=greenB>" . $sPOrderId . "领料单确认成功!</div><br>";

            $IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','工单领料确认','数据更新','领料单确认成功','Y','$Operator')";
            $IN_res = $myPDO->exec($IN_recode);

        }
    }


}

include "../basic/quit_pdo.php";


function unescape($str)
{
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f) $ret .= chr($val);
            else if ($val < 0x800) $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
            else $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
            $i += 5;
        }
        else if ($str[$i] == '%') {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        }
        else $ret .= $str[$i];
    }

    return $ret;
}

?>