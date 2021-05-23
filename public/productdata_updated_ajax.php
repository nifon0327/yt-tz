<?php
//电信---yang 20120801
session_start();
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item = "产品资料";            //需处理
$Log_Funtion = "数据更新";
$DateTime = date("Y-m-d H:i:s");
$Date = date("Y-m-d");
$Operator = $Login_P_Number;
$OperationResult = "Y";

switch ($ActionId) {
    case "ts":
        $Log_Funtion = "产品标准图业务初审";
        $sql = "UPDATE $DataIn.productstandimg SET Estate=1 WHERE ProductId=$ProductId";
        $result = mysql_query($sql);
        if ($result) {
            $Log = "<p>产品 $ProductId 的检验标准图业务初审成功.";
            echo "Y";
        } else {
            $Log = "<p>产品 $ProductId 的检验标准图业务初审失败.";
            echo "N";
        }
        break;

    case "bjRemark":
        $UpdateSql = "UPDATE $DataIn.productdata SET bjRemark='$bjRemark' WHERE ProductId='$ProductId'";
        $UpdateReuslt = mysql_query($UpdateSql);
        if ($UpdateReuslt && mysql_affected_rows() > 0) {
            echo "Y";
        } else {
            echo "N";
        }
        break;

    case "productsize":

        $UpdateSql = "UPDATE $DataIn.productdata SET productsize='$productsize' WHERE ProductId='$ProductId'";
        $UpdateReuslt = mysql_query($UpdateSql);
        if ($UpdateReuslt && mysql_affected_rows() > 0) {
            echo "Y";
        } else {
            echo "N";
        }
        break;

    case "HSCode":

        $CheckRow = mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.customscode WHERE ProductId='$ProductId'", $link_id));
        $CheckId = $CheckRow["Id"];

        if ($CheckId > 0) {
            $UpdateSql = "UPDATE $DataIn.customscode SET HSCode='$HSCode' WHERE ProductId='$ProductId'";
            $UpdateReuslt = mysql_query($UpdateSql);
            if ($UpdateReuslt && mysql_affected_rows() > 0) {
                echo "Y";
            } else {
                echo "N";
            }
        } else {
            $InsertSql = "INSERT INTO $DataIn.customscode (Id,ProductId,HSCode,Remark,Date,Estate,Locks,
		         Operator,PLocks,creator,created,modifier,modified)VALUES(NULL,'$ProductId','$HSCode','','$Date',
		         '1','0','$Operator','0','$Operator','$DateTime','$Operator','$DateTime')";
            $inAction = @mysql_query($InsertSql);
            if ($inAction) {
                echo "Y";
            } else {
                echo "N";
            }
        }

        break;

    case "taxtype":

        $UpdateSql = "UPDATE $DataIn.productdata SET taxtypeId='$taxtypeId' WHERE ProductId='$ProductId'";
        $UpdateReuslt = mysql_query($UpdateSql);
        if ($UpdateReuslt && mysql_affected_rows() > 0) {
            echo "Y";
        } else {
            echo "N";
        }
        break;

    case "replace":

        $YSql = "SELECT P.Id, P.ProductId, P.cName, P.eCode, TD.CmptType, TD.BuildingNo, TD.FloorNo, TD.CmptNo, TD.SN 
        FROM productdata P 
        INNER JOIN trade_drawing TD ON TD.Id = P.drawingId
        INNER JOIN trade_object TT ON TT.Id = TD.TradeId WHERE P.Id = $Ids";
        $YResult = mysql_query($YSql);
        if ($YRow = mysql_fetch_array($YResult)) {
            $YId = $YRow['Id'];
            $YPId = $YRow['ProductId'];
            $YcName = $YRow['cName'];
            $YeCode = $YRow['eCode'];
            $TSql = "SELECT P.Id, P.ProductId, P.cName, P.eCode, TD.CmptType, TD.BuildingNo, TD.FloorNo, TD.CmptNo, TD.SN 
            FROM productdata P 
            INNER JOIN trade_drawing TD ON TD.Id = P.drawingId
            INNER JOIN trade_object TT ON TT.Id = TD.TradeId WHERE P.ProductId = $GID";
            $TResult = mysql_query($TSql);
            if ($TRow = mysql_fetch_array($TResult)) {
                $TId = $TRow['Id'];
                $TPId = $TRow['ProductId'];
                $TcName = $TRow['cName'];
                $TeCode = $TRow['eCode'];
                $uResult = mysql_query("SELECT ReplaceNO,Reason FROM $DataIn.ck_substitute WHERE YProductId = $TPId AND Validity = '1' order by Id desc Limit 1", $link_id);
                if ($uRow = mysql_fetch_array($uResult)) {
                    $ReplaceNO = $uRow["ReplaceNO"];
                    $Reason = $uRow["Reason"];
                    echo "替板构件已有替板记录，无法替换，请查看 <br/> $TeCode";
                } else {
                    $UpSql = "INSERT INTO $DataIn.ck_substitute(Id, ReplaceNO, YProductId, YeCode, TProductId, TeCode, Estate, Operator, operating_time, Reason) VALUES (NULL , '$THNO', $YPId, '$YeCode', $TPId, '$TeCode', 1, '$Operator', '$DateTime', '$Reason')";
                    $UpResult = mysql_query($UpSql);
                    if ($UpResult && mysql_affected_rows() > 0) {
                        $UpYSql = "UPDATE $DataIn.productdata SET eCode='$TeCode' WHERE ProductId='$YPId'";
                        $UpTSql = "UPDATE $DataIn.productdata SET eCode='$YeCode' WHERE ProductId='$TPId'";
                        $UpYReuslt = mysql_query($UpYSql);
                        $UpTReuslt = mysql_query($UpTSql);
                        if ($UpYReuslt && $UpTReuslt) {
                            echo "Y";
                        } else {
                            echo "构件替换失败！";
                        }
                    } else {
                        echo '构件替换新增失败！';
                    }
                }
            } else {
                echo '替换构件不存在！';
            }
        } else {
            echo '构件不存在！';
        }

        break;

    case "back":
        $Sql = "SELECT CS.Id,CS.ReplaceNO,CS.Reason,CS.YProductId,CS.YeCode,CS.TProductId,CS.TeCode FROM $DataIn.ck_substitute CS LEFT JOIN productdata P ON P.ProductId = YProductId WHERE P.Id = $Ids AND CS.Validity = '1'  order by CS.Id desc Limit 1";
        $Result = mysql_query($Sql, $link_id);
        if ($Row = mysql_fetch_array($Result)) {
            $Id = $Row["Id"];
            $ReplaceNO = $Row["ReplaceNO"];
            $Reason = $Row["Reason"];
            $YProductId = $Row["YProductId"];
            $YeCode = $Row["YeCode"];
            $TProductId = $Row["TProductId"];
            $TeCode = $Row["TeCode"];
            $mySql = "SELECT Id FROM $DataIn.ck_substitute WHERE YProductId = $TProductId";
            $myResult = mysql_query($mySql, $link_id);
            if ($myRow = mysql_fetch_array($myResult)) {
                echo "替板构件已有替板记录，无法撤销，请查看 <br/> $TeCode";
            } else {
                $UpYSql = "UPDATE $DataIn.productdata SET eCode='$YeCode' WHERE ProductId='$YProductId'";
                $UpTSql = "UPDATE $DataIn.productdata SET eCode='$TeCode' WHERE ProductId='$TProductId'";
                $UpYReuslt = mysql_query($UpYSql);
                $UpTReuslt = mysql_query($UpTSql);
                if ($UpYReuslt && $UpTReuslt) {
                    echo "Y";
                    $DelSql = "UPDATE $DataIn.ck_substitute SET Validity = '0' WHERE Id='$Id'";
                    $DelRes = mysql_query($DelSql);
                } else {
                    echo "构件替换失败！";
                }
            }
        } else {
            echo "此构件暂无替板记录";
        }
        break;

}
$IN_recode = "INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Lo4g_Funtion','$Log','$OperationResult','$Operator')";
$IN_res = @mysql_query($IN_recode);
?>