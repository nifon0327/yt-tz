<?php
//EWEN 2013-02-19 OK
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber = 24;
$tableMenuS = 400;
ChangeWtitle("$SubCompany 非bom配件资料");
$funFrom = "nonbom4";
$From = $From == "" ? "m" : $From;
$Th_Col = "选项|40|序号|40|配件编号|60|配件名称|300|规格|150|配件分类|100|采购|60|默认供应商|100|单位|40|货币|30|单价|60|在库|60|实际库存|60|最低库存|60|入库地点|70|使用年限|70|盘点时间|70|内部维修人|80|外部维修公司|80|内部保养人|80|外部保养公司|80|备注|150|款项收回<br>条件(PCS)|80|下单数量|80|关联BOM供应商|150|可用状态|50|更新日期|80|操作员|60";

$Pagination = $Pagination == "" ? 0 : $Pagination;
$Page_Size = 100;
$ActioToS = "17,34";
$nowWebPage = $funFrom . "_m";
include "../model/subprogram/read_model_3.php";
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
$TitlePre = "<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 0);
$mySql = "SELECT A.Id,A.GoodsId,A.GoodsName,A.GoodSpec,A.BarCode,A.Price,A.Unit,A.Attached,A.Estate,A.Locks,A.Date,A.Operator,B.TypeName,C.wStockQty,
C.oStockQty,C.mStockQty,D.Forshort,D.CompanyId,E.Name AS Buyer,F.Symbol,K.Name AS rkName,N.Frequency AS nxFrequency,X.Frequency AS pdFrequency 
FROM $DataPublic.nonbom4_goodsdata A
LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=A.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain E ON E.Number=B.BuyerId
LEFT JOIN $DataPublic.currencydata F ON F.Id=D.Currency
LEFT JOIN $DataPublic.nonbom0_ck K ON K.Id=A.CkId
LEFT JOIN $DataPublic.nonbom6_nx  N ON N.Id=A.nxId
LEFT JOIN $DataPublic.nonbom6_nx X  ON X.Id=A.pdDate
WHERE A.Estate='2' $SearchRows AND (B.cSign='0' OR B.cSign='$Login_cSign') ORDER BY A.Estate DESC,A.Date DESC,A.GoodsId DESC";
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    $Dir = anmaIn("download/nonbom/", $SinkOrder, $motherSTR);
    do {
        $m = 1;
        $Id = $myRow["Id"];
        $GoodsId = $myRow["GoodsId"];
        $GoodsName = $myRow["GoodsName"];
        $GoodSpec = $myRow["GoodSpec"];
        $BarCode = $myRow["BarCode"];
        $TypeName = $myRow["TypeName"] == "" ? "&nbsp;" : $myRow["TypeName"];
        $Estate = "<div class='redB'>未审核</div>";
        $Date = $myRow["Date"];
        $Operator = $myRow["Operator"];
        include "../model/subprogram/staffname.php";
        $Attached = $myRow["Attached"];
        if ($Attached == 1) {
            $Attached = $GoodsId . ".jpg";
            $Attached = anmaIn($Attached, $SinkOrder, $motherSTR);
            $GoodsName = "<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
        }
        include "../model/subprogram/good_Property.php";//非BOM配件属性
        $Symbol = $myRow["Symbol"];
        $Price = $myRow["Price"];
        $Unit = $myRow["Unit"];
        $Buyer = $myRow["Buyer"];
        $Forshort = $myRow["Forshort"];
        $wStockQty = del0($myRow["wStockQty"]);
        $oStockQty = del0($myRow["oStockQty"]);
        $mStockQty = del0($myRow["mStockQty"]);
        $CompanyId = $myRow["CompanyId"];
        //加密
        $CompanyId = anmaIn($CompanyId, $SinkOrder, $motherSTR);
        $Forshort = "<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
        //历史单价
        $Price = "<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
        //配件分析
        $GoodsId = "<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
        $Locks = $myRow["Locks"];
        $rkName = $myRow["rkName"] == "" ? "&nbsp;" : $myRow["rkName"];
        $nxFrequency = $myRow["nxFrequency"] == "" ? "&nbsp;" : $myRow["nxFrequency"];
        $pdFrequency = $myRow["pdFrequency"] == "" ? "&nbsp;" : $myRow["pdFrequency"];

        //************************************************
        $WxNumber = $myRow["WxNumber"];
        if ($WxNumber > 0 || $Property2 == 1) {
            $CheckWxNumberResult = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$WxNumber", $link_id));
            $WxName = $CheckWxNumberResult["Name"];
            $WxNumber = $WxName;
        } else {
            $WxNumber = "&nbsp;";
        }
        //************************************************
        $WxCompanyId = $myRow["WxCompanyId"];
        if ($WxCompanyId > 0 || $Property3 == 1) {
            $CheckWxCompanyResult = mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$WxCompanyId", $link_id));
            $WxForshort = $CheckWxCompanyResult["Forshort"];
            $WxCompanyId = $WxForshort;
        } else {
            $WxCompanyId = "&nbsp;";
        }
        //************************************************
        $ByNumber = $myRow["ByNumber"];//内部保养人
        if ($ByNumber > 0 || $Property4 == 1) {
            $CheckByNumberResult = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$ByNumber", $link_id));
            $ByName = $CheckByNumberResult["Name"];
            $ByNumber = $ByName;
        } else {
            $ByNumber = "&nbsp;";
        }
        //************************************************
        $ByCompanyId = $myRow["ByCompanyId"];
        if ($ByCompanyId > 0 || $Property5 == 1) {//外部保养公司
            $CheckByCompanyResult = mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$ByCompanyId", $link_id));
            $ByForshort = $CheckByCompanyResult["Forshort"];
            $ByCompanyId = $ByForshort;
        } else {
            $ByCompanyId = "&nbsp;";
        }
        include "nonbom4_bomgetAmount.php"; //非BOM配件（模具部分，订单达到一定数量时，要退回模具费用，做供应商扣款)
        $ValueArray = array(
            array(0 => $GoodsId, 1 => "align='center'"),
            array(0 => $GoodsName),
            array(0 => $GoodSpec),
            array(0 => $TypeName),
            array(0 => $Buyer, 1 => "align='center'"),
            array(0 => $Forshort),
            array(0 => $Unit, 1 => "align='center'"),
            array(0 => $Symbol, 1 => "align='center'"),
            array(0 => $Price, 1 => "align='right'"),
            array(0 => $wStockQty, 1 => "align='right'"),
            array(0 => $oStockQty, 1 => "align='right'"),
            array(0 => $mStockQty, 1 => "align='right'"),
            array(0 => $rkName, 1 => "align='center'"),
            array(0 => $nxFrequency, 1 => "align='center'"),
            array(0 => $pdFrequency, 1 => "align='center'"),
            array(0 => $WxNumber, 1 => "align='left'"),
            array(0 => $WxCompanyId, 1 => "align='left'"),
            array(0 => $ByNumber, 1 => "align='left'"),
            array(0 => $ByCompanyId, 1 => "align='left'"),
            array(0 => $Remark),
            array(0 => $GetQty, 1 => "align='center'"),
            array(0 => $ProductOrderQty, 1 => "align='center'"),
            array(0 => $BOMCompanyStr),
            array(0 => $Estate, 1 => "align='center'"),
            array(0 => $Date, 1 => "align='center'"),
            array(0 => $Operator, 1 => "align='center'")
        );
        $checkidValue = $Id;
        include "../model/subprogram/read_model_6.php";
    } while ($myRow = mysql_fetch_array($myResult));
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