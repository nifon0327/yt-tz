<?php
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

echo "<table stype='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' width='430' cellspacing='0' border='0'>
    <tr bgcolor='#F0F5F8'>
    <td width='50' align='center' class='A1101'>栋号</td>
    <td width='50' align='center' class='A1101'>楼层</td>
    <td width='100' align='center' class='A1101'>项目名称</td>
    <td width='150' align='center' class='A1101'>构件类别</td>
    <td width='50' align='center' class='A1101'>数量</td>
    </tr>";
$ordercolor = 3;
$sListResult = mysql_query("SELECT DISTINCT P.BuildingNo, P.FloorNo, TT.Forshort, P.CmptNo as CmptType, COUNT(SP.Qty) AS gross  FROM $DataIn.ch1_shipsplit SP
        INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SP.POrderId 
        INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
        INNER JOIN $DataIn.trade_object TT ON TT.CompanyId	= P.CompanyId
        WHERE S.Estate > 0 AND SP.Estate = 1 AND S.SeatId ='$SeatId' AND TT.CompanyId = '$CompanyId' 
        GROUP BY P.BuildingNo, P.FloorNo, TT.Forshort, P.CmptNo", $link_id);
$aaa = "SELECT DISTINCT TD.BuildingNo, TD.FloorNo, TT.Forshort, TD.CmptType, COUNT(SP.Qty) AS gross  FROM $DataIn.ch1_shipsplit SP
        INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SP.POrderId 
        INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
        INNER JOIN $DataIn.trade_drawing TD ON concat_ws('-',TD.BuildingNo,TD.FloorNo,TD.CmptNo,TD.SN) =P.cName 
        INNER JOIN $DataIn.trade_object TT ON TT.Id = TD.TradeId
        WHERE S.Estate > 0 AND SP.Estate = 1 AND S.SeatId ='$SeatId' AND TT.CompanyId = '$CompanyId' 
        GROUP BY TD.BuildingNo, TD.FloorNo, TT.Forshort, TD.CmptType";
$i = 1;
if ($TypeRows = mysql_fetch_array($sListResult)) {

    $BuildingNo = $TypeRows["BuildingNo"];
    $FloorNo = $TypeRows["FloorNo"];
    $Forshort = $TypeRows["Forshort"];
    $CmptTypes = "";
    $gross = "";

    do {

        $BuildingNo1 = $TypeRows["BuildingNo"];
        $FloorNo1 = $TypeRows["FloorNo"];
        $CmptType1 = $TypeRows["CmptType"];
        $gross1 = $TypeRows['gross'];

        if ($BuildingNo == $BuildingNo1 && $FloorNo == $FloorNo1) {

            //相同的统计
            if ($CmptTypes == "" || $gross == "") {
                $CmptTypes = $CmptType1;
                $gross = $gross1;
            } else {
                $CmptTypes = $CmptTypes . ';' . $CmptType1;
                $gross = $gross+$gross1;
            }
        } else {

            echo "<tr bgcolor='#FFFFFF'><td align='center' height='20' class='A0111'>$BuildingNo</td>";
            echo "<td align='center' class='A0101'>$FloorNo</td>";
            echo "<td class='A0101'>$Forshort</td>";
            echo "<td class='A0101'>$CmptTypes</td>";
            echo "<td class='A0101'>$gross</td>";
            echo "</tr>";

            $BuildingNo = $BuildingNo1;
            $FloorNo = $FloorNo1;
            $CmptTypes = $CmptType1;
            $gross = $gross1;
        }

        $i++;
    } while ($TypeRows = mysql_fetch_array($sListResult));

    echo "<tr bgcolor='#FFFFFF'><td align='center' height='20' class='A0111'>$BuildingNo</td>";
    echo "<td align='center' class='A0101'>$FloorNo</td>";
    echo "<td class='A0101'>$Forshort</td>";
    echo "<td class='A0101'>$CmptTypes</td>";
    echo "<td class='A0101'>$gross</td>";
    echo "</tr>";
}

echo "</table>";

?>