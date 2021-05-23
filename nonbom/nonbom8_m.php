<?php
//EWEN 2013-03-01 OK
include "../model/modelhead.php";
//步骤2：需处理
$tableMenuS = 400;
ChangeWtitle("$SubCompany 非bom配件申领审核");
$funFrom = "nonbom8";
$From = $From == "" ? "m" : $From;
$Th_Col = "选项|40|序号|40|申领日期|100|使用地点|60|分类|100|客户项目|150|编码|60|配件名称|250|规格|150|条码|100|单位|40|申领数量|60|备注|300|状态|40|申领人|60";
$Pagination = $Pagination == "" ? 1 : $Pagination;
$Page_Size = 100;
$ActioToS = "17,34";
$sumCols = "8";            //求和列,需处理
$nowWebPage = $funFrom . "_m";

include "../model/subprogram/read_model_3.php";
if ($From != "slist") {
    $SearchRows = "";
    //月份查询
}

echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
$TitlePre = "<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 0);
$mySql = "SELECT A.Id,A.GoodsId,A.Qty,A.Remark,A.Estate,A.Locks,A.Date,A.Operator,
B.GoodsName,B.BarCode,B.Attached,B.Unit,B.GoodSpec,
C.TypeName,
D.wStockQty,D.oStockQty,D.mStockQty ,E.Name AS WorkAdd,T.Forshort AS Company
FROM $DataIn.nonbom8_outsheet A
LEFT JOIN $DataPublic.nonbom4_goodsdata B ON B.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom2_subtype C  ON C.Id=B.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock D ON D.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom0_ck  E ON E.Id=A.WorkAdd AND E.TypeId IN (0,2)
LEFT JOIN $DataPublic.trade_object T ON T.Id = B.TradeId
WHERE A.Estate='2' $SearchRows ORDER BY A.Date DESC,A.Id DESC";

$myResult = mysql_query($mySql . " $PageSTR", $link_id);
$QtySum = 0;
if ($myRow = mysql_fetch_array($myResult)) {
    $Dir = anmaIn("download/zwwp/", $SinkOrder, $motherSTR);
    do {
        $m = 1;
        $Id = $myRow["Id"];
        $Date = $myRow["Date"];
        $TypeName = $myRow["TypeName"];
        $GoodsId = $myRow["GoodsId"];
        $GoodsName = $myRow["GoodsName"];
        $GoodSpec = $myRow["GoodSpec"];
        $Attached = $myRow["Attached"];
        $BarCode = $myRow["BarCode"];
        $Unit = $myRow["Unit"];
        $WorkAdd = $myRow["WorkAdd"];
        $Qty = $myRow["Qty"];
        $QtySum += $Qty;
        $Remark = $myRow["Remark"] == "" ? "&nbsp;" : $myRow["Remark"];
        $Operator = $myRow["Operator"];
        include "../model/subprogram/staffname.php";

        if ($Attached == 1) {
            $Attached = $GoodsId . ".jpg";
            $Attached = anmaIn($Attached, $SinkOrder, $motherSTR);
            $GoodsName = "<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
        }
        include "../model/subprogram/good_Property.php";//非BOM配件属性
        $wStockQty = $myRow["wStockQty"];
        $oStockQty = $myRow["oStockQty"];
        $mStockQty = $myRow["mStockQty"];
        $Estate = "<span class='redB'>未审核</span>";
        $Locks = $myRow["Locks"];
        $Company = $myRow["Company"];
        //配件分析
        $GoodsId = "<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
        $ValueArray = array(
            array(0 => $Date, 1 => "align='center'"),
            array(0 => $WorkAdd),
            array(0 => $TypeName),
            array(0 => $Company),
            array(0 => $GoodsId, 1 => "align='center'"),
            array(0 => $GoodsName),
            array(0 => $GoodSpec),
            array(0 => $BarCode, 1 => "align='center'"),
            array(0 => $Unit, 1 => "align='center'"),
            array(0 => $Qty, 1 => "align='right'"),
            array(0 => $Remark),
            array(0 => $Estate, 1 => "align='center'"),
            array(0 => $Operator, 1 => "align='center'")
        );
        $checkidValue = $Id;
        include "../model/subprogram/read_model_6.php";
    } while ($myRow = mysql_fetch_array($myResult));
    //合计

} else {
    noRowInfo($tableWidth);
}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>