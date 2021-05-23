<?php
//电信-zxq 2012-08-01
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId = "ListTB" . $RowId;
$subTableWidth = 1230;

echo "<table id='$TableId'  cellspacing='0' border='0' align='center' bgcolor='#FFFFFF'>
    <tr style='background-color: #F0F5F8'>
	<td width='30' height='20' class='A1111'>序号</td>
	<td width='100' align='center' class='A1101'>待购流水号</td>
	<td width='320' align='center' class='A1101'>配件名称</td>
    <td width='45' align='center' class='A1101'>QC图</td>
	<td width='50' align='center' class='A1101'>历史订单</td>
	<td width='30' align='center' class='A1101'>单位</td>
	<td width='55' align='center' class='A1101'>订单数量</td>
    <td width='55' align='center' class='A1101'>在库</td>
	<td width='55' align='center' class='A1101'>已备料数</td>
	<td width='50' align='center' class='A1101'>生产数量</td>
	<td width='55' align='center' class='A1101'>存储位置</td>
	</tr>";


if ($fromPage == "finished") {

    $checkOrderRow = mysql_fetch_array(mysql_query("SELECT Qty FROM   $DataIn.yw1_ordersheet WHERE POrderId ='$POrderId'", $link_id));
    $Qty = $checkOrderRow["Qty"];

    $sListSql = "SELECT S.POrderId,ROUND(G.OrderQty*(S.Qty/$Qty),2) AS OrderQty,
	G.StockId,G.CompanyId,G.BuyerId,D.StuffId,D.StuffCname,D.Picture,
    D.Gfile,D.Gstate,D.TypeId,B.Name,C.Forshort,C.Currency,MP.Remark AS Position,
    ST.mainType,MT.TypeColor ,U.Name AS UnitName,K.tStockQty,G.blSign 
	FROM  $DataIn.yw1_scsheet  S 
	INNER JOIN $DataIn.cg1_stocksheet G ON G.POrderId = S.POrderId 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
	LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
	LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
	LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id=ST.mainType
	LEFT JOIN $DataIn.base_mposition MP ON MP.Id=D.SendFloor  
	LEFT JOIN $DataIn.staffmain B ON B.Number=G.BuyerId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=G.CompanyId
    LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
	WHERE S.sPOrderId='$sPOrderId' AND G.Level=1 ORDER BY G.blsign DESC,G.StockId";
}
else {


    $checkOrderRow = mysql_fetch_array(mysql_query("SELECT SC.Qty,(CG.addQty+CG.FactualQty) AS xdQty,SC.mStockId
	FROM  $DataIn.yw1_scsheet SC 
	LEFT  JOIN $DataIn.yw1_ordersheet    S  ON S.POrderId = SC.POrderId 
	LEFT  JOIN $DataIn.cg1_stocksheet    CG ON CG.StockId = SC.mStockId WHERE SC.sPOrderId ='$sPOrderId'", $link_id));
    $Qty = $checkOrderRow["Qty"];
    $xdQty = $checkOrderRow["xdQty"];
    $Relation = $Qty / $xdQty;
    $mStockId = $checkOrderRow["mStockId"];

    $sListSql = " SELECT G.POrderId,ROUND(A.OrderQty*$Relation,2) AS OrderQty,A.StockId,
		        G.CompanyId,G.BuyerId,D.StuffId,D.StuffCname,D.Picture,D.Gfile,D.Gstate,D.TypeId,F.Remark AS Position,M.Name,
		        P.Forshort,P.Currency,T.mainType,MT.TypeColor,U.Name AS UnitName,K.tStockQty,U.Decimals,G.blSign 
	            FROM  $DataIn.cg1_semifinished   A 
                INNER JOIN $DataIn.cg1_stocksheet G  ON G.StockId = A.StockId
				INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=A.StuffId 
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
				INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				INNER JOIN $DataIn.stuffmaintype MT ON MT.Id=T.mainType
				INNER JOIN $DataIn.stuffunit U ON U.Id=D.Unit
				LEFT JOIN  $DataIn.staffmain M ON M.Number=G.BuyerId 
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
				LEFT JOIN  $DataIn.base_mposition F ON F.Id=D.SendFloor
				WHERE  A.POrderId='$POrderId' AND A.mStockId='$mStockId' ORDER BY G.blsign DESC,G.StockId ";
}

$sListResult = mysql_query($sListSql, $link_id);
$i = 1;
$d = anmaIn("download/stufffile/", $SinkOrder, $motherSTR);
if ($StockRows = mysql_fetch_array($sListResult)) {
    do {
        $thisId = $StockRows["Id"];
        $StockId = $StockRows["StockId"];
        $StuffCname = $StockRows["StuffCname"];
        $Price = $StockRows["Price"];
        $Forshort = $StockRows["Forshort"];
        $Buyer = $StockRows["Name"];
        $BuyerId = $StockRows["BuyerId"];
        $OrderQty = $StockRows["OrderQty"];
        $Date = $StockRows["Date"];
        $StuffId = $StockRows["StuffId"];
        $Picture = $StockRows["Picture"];
        $TypeId = $StockRows["TypeId"];
        $mainType = $StockRows["mainType"];
        $TypeColor = $StockRows["TypeColor"];
        $Currency = $StockRows["Currency"];
        $tStockQty = $StockRows["tStockQty"];
        $UnitName = $StockRows["UnitName"] == "" ? "&nbsp;" : $StockRows["UnitName"];
        $Position = $StockRows["Position"] == "" ? "-" : $StockRows["Position"];
        $blSign = $StockRows["blSign"];

        $llQty = 0;
        $scQty = "-";
        //检查是否有图片
        include "../model/subprogram/stuffimg_model.php";
        include "../model/subprogram/stuff_Property.php";//配件属性


        if ($blSign == 1) {
            //备领料情况
            $llQty = 0;
            $llBgColor = "";
            $llEstate = "";

            $checkllQty = mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE sPOrderId = $sPOrderId AND StockId='$StockId'", $link_id);
            $llQty = mysql_result($checkllQty, 0, "llQty");
            $llQty = $llQty == "" ? 0 : $llQty;
            // 精度计算
            $llQty = round($llQty, 2);
            if ($llQty > $OrderQty) {//领料总数大于订单数,提示出错
                $llBgColor = " style='color:#FF0000;font-weight: bold;'";
            }
            else {
                if ($llQty == $OrderQty) {//刚好全领，绿色
                    $llBgColor = " style='color:#009900;font-weight: bold;'";
                }
                else {                //未领完，黄色
                    $llBgColor = " style='color:#FF6633;font-weight: bold;'";
                }
            }
            $llEstate = mysql_result($checkllQty, 0, "llEstate");
            $llEstate = $llEstate > 0 ? "★" : "";

            ///////////////////////////////////////////
            //库存数量
            if ($tStockQty >= $OrderQty - $llQty) {
                $tStockQty = "<div style='color:#009900;font-weight: bold;'>$tStockQty</div>";
            }
            else {
                $tStockQty = "<div style='color:#FF6633;font-weight: bold;'>$tStockQty</div>";
            }

            $llQty = $llQty == 0 ? "-" : $llQty;

        }
        else {
            $tStockQty = "-";
            $llQty = "-";
            $Position = "-";
        }

        $CheckscQty = mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty 
			FROM $DataIn.sc1_cjtj C 
			WHERE  C.sPOrderId = '$sPOrderId' AND C.StockId = $StockId ", $link_id));
        $scQty = $CheckscQty["scQty"];
        $scQty = $scQty == 0 ? "-" : $scQty;
        if ($scQty == $OrderQty) {
            $scQty = "<span class='greenB'>$scQty</span>";
        }
        else if ($scQty < $OrderQty && $scQty != 0) {
            $scQty = "<span class='yellowB'>$scQty</span>";
        }

        //配件QC检验标准图
        $QCImage = "";
        include "../model/subprogram/stuffimg_qcfile.php";
        $QCImage = $QCImage == "" ? "&nbsp;" : $QCImage;
        //历史订单
        $OrderQtyInfo = "<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>view</a>";
        //REACH 法规图
        //include "../model/subprogram/stuffreach_file.php";


        echo "<tr bgcolor='$TypeColor'>
			<td  align='center' height='20' class='A0111' bgcolor='$Sbgcolor'>$i</td>";//配件状态
        echo "<td class='A0101' align='center'>$StockId</td>";//采购流水号
        echo "<td class='A0101'>$StuffCname</td>";//配件名称
        echo "<td class='A0101' align='center'>$QCImage</td>";//QC图
        //echo"<td class='A0101' align='center'>$ReachImage</td>";//REACH
        echo "<td class='A0101' align='center'>$OrderQtyInfo</td>";//历史订单
        echo "<td class='A0101' align='center'>$UnitName</td>";
        echo "<td class='A0101' align='right' >$OrderQty</td>";//订单需求数量
        echo "<td class='A0101' align='right' >$tStockQty</td>";//在库
        echo "<td class='A0101' align='right' $llBgColor> $llEstate $llQty</td>";//领料数
        echo "<td class='A0101' align='center'>$scQty</td>";
        echo "<td class='A0101' align='center'>$Position</td>";//仓库位置
        echo "</tr>";
        $i++;
    } while ($StockRows = mysql_fetch_array($sListResult));
}
echo "</table>";
?>