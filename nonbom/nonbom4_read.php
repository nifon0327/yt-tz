<style type="text/css">
    <!--
    .moveLtoR {
        filter: revealTrans(Transition=6, Duration=0.3)
    }

    .moveRtoL {
        filter: revealTrans(Transition=7, Duration=0.3)
    }

    /* 为 DIV 加阴影 */
    .out {
        position: relative;
        background: #006633;
        margin: 10px auto;
        width: 400px;
    }

    .in {
        background: #FFFFE6;
        border: 1px solid #555;
        padding: 10px 5px;
        position: relative;
        top: -5px;
        left: -5px;
    }

    /* 为 图片 加阴影 */
    .imgShadow {
        position: relative;
        background: #bbb;
        margin: 10px auto;
        width: 220px;
    }

    .imgContainer {
        position: relative;
        top: -5px;
        left: -5px;
        background: #fff;
        border: 1px solid #555;
        padding: 0;
    }

    .imgContainer img {
        display: block;
    }

    .glow1 {
        filter: glow(color=#FF0000, strengh=2)
    }

    .list {
        position: relative;
        color: #FF0000;
    }

    .list span img { /*CSS for enlarged image*/
        border-width: 0;
        padding: 2px;
        width: 100px;
    }

    .list span {
        position: absolute;
        padding: 3px;
        border: 1px solid gray;
        visibility: hidden;
        background-color: #FFFFFF;
    }

    .list:hover {
        background-color: transparent;
    }

    .list:hover span {
        visibility: visible;
        top: 0;
        left: 28px;
    }

    -->
</style>
<?php
//EWEN 2013-02-18 OK
include "../model/modelhead.php";
//步骤2：需处理
ChangeWtitle("$SubCompany 非bom配件资料");
$funFrom = "nonbom4";
$From = $From == "" ? "read" : $From;
$Th_Col = "选项|50|序号|30|客户项目|150|配件编号|50|配件名称|250|规格|100|APP图|35|配件分类|100|品牌|120|采购|60|默认供应商|180|单位|40|货币|30|默认单价|60|在库|60|采购库存|60|最低库存|60|资产类型|70|折旧期|70|残值率|70|入库地点|70|使用年限|70|盘点时间|70|内部维修人|80|外部维修公司|80|内部保养人|80|外部保养公司|80|备注|150|款项收回<br>条件(PCS)|80|关联BOM供应商|250|可用状态|50|更新日期|70|操作员|50";

$Pagination = $Pagination == "" ? 1 : $Pagination;
$Page_Size = 100;
$ActioToS = "1,2,3,4,5,6,7,8,13,40";
$nowWebPage = $funFrom . "_read";
include "../model/subprogram/read_model_3.php";
if ($From != "slist") {    //非查询：过滤采购、结付方式、供应商、月份
    $SearchRows = "";
    //采购选择
    $checkResult = mysql_query("SELECT B.BuyerId,C.Name FROM $DataPublic.nonbom4_goodsdata A LEFT JOIN $DataPublic.nonbom2_subtype B ON B.Id=A.TypeId LEFT JOIN $DataPublic.staffmain C ON C.Number=B.BuyerId GROUP BY B.BuyerId ORDER BY C.Name", $link_id);
    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='BuyerId' id='BuyerId' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>全部采购</option>";
        do {
            $BuyerIdTemp = $checkRow["BuyerId"];
            $BuyerIdName = $checkRow["Name"];
            if ($BuyerIdTemp != "") {
                if ($BuyerId == $BuyerIdTemp) {
                    echo "<option value='$BuyerIdTemp' selected>$BuyerIdName</option>";
                    $SearchRows .= " AND B.BuyerId='$BuyerIdTemp'";
                } else {
                    echo "<option value='$BuyerIdTemp'>$BuyerIdName</option>";
                }
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }
    //分类选择
    $checkResult = mysql_query("SELECT  A.TypeId,B.TypeName FROM $DataPublic.nonbom4_goodsdata A LEFT JOIN $DataPublic.nonbom2_subtype B ON B.Id=A.TypeId WHERE 1 $SearchRows AND (B.cSign='0' OR B.cSign='$Login_cSign') GROUP BY A.TypeId ORDER BY A.TypeId,B.TypeName", $link_id);
    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='TypeId' id='TypeId' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>全部分类</option>";
        do {
            $TypeIdTemp = $checkRow["TypeId"];
            $TypeNameTemp = $checkRow["TypeName"];
            if ($TypeId == $TypeIdTemp) {
                echo "<option value='$TypeIdTemp' selected>$TypeNameTemp </option>";
                $SearchRows .= " AND A.TypeId='$TypeIdTemp'";
            } else {
                echo "<option value='$TypeIdTemp'>$TypeNameTemp</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }
    //供应商
    $checkResult = mysql_query("SELECT  C.CompanyId,D.Forshort,D.Letter FROM $DataPublic.nonbom4_goodsdata A LEFT JOIN $DataPublic.nonbom2_subtype B ON B.Id=A.TypeId LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=C.CompanyId WHERE 1 $SearchRows and C.CompanyId>0 GROUP BY C.CompanyId ORDER BY D.Letter,D.Forshort", $link_id);
    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>全部供应商</option>";
        do {
            $CompanyIdTemp = $checkRow["CompanyId"];
            $ForshortTemp = $checkRow["Letter"] . "-" . $checkRow["Forshort"];
            if ($CompanyId == $CompanyIdTemp) {
                echo "<option value='$CompanyIdTemp' selected>$ForshortTemp</option>";
                $SearchRows .= " AND C.CompanyId='$CompanyIdTemp'";
            } else {
                echo "<option value='$CompanyIdTemp'>$ForshortTemp</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }
     //审核
    $checkResult = mysql_query("SELECT DISTINCT Estate FROM $DataPublic.nonbom4_goodsdata ", $link_id);
    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='sEstate' id='sEstate' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>全部状态</option>";
        do {
            $thisEstate = $checkRow["Estate"];
            $selected = $thisEstate == $sEstate ? 'selected':'';
            switch ($thisEstate) {
                case 0:
                    echo "<option value='$thisEstate' $selected>审核失败</option>";
                    break;
                case 1:
                    echo "<option value='$thisEstate' $selected>审核通过</option>";
                    break;
                case 2:
                    echo "<option value='$thisEstate' $selected>未审核</option>";
                    break;
                case 3:
                    echo "<option value='$thisEstate' $selected>审核退回</option>";
                    break;
            }
            if ($thisEstate == $sEstate) {

                $SearchRows .= " AND A.Estate='$thisEstate'";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }
    //款项收回配件
    $GetQtySign = $GetQtySign == "" ? 0 : $GetQtySign;
    $GetQtyStr = "GetQtySign" . $GetQtySign;
    $$GetQtyStr = "selected";
    echo "<select name='GetQtySign' id='GetQtySign' onchange='ResetPage(this.name)'>";
    echo "<option value='0' $GetQtySign0>全部</option>";
    echo "<option value='1' $GetQtySign1>款项收回配件</option>";
    echo "<option value='2' $GetQtySign2>半年未下单配件</option>";
    echo "</select>&nbsp;";
    switch ($GetQtySign) {
        case 1:
            $SearchRows .= " AND  A.GetQty>0";
            break;
        case 2:
            $SearchRows .= " AND NOT EXISTS (SELECT G.GoodsId FROM $DataIn.nonbom6_cgsheet G LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=G.Mid WHERE G.GoodsId=A.GoodsId AND DATEDIFF(CURDATE(),B.Date)<180) AND NOT EXISTS (SELECT G.GoodsId FROM $DataOut.nonbom6_cgsheet G  LEFT JOIN $DataIn.nonbom6_cgmain B ON B.Id=G.Mid WHERE G.GoodsId=A.GoodsId AND DATEDIFF(CURDATE(),B.Date)<180)";
            break;
    }
}
//<a href='nonbom4_error.php' target='_blank'>错误配件分析</a>
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr ";
include "../model/subprogram/read_model_5.php";
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 0);
$mySql = "SELECT A.Id,A.GoodsId,A.GoodsName,A.GoodSpec,A.Price,A.brand,A.Unit,A.ReturnReasons,A.Attached,A.AppIcon,A.Estate,A.Locks,A.Date,A.Operator,B.TypeName,C.wStockQty,C.oStockQty,C.mStockQty,D.Forshort,D.CompanyId,E.Name AS Buyer,F.Symbol,A.Remark ,K.Name AS rkName,N.Frequency AS nxFrequency,X.Frequency AS pdFrequency,A.ByNumber,A.ByCompanyId,A.WxNumber,A.WxCompanyId,A.GetQty,A.GetSign,B.mainType,A.Introduction,A.Salvage,T.Name AS AssetTypeName,DP.Depreciation,TT.Forshort AS Company 
FROM $DataPublic.nonbom4_goodsdata A
LEFT JOIN $DataPublic.nonbom2_subtype B  ON B.Id=A.TypeId
LEFT JOIN $DataPublic.nonbom5_goodsstock C ON C.GoodsId=A.GoodsId
LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=C.CompanyId
LEFT JOIN $DataPublic.staffmain E ON E.Number=B.BuyerId
LEFT JOIN $DataPublic.currencydata F ON F.Id=D.Currency
LEFT JOIN $DataPublic.nonbom0_ck K ON K.Id=A.CkId
LEFT JOIN $DataPublic.nonbom6_nx  N ON N.Id=A.nxId
LEFT JOIN $DataPublic.nonbom6_nx X  ON X.Id=A.pdDate
LEFT JOIN $DataPublic.nonbom0_assettype T  ON T.Id=A.AssetType
LEFT JOIN $DataPublic.nonbom6_depreciation DP  ON DP.Id=A.DepreciationId
LEFT JOIN $DataPublic.trade_object TT ON TT.Id = A.TradeId
WHERE 1 $SearchRows AND (B.cSign='0' OR B.cSign='$Login_cSign') ORDER BY A.Estate DESC,A.Date DESC,A.GoodsId DESC";
//echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    $Dir = anmaIn("download/nonbom/", $SinkOrder, $motherSTR);
    $Dir_intro = anmaIn("download/nobom_intro/", $SinkOrder, $motherSTR);
    do {
        $m = 1;
        $Id = $myRow["Id"];
        $GoodsId = $myRow["GoodsId"];
        $GoodSpec = $myRow["GoodSpec"];
        $brand = $myRow["brand"];
        $GoodsName = $myRow["GoodsName"];
        $mainType = $myRow["mainType"];
        $TypeName = $myRow["TypeName"] == "" ? "&nbsp;" : $myRow["TypeName"];
        $Remark = $myRow["Remark"] == "" ? "&nbsp;" : $myRow["Remark"];
        switch ($myRow["Estate"]) {
            case 0:
                $Estate = "<div class='redB'>×</div>";
                break;
            case 1:
                $Estate = "<div class='greenB'>√</div>";
                break;
            case 2:
                $Estate = "<div class='redB'>未审核</div>";
                break;
            case 3:
                $ReturnReasons = $myRow["ReturnReasons"] == "" ? "未填写退回原因" : $myRow["ReturnReasons"];
                $Estate = "<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
                break;
        }
        $Date = $myRow["Date"];
        $Operator = $myRow["Operator"];
        include "../model/subprogram/staffname.php";
        $Attached = $myRow["Attached"];
        $Introduction = $myRow["Introduction"];
        if ($Attached == 1) {
            $Attached = $GoodsId . ".jpg";
            if ($Introduction != "") {
                $Attached = $Introduction;
// 			
                $Attached = anmaIn($Attached, $SinkOrder, $motherSTR);

                //echo("\n$Dir_intro<br/>$Attached");

                $GoodsName = "<span onClick='OpenOrLoad(\"$Dir_intro\",\"$Attached\",\"\",\"intro\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
            } else {
                $Attached = anmaIn($Attached, $SinkOrder, $motherSTR);
                $GoodsName = "<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
            }


        }

        $AppIcon = $myRow["AppIcon"];
        if ($AppIcon == 1) {
            $AppFilePath = "../download/nonbom/" . $GoodsId . "_s.png";
            $noStatue = "onMouseOver=\"window.status='none';return true\"";
            $AppFileSTR = "<span class='list' >View<span><img src='$AppFilePath' $noStatue/></span></span>";
        } else {
            $AppFileSTR = "&nbsp;";
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

        $Company = $myRow["Company"];
        $CompanyId = $myRow["CompanyId"];
        //加密
        $CompanyId = anmaIn($CompanyId, $SinkOrder, $motherSTR);
        $Forshort = "<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
        //历史单价
        $Price = "<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
        //配件分析
        $GoodsIdStr = "<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
        $Locks = $myRow["Locks"];
        $rkName = $myRow["rkName"] == "" ? "&nbsp;" : $myRow["rkName"];
        $nxFrequency = $myRow["nxFrequency"] == "" ? "&nbsp;" : $myRow["nxFrequency"];
        $pdFrequency = $myRow["pdFrequency"] == "" ? "&nbsp;" : $myRow["pdFrequency"];
        $AssetTypeName = $myRow["AssetTypeName"] == "" ? "&nbsp;" : $myRow["AssetTypeName"];
        $Salvage = $myRow["Salvage"] == "" ? "&nbsp;" : $myRow["Salvage"];
        $Depreciation = $myRow["Depreciation"] == "" ? "&nbsp;" : $myRow["Depreciation"];
        //************************************************
        $PassId = "";
        $WxNumber = $myRow["WxNumber"];
        if ($WxNumber > 0 || $Property2 == 1) {
            $CheckWxNumberResult = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$WxNumber", $link_id));
            $WxName = $CheckWxNumberResult["Name"];
            $PassId = $GoodsId . "|" . $WxNumber . "|2";
            $WxNumber = "<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom4_upMaintainer\",\"$PassId\")' src='../images/edit.gif' title='内部维修人操作' width='13' height='13'>$WxName";
        } else {
            $WxNumber = "&nbsp;";
        }
        //************************************************
        $PassId = "";
        $WxCompanyId = $myRow["WxCompanyId"];
        if ($WxCompanyId > 0 || $Property3 == 1) {
            $CheckWxCompanyResult = mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$WxCompanyId", $link_id));
            $WxForshort = $CheckWxCompanyResult["Forshort"];
            $PassId = $GoodsId . "|" . $WxCompanyId . "|3";
            $WxCompanyId = "<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom4_upMaintainer\",\"$PassId\")' src='../images/edit.gif' title='外部保养公司操作' width='13' height='13'>$WxForshort";
        } else {
            $WxCompanyId = "&nbsp;";
        }
        //************************************************
        $PassId = "";
        $ByNumber = $myRow["ByNumber"];//内部保养人
        if ($ByNumber > 0 || $Property4 == 1) {
            $PassId = $GoodsId . "|" . $ByNumber . "|4";
            $CheckByNumberResult = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$ByNumber", $link_id));
            $ByName = $CheckByNumberResult["Name"];
            $ByNumber = "<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom4_upMaintainer\",\"$PassId\")' src='../images/edit.gif' title='内部保养人操作' width='13' height='13'>$ByName";
        } else {
            $ByNumber = "&nbsp;";
        }
        //************************************************
        $PassId = "";
        $ByCompanyId = $myRow["ByCompanyId"];
        if ($ByCompanyId > 0 || $Property5 == 1) {//外部保养公司
            $PassId = $GoodsId . "|" . $ByCompanyId . "|5";
            $CheckByCompanyResult = mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataPublic.nonbom3_retailermain WHERE CompanyId=$ByCompanyId", $link_id));
            $ByForshort = $CheckByCompanyResult["Forshort"];
            $ByCompanyId = "<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom4_upMaintainer\",\"$PassId\")' src='../images/edit.gif' title='外部保养公司操作' width='13' height='13'>$ByForshort";
        } else {
            $ByCompanyId = "&nbsp;";
        }
        include "nonbom4_bomgetAmount.php"; //非BOM配件（模具部分，订单达到一定数量时，要退回模具费用，做供应商扣款)

        $ValueArray = array(
            array(0 => $Company, 1 => "align='center'"),
            array(0 => $GoodsIdStr, 1 => "align='center'"),
            array(0 => $GoodsName),
            array(0 => $GoodSpec),
            array(0 => $AppFileSTR, 1 => "align='center'"),
            array(0 => $TypeName),
            array(0 => $brand, 1 => "align='center'"),
            array(0 => $Buyer, 1 => "align='center'"),
            array(0 => $Forshort),
            array(0 => $Unit, 1 => "align='center'"),
            array(0 => $Symbol, 1 => "align='center'"),
            array(0 => $Price, 1 => "align='right'"),
            array(0 => $wStockQty, 1 => "align='right'"),
            array(0 => $oStockQty, 1 => "align='right'"),
            array(0 => $mStockQty, 1 => "align='right'"),
            array(0 => $AssetTypeName, 1 => "align='center'"),
            array(0 => $Depreciation, 1 => "align='center'"),
            array(0 => $Salvage, 1 => "align='center'"),
            array(0 => $rkName, 1 => "align='center'"),
            array(0 => $nxFrequency, 1 => "align='center'"),
            array(0 => $pdFrequency, 1 => "align='center'"),
            array(0 => $WxNumber, 1 => "align='left'"),
            array(0 => $WxCompanyId, 1 => "align='left'"),
            array(0 => $ByNumber, 1 => "align='left'"),
            array(0 => $ByCompanyId, 1 => "align='left'"),
            array(0 => $Remark),
            array(0 => $GetQty, 1 => "align='center'"),
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