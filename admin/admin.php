<?php
//defined('IN_COMMON') || include '../basic/common.php';

include "../model/modelhead.php";
//步骤2：
$DateTime = date("Y-m-d H:i:s");

     $CheckSplitResult=mysql_query("SELECT PD.* , I.ID AS DID FROM pd_g79 PD LEFT JOIN inventory_stackinfo I ON I.StackNo = PD.dh GROUP BY PD.Id",$link_id);
if ($CheckSplitRow=mysql_fetch_array($CheckSplitResult)) {
    $i = 1;
    do {

        $DID = $CheckSplitRow["DID"];//垛号库位关联id
        $Id = $CheckSplitRow["Id"];//盘点id
        $dh = $CheckSplitRow["dh"];//盘点垛号
        $kw = $CheckSplitRow['kw'];

        $sheetInSql = "SELECT P.ProductId FROM pd_g79 PD LEFT JOIN trade_drawing T ON PD.build = T.BuildingNo AND PD.floor = T.FloorNo AND PD.gj = T.CmptNo
	INNER JOIN productdata P ON P.cName = T.ProdcutCname
	LEFT JOIN productstock PS ON P.ProductId = PS.ProductId
	WHERE T.TradeId = 41 and PD.Id = $Id LIMIT 1";
        $sheetInAction = @mysql_query($sheetInSql, $link_id);
//               $ShipId=mysql_insert_id();
        if ($sheetInRow = mysql_fetch_array($sheetInAction)) {
            $ProductId = $sheetInRow['ProductId'];

            if ($DID != "" || $DID != NULL) {
                $sql = "INSERT INTO inventory_data(StackID,ProductID,`Status`,Result,CreateDT)
                VALUES($DID,$ProductId,1,0,CURRENT_TIMESTAMP);";
                $pUpResult = mysql_query($sql, $link_id);
                if ($pUpRow = mysql_fetch_array($pUpResult)) {
                    $UpSql = "UPDATE pd_g79  SET zt='1' WHERE Id='$Id'";
                    $UpResult = @mysql_query($UpSql);
                } else {
                    $Log .= "<div class='redB'>$Id   $DID   失败.</div><br>";
                }
            } else {
                $SplitResult = mysql_query("SELECT ID FROM inventory_stackinfo WHERE StackNo = $dh", $link_id);
                if ($SplitRow = mysql_fetch_array($SplitResult)) {
                    $pdh = $SplitRow['ID'];
                    $sql = "INSERT INTO inventory_data(StackID,ProductID,`Status`,Result,CreateDT)
                VALUES($pdh,$ProductId,1,0,CURRENT_TIMESTAMP);";
                    $pUpResult = mysql_query($sql, $link_id);
                    if ($pUpRow = mysql_fetch_array($pUpResult)) {
                        $UpSql = "UPDATE pd_g79  SET zt='1' WHERE Id='$Id'";
                        $UpResult = @mysql_query($UpSql);
                    } else {
                        $Log .= "<div class='redB'>$Id   $pdh   失败.</div><br>";
                    }
                } else {
                    $sql = "INSERT INTO inventory_stackinfo(StackNo,CreateDT,SeatId) 
              VALUES('$dh',CURRENT_TIMESTAMP,'$kw')";
                    $dkResult = mysql_query($sql, $link_id);
                    $dkId = mysql_insert_id();
                    if ($dkRow = mysql_fetch_array($dkResult)) {
                        $sql = "INSERT INTO inventory_data(StackID,ProductID,`Status`,Result,CreateDT)
                VALUES($dkId,$ProductId,1,0,CURRENT_TIMESTAMP);";
                        $pUpResult = mysql_query($sql, $link_id);
                        if ($pUpRow = mysql_fetch_array($pUpResult)) {
                            $UpSql = "UPDATE pd_g79  SET zt='1' WHERE Id='$Id'";
                            $UpResult = @mysql_query($UpSql);
                        } else {
                            $Log .= "<div class='redB'>$Id 失败.</div><br>";
                        }
                    }
                }
            }


        }
    } while ($CheckSplitRow = mysql_fetch_array($CheckSplitResult));
}
include "../model/logpage.php";
?>