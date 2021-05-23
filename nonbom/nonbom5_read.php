<?php
include "../model/modelhead.php";
//步骤2：需处理
ChangeWtitle("$SubCompany 非bom配件申购明细");
$funFrom = "nonbom5";
$From = $From == "" ? "read" : $From;
$Th_Col = "选项|40|序号|30|客户项目|150|采购|50|供应商|150|配件分类|100|配件编码|60|配件名称|300|规格|150|申购备注|200|货币|30|增值税率|60|单价|70|最低单价|70|本次申购|60|单位|40|金额|70|申购总数|60|在库|60|采购库存|60|最低库存|60|申购状态|50|申购时间|70|申购人|60";
$Pagination = $Pagination == "" ? 1 : $Pagination;
$singel = $singel == "" ? 0 : $singel;
$Page_Size = 100;
$ActioToS = $AuditState == 4 ? "1,2,3,4,7,8,51,52" : "1,2,3,4,7,8,51";//默认有生成采购单，审核退回多一个菜单

$nowWebPage = $funFrom . "_read";
include "../model/subprogram/read_model_3.php";
$SearchRows .= " AND A.Mid='0'";
if ($singel == 1) {  //个人申请申购
    $SearchRows .= " AND A.Operator=$Login_P_Number";
}
if ($From != "slist") {
    $TempAuditStateSTR = "AuditStateSTR" . strval($AuditState);
    $$TempAuditStateSTR = "selected";
    echo "<select name='AuditState' id='AuditState' onchange='ResetPage(this.name)'>
	<option value='' $AuditStateSTR>全  部</option>
	<option value='1' $AuditStateSTR1>已审核</option>
	<option value='2' $AuditStateSTR2>未审核</option>
	<option value='4' $AuditStateSTR4>审核退回</option>
	</select>&nbsp;";//取消初审，终审
    //采购选择
    if ($AuditState > 0) {
        if ($AuditState == 2) {
            $SearchRows .= " AND A.Estate IN (2,3)";
        } else {
            $SearchRows .= " AND A.Estate='$AuditState'";
        }
    }

    $checkResult = mysql_query("SELECT A.BuyerId,D.Name 
							   FROM $DataIn.nonbom6_cgsheet A 
							   LEFT JOIN $DataPublic.staffmain D ON D.Number=A.BuyerId 
							   WHERE 1 $SearchRows GROUP BY A.BuyerId ORDER BY D.Name", $link_id);

    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='BuyerId' id='BuyerId' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>全部采购</option>";
        do {
            $BuyerIdTemp = $checkRow["BuyerId"];
            $BuyerIdName = $checkRow["Name"];
            if ($BuyerId == $BuyerIdTemp) {
                echo "<option value='$BuyerIdTemp' selected>$BuyerIdName</option>";
                $SearchRows .= " AND A.BuyerId='$BuyerIdTemp'";
                //$SearchRows.=" AND C.BuyerId='$BuyerIdTemp'";
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
							   WHERE 1 $SearchRows GROUP BY E.CompanyId ORDER BY E.CompanyId,E.Forshort", $link_id);
    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>全部供应商</option>";
        do {
            $CompanyIdTemp = $checkRow["CompanyId"];
            $ForshortTemp = $checkRow["Forshort"];
            if ($CompanyId == $CompanyIdTemp) {
                echo "<option value='$CompanyIdTemp' selected>$ForshortTemp</option>";
                //$SearchRows.=" AND C.CompanyId='$CompanyIdTemp'";
                $SearchRows .= " AND A.CompanyId='$CompanyIdTemp'";
            } else {
                echo "<option value='$CompanyIdTemp'>$ForshortTemp</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }

    $checkResult = mysql_query("SELECT  B.mainType,Dm.Name  
							   FROM $DataIn.nonbom6_cgsheet A 
							   LEFT JOIN $DataPublic.nonbom4_goodsdata C ON C.GoodsId=A.GoodsId 
							   LEFT JOIN $DataPublic.nonbom2_subtype B ON B.Id=C.TypeId 
	                           LEFT JOIN $DataPublic.nonbom1_maintype Dm  ON Dm.Id=B.mainType 
							   WHERE 1 $SearchRows AND B.mainType>0 GROUP BY B.mainType", $link_id);

    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='mainType' id='mainType' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>选择主分类</option>";
        do {
            $mainTypeTemp = $checkRow["mainType"];
            $NameTemp = $checkRow["Name"];
            if ($mainType == $mainTypeTemp) {
                echo "<option value='$mainTypeTemp' selected>$NameTemp </option>";
                //$SearchRows.=" AND Dd.mainType='$mainTypeTemp'";
                $SearchRows .= " AND B.mainType='$mainTypeTemp'";
            } else {
                echo "<option value='$mainTypeTemp'>$NameTemp</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }
}
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr <input id='singel' type='hidden' name='singel' value='$singel'>";
echo "<span class='btn-confirm' style='width: auto;' onclick='Added()'>新增申购单</span>
            <span class='btn-confirm' style='width: auto;' onclick='BuySave()'>生成采购单</span>";
include "../model/subprogram/read_model_5.php";
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 0);
$mySql = "SELECT A.Id,A.GoodsId,A.Qty,A.BuyerId,A.mainType,A.Price,(A.Qty*A.Price) AS Amount,A.Remark,A.ReturnReasons,A.Estate,A.Locks,A.Date,A.Operator,B.TypeName,C.Forshort,C.CompanyId,D.GoodsName,D.GoodSpec,D.Unit,D.Attached,E.wStockQty,E.oStockQty,E.mStockQty,F.Name AS StaffName,G.Symbol,H.Name AS AddTaxValueName,T.Forshort AS Company
	FROM $DataIn.nonbom6_cgsheet A
	LEFT JOIN $DataIn.nonbom3_retailermain C ON C.CompanyId=A.CompanyId
	LEFT JOIN $DataIn.nonbom4_goodsdata D ON D.GoodsId=A.GoodsId
	LEFT JOIN $DataIn.nonbom2_subtype B  ON B.Id=D.TypeId
	LEFT JOIN $DataIn.nonbom5_goodsstock E ON E.GoodsId=A.GoodsId
	LEFT JOIN $DataIn.staffmain F ON F.Number=A.BuyerId
	LEFT JOIN $DataIn.currencydata G ON G.Id=C.Currency 
	LEFT JOIN $DataIn.provider_addtax H ON H.Id = A.AddTaxValue
	LEFT JOIN $DataIn.trade_object T ON T.Id = D.TradeId
	WHERE 1 $SearchRows ORDER BY A.Date DESC,A.Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    $Dir = anmaIn("download/nonbom/", $SinkOrder, $motherSTR);
    do {
        $m = 1;
        $Id = $myRow["Id"];
        $Company = $myRow["Company"];
        $GoodsId = $myRow["GoodsId"];
        $BuyerId = $myRow["BuyerId"];
        $Iden = ($Login_P_Number != '' && $Login_P_Number == $BuyerId) ? 1 : 0;//
        $StaffName = '<input type="hidden" input-name="StaffNoUd[]" value="' . $BuyerId . '" iden="' . $Iden . '"/>' . $myRow["StaffName"];//添加验证数据--用户id
        $GoodsName = $myRow["GoodsName"];
        $GoodSpec = $myRow["GoodSpec"];
        $BarCode = $myRow["BarCode"] == "" ? "&nbsp;" : $myRow["BarCode"];;
        $Remark = $myRow["Remark"] == "" ? "&nbsp;" : $myRow["Remark"];
        $TypeName = $myRow["TypeName"] == "" ? "&nbsp;" : $myRow["TypeName"];
        $AddTaxValueName = $myRow["AddTaxValueName"];
        $Estate = "<input input-name='AuditStateUd[]' value='" . $myRow["Estate"] . "' type='hidden' />";//添加验证所需数据--审核
        switch ($myRow["Estate"]) {
            case 1:
                $Estate .= "<div class='greenB'>已审核</div>";
                break;
            case 2:
            case 3:
                $Estate .= "<div class='redB'>未审核</div>";
                break;
            case 4:
                $ReturnReasons = $myRow["ReturnReasons"] == "" ? "未填写退回原因" : $myRow["ReturnReasons"];
                $Estate .= "<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
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
        $Price = $myRow["Price"];
        $Qty = del0($myRow["Qty"]);
        $Amount = sprintf("%.3f", $myRow["Amount"]);
        $Forshort = $myRow["Forshort"];
        $wStockQty = del0($myRow["wStockQty"]);
        $oStockQty = del0($myRow["oStockQty"]);
        $mStockQty = del0($myRow["mStockQty"]);
        $CompanyId = $myRow["CompanyId"];
        $checkQty = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.nonbom6_cgsheet WHERE Mid=0 AND GoodsId='$GoodsId'", $link_id));
        $sgSUM = del0($checkQty["Qty"]);
        //加密
        $CompanyId = anmaIn($CompanyId, $SinkOrder, $motherSTR);
        $Forshort = "<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a><input type='hidden' input-name='forShortUd[]' value='" . $myRow["CompanyId"] . "'/>";
        //历史单价
        $Price = "<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
        //配件分析
        $GoodsId = "<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
        $Locks = $myRow["Locks"];
        //申购总数计算
        //默认是最低价格
        //  $DefaultPriceResult=mysql_fetch_array()
        $GoodsName .= '<input type="hidden" input-name="mainTypeUd[]" value="' . $myRow['mainType'] . '"/>';//添加验证数据--配件主分类id
        $ValueArray = array(
            array(0 => $Company),
            array(0 => $StaffName, 1 => "align='center'"),
            array(0 => $Forshort),
            array(0 => $TypeName),
            array(0 => $GoodsId, 1 => "align='center'"),
            array(0 => $GoodsName),
            array(0 => $GoodSpec),
            array(0 => $Remark),
            array(0 => $Symbol, 1 => "align='right'"),
            array(0 => $AddTaxValueName, 1 => "align='center'"),
            array(0 => $Price, 1 => "align='right'"),
            array(0 => $lowPrice, 1 => "align='right'"),
            array(0 => $Qty, 1 => "align='right'"),
            array(0 => $Unit, 1 => "align='center'"),
            array(0 => $Amount, 1 => "align='right'"),
            array(0 => $sgSUM, 1 => "align='right'"),
            array(0 => $wStockQty, 1 => "align='right'"),
            array(0 => $oStockQty, 1 => "align='right'"),
            array(0 => $mStockQty, 1 => "align='right'"),
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
<script>
    function BuySave() {
        ActionTo(51, 2, 'tomain', '_self', 0);
    }

    function Added() {
        ActionTo(2, 0, 'add', '_self', 0);
    }
</script>
