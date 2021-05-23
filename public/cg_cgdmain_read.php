<style type="text/css">
  <!--
  .moveLtoR {
    filter: revealTrans(Transition=6, Duration=0.3)
  }

  ;
  .moveRtoL {
    filter: revealTrans(Transition=7, Duration=0.3)
  }

  ;
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

  -->
</style>
<?php
//include "../admin/cg_setdelivertdate.php"; //处理已下采购单未生成交期
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
$ColsNumber = 27;
$tableMenuS = 600;
ChangeWtitle("$SubCompany 采购单列表");
$funFrom = "cg_cgdmain";
$From = $From == "" ? "read" : $From;
$sumCols = "10,11,14,15,16,17,18,19,20,21";      //求和列,需处理
$MergeRows = 5;
$Th_Col = "操作|30|客户名称|60|采购单号|200|备注|40|Excel常州|70|Excel上海|70|选项|60|序号|30|配件ID|40|配件名称|250|图档|30|历史<br>订单|40|QC图|40|品检<br>报告|40|认证|40|开发|40|需求数|45|增购数量|45|实购数|45|含税价|50|单位|45|金额|60|金额(RMB)|60|收货数|45|领料数|45|欠数|45|退货|45|补仓|45|请款<br>方式|30|货款|30|采购日期|70|交期|90|采购流水号|100|供应商|80|预付金额|80|出货日期|70";

//必选，分页默认值
$Pagination = $Pagination == "" ? 1 : $Pagination;  //默认分页方式:1分页，0不分页
$Page_Size = 200;              //每页默认记录数量,13
$ActioToS = "1,3,26,27,22,7,8";
//步骤3：
if ($Estate == 1 && $From != "slist") {
    $otherAction = "<span onclick='javascript:showMaskDiv()' $onClickCSS>请款</span>";
}
$nowWebPage = $funFrom . "_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if ($From != "slist") {  //非查询：过滤采购、结付方式、供应商、月份
    $SearchRows = "";
    $BuyerId = $BuyerId == "" ? $Login_P_Number : $BuyerId;

    //特采单
    echo "<select name='bigClass' id='bigClass' onchange='document.form1.submit();'>";
    if ($bigClass == "9002" || $bigClass == '') {
        echo "<option value='9002' selected>钢筋半成品</option>";
        $SearchRows = " and A.TypeId = '9002' ";
    }
    else {
        echo "<option value='9002' >钢筋半成品</option>";
    }
    if ($bigClass == "9006") {
        echo "<option value='9006' selected>混凝土</option>";
        $SearchRows = " and A.TypeId = '9006' ";
    }
    else {
        echo "<option value='9006' >混凝土</option>";
    }
    if ($bigClass == "9019") {
        echo "<option value='9019' selected>预埋</option>";
        $SearchRows = " and A.TypeId = '9019' ";
    }
    else {
        echo "<option value='9019' >预埋</option>";
    }

    if ($bigClass == "tc") {
        echo "<option value='tc' selected>特采单</option>";
        $SearchRows = " and S.AddRemark  != '' ";
    }
    else {
        echo "<option value='tc' >特采单</option>";
    }
    if ($bigClass == "tcgc") {
        echo "<option value='tcgc' selected>特采钢材</option>";
        $SearchRows = " and S.AddRemark  != '' and A.TypeId = '9018' ";
    }
    else {
        echo "<option value='tcgc' >特采钢材</option>";
    }
    if ($bigClass == "tcym") {
        echo "<option value='tcym' selected>特采预埋</option>";
        $SearchRows = " and S.AddRemark  != '' and A.TypeId = '9019' ";
    }
    else {
        echo "<option value='tcym' >特采预埋</option>";
    }
    echo "</select>&nbsp;";

    // 采购
    if ($Login_P_Number != 10029 && $Login_P_Number != 10057 && $Login_P_Number != 10024) {
        $mysql = "SELECT M.BuyerId,S.Name,S.Estate,S.JobId
	FROM $DataIn.cg1_stockmain M 
	LEFT JOIN $DataPublic.staffmain S ON S.Number=M.BuyerId
	WHERE 1 AND (S.BranchId=4  OR   TIMESTAMPDIFF(MONTH,M.Date,Now())<=6) AND S.cSign='$Login_cSign'  GROUP BY M.BuyerId ORDER BY S.JobId desc,S.Estate DESC,S.Number DESC";
    }
    else {
        $mysql = "SELECT M.BuyerId,S.Name,S.Estate,S.JobId
	FROM $DataIn.cg1_stockmain M 
	LEFT JOIN $DataPublic.staffmain S ON S.Number=M.BuyerId
	WHERE 1 AND (S.BranchId=4  OR   TIMESTAMPDIFF(MONTH,M.Date,Now())<=6) AND S.cSign='$Login_cSign' and S.Number = $Login_P_Number GROUP BY M.BuyerId ORDER BY S.JobId desc,S.Estate DESC,S.Number DESC";
    }
    $buyerSql = mysql_query("$mysql", $link_id);
    $isBuyer = 0;

    if ($buyerRow = mysql_fetch_array($buyerSql)) {
        echo "<select name='BuyerId' id='BuyerId' onchange='zhtj(this.name)'>";
        //echo "<option value='' selected>全部</option>";
        do {
            $thisBuyerId = $buyerRow["BuyerId"];
            $Buyer = $buyerRow["Name"];
            if ($isBuyer == 0) {
                $theFirstBuyerId = $thisBuyerId;
                $isBuyer = 1;
            }
            $FontColor = "";
            if ($buyerRow["Estate"] != 1 || $buyerRow["JobId"] == 4) {
                $FontColor = "style='color:#99CC99'";
            }
            if ($BuyerId == $thisBuyerId) {
                echo "<option value='$thisBuyerId' $FontColor selected>$Buyer</option>";
                $SearchRows .= " and M.BuyerId='$thisBuyerId'";
                $isBuyer = 2;
            }
            else {
                echo "<option value='$thisBuyerId' $FontColor>$Buyer </option>";
            }
        } while ($buyerRow = mysql_fetch_array($buyerSql));
        echo "</select>&nbsp;";
    }
    if ($isBuyer == 1) {  //表示，非采购人员，第一次登入时无选择Buyerid
        $SearchRows .= " and M.BuyerId='$theFirstBuyerId'";
    }

    $SearchRows .= $SearchRows == "" ? " AND M.BuyerId='$FristBurerId'" : '';

    //项目名称
    if ($bigClass != "tc") {
        $mysql = "select a.Letter, a.Forshort, b.TradeNo, a.CompanyId
            from $DataIn.trade_object a
            INNER join $DataIn.trade_info b on a.id = b.TradeId
            where a.ObjectSign = 2 order by a.Letter";
    }
    else {
        $mysql = "select a.Letter, a.Forshort, b.TradeNo, a.CompanyId
            from $DataIn.trade_object a
            INNER join $DataIn.trade_info b on a.id = b.TradeId
            where 1 > 2 and a.ObjectSign = 2 order by a.Letter";
    }

    $tradeSql = mysql_query("$mysql", $link_id);
    if ($tradeRow = mysql_fetch_array($tradeSql)) {
        echo "<select name='TradeNo' id='TradeNo' onchange='document.form1.submit();'>";
        echo "<option value='all' selected>全部项目</option>";
        do {
            $Letter = $tradeRow["Letter"];
            $Forshort = $tradeRow["Forshort"];
            $Forshort = $Letter . '-' . $Forshort;
            $ThisTradeNo = $tradeRow["CompanyId"];
            $TradeNo = $TradeNo == "" ? $ThisTradeNo : $TradeNo;

            if ($AddRemark != "tc") {
                if ($TradeNo == $ThisTradeNo) {
                    echo "<option value='$ThisTradeNo' selected>$Forshort</option>";
                    $SearchRows .= " and OM.CompanyId = '$ThisTradeNo' ";
                }
                else {
                    echo "<option value='$ThisTradeNo'>$Forshort</option>";
                }
            }
        } while ($tradeRow = mysql_fetch_array($tradeSql));
        echo "</select>&nbsp;";
    }


    //交期
    $dateSql = mysql_query("SELECT S.DeliveryWeek FROM cg1_stocksheet S 
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN yw1_ordersheet Y ON Y.POrderId = S.POrderId
	LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber WHERE 1 $SearchRows AND ( S.FactualQty + S.AddQty ) > 0 group by S.DeliveryWeek ASC", $link_id);

    if ($dateRow = mysql_fetch_array($dateSql)) {
        echo "<select name='Delivery' id='Delivery' onchange='document.form1.submit();'>";
        //echo "<option value='all' >全部交期</option>";
        do {
            $DeliveryWeek = $dateRow["DeliveryWeek"];

            if ($DeliveryWeek > 0) {

                $week = substr($DeliveryWeek, 4, 2);
                $dateArray = GetWeekToDate($DeliveryWeek, "m/d");
                $weekName = "Week " . $week;
                $dateSTR = $dateArray[0] . "-" . $dateArray[1];


                if ($Delivery == $DeliveryWeek) {
                    echo "<option value='$DeliveryWeek' selected>$weekName ($dateSTR)</option>";
                    $SearchRows .= " AND S.DeliveryWeek = $DeliveryWeek";
                }
                else {
                    echo "<option value='$DeliveryWeek' >$weekName ($dateSTR)</option>";
                }
            }
            else {

                if ($DeliveryWeek == "0") {
                    if ($Delivery == $DeliveryWeek) {
                        echo "<option value='$DeliveryWeek' selected>未设置交期</option>";
                        $SearchRows .= " AND S.DeliveryWeek = $DeliveryWeek";
                    }
                    else {
                        echo "<option value='$DeliveryWeek' selected>未设置交期</option>";
                    }
                }
                else {
                    echo "<option value='$DeliveryWeek' selected>交期错误</option>";
                }
            }
        } while ($dateRow = mysql_fetch_array($dateSql));
        echo "</select>&nbsp;";
    }

    //结付方式
    $GysPayModeResult = mysql_query("SELECT Id,Name FROM $DataIn.providerpaymode
	 WHERE Estate=1", $link_id);
    if ($GysPayModeRow = mysql_fetch_array($GysPayModeResult)) {
        echo "<select name='GysPayMode' id='GysPayMode' onchange='zhtj(this.name)'>";
        echo "<option value='-1' selected>全部</option>";
        do {
            $GysPayModeId = $GysPayModeRow["Id"];
            $GysPayModeName = $GysPayModeRow["Name"];
            if ($GysPayMode == $GysPayModeId) {
                echo "<option value='$GysPayModeId' selected>$GysPayModeName</option>";
                $SearchRows .= " AND V.GysPayMode='$GysPayModeId'";
            }
            else {
                echo "<option value='$GysPayModeId'>$GysPayModeName</option>";
            }
        } while ($GysPayModeRow = mysql_fetch_array($GysPayModeResult));
        echo "</select>&nbsp;";
    }
    //供应商
    $providerSql = mysql_query("SELECT M.CompanyId,V.Forshort,V.Letter,V.Estate 
FROM $DataIn.cg1_stockmain M 
LEFT JOIN $DataIn.cg1_stocksheet S ON S.Mid=M.Id 
LEFT JOIN $DataIn.trade_object V ON V.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId  
LEFT JOIN yw1_ordersheet Y ON Y.POrderId = S.POrderId 
LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber 
WHERE 1 $SearchRows  GROUP BY M.CompanyId ORDER BY V.Letter", $link_id);
    if ($providerRow = mysql_fetch_array($providerSql)) {
        echo "<select name='CompanyId' id='CompanyId' onchange='zhtj(this.name)'>";
        echo "<option value='all' selected>全部供应商</option>";
        do {
            $Letter = $providerRow["Letter"];
            $Forshort = $providerRow["Forshort"];
            $providerEstate = $providerRow["Estate"] == 0 ? "(禁用)" : "";
            $Forshort = $Letter . '-' . $Forshort;
            $thisCompanyId = $providerRow["CompanyId"];
            $CompanyId = $CompanyId == "" ? $thisCompanyId : $CompanyId;
            if ($thisCompanyId) {
                if ($CompanyId == $thisCompanyId) {
                    echo "<option value='$thisCompanyId' selected>$Forshort $providerEstate</option>";
                    $SearchRows .= " and M.CompanyId='$thisCompanyId'";
                }
                else {
                    echo "<option value='$thisCompanyId'>$Forshort</option>";
                }
            }
        } while ($providerRow = mysql_fetch_array($providerSql));
        echo "</select>&nbsp;";

        //PO
        $orderPOSql = mysql_query("SELECT DISTINCT M.purchaseOrderNo
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.trade_object V ON V.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.ch1_shipsheet E ON E.PorderId=S.PorderId
LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=E.Mid
LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderPO = Y.OrderPO
WHERE 1 $SearchRows AND S.Mid>0 GROUP BY S.StockId ORDER BY S.Mid DESC,M.Date DESC,S.POrderId", $link_id);
        if ($orderPORow = mysql_fetch_array($orderPOSql)) {
            echo "<select name='POrderId' id='POrderId' onchange='document.form1.submit();'>";
            do {
                $thisPOrderId = $orderPORow["purchaseOrderNo"];
                $POrderId = $POrderId == "" ? $thisPOrderId : $POrderId;
                if ($thisPOrderId) {
                    if ($POrderId == $thisPOrderId) {
                        echo "<option value='$thisPOrderId' selected>$POrderId</option>";
                        $SearchRows .= " and M.purchaseOrderNo='$thisPOrderId'";
                    }
                    else {
                        echo "<option value='$thisPOrderId'>$thisPOrderId</option>";
                    }
                }

            } while ($orderPORow = mysql_fetch_array($orderPOSql));
            echo "</select>&nbsp;";
        }

        //月份
        $date_Result = mysql_query("SELECT DISTINCT DATE_FORMAT(M.Date,'%Y-%m') as Date 
		FROM $DataIn.cg1_stockmain M
		LEFT JOIN $DataIn.cg1_stocksheet S ON S.Mid=M.Id 
		 LEFT JOIN yw1_ordersheet Y ON Y.POrderId = S.POrderId 
		 LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
		 LEFT JOIN yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber 
		LEFT JOIN $DataIn.trade_object V ON V.CompanyId=M.CompanyId
		WHERE 1 $SearchRows  order by Date DESC", $link_id);

        if ($dateRow = mysql_fetch_array($date_Result)) {
            echo "<select name='chooseDate' id='chooseDate' onchange='document.form1.submit()'>";
            do {
                $dateValue = $dateRow["Date"];
                $StartDate = $dateValue . "-01";
                $EndDate = date("Y-m-t", strtotime($StartDate));
                $chooseDate = $chooseDate == "" ? $dateValue : $chooseDate;
                if ($chooseDate == $dateValue) {
                    echo "<option value='$dateValue' selected>$dateValue</option>";
                    $SearchRows .= " and ((M.Date>='$StartDate' and M.Date<='$EndDate') )";
                }
                else {
                    echo "<option value='$dateValue'>$dateValue</option>";
                }
            } while ($dateRow = mysql_fetch_array($date_Result));
            echo "</select>&nbsp;";
        }
        else {
            //无月份记录
            $SearchRows .= " and M.Date=''";
        }
    }
    else {
        //无供应商记录
        $SearchRows .= " and M.CompanyId=''";
    }

    // 采购单号
    $orderSql = mysql_query("SELECT M.PurchaseID
		FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.trade_object V ON V.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.ch1_shipsheet E ON E.PorderId=S.PorderId
LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=E.Mid
LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
		WHERE 1 $SearchRows and Y.OrderPO is not null and S.Mid>0 
		GROUP BY M.PurchaseID ORDER BY M.PurchaseID", $link_id);
    if ($orderRow = mysql_fetch_array($orderSql)) {
        echo "<select name='PurchaseID' id='PurchaseID' onchange='document.form1.submit();'>";
        echo "<option value='all' selected>全部采购单</option>";
        do {
            $thisPurchaseID = $orderRow["PurchaseID"];
            $PurchaseID = $PurchaseID == "" ? $thisPurchaseID : $PurchaseID;

            if ($PurchaseID == $thisPurchaseID) {
                echo "<option value='$thisPurchaseID' selected>$PurchaseID</option>";
                $SearchRows .= " and M.PurchaseID='$thisPurchaseID'";
            }
            else {
                echo "<option value='$thisPurchaseID'>$thisPurchaseID</option>";
            }
        } while ($orderRow = mysql_fetch_array($orderSql));
        echo "</select>&nbsp;";
    }

    $Estate = $Estate == "" ? -1 : $Estate;
    $TempEstateSTR = "EstateSTR" . strval($Estate);
    $$TempEstateSTR = "selected";


//结付状态
    echo "<select name='Estate' id='Estate' onchange='zhtj(this.name)'>";
    echo "<option value='-1' $EstateSTR1>请款状态</option>";
    echo "<option value='1' $EstateSTR1>未请款</option>";
    echo "<option value='2' $EstateSTR2>请款中</option>";
    echo "<option value='3' $EstateSTR3>请款通过</option>";
    echo "<option value='0' $EstateSTR0>已结付</option>";
    echo "</select>&nbsp;";
    $isEstate = 0;
    if ($Estate == 1) {
        $isEstate = $Estate;
        $SearchRows .= " and F.Estate IS NULL";
    }
    else {
        if ($Estate >= 0) {
            $SearchRows .= " and F.Estate='$Estate'";
        }
    }


}
else {

    $Pos = strpos($SearchRows, "and F.Estate", 0);
    if ($Pos > 0) {
        $SearchRows = trim($SearchRows);
        $Estate = substr($SearchRows, strlen($SearchRows) - 1);
        if ($Pos == 1) {
            $SearchRows = "";
        }
        else {
            $SearchRows = substr($SearchRows, 0, $Pos - 1);
        }
    }

    $SearchEstate = "";
    $Estate = $Estate == "" ? -1 : $Estate;
    $TempEstateSTR = "EstateSTR" . strval($Estate);
    $$TempEstateSTR = "selected";
//结付状态
    echo "<select name='Estate' id='Estate' onchange='zhtj(this.name)'>";
    echo "<option value='-1' $EstateSTR1>请款状态</option>";
    echo "<option value='1' $EstateSTR1>未请款</option>";
    echo "<option value='2' $EstateSTR2>请款中</option>";
    echo "<option value='3' $EstateSTR3>请款通过</option>";
    echo "<option value='0' $EstateSTR0>已结付</option>";
    echo "</select>&nbsp;";
    if ($Estate == 1 || $Estate == "") {
        $SearchEstate .= " and F.Estate IS NULL";
    }
    else {
        if ($Estate < 0) $SearchEstate = "";
        else  $SearchEstate .= " and F.Estate='$Estate'";
    }

}
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
	$CencalSstr";
//步骤5：
include "../model/subprogram/read_model_5.php";
echo "<div id='Jp' style='position:absolute; left:341px; top:229px; width:300px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ObjId' type='hidden' id='ObjId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='fstr' type='hidden' id='fstr'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";


//步骤6：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_TitleMy($Th_Col, "1", 1);

$numberArray = array();
//$numberArray = array(10002,10007,10008,10341,10369,10868,10871);
if ($APP_CONFIG['PROCUREMENT_BRANCHID'] == $Login_BranchId || in_array($Login_GroupId, $APP_CONFIG['IT_DEVELOP_GROUPID'])) {
    $numberArray[] = $Login_P_Number;
}
$numberArray[] = 10019;

$mySql = "SELECT M.Date,M.PurchaseID,M.Remark,OM.CompanyId as CID,M.purchaseOrderNo,
S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,
S.CompanyId,S.BuyerId,S.DeliveryDate,S.DeliveryWeek,S.StockRemark,S.AddRemark,S.Estate,S.Locks,S.rkSign,
A.StuffCname,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.TypeId,A.DevelopState,H.Date as OutDate,U.Name AS UnitName,U.Decimals,V.Forshort,F.AutoSign ,V.ProviderType,Y.OrderPO 
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.cg1_stockmain M ON S.Mid=M.Id 
LEFT JOIN $DataIn.trade_object V ON V.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.ch1_shipsheet E ON E.PorderId=S.PorderId
LEFT JOIN $DataIn.ch1_shipmain H ON H.Id=E.Mid
LEFT JOIN $DataIn.cw1_fkoutsheet F ON F.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderPO = Y.OrderPO
WHERE 1 $SearchRows  $SearchEstate   AND S.Mid>0 GROUP BY S.StockId ORDER BY S.Mid DESC,M.Date DESC,S.POrderId";
//echo $mySql;
$mainResult = mysql_query($mySql . " $PageSTR", $link_id);
$DefaultBgColor = $theDefaultColor;
if ($mainRows = mysql_fetch_array($mainResult)) {
    $tbDefalut = 0;
    $midDefault = "";
    $d = anmaIn("download/stufffile/", $SinkOrder, $motherSTR);
    do {
        $m = 1;
        $Id = $mainRows["Id"];
        $theDefaultColor = $DefaultBgColor;
        $Mid = $mainRows["Mid"];
        $POrderId = $mainRows["POrderId"];
        $purchaseOrderNo = $mainRows["purchaseOrderNo"];
        $temCId = $mainRows['CID'];
        $sql = "select Forshort from $DataIn.trade_object where CompanyId = $temCId";
        $ret = mysql_query($sql);
        $CId = mysql_fetch_row($ret)[0];
        $CId = $CId == '' ? '特采单' : $CId;
        $Date = $mainRows["Date"];
        $Dates = $Date . "：" . CountDays($Date, 10); //10为无用参数
        $PurchaseID = $mainRows["PurchaseID"];
        $Remark = $mainRows["Remark"] == "" ? "&nbsp" : "<img src='../images/remark.gif' title='$mainRows[Remark]' width='16' height='16'>";
        $MidSTR = anmaIn($Mid, $SinkOrder, $motherSTR);
        $PurchaseIDStr = "<a href='../model/subprogram/purchaseorder_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
        $upMian = "<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"cg_cgdmain_upmain\",$Mid)' src='../images/edit.gif' alt='更新采购单资料!采购日期：$Dates' width='13' height='13'>";

        //下载PDF文档**********************************************
        $PurchaseFile = $PurchaseID . '.pdf';
        //echo $PurchaseID.'.PDF';
        if (file_exists("../download/PurchasePDF/" . $PurchaseFile)) {
            $PurchaseFile = anmaIn($PurchaseFile, $SinkOrder, $motherSTR);
            $tmpd = anmaIn("download/PurchasePDF/", $SinkOrder, $motherSTR);
            $donwloadFileIP = "..";    //无IP，则用原来的方式
            $donwloadFileaddress = "$donwloadFileIP/admin/openorload.php";
            $PurchaseFile = "<a href=\"$donwloadFileaddress?d=$tmpd&f=$PurchaseFile&Type=&Action=6\" target=\"download\" title='下载采购单(PDF)'><img src='../images/down.gif'  alt='下载采购单(PDF)' width='18' height='18'></a>";
        }
        else {
            $PurchaseFile = "";
        }

        $PurchaseToPDF = "<a href='PurchaseToPDF.php?f=$MidSTR' target='_blank'>生成PDF</a>";

        //********************************************************************************************

        $overSign = 0;
        $ProviderType = $mainRows["ProviderType"];  //代购供应商 所有改物料全部送货完才能请款
        //明细资料
        $xmlFile = "<a href='cg_cgdmain_excel.php?Mid=$Mid&SealCompanyId=1' target='_self'>Excel(上海研砼)</a>";
        $excelFile = "<a href='cg_cgdmain_excel.php?Mid=$Mid&SealCompanyId=2' target='_self'>Excel(常州砼筑)</a>";
        $StuffId = $mainRows["StuffId"];
        if ($StuffId != "") {
            $checkidValue = $mainRows["Id"];
            $StuffCname = $mainRows["StuffCname"];
            $OrderQty = $mainRows["OrderQty"];
            $FactualQty = $mainRows["FactualQty"];
            $AddQty = $mainRows["AddQty"];
            $Qty = $FactualQty + $AddQty;
            $Price = $mainRows["Price"];
            $Amount = sprintf("%.2f", $Qty * $Price);
            $tempStockId = $StockId = $mainRows["StockId"];
            $Estate = $mainRows["Estate"];
            $UnitName = $mainRows["UnitName"] == "" ? "&nbsp;" : $mainRows["UnitName"];
            $Decimals = $mainRows["Decimals"];
            $Locks = $mainRows["Locks"];
            $BuyerId = $mainRows["BuyerId"];
            $CompanyId = $mainRows["CompanyId"];
            $OrderPO = $mainRows["OrderPO"];
            $POrderId = $mainRows["POrderId"];
            $tdBGCOLOR = $POrderId == "" ? "bgcolor='#FFCC99'" : "";
            $PQty = $mainRows["PQty"];
            $PackRemark = $mainRows["PackRemark"];
            $sgRemark = $mainRows["sgRemark"];
            $ShipType = $mainRows["ShipType"];
            $Leadtime = $mainRows["Leadtime"];

            $Forshort = $mainRows["Forshort"];
            $TypeId = $mainRows["TypeId"];
            $Gremark = $mainRows["Gremark"];
            $Gfile = $mainRows["Gfile"];
            $Gstate = $mainRows["Gstate"];
            include "../model/subprogram/stuffimg_Gfile.php";  //图档显示
            //检查是否有图片

            //配件QC检验标准图
            include "../model/subprogram/stuffimg_qcfile.php";
            //配件品检报告qualityReport
            include "../model/subprogram/stuff_get_qualityreport.php";
            //REACH 法规图
            include "../model/subprogram/stuffreach_file.php";

            $Picture = $mainRows["Picture"];
            include "../model/subprogram/stuffimg_model.php";
            include "../model/subprogram/stuff_Property.php";//配件属性

            $OrderQtyInfo = "<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";
            //供应商结付货币的汇率
            $Rate = 1;
            $currency_Temp = mysql_query("SELECT C.Rate FROM $DataPublic.currencydata C
			                              LEFT JOIN $DataIn.trade_object P  ON P.Currency=C.Id 
			                              WHERE P.CompanyId='$CompanyId' ORDER BY C.Id LIMIT 1", $link_id);
            if ($RowTemp = mysql_fetch_array($currency_Temp)) {
                $Rate = $RowTemp["Rate"];//汇率
            }

            $rmbAmount = sprintf("%.2f", $Amount * $Rate);
            ///仓库情况////////////////////////////////////////

            //收货情况
            $rkTemp = mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId' order by Id", $link_id);
            $rkQty = mysql_result($rkTemp, 0, "Qty");
            $rkQty = $rkQty == "" ? 0 : $rkQty;
            //领料情况
            $llTemp = mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' order by Id", $link_id);
            $llQty = mysql_result($llTemp, 0, "Qty");
            $llQty = $llQty == "" ? 0 : $llQty;
            $llBgColor = "";
            if ($tdBGCOLOR == "") {
                if ($llQty == $OrderQty) {
                    $llBgColor = "class='greenB'";
                }
                else {
                    $llBgColor = "class='yellowB'";
                }
            }
            else {
                $llBgColor = "class='greenB'";
            }
            //退换数量
            $UnionSTR7 = mysql_query("SELECT SUM(Qty) AS thQty FROM $DataIn.ck2_thsheet WHERE StuffId='$StuffId'", $link_id);
            $thQty = mysql_result($UnionSTR7, 0, "thQty");
            $thQty = $thQty == "" ? 0 : $thQty;

            $LockRemark = "";
            //补仓数量
            $UnionSTR8 = mysql_query("SELECT SUM(Qty) AS bcQty FROM $DataIn.ck3_bcsheet WHERE StuffId='$StuffId'", $link_id);
            $bcQty = mysql_result($UnionSTR8, 0, "bcQty");
            $bcQty = $bcQty == "" ? 0 : $bcQty;
            if ($bcQty < $thQty) {
                if ($isEstate == 1) {  //如果是请款，则不用锁定
                    //$LockRemark="未补完货!";
                }
                if ($ProviderType == 1) $overSign = 1;
                $bcQty = "<span class='redB'>$bcQty</span>";
            }
            else {
                if ($bcQty > 0) {
                    $bcQty = "<span class='greenB'>$bcQty</span>";
                }
            }
            if ($thQty > 0) $thQty = "<a href='ck_th_read.php?tempStuffId=$StuffId' target='_blank'><span style='color:#000'>$thQty</span></a>";


            //结付情况/**/

            $checkPay = mysql_query("SELECT Estate,Month FROM $DataIn.cw1_fkoutsheet WHERE StockId='$StockId' ORDER BY Id DESC LIMIT 1", $link_id);
            if ($checkPayRow = mysql_fetch_array($checkPay)) {
                $cwEstate = $checkPayRow["Estate"];
                $AskMonth = $checkPayRow["Month"];
                switch ($cwEstate) {
                    case 0://已结付
                        $cwEstate = "<div class='greenB' title='已结付...货款月份:$AskMonth'>√</div>";
                        $LockRemark = "已结付，锁定操作";
                        break;
                    case 2:  //请款中
                        $cwEstate = "<div class='yellowB' title='请款中...货款月份:$AskMonth'>×.</div>";
                        $LockRemark = "已请款，锁定操作";
                        break;
                    case 3://请款通过
                        $cwEstate = "<div class='yellowB' title='等候结付...货款月份:$AskMonth'>√.</div>";
                        $LockRemark = "已请款通过，锁定操作";
                        break;
                }
            }
            else {
                $cwEstate = "<div class='redB'>×</div>";
            }

            //1表示手动请款审核,2.表示手动请款自动通过,3表示自动请款审核,4表示自动请款自动通过
            $Autobgcolor = "";
            $AutoSign = $mainRows["AutoSign"];
            switch ($AutoSign) {
                case 2:
                    $AutoSign = "<image src='../images/AutoCheckB.png' style='width:20px;height:20px;' title='人工请款自动通过'/>";
                    break;
                case 4:
                    $AutoSign = "<image src='../images/AutoCheck.png' style='width:20px;height:20px;' title='系统请款自动通过'/>";
                    //$Autobgcolor="bgcolor='##FF0000'";
                    break;
                default:
                    $AutoSign = "&nbsp;";
                    break;
            }
            //已付订金 add by zx 2011-01-24
            $PreAmount = "&nbsp;";
            $checkPrePay = mysql_query("SELECT Amount   FROM $DataIn.cw2_fkdjsheet WHERE PurchaseID='$PurchaseID' AND Estate!=1 ORDER BY Id DESC LIMIT 1", $link_id);
            if ($checkPrePayRow = mysql_fetch_array($checkPrePay)) {
                if ($isEstate == 1) {  //如果是请款，则不用锁定
                    $PreAmount = $checkPrePayRow["Amount"];
                }
                else {
                    if ($Login_P_Number != 10341 && $Login_P_Number != 10868) $LockRemark = "已付订金，锁定操作";
                    $PreAmount = $checkPrePayRow["Amount"];
                }

            }

            //尾数
            $rkSign = $mainRows["rkSign"];
            $Mantissa = $Qty - $rkQty;
            $Sid = anmaIn($StockId, $SinkOrder, $motherSTR);
            if ($Mantissa <= 0) {
                $BGcolor = "class='greenB'";
                $StockIdShow = "<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
                if ($Mantissa < 0) {
                    $BGcolor = "class='redB'";
                    //$Mantissa="错误";
                    $Mantissa = "<div class='redB' title='错误(入库数量>采购数量)'>错误</div>";
                }
                if ($rkSign > 0) {
                    //更改入库标记
                    $uprkSignSql = "UPDATE $DataIn.cg1_stocksheet  SET rkSign=0 WHERE StockId='$StockId'";
                    $UprkResult = mysql_query($uprkSignSql);
                    echo "<div class='redB'>入库标志更新:该采购单已全部入库</div>";
                }
            }
            else {
                if ($ProviderType == 1) $overSign = 1;
                if ($rkSign == 0) {
                    //更改入库标记
                    $rk_Sign = $rkQty > 0 ? 2 : 1;
                    $uprkSignSql = "UPDATE $DataIn.cg1_stocksheet SET rkSign='$rk_Sign' WHERE StockId='$StockId'";
                    $UprkResult = mysql_query($uprkSignSql);
                    echo "<div class='redB'>入库标志更新:该采购单已全部入库</div>";
                }
                $StockIdShow = $StockId;
                if ($Mantissa == $Qty) {
                    $BGcolor = "class='redB'";

                }
                else {
                    //$LockRemark="已收货，锁定操作";

                    $BGcolor = "class='yellowB'";
                    $StockIdShow = "<a href='ck_rk_list.php?Sid=$Sid' target='_blank'>$StockId</a>";
                }
                /*
                        if ($isEstate==1) {  //如果是请款，则不用锁定
                            $LockRemark="未收完货!";
                        }
                        */
            }

            //默认单价
            $priceRes = mysql_query("SELECT S.Price FROM $DataIn.stuffdata S WHERE S.StuffId='$StuffId'", $link_id);
            if ($priceRow = mysql_fetch_array($priceRes)) {
                $DefaultPrice = $priceRow["Price"];
            }
            if ($DefaultPrice != $Price) {
                $Price = "<div class='redB'>$Price</div>";
                $PriceTitle = "Title=\"默认单价：$DefaultPrice\"";
            }

            //////////////////////////////////////////////////
            ///权限///////////////////////////////////////////
            if ($Estate == 1) {
                $LockRemark = "未审核";
            }
            if ($Locks == 0) {//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
                if ($Keys & mLOCK) {
                    if ($LockRemark != "") {//财务强制锁定
                        $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
                    }
                    else {
                        $Choose = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
                    }
                }
                else {    //A2：无权限对锁定记录操作
                    $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
                }
            }
            else {
                if (($BuyerId == $Login_P_Number && ($Keys & mUPDATE || $Keys & mDELETE)) || $Keys & mLOCK) {//有权限
                    if ($LockRemark != "") {
                        $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png'  title='$LockRemark' width='15' height='15'>";
                    }
                    else {
                        $Choose = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'>";
                    }
                }
                else {//无权限
                    $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png'  title='锁定操作!' width='15' height='15'>";
                }
            }

            $cName = $mainRows["cName"];
            $Client = $mainRows["Client"];
            include "../model/subprogram/cg_cgd_jj.php";
            //交货日期颜色
            $OnclickStr = "";
            $DeliveryDate = $mainRows["DeliveryDate"];
            if (in_array($Login_P_Number, $numberArray)) {
                $OnclickStr = "onclick='set_weekdate(this,$StockId)' style='CURSOR: pointer;'";

            }

            $OutDate = $mainRows["OutDate"] == "" ? "&nbsp" : $mainRows["OutDate"];

            //交货周计算
            $DeliveryDate = $mainRows["DeliveryDate"];
            $DeliveryWeek = $mainRows["DeliveryWeek"];
            include "../model/subprogram/deliveryweek_toweek.php";
            //原交货日期

            $CheckOldDate = mysql_query("SELECT YEARWEEK(DeliveryDate,1) AS Week,Remark FROM $DataIn.cg1_DeliveryDate WHERE StockId='$StockId' AND DeliveryDate!='$DeliveryDate' ORDER BY Id DESC LIMIT 1", $link_id);
            if ($oldDateRow = mysql_fetch_array($CheckOldDate)) {
                $oldDeliveryDate = "Week " . substr($oldDateRow["Week"], 4, 2);
                $DeliveryRemark = $oldDateRow["Remark"];
                $dateSignImage = "<div style='float:left;margin:0px 5px 0px 5px'><img src='../images/icon_abnormal.gif'  width='20' height='20' title='原交期:" . $oldDeliveryDate . "；原因:" . $DeliveryRemark . "' style='vertical-align:middle;'/></div>";
            }
            else {
                $dateSignImage = "";
            }
            $DevelopWeekState = 1;
            $DevelopState = $mainRows["DevelopState"];
            include "../model/subprogram/stuff_developstate.php";

            $showPurchaseorder = "<img onClick='ShowOrHideThisLayer(StuffList$i,showtable$i,StuffList$i,\"$tempStockId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
            $XtableWidth = 0;
            $StuffListTB = "<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";

            ////////////////////////////////////////////////////
            if ($tbDefalut == 0 && $midDefault == "") {//首行
                //并行列
                echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' bgcolor='#f5f5f5'><tr>";
                echo "<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$CId</td>";
                $unitWidth = $tableWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101 click-PO' width='$Field[$m]' align='center'>$purchaseOrderNo<br>$PurchaseIDStr <br>$PurchaseFile $taxPurchaseFile <br> $PurchaseToPDF  </td>";//下单日期
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$excelFile</td>";
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$xmlFile</td>";
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                //并行宽
                //echo"<td width='$unitWidth' class='A0101'>";
                echo "<td width='' class='A0101'>";
                $midDefault = $Mid;
            }

            $AddRemark = $mainRows["AddRemark"];
            if ($AddRemark != "") {
                $StuffId = "<div title='$AddRemark'>$StuffId</div>";
            }
            if ($midDefault != "" && $midDefault == $Mid) {//同属于一个主ID，则依然输出明细表格
                $m = 13;
                echo "<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
                echo "<tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
                $unitFirst = $Field[$m] - 1;
                echo "<td class='A0001' width='$unitFirst'   $tdBGCOLOR>$Choose&nbsp;$showPurchaseorder</td>";//选项
                $m = $m + 2;

                echo "<td class='A0001' width='$Field[$m]' align='center'>$i<input type='hidden' id='overSign$i' value='$overSign'> </td>";      //序号
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";  //配件ID
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]'>$StuffCname</td>";    //配件名称
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$Gfile</td>";    //图档
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$OrderQtyInfo</td>";    //历史订单
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$QCImage</td>";  //QC标准图
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$ReachImage</td>"; //REACH
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$DevelopState</td>"; //REACH
                $m = $m + 2;
                $FactualQty = round($FactualQty, $Decimals);
                echo "<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";    //需求数量
                $m = $m + 2;
                $AddQty = round($AddQty, $Decimals);
                echo "<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";    //增购数量
                $m = $m + 2;
                $Qty = round($Qty, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";    //实购数量
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";    //单价
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";    //单位
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";    //金额
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$rmbAmount</td>";  //金额RMB
                $m = $m + 2;
                $rkQty = round($rkQty, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";    //收货数量
                $m = $m + 2;
                $llQty = round($llQty, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'><div $llBgColor>$llQty</div></td>";    //领料数量
                $m = $m + 2;
                $Mantissa = round($Mantissa, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";  //欠数数量
                $m = $m + 2;
                $thQty = round($thQty, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$thQty</td>";//退货
                $m = $m + 2;
                $bcQty = round($bcQty, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$bcQty</td>";// 补仓
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$AutoSign</td>";//结付状态
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";//结付状态
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$Date</td>";//采购日期
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center' $OnclickStr>$dateSignImage $DeliveryWeek</td>";
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'><div title='$Client : $cName'>$StockIdShow</div></td>";
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";//供应商
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$PreAmount</td>";//订金
                $m = $m + 2;
                echo "<td  class='A0000' width='' align='center'>$OutDate</td>";
                echo "</tr></table>";
                $i++;
            }
            else {
                //新行开始
                echo "</td></tr></table>";//结束上一个表格
                //并行列
                echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' bgcolor='#f5f5f5'><tr>";
                echo "<td scope='col' class='A0111' width='$Field[$m]' align='center'>$upMian</td>";//更新
                $unitWidth = $tableWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$CId</td>"; // 项目名称
                $unitWidth = $tableWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101 click-PO' width='$Field[$m]' align='center'>$purchaseOrderNo<br>$PurchaseIDStr <br>$PurchaseFile $taxPurchaseFile <br> $PurchaseToPDF</td>";//下单日期

                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Remark</td>";    //下单备注
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$excelFile</td>";
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$xmlFile</td>";
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                //并行宽
                //echo"<td width='$unitWidth' class='A0101'>";
                echo "<td width='' class='A0101'>";
                $midDefault = $Mid;
                echo "<table width='100%' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;' bgcolor='#FFFFFF'>";
                echo "<tr bgcolor='$theDefaultColor' onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
                $unitFirst = $Field[$m] - 1;
                echo "<td class='A0001' width='$unitFirst'  $tdBGCOLOR>$Choose&nbsp;$showPurchaseorder</td>";//选项
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$i <input type='hidden' id='overSign$i' value='$overSign'> </td>";      //序号
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$StuffId</td>";  //配件ID
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]'>$StuffCname</td>";    //配件名称
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$Gfile</td>";    //图档
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$OrderQtyInfo</td>";    //历史订单
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$QCImage</td>";  //QC标准图
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$qualityReport</td>"; //品检报告
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$ReachImage</td>"; //REACH
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$DevelopState</td>"; //REACH
                $m = $m + 2;
                $FactualQty = round($FactualQty, $Decimals);
                echo "<td class='A0001' width='$Field[$m]' align='right'>$FactualQty</td>";    //需求数量
                $m = $m + 2;
                $AddQty = round($AddQty, $Decimals);
                echo "<td class='A0001' width='$Field[$m]' align='right'>$AddQty</td>";    //增购数量
                $m = $m + 2;
                $Qty = round($Qty, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$Qty</td>";    //实购数量
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$Price</td>";    //单价
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$UnitName</td>";    //单位
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$Amount</td>";    //金额
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$rmbAmount</td>";  //金额RMB
                $m = $m + 2;
                $rkQty = round($rkQty, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$rkQty</td>";    //收货数量
                $m = $m + 2;
                $llQty = round($llQty, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'><div $llBgColor>$llQty</div></td>";    //领料数量
                $m = $m + 2;
                $Mantissa = round($Mantissa, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'><div $BGcolor>$Mantissa</div></td>";  //欠数数量
                $m = $m + 2;
                $thQty = round($thQty, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$thQty</td>";//退货
                $m = $m + 2;
                $bcQty = round($bcQty, $Decimals);
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$bcQty</td>";//退货
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$AutoSign</td>";//结付状
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$cwEstate</td>";    //结付状态
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$Date</td>";//采购日期
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center' $OnclickStr>$dateSignImage $DeliveryWeek</td>";
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'><div title='$Client : $cName'>$StockIdShow</div></td>";
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$Forshort</td>";//供应商
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='right'>$PreAmount</td>";//订金
                $m = $m + 2;
                echo "<td  class='A0000' width='' align='center'>$OutDate</td>";
                echo "</tr></table>";
                $i++;
            }
            echo $StuffListTB;
        }
    } while ($mainRows = mysql_fetch_array($mainResult));
    echo "</tr></table>";

}
else {
    noRowInfo($tableWidth);
}
//步骤7：
echo '</div>';
$curMonth = date('Y-m');

function List_TitleMy($Th_Col, $Sign, $Height)
{
    if ($Height == 1) {        //高度自动
        $HeightSTR = "height='25'";
    }
    else {
        $HeightSTR = "height='30'";
    }
    $Field = explode("|", $Th_Col);
    $Count = count($Field);
    if ($Sign == 1) {
        $tId = "id='TableHead'";
    }
    $tableWidth = 0;
    for ($i = 0; $i < $Count; $i = $i + 2) {
        $j = $i;
        $k = $j + 1;
        $tableWidth += $Field[$k];
    }
    if (isFireFox() == 1) {
        $tableWidth = $tableWidth + $Count * 2;
    }
    if (isSafari6() == 1) {
        $tableWidth = $tableWidth + ceil($Count * 1.5) + 1;
    }
    if (isGoogleChrome() == 1) {
        $tableWidth = $tableWidth + ceil($Count * 1.5);
    }
    for ($i = 0; $i < $Count; $i = $i + 2) {
        if ($Sign == 1) {
            $Class_Temp = $i == 0 ? "A1111" : "A1101";
        }
        else {
            $Class_Temp = $i == 0 ? "A0111" : "A0101";
        }
        $j = $i;
        $k = $j + 1;
        if (isSafari6() == 0 || isGoogleChrome() == 1) {
            if ($k == ($Count - 1)) {
                $Field[$k] = "";
            }
        }
        $h = $j + 2;
        if (($Field[$j] == "中文名" && $Field[$h] == "&nbsp;") || $Field[$j] == "&nbsp;") {
            $Class_Temp = $Sign == 1 ? "A1100" : "A0100";
        }
        $TableStr .= "<td width='$Field[$k]' Class='$Class_Temp'>$Field[$j]</td>";
    }
    echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $tId><tr $HeightSTR class='' align='center'>" . $TableStr . "</tr></table>";
    echo "<div id='floatTable' class='t-list' style='display: none;'><table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' ><tr height='30' class='' align='center'>" . $TableStr . "</tr></table></div>";
    if ($Sign == 0) {
        echo "<iframe name=\"download\" style=\"display:none\"></iframe>";
    }
}

?>
<div id='divShadow' class="divShadow" style="display:none;">
  <div class='divInfo' id='divInfo'>
    <table width="300">
      <tr>
        <td align="left">请输入请款月份</td>
      </tr>
      <tr>
        <td align="center">
          <input name="Month" type="text" id="Month" value="<?php echo $curMonth; ?>" maxlength="7">
        </td>
      </tr>
      <tr>
        <td align="right"><a href="javascript:ckeckForm()">确定</a> &nbsp;&nbsp;
          <a href="javascript:closeMaskDiv()">取消</a></td>
      </tr>
    </table>
  </div>
</div>
<div id="divPageMask" class="divPageMask" style="display:none;">
  <iframe scrolling="no" height="100%" width="100%" marginwidth="0" marginheight="0" src="../model/MaskBgColor.htm"></iframe>
</div>
<?php
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

<script src='../model/weekdate.js' type=text/javascript></script>
<script src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script language="JavaScript" type="text/JavaScript">
var weekdate = new WeekDate();
function showMaskDiv() {	//显示遮罩对话框
    var formlength = form1.elements.length;
    var tempcheckid = document.getElementsByName("checkid[]");
    /*
    var qkSign =0;
        for (var i=0;i<tempcheckid.length;i++){
                 var k=i+1;
                    if(tempcheckid[i].checked){
                             var overSign = document.getElementById("overSign"+ k).value;
                               if(overSign==1)qkSign=1;
                        }
        }
     if(qkSign==1){
               alert("代购供应商，请款配件必须送货和补仓全部完毕才能请款！"); return false ;
      }
     */
    //检查是否有选取记录
    UpdataIdX = 0;
    for (var i = 0; i < formlength; i++) {
        var e = form1.elements[i];
        if (e.type == "checkbox") {
            var NameTemp = e.name;
            var Name = NameTemp.search("checkid");//防止有其它参数用到checkbox，所以要过滤
            if (e.checked && Name != -1) {
                UpdataIdX = UpdataIdX + 1;
                break;
            }
        }
    }

    //如果没有选记录
    if (UpdataIdX == 0) {
        alert("没有选取记录!");
    }
    else {
        //document.form1.Month.value="";
        document.getElementById('divShadow').style.display = 'block';
        divPageMask.style.width = document.body.scrollWidth;
        divPageMask.style.height = document.body.scrollHeight > document.body.clientHeight ? document.body.scrollHeight : document.body.clientHeight;
        document.getElementById('divPageMask').style.display = 'block';
    }
}

function closeMaskDiv() {	//隐藏遮罩对话框
    document.getElementById('divShadow').style.display = 'none';
    document.getElementById('divPageMask').style.display = 'none';
}

function ckeckForm() {
    //检查月份
    var checkMonth = yyyymmCheck(document.form1.Month.value);
    if (checkMonth) {
        for (var i = 0; i < form1.elements.length; i++) {
            var e = form1.elements[i];
            var NameTemp = e.name;
            var Name = NameTemp.search("checkid");//防止有其它参数用到checkbox，所以要过滤
            if (e.type == "checkbox" && Name != -1) {
                e.disabled = false;
            }
        }
        document.form1.action = "cg_cgdmainP_updated.php?ActionId=14";
        document.form1.submit();
    }
    else {
        alert("格式不对(YYYY-MM)");
    }
}
///以上的是从结付那边拷贝过来...20130913

function clearDeliveryWeek() {
    var StockId = document.getElementById("clearStockId").value;
    if (confirm("确定设置：" + StockId + "的采购交期为待定?")) {
        myurl = "purchaseorder_updated.php?StockId=" + StockId + "&ActionId=jqdd";
        var ajax = InitAjax();
        ajax.open("GET", myurl, true);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4) {
                // alert(ajax.responseText);
                weekdate.hide()
            }
        }
        ajax.send(null);
    }
}


function set_weekdate(el, StockId) {
    document.getElementById("clearStockId").value = StockId;
    var saveFun = function () {
        if (weekdate.Value > 0) {
            var tempWeeks = weekdate.Value.toString();
            tempWeeks = "Week " + tempWeeks.substr(4, 2);
            var tempDeliveryDate = weekdate.getWedday("-");
            var updateWeekRemark = encodeURIComponent(document.getElementById("updateWeekRemark").value);
            myurl = "purchaseorder_updated.php?StockId=" + StockId + "&DeliveryDate=" + tempDeliveryDate + "&updateWeekRemark=" + updateWeekRemark + "&ActionId=jq";
            //  alert(myurl);return;
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {
                    el.innerHTML = tempWeeks;
                }
            }
            ajax.send(null);
        }
    };
    weekdate.show(el, 1, saveFun, "");
}

function updateJq(TableId, runningNum) {//行即表格序号;列，流水号，更新源
    var InfoSTR = "";
    var buttonSTR = "";
    var theDiv = document.getElementById("Jp");
    var tempTableId = document.form1.ActionTableId.value;
    theDiv.style.top = event.clientY + document.body.scrollTop + 'px';
    theDiv.style.left = event.clientX + document.body.scrollLeft - parseInt(theDiv.style.width) + 'px';
    if (theDiv.style.visibility == "hidden" || TableId != tempTableId) {
        document.form1.ActionTableId.value = TableId;
        InfoSTR = "<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='14' class='TM0000' readonly>的采购单交货期:<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
        infoShow.innerHTML = InfoSTR;
        theDiv.className = "moveRtoL";
        if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
            theDiv.filters.revealTrans.apply();//防止错误
            theDiv.filters.revealTrans.play(); //播放
        }
        else {
            theDiv.style.opacity = 0.9;
        }
        theDiv.style.visibility = "";
        theDiv.style.display = "";
    }
}

function CloseDiv() {
    var theDiv = document.getElementById("Jp");
    theDiv.className = "moveLtoR";
    if (isIe()) {
        theDiv.filters.revealTrans.apply();
        theDiv.filters.revealTrans.play();
    }
    theDiv.style.visibility = "hidden";
    infoShow.innerHTML = "";
    closeMaskBack();
}

function aiaxUpdate() {
    var tempTableId = document.form1.ActionTableId.value;
    var temprunningNum = document.form1.runningNum.value;
    var tempDeliveryDate = document.form1.DeliveryDate.value;
    myurl = "purchaseorder_updated.php?StockId=" + temprunningNum + "&DeliveryDate=" + tempDeliveryDate + "&ActionId=jq";

    var ajax = InitAjax();
    ajax.open("GET", myurl, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4) {// && ajax.status ==200
            if (tempDeliveryDate == "") {
                tempDeliveryDate = "未设置";
            }
            var ColorDate = Number(DateDiff(tempDeliveryDate));
            if (ColorDate < 2) {
                eval("ListTable" + tempTableId).rows[0].cells[25].innerHTML = "<div class='redB'>" + tempDeliveryDate + "</div>";
            }
            else {
                if (ColorDate < 5) {
                    eval("ListTable" + tempTableId).rows[0].cells[25].innerHTML = "<div class='yellowB'>" + tempDeliveryDate + "</div>";
                }
                else {
                    eval("ListTable" + tempTableId).rows[0].cells[25].innerHTML = "<div class='greenB'>" + tempDeliveryDate + "</div>";
                }
            }

        }
    }
    ajax.send(null);
    CloseDiv();
}

function zhtj(obj) {
    switch (obj) {
        case "BuyerId"://改变采购
            document.forms["form1"].elements["GysPayMode"].value = "";
            if (document.all("CompanyId") != null) {
                document.forms["form1"].elements["CompanyId"].value = "";
            }
            if (document.all("chooseDate") != null) {
                document.forms["form1"].elements["chooseDate"].value = "";
            }
            break;
        case "GysPayMode":
            if (document.all("CompanyId") != null) {
                document.forms["form1"].elements["CompanyId"].value = "";
            }
            if (document.all("chooseDate") != null) {
                document.forms["form1"].elements["chooseDate"].value = "";
            }
            break;
        case "CompanyId":
            if (document.all("chooseDate") != null) {
                document.forms["form1"].elements["chooseDate"].value = "";
            }
            break;
    }
    document.form1.action = "cg_cgdmain_read.php";
    document.form1.submit();
}

function selectAllOfPO(startRow, rows, theDefaultColor, thePointerColor, theMarkColor, theMerge) {
    //if(ChooseTemp=="false"){
    var e = window.event;
    window.event ? e.cancelBubble = true : e.stopPropagation();

    var e = document.getElementById("checkid" + startRow);
    console.log(e);

    var check = !e.checked;

    var listTable = document.getElementById("ListTable1");
    for (var i = startRow - 1; i < rows + startRow - 1; i++) {
        var row = i + 1;
        var e = document.getElementById("checkid" + row);

//            if(e.checked==false){
//                e.checked=true;
//                chooseRow(listTable,row,"click",theDefaultColor,thePointerColor,theMarkColor,"",theMerge);
//            }
        if (e.checked != check) {
            e.checked = check;
            chooseRow(listTable, row, "click", theDefaultColor, thePointerColor, theMarkColor, "", theMerge);
        }
    }

    ChooseTemp = "true";
}


function ShowAddorUpdate(TableId, RowId, param1, toObj, WebPage, FileDir, divWidth, divHeight) {
    IE_FireFox(); //加入Event，才能兼容FireFox
    showMaskBack();
    var InfoSTR = "";
    var buttonSTR = "";
    var theDiv = document.getElementById("Jp");
    var ObjId = document.form1.ObjId.value;
    var tempTableId = document.form1.ActionTableId.value;

    theDiv.style.width = divWidth + "px";
    theDiv.style.height = divHeight + "px";

    //var ev = event || window.event;

    theDiv.style.top = event.clientY + document.body.scrollTop + 'px';
    if (toObj == -1) {
        theDiv.style.left = event.clientX + document.body.scrollLeft + 'px';
    }
    else {
        theDiv.style.left = event.clientX + document.body.scrollLeft - parseInt(theDiv.style.width) + 'px';
    }
    //theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
    if (theDiv.style.visibility == "hidden" || toObj != ObjId) {

        document.form1.ObjId.value = toObj;
        document.form1.ActionTableId.value = TableId;
        document.form1.ActionRowId.value = RowId;


        DivSandH("" + WebPage + "", FileDir, divWidth, divHeight, param1, toObj);
        //infoShow.innerHTML=InfoSTR+buttonSTR;
        theDiv.className = "moveRtoL";
        if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
            theDiv.filters.revealTrans.apply();//防止错误
            theDiv.filters.revealTrans.play(); //播放
        }
        else {
            theDiv.style.opacity = 0.9;
        }
        theDiv.style.visibility = "";
        theDiv.style.display = "";
    }

}

function DivSandH(WebPage, FileDir, divWidth, divHeight, param1, toObj) {

    if (FileDir == "public") {
        //var url="../"+FileDir+"/"+WebPage+"_mask.php?DeliveryValue="+DeliveryValue;

        var url = WebPage + "?divWidth=" + divHeight + "&divHeight=" + divHeight + "&param1=" + param1 + "&toObj=" + toObj

    }
    else {
        //var url="../admin/"+WebPage+"_mask.php?DeliveryValue="+DeliveryValue;
        var url = "../" + FileDir + "/" + WebPage + "";
    }
    //var show=eval("divInfo");
    var ajax = InitAjax();
    //console.log(url);
    ajax.open("GET", url, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4) {// && ajax.status ==200
            var BackData = ajax.responseText;
            //alert("BackData");
            infoShow.innerHTML = BackData;

        }
    }
    ajax.send(null);
}


function ToPDF(MidSTR) {
    //$PurchaseToPDF="<a href='PurchaseToPDF.php?f=$MidSTR' target='_blank'>生成PDF</a>";
    //alert(MidSTR);
    var SealCompanyId = document.getElementById("SealCompanyId").value;
    //alert(SealCompanyId);
    window.open('PurchaseToPDF.php?f=' + MidSTR + '&SealCompanyId=' + SealCompanyId, 'ToPDF');
    //return false;
    CloseDiv();
}
</script>
<script>
jQuery('.click-PO').mousedown(function () {
    if (jQuery(this).attr('class').indexOf('checked') === -1) {
        jQuery(this).parent().find('input[type="checkbox"]').attr('checked', 'checked');
        jQuery(this).addClass('checked');
    } else {
        jQuery(this).parent().find('input[type="checkbox"]').attr('checked', false);
        jQuery(this).removeClass('checked');
    }

})

</script>