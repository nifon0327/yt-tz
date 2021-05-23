<?php
//EWEN 2013-02-20 OK
include "../model/modelhead.php";
//步骤2：需处理
$tableMenuS = 400;
ChangeWtitle("$SubCompany 非bom配件申购初审列表");
$funFrom = "nonbom5";
$From = $From == "" ? "m" : $From;
$Th_Col = "选项|60|序号|40|客户项目|150|采购|80|供应商|130|配件分类|100|配件编码|50|配件名称|250|规格|150|申购备注|200|配件条码|100|货币|30|单价|50|本次申购|60|单位|40|金额|60|申购总数|60|在库|60|采购库存|60|最低库存|60|申购状态|50|申购时间|80|申购人|60";

$Pagination = $Pagination == "" ? 1 : $Pagination;
$Page_Size = 100;
$Estate = $Estate == "" ? 2 : $Estate;
$nowWebPage = $funFrom . "_m";
include "../model/subprogram/read_model_3.php";
if ($From != "slist") {
    $SearchRows = "A.Estate IN (2,3)";
    echo "<select name='MidSign' id='MidSign' onchange='ResetPage(this.name)'>";
    if ($MidSign == 0) {//未下单的审核
        echo "<option value='1' >已下单记录审核</option><option value='0' selected>未下单记录审核</option>";
        $SearchRows .= " AND A.Mid='0'";
        $MSearchRows = " ";
        $ActioToS .= "34,17";
    } else {//已下单的审核
        echo "<option value='1' selected>已下单记录审核</option><option value='0'>未下单记录审核</option>";
        $SearchRows .= " AND A.Mid>'0'";
        $ActioToS .= "3,17";
    }
    echo "</select>&nbsp;";
    /////////////////////////////////
    //采购选择
    $checkResult = mysql_query("SELECT A.BuyerId,D.Name 
							   FROM $DataIn.nonbom6_cgsheet A 
							   LEFT JOIN $DataPublic.staffmain D ON D.Number=A.BuyerId
							   WHERE $SearchRows GROUP BY A.BuyerId ORDER BY D.Name", $link_id);

    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='BuyerId' id='BuyerId' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>全部采购</option>";
        do {
            $BuyerIdTemp = $checkRow["BuyerId"];
            $BuyerIdName = $checkRow["Name"];
            if ($BuyerId == $BuyerIdTemp) {
                echo "<option value='$BuyerIdTemp' selected>$BuyerIdName</option>";
                $SearchRows .= " AND A.BuyerId='$BuyerIdTemp'";
            } else {
                echo "<option value='$BuyerIdTemp'>$BuyerIdName</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }
    //供应商
    $checkResult = mysql_query("SELECT  E.CompanyId,E.Forshort 
							   FROM $DataIn.nonbom6_cgsheet A 
							   LEFT JOIN $DataPublic.nonbom3_retailermain E ON E.CompanyId=A.CompanyId 
							   WHERE $SearchRows GROUP BY E.CompanyId ORDER BY E.CompanyId,E.Forshort", $link_id);
    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>全部供应商</option>";
        do {
            $CompanyIdTemp = $checkRow["CompanyId"];
            $ForshortTemp = $checkRow["Forshort"];
            if ($CompanyId == $CompanyIdTemp) {
                echo "<option value='$CompanyIdTemp' selected>$ForshortTemp</option>";
                $SearchRows .= " AND A.CompanyId='$CompanyIdTemp'";
            } else {
                echo "<option value='$CompanyIdTemp'>$ForshortTemp</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }


    //主分类

    $checkResult = mysql_query("SELECT  A.mainType,B.Name 
							   FROM $DataIn.nonbom6_cgsheet A
							   LEFT JOIN $DataPublic.nonbom1_maintype B ON B.ID=A.mainType
							   WHERE $SearchRows GROUP BY A.mainType ORDER BY A.mainType", $link_id);

    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='mainType' id='mainType' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>全部主分类</option>";
        do {
            $mainTypeTemp = $checkRow["mainType"];
            $mainNameTemp = $checkRow["Name"];
            if ($mainType == $mainTypeTemp) {
                echo "<option value='$mainType' selected>$mainNameTemp</option>";
                $SearchRows .= " AND A.mainType='$mainType'";
            } else {
                echo "<option value='$mainTypeTemp'>$mainNameTemp</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }

    ////////////////////////////////
}
//$ActioToS.=$Estate==2?",17":"";
/*if($BuyerId!="" && $CompanyId!="" && $mainType!=""){
	$ActioToS.=",128";
	}*/
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
$TitlePre = "<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 0);
$mySql = "SELECT A.Id,A.GoodsId,A.Qty,A.Price,(A.Qty*A.Price) AS Amount,A.Remark,A.Estate,A.Locks,A.Date,A.Operator,A.ForshortId,
B.TypeName,
C.Forshort,C.CompanyId,
D.GoodsName,D.BarCode,D.Unit,D.Attached,D.GoodSpec,
E.wStockQty,E.oStockQty,E.mStockQty,F.Name AS StaffName,G.Symbol,T.Forshort AS Company 
FROM $DataIn.nonbom6_cgsheet A
LEFT JOIN $DataPublic.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
LEFT JOIN $DataPublic.nonbom4_goodsdata D ON D.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=D.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock E ON E.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.staffmain F ON F.Number=A.BuyerId
LEFT JOIN $DataPublic.currencydata G ON G.Id=C.Currency 
LEFT JOIN $DataPublic.trade_object T ON T.Id = D.TradeId
WHERE $SearchRows ORDER BY A.Date DESC,A.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    $Dir = anmaIn("download/nonbom/", $SinkOrder, $motherSTR);
    do {
        $m = 1;
        $Id = $myRow["Id"];
        $GoodsId = $myRow["GoodsId"];
        $StaffName = $myRow["StaffName"];
        $GoodsName = $myRow["GoodsName"];
        $GoodSpec = $myRow["GoodSpec"];
        $BarCode = $myRow["BarCode"];
        $Remark = $myRow["Remark"] == "" ? "&nbsp;" : $myRow["Remark"];
        $TypeName = $myRow["TypeName"] == "" ? "&nbsp;" : $myRow["TypeName"];
        switch ($myRow["Estate"]) {
            case 1:
                $Estate = "<div class='greenB'>已审核</div>";
                break;
            case 2:
            case 3:
                $Estate = "<div class='redB'>未审核</div>";
                break;
            case 4:
                $Estate = "<div class='redB'>审核退回</div>";
                break;
        }
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
        $Unit = $myRow["Unit"];
        $Symbol = $myRow["Symbol"];
        $Price = sprintf("%.3f", $myRow["Price"]);
        $Qty = del0($myRow["Qty"]);
        $Amount = sprintf("%.3f", $myRow["Amount"]);
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
        //申购总数计算
        $checkQty = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom6_cgsheet WHERE Mid=0 AND GoodsId='$GoodsId' ", $link_id));
        $sgSUM = del0($checkQty["Qty"]);
        $ForshortId = $myRow["ForshortId"];
        $Company = $myRow["Company"];
        //配件分析
        $GoodsId = "<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
        $Locks = $myRow["Locks"];
        $ValueArray = array(
            array(0 => $Company, 1 => "align='center'"),
            array(0 => $StaffName, 1 => "align='center'"),
            array(0 => $Forshort, 1 => "align='center'"),
            array(0 => $TypeName),
            array(0 => $GoodsId, 1 => "align='center'"),
            array(0 => $GoodsName),
            array(0 => $GoodSpec),
            array(0 => $Remark),
            array(0 => $BarCode, 1 => "align='center'"),
            array(0 => $Symbol, 1 => "align='right'"),
            array(0 => $Price, 1 => "align='right'"),
            array(0 => $Qty, 1 => "align='right'"),
            array(0 => $Unit, 1 => "align='center'"),
            array(0 => $Amount, 1 => "align='right'"),
            array(0 => $sgSUM, 1 => "align='center'"),
            array(0 => $wStockQty, 1 => "align='center'"),
            array(0 => $oStockQty, 1 => "align='center'"),
            array(0 => $mStockQty, 1 => "align='center'"),
            array(0 => $Estate, 1 => "align='center'"),
            array(0 => $Date),
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