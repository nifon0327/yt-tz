<?php
include "../model/modelhead.php";
//步骤2：需处理
$ColsNumber = 17;
$tableMenuS = 500;
ChangeWtitle("$SubCompany 待购列表");
$funFrom = "cg_cgdsheet";
$From = $From == "" ? "read" : $From;

$sumCols = "6,7,17,18,19,20,21";      //求和列,需处理！！！！！！
$Th_Col = "选项|60|序号|30|采购流水号|110|业务单号|80|下单时间|60|配件ID|45|订单数量|60|含税价|50|税率|50|配件名称|250|供应商|80|品牌|60|采购员|60|操作|100|送货</br>楼层|40|历史<br>资料|40|单位|45|需购<br>数量|45|使用<br>库存|45|增购<br>数量|45|实购<br>数量|45|金额|55|审核</br>状态|35|预定交期|65|采购<br>备注|30|增购备注|160|可用<br>库存|40|最低<br>库存|40";
//必选，分页默认值
$Pagination = 0;  //默认分页方式:1分页，0不分页
$Page_Size = 20;              //每页默认记录数量
$nowWebPage = $funFrom . "_read";
include "../model/subprogram/read_model_3.php";
$SearchRows .= " AND S.blsign = 1 ";//需采购的配件需求单
//步骤4：需处理-可选条件下拉框
if ($From != "slist") {  //非查询：过滤采购、结付方式、供应商、月份
    //检查进入者是否有采购记录:是则默认显示该员工的记录，否则显示读入的第一个员工记录

    $checkSql = mysql_query("SELECT S.Id 
	FROM $DataIn.cg1_stocksheet S 
	INNER JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
	WHERE S.Mid=0 $SearchRows AND (S.FactualQty+S.AddQty)>0 AND S.BuyerId='$Login_P_Number' Limit 1", $link_id);
    if ($checkRow = mysql_fetch_array($checkSql)) {
        $Number = $Number == "" ? $Login_P_Number : $Number;//首次打开页面时，如果员工有采购记录，则为默认采购
    }

    $ActioToS = "1,2,3,4,22,26,13,51,21";

    //特采单
    echo "<select name='bigClass' id='bigClass' onchange='document.form1.submit();'>";
    if ($bigClass == "9002" || $bigClass == '') {
        echo "<option value='9002' selected>钢筋半成品</option>";
        $SearchRows .= " and T.TypeId = '9002' ";
    }
    else {
        echo "<option value='9002' >钢筋半成品</option>";
    }
    if ($bigClass == "9006") {
        echo "<option value='9006' selected>混凝土</option>";
        $SearchRows .= " and T.TypeId = '9006' ";
    }
    else {
        echo "<option value='9006' >混凝土</option>";
    }
    if ($bigClass == "9019") {
        echo "<option value='9019' selected>预埋</option>";
        $SearchRows .= " and T.TypeId = '9019' ";
    }
    else {
        echo "<option value='9019' >预埋</option>";
    }

    if ($bigClass == "tc") {
        echo "<option value='tc' selected>特采单</option>";
        $SearchRows .= " and S.AddRemark  != '' ";
    }
    else {
        echo "<option value='tc' >特采</option>";
    }
    if ($bigClass == "tcgc") {
        echo "<option value='tcgc' selected>特采钢材</option>";
        $SearchRows .= " and S.AddRemark  != '' and T.TypeId = '9018' ";
    }
    else {
        echo "<option value='tcgc' >特采钢材</option>";
    }
    if ($bigClass == "tcym") {
        echo "<option value='tcym' selected>特采预埋</option>";
        $SearchRows .= " and S.AddRemark  != '' and T.TypeId = '9019' ";
    }
    else {
        echo "<option value='tcym' >特采预埋</option>";
    }
    echo "</select>&nbsp;";

//项目名称
        $mysql = "select a.Letter, a.Forshort, b.TradeNo, a.CompanyId
            from $DataIn.trade_object a
            INNER join $DataIn.trade_info b on a.id = b.TradeId
            where a.ObjectSign = 2 order by a.Letter";

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

                if ($TradeNo == $ThisTradeNo) {
                    echo "<option value='$ThisTradeNo' selected>$Forshort</option>";
                    $SearchRows .= " and M.CompanyId = '$ThisTradeNo' ";
                }
                else {
                    echo "<option value='$ThisTradeNo'>$Forshort</option>";
                }
        } while ($tradeRow = mysql_fetch_array($tradeSql));
        echo "</select>&nbsp;";
    }


    //交期
    $dateSql = mysql_query("SELECT
	S.DeliveryWeek
FROM
	cg1_stocksheet S
	LEFT JOIN yw1_ordersheet Y ON Y.POrderId = S.POrderId
	LEFT JOIN yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
WHERE
	1 $SearchRows AND S.Mid = 0 AND ( S.FactualQty + S.AddQty ) > 0 group by S.DeliveryWeek ASC ", $link_id);
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

    //采购
    if ($Login_P_Number !=10029  && $Login_P_Number !=10057 && $Login_P_Number !=10024) {
        $mysql = "SELECT  S.BuyerId,IF(S.BuyerId=0,'未设置采购',I.Name) AS Name 
	FROM $DataIn.cg1_stocksheet S 
    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
	LEFT JOIN $DataPublic.staffmain I ON S.BuyerId=I.Number 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	WHERE S.Mid=0 $SearchRows AND (S.FactualQty+S.AddQty)>0 GROUP BY S.BuyerId ORDER BY BuyerId";
    }else{
        $mysql = "SELECT  S.BuyerId,IF(S.BuyerId=0,'未设置采购',I.Name) AS Name 
	FROM $DataIn.cg1_stocksheet S 
    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
	LEFT JOIN $DataPublic.staffmain I ON S.BuyerId=I.Number 
	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	WHERE S.Mid=0 $SearchRows AND (S.FactualQty+S.AddQty)>0 and I.Number=$Login_P_Number GROUP BY S.BuyerId ORDER BY BuyerId";
    }
    $buyerSql = mysql_query("$mysql", $link_id);
    if ($buyerRow = mysql_fetch_array($buyerSql)) {
        echo "<select name='Number' id='Number' onchange='zhtj()'>";
        echo "<option value='all' selected>全部采购</option>";
        do {
            $thisBuyerId = $buyerRow["BuyerId"];
            $Buyer = $buyerRow["Name"];
            if ($thisBuyerId != "" && $Buyer != "未设置采购") {
                $Number = $Number == "" ? $thisBuyerId : $Number;
            }

            if ($Number == $thisBuyerId) {
                echo "<option value='$thisBuyerId' selected>$Buyer</option>";
                $SearchRows .= " AND S.BuyerId='$thisBuyerId'";
            }
            else {
                echo "<option value='$thisBuyerId'>$Buyer</option>";
            }
        } while ($buyerRow = mysql_fetch_array($buyerSql));
        echo "</select>&nbsp;";
    }
    //供应商
    $providerSql = mysql_query("SELECT 
	S.CompanyId,IF(S.CompanyId=0,'未设置供应商',P.Forshort) AS Forshort,P.Letter 
	FROM $DataIn.cg1_stocksheet S
    LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
	INNER JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
	WHERE 1 $SearchRows and S.Mid=0 and (S.FactualQty+S.AddQty)>0   
	GROUP BY S.CompanyId ORDER BY P.Letter", $link_id);
    if ($providerRow = mysql_fetch_array($providerSql)) {
        echo "<select name='CompanyId' id='CompanyId' onchange='document.form1.submit();'>";
        echo "<option value='all' selected>全部供应商</option>";
        do {
            $Letter = $providerRow["Letter"];
            $Forshort = $providerRow["Forshort"];
            $Forshort = $Letter . '-' . $Forshort;
            $thisCompanyId = $providerRow["CompanyId"];
            $CompanyId = $CompanyId == "" ? $thisCompanyId : $CompanyId;

            if ($CompanyId == $thisCompanyId) {
                echo "<option value='$thisCompanyId' selected>$Forshort</option>";
                $SearchRows .= " and S.CompanyId='$thisCompanyId'";
            }
            else {
                echo "<option value='$thisCompanyId'>$Forshort</option>";
            }
        } while ($providerRow = mysql_fetch_array($providerSql));
        echo "</select>&nbsp;";
    }
    else {

    }

    //分类
    $typeSql = mysql_query("SELECT
	P.TypeName, PD.TypeId 
FROM
	$DataIn.cg1_stocksheet S
	LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = S.POrderId
	INNER JOIN $DataIn.productdata PD ON PD.ProductId = Y.ProductId
	INNER JOIN producttype P ON P.TypeId = PD.TypeId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId = S.StuffId
	LEFT JOIN $DataIn.stuffunit U ON U.Id = A.Unit
	LEFT JOIN $DataIn.stufftype T ON T.TypeId = A.TypeId 
WHERE
	1 
	$SearchRows
	AND S.Mid = 0 
	AND ( S.FactualQty + S.AddQty ) > 0 
GROUP BY
	P.TypeName", $link_id);
    if ($typeRow = mysql_fetch_array($typeSql)) {
        echo "<select name='TypeId' id='TypeId' onchange='document.form1.submit();'>";
        do {
            $theTypeId = $typeRow["TypeId"];
            $TypeName = $typeRow["TypeName"];
            $TypeId = $TypeId == '' ? $theTypeId : $TypeId;
            if ($TypeId == $theTypeId) {
                echo "<option value='$theTypeId' style= 'font-weight: bold' selected>$TypeName</option>";
                $SearchRows .= " AND PD.TypeId='$theTypeId'";
            }
            else {
                echo "<option value='$theTypeId' style= 'font-weight: bold'>$TypeName</option>";
            }
        } while ($typeRow = mysql_fetch_array($typeSql));
        echo "</select>&nbsp;";
    }


    //栋号
    $orderPOSql = mysql_query("SELECT M.BuildNo
		           FROM $DataIn.cg1_stocksheet S
		           LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
		           	INNER JOIN $DataIn.productdata PD ON PD.ProductId = Y.ProductId
                  LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
		           INNER JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
		           INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		           LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
		           WHERE 1 $SearchRows and Y.OrderPO is not null and S.Mid=0 and (S.FactualQty+S.AddQty)>0
		           GROUP BY M.BuildNo ORDER BY M.BuildNo", $link_id);
    if ($orderPORow = mysql_fetch_array($orderPOSql)) {
        echo "<select name='BuildNo' id='BuildNo' onchange='ResetPage_cg(1);'>";
        do {
            $thisBuildNo = $orderPORow["BuildNo"];

            $thisBuildNo = $thisBuildNo == "" ? "未设置栋号" : $thisBuildNo;
            $BuildNo = $BuildNo == "" ? $thisBuildNo : $BuildNo;
            if ($BuildNo == $thisBuildNo) {
                echo "<option value='$thisBuildNo' selected>$thisBuildNo</option>";
                $SearchRows .= " and M.BuildNo='$thisBuildNo'";
            }
            else {
                echo "<option value='$thisBuildNo'>$thisBuildNo</option>";
            }
        } while ($orderPORow = mysql_fetch_array($orderPOSql));
        echo "</select>&nbsp;";
    }


    //PO
    $orderPOSql = mysql_query("SELECT Y.OrderPO
		           FROM $DataIn.cg1_stocksheet S
		           LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
		           INNER JOIN $DataIn.productdata PD ON PD.ProductId = Y.ProductId
                  LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
		           INNER JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
		           INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		           LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
		           WHERE 1 $SearchRows and Y.OrderPO is not null and S.Mid=0 and (S.FactualQty+S.AddQty)>0
		           GROUP BY Y.OrderPO ORDER BY Y.OrderPO", $link_id);
    if ($orderPORow = mysql_fetch_array($orderPOSql)) {
        echo "<select name='OrderPO' id='OrderPO' onchange='document.form1.submit();'>";
        do {
            $thisOrderPO = $orderPORow["OrderPO"];
            $OrderPO = $OrderPO == "" ? $thisOrderPO : $OrderPO;

            if ($OrderPO == $thisOrderPO) {
                echo "<option value='$thisOrderPO' selected>$OrderPO</option>";
                $SearchRows .= " and Y.OrderPO='$thisOrderPO'";
            }
            else {
                echo "<option value='$thisOrderPO'>$thisOrderPO</option>";
            }
        } while ($orderPORow = mysql_fetch_array($orderPOSql));
        echo "</select>&nbsp;";
    }

    //原材料
    $TypeNameSql = mysql_query("SELECT ST.TypeName, SP.Property
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
INNER JOIN $DataIn.productdata PD ON PD.ProductId = Y.ProductId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
LEFT JOIN $DataIn.stuffproperty SP ON SP.StuffId = S.StuffId 
LEFT JOIN $DataIn.stuffpropertytype ST ON ST.Id = SP.Property 
WHERE 1 $SearchRows and S.Mid=0 and (S.FactualQty+S.AddQty)>0 GROUP BY ST.TypeName ", $link_id);

    if ($TypeNameRow = mysql_fetch_array($TypeNameSql)) {
        echo "<select name='PTypeName' id='PTypeName' onchange='document.form1.submit();'>";
        //echo "<option value='' selected></option>";
        do {
            $thisTypeName = $TypeNameRow["TypeName"];
            $Property = $TypeNameRow["Property"];
            $PTypeName = $PTypeName == "" ? "18" : $PTypeName;
            if ($PTypeName == $Property) {
                echo "<option value='$Property' selected>$thisTypeName</option>";
                $SearchRows .= " and SP.Property='$Property'";
            }
            else {
                echo "<option value='$Property'>$thisTypeName</option>";
            }
        } while ($TypeNameRow = mysql_fetch_array($TypeNameSql));
        echo "</select>&nbsp;";
    }

    //正常单，锁定单
    $LockEstate = $LockEstate == "" ? 1 : $LockEstate;
    $LockStr = "LockEstate" . $LockEstate;
    $$LockStr = "selected";
    echo "<select name='LockEstate' id='LockEstate' onchange='document.form1.submit();'>";
    echo "<option value='0' $LockEstate0>全部</option>";
    echo "<option value='1' $LockEstate1>正常单</option>";
    echo "<option value='2' $LockEstate2>锁定单</option>";
    echo "</select>&nbsp;";

}
else {
    $ActioToS = "3,4,22,26,13,51,21";

}
//检查进入者是否采购
$checkResult = mysql_query("SELECT JobId FROM $DataIn.staffmain WHERE Number=$Login_P_Number order by Id LIMIT 1", $link_id);
if ($checkRow = mysql_fetch_array($checkResult)) {
    $JobId = $checkRow["JobId"];//3为采购
}

$numberArray = array();
if ($APP_CONFIG['PROCUREMENT_BRANCHID'] == $Login_BranchId || in_array($Login_GroupId, $APP_CONFIG['IT_DEVELOP_GROUPID'])) {
    $numberArray[] = $Login_P_Number;
}
$numberArray[] = 10019;

echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr ";

echo "&nbsp;&nbsp;<span name='Submit' value='导入采购信息' onClick='ToImportPrice()' class='btn-confirm' style='width: auto;font-size: 12px'>导入采购信息</span>";
echo "&nbsp;&nbsp;<span name='Submit' value='导出采购信息' onClick='ToExportPrice()' class='btn-confirm' style='width: auto;font-size: 12px'>导出采购信息</span>";
echo "&nbsp;&nbsp;<span name='Submit' value='导出采购汇总' onClick='ToExportAll()' class='btn-confirm' style='width: auto;font-size: 12px'>导出采购汇总</span>";
//步骤5：
$helpFile = 0;
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 1);
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek", $link_id));
$curWeeks = $dateResult["CurWeek"];


$mySql = "SELECT S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,S.cgSign,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,
S.DeliveryDate,S.DeliveryWeek,S.StockRemark,S.AddRemark,S.Estate,S.Locks,Y.OrderPO,A.StuffCname,A.Gfile,A.Gstate,A.Gremark,A.Picture,A.SendFloor,
A.TypeId,A.DevelopState,A.Price AS  DefaultPrice ,A.NoTaxPrice,U.Name AS UnitName,A.ForcePicSpe,T.ForcePicSign,TIMESTAMPDIFF(DAY,S.ywOrderDTime,NOW()) AS xdDays,A.PriceDetermined,
O.Forshort,I.Name AS BuyerName,A.Remark,IFNULL(X.Name,'未设置') AS TaxName  
FROM $DataIn.cg1_stocksheet S
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
LEFT JOIN $DataIn.productdata PD ON PD.ProductId=Y.ProductId
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit
LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId  
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = S.CompanyId 
LEFT JOIN $DataIn.providersheet P ON P.CompanyId=S.CompanyId
LEFT JOIN $DataIn.provider_addtax X ON X.Id = P.AddValueTax 
LEFT JOIN $DataIn.staffmain I ON I.Number=S.BuyerId
LEFT JOIN $DataIn.stuffproperty SP ON SP.StuffId = S.StuffId 
WHERE 1 $SearchRows and S.Mid=0 and (S.FactualQty+S.AddQty)>0 ORDER BY Y.OrderPO,S.StuffId DESC,S.ywOrderDTime ";
//if ($Login_P_Number==10871)
//echo $mySql;

$myResult = mysql_query($mySql . " $PageSTR", $link_id);
$tempStuffId = "";
$DefaultBgColor = $theDefaultColor;
if ($myRow = mysql_fetch_array($myResult)) {
    $d = anmaIn("download/stufffile/", $SinkOrder, $motherSTR);

    $RowsArr = array();
    $Data = array();
    do {
        $m = 1;
        $LockRemark = "";
        $OrderPO = toSpace($myRow["OrderPO"]);
        $POrderId = $myRow["POrderId"];
        $tdBGCOLOR = $POrderId == "" ? "bgcolor='#FFCC99'" : "";
        $PQty = $myRow["PQty"];
        $PackRemark = $myRow["PackRemark"];
        $sgRemark = $myRow["sgRemark"];
        $ShipType = $myRow["ShipType"];
        $cgSign = $myRow["cgSign"];

        $theDefaultColor = $DefaultBgColor;
        $OrderSignColor = $POrderId == "" ? "bgcolor='#FFCC99'" : "";

        $xdDays = $myRow["xdDays"];
        $xdDays = $xdDays == 0 ? "today" : $xdDays . "d";

        $Id = $myRow["Id"];
        $StockId = $myRow["StockId"];
        //加急订单标色
        include "../model/subprogram/cg_cgd_jj.php";
        $StuffId = $myRow["StuffId"];
        $cName = $myRow["cName"];
        $Client = $myRow["Client"];
        $tempStockId = $StockId;
        $StuffCname = $myRow["StuffCname"];
        $TypeId = $myRow["TypeId"];
        //配件QC检验标准图
        $QCImage = "";
//        include "../model/subprogram/stuffimg_qcfile.php";
        $QCImage = $QCImage == "" ? "&nbsp;" : $QCImage;
        $Gremark = $myRow["Gremark"];
        $Gfile = $myRow["Gfile"];
        $tempGfile = $Gfile;  ////2012-10-29
        $Gstate = $myRow["Gstate"];
        //REACH 法规图
//        include "../model/subprogram/stuffreach_file.php";
        //=====
//        include "../model/subprogram/stuffimg_Gfile.php";  //图档显示
        //检查是否有图片
        $Picture = $myRow["Picture"];
//        include "../model/subprogram/stuffimg_model.php";
        include "../model/subprogram/stuff_Property.php";//配件属性

        $ForcePicSpe = $myRow["ForcePicSpe"];
        $ForcePicSign = $myRow["ForcePicSign"];

        if ($ForcePicSpe >= 0) {  //-1表示用stufftype用的，否则用它指定
            $ForcePicSign = $ForcePicSpe;
        }

        $sql = "SELECT StockId FROM $DataIn.cg1_unlockstock  WHERE StockId='$tempStockId'";
        $Sresult = mysql_query($sql, $link_id);
        if (mysql_num_rows($Sresult) <= 0) {
            switch ($ForcePicSign) {
                case 0:
                    $ForcePicSign = "无图需求";
                    break;
                case 1:
                    $ForcePicSign = "需要图片";
                    if ($Picture != 1) {  //需要图片，而无图片或重新上传或需要审核
                        $LockRemark = "需要图片?重新上传中?正在审核";
                    }
                    break;
                case 2:
                    $ForcePicSign = "需要图档";
                    if ($Gstate != 1 || $tempGfile == "") {  //需要图档，而无图档或重新上传或需要审核
                        $LockRemark = "需要图档?重新上传中?正在审核";
                    }
                    break;
                case 3:
                    $ForcePicSign = "图片/图档";
                    if ($Picture != 1 || $Gstate != 1 || $tempGfile == "") {  //需要图片/图档，而无图片/图档或重新上传或需要审核
                        $LockRemark = "需要图片和图档?重新上传中?正在审核";
                    }
                    break;
                case 4:
                    $ForcePicSign = "强行锁定";
                    $LockRemark = "强行锁定中，请配件资料管理人解除";
                    break;
            }
        }


        $SendFloor = $myRow["SendFloor"];
        include "../model/subprogram/stuff_GetFloor.php";
        $OrderQty = $myRow["OrderQty"];
        $StockQty = $myRow["StockQty"];
        $AddQty = $myRow["AddQty"];
        $FactualQty = $myRow["FactualQty"];
        $Qty = $AddQty + $FactualQty;
        $Price = $myRow["Price"];
        $Amount = sprintf("%.2f", $Qty * $Price);//本记录金额合计
        $PriceDetermined = $myRow["PriceDetermined"];

        //默认单价
        $DefaultPrice = $myRow["DefaultPrice"];
        //$NoTaxPrice=$myRow["NoTaxPrice"];
        $PriceTitle = "";
        if ($PriceDetermined == 1 && $Price == 0.00) {
            $Price = "<span class='redB'>价格待定</span>";
        }
        else if ($DefaultPrice != $Price) {
            $Price = "<div class='redB'>$Price</div>";
            $PriceTitle = "Title=\"默认单价：$DefaultPrice\"";
        }

        //默认供应商
        $CompanyId = $myRow["CompanyId"];
        $providerRes = mysql_query("SELECT S.CompanyId,P.Forshort FROM $DataIn.bps S 
								LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
								WHERE S.StuffId='$StuffId'", $link_id);
        if ($providerRow = mysql_fetch_array($providerRes)) {
            $DefaultCompanyId = $providerRow["CompanyId"];
            $DefaultForshort = $providerRow["Forshort"];
        }

        if ($DefaultCompanyId != $CompanyId && $DefaultCompanyId != "") {
            $WarnRemark = "默认供应商已更改为:$DefaultForshort";
            $OrderSignColor = "bgcolor='#FFFF00'";
        }
        else {
            $WarnRemark = "";
        }

        $Estate = $myRow["Estate"];
        $UnitName = $myRow["UnitName"] == "" ? "&nbsp;" : $myRow["UnitName"];
        $StockRemark = $myRow["StockRemark"];
        $StockRemarkTB = "<input type='hidden' id='StockRemark$i' name='StockRemark$i' value='$StockRemark'/>";
        if ($StockRemark == "") {
            $StockRemark = "&nbsp;";
        }
        else {
            $StockRemark = "<div title='$StockRemark'><img src='../images/remark.gif'></div>";
        }
        $AddRemark = $myRow["AddRemark"] == "" ? "&nbsp;" : $myRow["AddRemark"];


        $Locks = 1;
        $checkKC = mysql_fetch_array(mysql_query("SELECT oStockQty,mStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' ORDER BY StuffId", $link_id));
        $oStockQty = $checkKC["oStockQty"];
        $mStockQty = $checkKC["mStockQty"] == 0 ? "&nbsp;" : $checkKC["mStockQty"];

        //清0

        $checkNum = mysql_query("SELECT S.StuffId FROM $DataIn.cg1_stocksheet S
	                          WHERE S.StuffId=$StuffId and S.Mid!=0 LIMIT 1", $link_id);
        if ($checkRow = mysql_fetch_array($checkNum)) {
            $OrderQtyInfo = "<a href='cg_historyorder.php?StuffId=$StuffId&Id=$Id' target='_blank'>查看</a>";
        }
        else {
            $OrderQtyInfo = "&nbsp;";
        }

        $OrderQty = zerotospace($OrderQty);
        $StockQty = zerotospace($StockQty);
        $FactualQty = zerotospace($FactualQty);
        $AddQty = zerotospace($AddQty);
        $oStockQty = zerotospace($oStockQty);
        if ($mStockQty > 0) {
            $mStockColor = "title='最低库存:$mStockQty'";
            $oStockQty = "<span style='color:#FF9900;font-weight:bold;'>$oStockQty</span>";
        }
        else {
            $mStockColor = "";
        }

        //检查是否未确定产品，是则锁定并标底色
        $CheckSignSql = mysql_query("SELECT Id FROM $DataIn.yw2_orderexpress WHERE POrderId ='$POrderId' AND Type='2' LIMIT 1", $link_id);
        if ($CheckSignRow = mysql_fetch_array($CheckSignSql)) {
            $LockRemark = "业务锁定,未确定产品";
            $OrderSignColor = "bgcolor='#FF0000'";
        }

        //检查是否锁定
        if ($cgSign == 0) {

            $CheckSignSql = mysql_query("SELECT Id FROM $DataIn.cg1_lockstock WHERE StockId ='$tempStockId' AND Locks=0 LIMIT 1", $link_id);
            if ($CheckSignRow = mysql_fetch_array($CheckSignSql)) {
                $LockRemark = "采购锁定,未确定产品";
                $OrderSignColor = "bgcolor='#FF0000'";
            }
            else {
                //echo "SELECT getStockIdLock('$tempStockId') AS Locks";
                $LocksResult = mysql_fetch_array(mysql_query("SELECT getStockIdLock('$tempStockId') AS Locks", $link_id));
                $mLocks = $LocksResult['Locks'];

                if ($mLocks > 0) {
                    $LockRemark = "半成品锁定";
                    $OrderSignColor = "bgcolor='#FF0000'";
                }
            }

        }
        $Estate = $Estate == 0 ? "<div class='greenB'>√</div>" : "<div class='redB'>×</div>";

        $DevelopWeekState = 1;
        $DevelopState = $myRow["DevelopState"];
//        include "../model/subprogram/stuff_developstate.php";


        $CheckStuffBuyResult = mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.stuffbuy WHERE StuffId=$StuffId", $link_id));
        $CheckStuffBuyId = $CheckStuffBuyResult["Id"];
        if ($CheckStuffBuyId != "") {
            $OrderSignColor = "bgcolor='#BF3EFF'";
        }

        if ($LockEstate == 1 && $LockRemark != "") continue;
        if ($LockEstate == 2 && $LockRemark == "") continue;


        //交货周计算
        $DeliveryDate = $myRow["DeliveryDate"];
        $DeliveryWeek = $myRow["DeliveryWeek"];
        include "../model/subprogram/deliveryweek_toweek.php";
//        $showPurchaseorder = "<img onClick='ShowOrHideThisLayer(StuffList$i,showtable$i,StuffList$i,\"$tempStockId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif'
//		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
        $XtableWidth = 0;
        $ii = 999 - $i;
        $StuffListTB = "<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none;position: absolute;z-index:$ii;'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30' align='left'><br><div id='showStuffTB$i' width='$XsubTableWidth'>&nbsp;</div><br></td></tr></table>";
        //交货日期颜色
        $OnclickStr = "";
        if (in_array($Login_P_Number, $numberArray)) {
            $OnclickStr = "onclick='set_weekdate(this,$StockId)' style='CURSOR: pointer;'";
        }

        $StockId = "<div title='$Client : $cName'>$StockId</div>";

        $ValueArray = array(
            array(0 => $StockId, 1 => "align='center'"),
            array(0 => $OrderPO, 1 => "align='center'"),
            array(0 => $xdDays, 1 => "align='center'"),
            array(0 => $StuffId, 1 => "align='center'"),
            array(0 => $OrderQty, 1 => "align='right'"),
            array(0 => $Price, 1 => "align='right' $PriceTitle"),
            array(0 => $myRow['TaxName'], 1 => "align='center'"),
            array(0 => $StuffCname),
            array(0 => $myRow['Forshort'], 1 => "align='center'"),
            array(0 => "", 1 => "align='center'"),  //品牌
            array(0 => $myRow['BuyerName'], 1 => "align='center'"),
            array(0 => "", 1 => "align='center'"),
            array(0 => $SendFloor, 1 => "align='center'"),
            array(0 => $OrderQtyInfo, 1 => "align='center'"),
            array(0 => $UnitName, 1 => "align='center'"),
            array(0 => $FactualQty, 1 => "align='right' $FactualColor"),
            array(0 => $StockQty, 1 => "align='right'"),
            array(0 => $AddQty, 1 => "align='right'"),
            array(0 => $Qty, 1 => "align='right'"),
            array(0 => $Amount, 1 => "align='right'"),
            array(0 => $Estate, 1 => "align='center'"),
            array(0 => $DeliveryWeek, 1 => "align='center'", 2 => $OnclickStr),
            array(
                0 => $StockRemark, 1 => "align='center' style='CURSOR: pointer'",
                2 => "onmousedown='window.event.cancelBubble=true;' onclick='addRemarks($i,$Id)'",
            ),
            array(0 => $AddRemark),
            array(0 => $oStockQty, 1 => "align='center' $mStockColor"),
            array(0 => $mStockQty, 1 => "align='center'"),
        );
        $checkidValue = $Id;
        $i++;
        $j++;

        $RowsArr[$OrderPO] = $RowsArr[$OrderPO] + 1;
        $RowsArr[$OrderPO . $StuffId] = $RowsArr[$OrderPO . $StuffId] + 1;

        //采购单明细表 - ID
        $Ids = $RowsArr[$OrderPO . $StuffId . "Ids"];
        if ($Ids) {
            $Ids = $Ids . "," . $Id;
        }
        else {
            $Ids = $Id;
        }
        $RowsArr[$OrderPO . $StuffId . "Ids"] = $Ids;

        //供应商
        $RowsArr[$OrderPO . $StuffId . "CompanyId"] = $CompanyId;
        //采购人员
        $RowsArr[$OrderPO . $StuffId . "BuyerId"] = $myRow["BuyerId"];
        //价格
        $RowsArr[$OrderPO . $StuffId . "Price"] = $myRow["Price"];

        //订单数量
        $OrderQtyTotal = $RowsArr[$OrderPO . $StuffId . "OrderQty"];
        if ($OrderQtyTotal) {
            $OrderQtyTotal = $OrderQtyTotal + $OrderQty;
        }
        else {
            $OrderQtyTotal = $OrderQty;
        }
        $RowsArr[$OrderPO . $StuffId . "OrderQty"] = $OrderQtyTotal;


        $Data[] = array(
            "OrderPO"           => $OrderPO,
            "StuffId"           => $StuffId,
            "checkid"           => $checkidValue,
            "valueArray"        => $ValueArray,
            "showPurchaseorder" => $showPurchaseorder,
            "StuffListTB"       => $StuffListTB,
            "StockRemarkTB"     => $StockRemarkTB,
        );

    } while ($myRow = mysql_fetch_array($myResult));

    $i = 1;
    $j = 1;

    echo "<table width='$tableWidth' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
    foreach ($Data as $Row) {

        $m = 1;

        $OrderPO = $Row["OrderPO"];
        $StuffId = $Row["StuffId"];
        $checkidValue = $Row["checkid"];
        $ValueArray = $Row["valueArray"];

        $showPurchaseorder = $Row["showPurchaseorder"];
        $StuffListTB = $Row["StuffListTB"];
        $StockRemarkTB = $Row["StockRemarkTB"];

        if (($Keys & mUPDATE) || ($Keys & mDELETE) || ($Keys & mLOCK)) {//有权限
            if ($LockRemark != "") {
                $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
            }
            else {
                $Choose = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue'  disabled><img src='../images/unlock.png' width='15' height='15'>";
            }
        }
        else {//无权限
            $Choose = "&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
        }

        //$ColsNumber着色列数
        echo "<tr bgcolor='$theDefaultColor'
		    onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);'
		    onmouseover='setPointer(this,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'
		    onmouseout='setPointer(this,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
        $ColbgColor = $ColbgColor == "" ? "bgcolor='#FFFFFF'" : $ColbgColor;

        echo "<td class='A0111' width='$Field[$m]' height='25' align='center' $ColbgColor style='position:relative'>$Choose&nbsp;$showPurchaseorder $StuffListTB $StockRemarkTB</td>";
        $m = $m + 2;
        echo "<td class='A0101' width='$Field[$m]' height='25' align='center' $OrderSignColor title='$WarnRemark'>$j</td>";//$OrderSignColor为订单状态标记色

        for ($k = 0; $k < count($ValueArray); $k++) {
            if ($ValueArray[$k][4] == "") {
                $m = $m + 2;
                $Value0 = $Value0_Title = $ValueArray[$k][0];
                if (isSafari6() == 0) {
                    if ($m == ($Count - 1)) {
                        $Field[$m] = "";
                    }
                }

                if ($k == 4) {
                    //订单数量
                    $OrderQtyTotal = $RowsArr[$OrderPO . $StuffId . "OrderQty"];
                    $Value0 = strlen($OrderQtyTotal) <= 0 ? "&nbsp;" : $OrderQtyTotal;

                }
                else if ($k == 11) {
                    //$pIds = $RowsArr[$OrderPO.$StuffId."pIds"];
                    $Ids = $RowsArr[$OrderPO . $StuffId . "Ids"];
                    $CompanyId = $RowsArr[$OrderPO . $StuffId . "CompanyId"];
                    $BuyerId = $RowsArr[$OrderPO . $StuffId . "BuyerId"];
                    $Price = $RowsArr[$OrderPO . $StuffId . "Price"];

                    $Value0 = " <a href='javascript:volid(0);' onclick='ToSetBuy(this, \"$Ids\", \"$CompanyId\", \"$BuyerId\", \"$Price\");'>设置采购信息</a>";
                }
                else {
                    $Value0 = strlen($Value0) <= 0 ? "&nbsp;" : $Value0;
                }
                if ($k == 1 || $k == 2) {
                    //PO PO时间
                    if ($OrderPO != $tempOrderPO) {
                        $tempStuffId = '';
                        if ($k == 2) $tempOrderPO = $OrderPO;
                        $rows = $RowsArr[$OrderPO];
                        echo "<td rowspan='$rows' class='A0101' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . " onmousedown='selectAllOfPO(\"$i\", \"$rows\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"$ColsNumber\");' onmouseover='eventStop();'>" . $Value0 . "</td>";
                    }

                }
                else if ($k >= 3 && $k <= 8) {
                    //配件id
                    if ($StuffId != $tempStuffId) {
                        $rows = $RowsArr[$OrderPO . $StuffId];
                        $downevent = "onmousedown='selectAllOfPO($i, $rows, \"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",$ColsNumber);' onmouseover='eventStop();' ";

                        if ($k == 8) {
                            $tempStuffId = $StuffId;
                            $downevent = "click='eventStop();' onmouseover='eventStop();' ";
                        }
                        echo "<td rowspan='$rows' class='A0101' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . " $downevent >" . $Value0 . "</td>";

                    }

                }
                else {
                    echo "<td  class='A0101' width='$Field[$m]' " . $ValueArray[$k][1] . " " . $ValueArray[$k][2] . ">" . $Value0 . "</td>";

                }
            }
        }
        echo "</tr>";
        $i++;
        $j++;

    }

    echo "</table>";
}
if ($i == 1) {
    noRowInfo($tableWidth);
}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

<div id='Jp' style='position:absolute; left:341px; top:229px; width:550px; height:230px;z-index:1;visibility:hidden;background:#FFF;' tabIndex=0>
  <div style='position:absolute;left:50px; top:30px;width:450px;height:170px;'>
    <table style='width:100%;height:100%;'>
      <tr>
        <td width="100px">供应商选择</td>
        <td>
            <?php
            $providerSql = mysql_query("select a.CompanyId, a.Letter, a.Forshort
                        from $DataIn.trade_object a 
                        where a.ProviderType >= 0
                        ORDER BY a.Letter", $link_id);
            if ($providerRow = mysql_fetch_array($providerSql)) {
                echo "<select name='sgCompanyId' id='sgCompanyId' >";
                echo "<option value='' selected>选择</option>";
                do {
                    $Letter = $providerRow["Letter"];
                    $Forshort = $providerRow["Forshort"];
                    $Forshort = $Letter . '-' . $Forshort;
                    $thisCompanyId = $providerRow["CompanyId"];

                    echo "<option value='$thisCompanyId'>$Forshort</option>";

                } while ($providerRow = mysql_fetch_array($providerSql));
                echo "</select>&nbsp;";
            }
            ?>
        </td>
      </tr>
      <tr>
        <td>采购人员选择</td>
        <td>
            <?php
            $staffSql = mysql_query("SELECT M.Number,M.Name as staffname FROM $DataPublic.staffmain M  
		                  WHERE M.Estate>0 AND M.BranchId IN (4,110)", $link_id);
            if ($staffRow = mysql_fetch_array($staffSql)) {
                echo "<select name='sgNumber' id='sgNumber' >";
                echo "<option value='' selected>选择</option>";
                do {
                    $pNumber = $staffRow["Number"];
                    $PName = $staffRow["staffname"];

                    echo "<option value='$pNumber'>$PName</option>";

                } while ($staffRow = mysql_fetch_array($staffSql));
                echo "</select>&nbsp;";
            }
            ?>
        </td>
      </tr>
      <tr>
        <td>采购价格</td>
        <td><input name='sgPrice' type='text' id='sgPrice' size='20' class='INPUT0100'></td>
      </tr>
      <tr>
        <td colspan='2' align="center">
          <input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='确认' onclick='aiaxUpdateBuy()' style="width:60px;height:24px;">&nbsp;&nbsp;
          <input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()' style="width:60px;height:24px;">
        </td>
      </tr>
    </table>
    <input type="hidden" name="sgIds" id="sgIds">
  </div>
</div>

<script language="JavaScript">
function zhtj() {
    if (document.all("CompanyId") != null) {
        document.forms["form1"].elements["CompanyId"].value = "";
    }
    document.form1.action = "cg_cgdsheet_read.php";
    document.form1.submit();
}

function addRemarks(index, Ids) {

    var Stockid = "StockRemark" + index;
    var oldStr = document.getElementById(Stockid).value;
    var inputStr = prompt("请输入采购备注", oldStr);
    if (inputStr) {
        inputStr = inputStr.replace(/(^\s*)|(\s*$)/g, "");  //去除前后空格
        var url = "cg_cgdsheet_updated.php?Id=" + Ids + "&ActionId=701&Remark=" + inputStr;
        var ajax = InitAjax();
        ajax.open("GET", url, true);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4) {// && ajax.status ==200
                if (ajax.responseText == "Y") {//更新成功
                    var tabIndex = "ListTable" + index;
                    var TDid = document.getElementById(tabIndex).rows[0].cells[18];
                    document.getElementById(Stockid).value = inputStr;
                    if (inputStr == "") {
                        TDid.innerHTML = "&nbsp;";
                    } else {
                        TDid.innerHTML = "<div title='" + inputStr + "'><img src='../images/remark.gif'/></div>";
                    }

                }
                else {
                    alert("更新采购备注失败！");
                }
            }
        }
        ajax.send(null);
    }
}
</script>
<script src='../model/weekdate.js' type=text/javascript></script>
<script src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script language="JavaScript" type="text/JavaScript">
var weekdate = new WeekDate();

function clearDeliveryWeek() {
    var StockId = document.getElementById("clearStockId").value;
    if (confirm("确定设置：" + StockId + "的采购交期为待定?")) {
        myurl = "purchaseorder_updated.php?StockId=" + StockId + "&ActionId=jqdd";
        var ajax = InitAjax();
        ajax.open("GET", myurl, true);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4) {
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
            // alert(myurl);return;
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
    closeMaskBack();
    var theDiv = document.getElementById("Jp");
    theDiv.className = "moveLtoR";
    if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
        theDiv.filters.revealTrans.apply();
        //theDiv.style.visibility = "hidden";
        theDiv.filters.revealTrans.play();
    }
    theDiv.style.visibility = "hidden";
    infoShow.innerHTML = "";
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

function aiaxUpdateBuy() {
    var sgIds = document.getElementById("sgIds").value;
    var sgCompanyId = document.getElementById("sgCompanyId").value;
    var sgNumber = document.getElementById("sgNumber").value;
    var sgPrice = jQuery.trim(document.getElementById("sgPrice").value);

    if (sgCompanyId == null || sgCompanyId == "") {
        alert("请选择供应商");
        return;
    }
    if (sgNumber == null || sgNumber == "") {
        alert("请选择采购人员");
        return;
    }
    if (sgPrice == null || sgPrice == "") {
        alert("请输入采购价格");
        return;
    }
    var Currency = /^\d+(\.\d+)?$/;
    if (!Currency.test(sgPrice)) {
        alert("输入采购价格格式不对");
        return;
    }

    var myurl = "cg_cgdsheet_updated3.php?sgIds=" + sgIds + "&sgCompanyId=" + sgCompanyId + "&sgNumber=" + sgNumber + "&sgPrice=" + sgPrice;
    //alert(myurl); //el.innerHTML=tempWeeks;return;
    var ajax = InitAjax();
    ajax.open("GET", myurl, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4) {// && ajax.status ==200
            //alert(ajax.responseText);
            //el.innerHTML=tempWeeks;
            if (ajax.responseText) {
                alert(ajax.responseText);
            }
            RefreshPage("<?php echo $nowWebPage; ?>")
        }

    }
    ajax.send(null);
    CloseDiv();
}

function All_elects(theDefaultColor, thePointerColor, theMarkColor, theMerge) {
    jQuery('input[name^="checkid"]:checkbox').prop("checked", true);
}

function selectAllOfPO(startRow, rows, theDefaultColor, thePointerColor, theMarkColor, theMerge) {
    //if(ChooseTemp=="false"){
    var e = window.event;
    window.event ? e.cancelBubble = true : e.stopPropagation();

    var e = document.getElementById("checkid" + startRow);
    var check = !e.checked;

    var listTable = document.getElementById("ListTable1");
    for (var i = startRow - 1; i < rows + startRow - 1; i++) {
        var row = i + 1;
        var e = document.getElementById("checkid" + row);
        if (e.checked != check) {
            e.checked = check;
            chooseRow(listTable, row, "click", theDefaultColor, thePointerColor, theMarkColor, "", theMerge);
        }
    }

    ChooseTemp = "true";
}

//3		反选记录行
function Instead_elects(theDefaultColor, thePointerColor, theMarkColor, theMerge) {
    jQuery('input[name^="checkid"]:checkbox').prop("checked", false);
}

function ToSetDelivery(el, pIds) {
    var saveFun = function () {
        if (weekdate.Value > 0) {
            var tempWeeks = weekdate.Value.toString();
            tempWeeks = "Week " + tempWeeks.substr(4, 2);
            var tempPIDate = weekdate.getFriday("-");
            var reg = /^(\d{4})-(\d{2})-(\d{2})$/;
            var regStr = reg.exec(tempPIDate);
            if (regStr == null) {
                alert("选择日期出现错误!");
                return;
            }
            ReduceWeeks = "";
            var updateWeekRemark = encodeURIComponent(document.getElementById("updateWeekRemark").value);
            myurl = "cg_cgdsheet_updated2.php?pIds=" + pIds + "&PIDate=" + tempPIDate + "&updateWeekRemark=" + updateWeekRemark + "&ReduceWeeks=" + ReduceWeeks;
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {
                    if (ajax.responseText) {
                        alert(ajax.responseText);
                    }
                    RefreshPage("<?php echo $nowWebPage; ?>")
                }
            }
            ajax.send(null);
        }
    };

    weekdate.show(el, 1, saveFun, "");
    if (ReduceWeeks == "" || ReduceWeeks == "-1") {
        weekdate.addOtherWeekInfo("&nbsp;&nbsp;采购交期:<select name='ReduceWeeks'  id='ReduceWeeks' style='width:80px'><option value='-1' selected>前一周</option><option value='0'>同周</option></select>");
    }
    else {
        weekdate.addOtherWeekInfo("&nbsp;&nbsp;采购交期:<select name='ReduceWeeks'  id='ReduceWeeks' style='width:80px'><option value='-1'>前一周</option><option value='0' selected>同周</option></select>");
    }
    return false;
}

function eventStop() {

    var e = window.event;
    window.event ? e.cancelBubble = true : e.stopPropagation();
}

function ToSetBuy(el, Ids, CompanyId, BuyerId, Price) {
    showMaskBack();

    document.getElementById("sgIds").value = Ids;
    if (CompanyId != "0") {
        document.getElementById("sgCompanyId").value = CompanyId;
    } else {
        document.getElementById("sgCompanyId").options[0].selected = true;
    }

    if (BuyerId != "0") {
        document.getElementById("sgNumber").value = BuyerId;
    } else {
        document.getElementById("sgNumber").options[0].selected = true;
    }

    if (parseFloat(Price) > 0) {
        document.getElementById("sgPrice").value = Price;
    } else {
        document.getElementById("sgPrice").value = "";
    }

    var theDiv = document.getElementById("Jp");
    theDiv.style.top = event.clientY + document.body.scrollTop + 'px';
    if (theDiv.style.visibility == "hidden") {
        theDiv.className = "moveRtoL";
        if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
            theDiv.filters.revealTrans.apply();//防止错误
            theDiv.filters.revealTrans.play(); //播放
        }
        theDiv.style.visibility = "";
        theDiv.style.display = "";
    }

    return false;
}

//2	改变选定行状态	表格	  行号（表格数目） 鼠标动作		非选定色	  鼠标经过色		选定色		   着色列数
function chooseRow(theTable, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor, theFrom, theMerge) {
    //求和两种形式：1、列表框选择求和的列	2、直接指定求和的列
    //分解求和列数据,以，分开
    //IE_FireFox();
    var MergeRows = document.form1.MergeRows.value;
    var sumCols = document.form1.sumCols.value;
    var SumCelLenght = 0;
    if (sumCols != "") {
        var SumCelArray = sumCols.split(",");
        SumCelLenght = SumCelArray.length;
    }
    var theRow = eval("theTable").rows[theRowNum - 1];
    var theCells = null;
    if ((thePointerColor == '' && theMarkColor == '') || typeof(theRow.style) == 'undefined') {
        return false;
    }
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;

    }
    else {
        return false;
    }


    var rowCellsCnt = theCells.length;
    var lastCellsCnt = rowCellsCnt - 1;

    var domDetect = null;
    var currentColor = null;
    var newColor = null;

    if (typeof(window.opera) == 'undefined' && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[lastCellsCnt].getAttribute('bgcolor');//注意应该取最后一列的底色做判断
        domDetect = true;
    }
    else {
        currentColor = theCells[lastCellsCnt].style.backgroundColor;//注意应该取最后一列的底色做判断

        domDetect = false;
    }

    //if ( currentColor == '' || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {//全选
    if (currentColor == null || currentColor == '' || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {//全选
        newColor = theMarkColor;

        marked_row[theRowNum] = true;
        //求和num.toFixed(2
        if (SumCelLenght > 0) {//在TableHead表的单元格读取和存储值
            //当前列
            var InfoSum = "";
            for (var L = 0; L < SumCelLenght; L++) {
                var NowColValue = SumCelArray[L];								//当前列
                var NowColName = Number(NowColValue) + Number(MergeRows);					//列名所在列；用于区分有并行和无并行的情况
                var ColName = TableHead.rows[0].cells[NowColName].innerText; //列名

                ColName = br_replace(ColName);  //把空格，回车符，换行符去掉  add by zx 20110322


                var OldSumAmount = TableHead.rows[0].cells[NowColValue].data;
                if (typeof(OldSumAmount) == "undefined")
                    OldSumAmount = 0;

                //var sumAmount=Number(OldSumAmount)+Number(theRow.cells[NowColValue].innerText)*1;	//累加值
                var ActualCol = theRowNum == 1 ? NowColValue : NowColValue - 2;
                var CurrentValue = theRow.cells[ActualCol].innerText;
                CurrentValue = br_replace(CurrentValue);
                CurrentValue = CurrentValue.replace(/\,/g, "");
                CurrentValue = CurrentValue == "" ? 0 : CurrentValue;
                var sumAmount = Number(OldSumAmount) + Number(CurrentValue);	//累加值

                sumAmount = sumAmount.toFixed(2);
                TableHead.rows[0].cells[NowColValue].data = sumAmount;
                InfoSum += ColName + "=" + sumAmount + "   ";
            }
            //window.status=InfoSum!=""?"选定行求和：   "+InfoSum:"";
            showWindowStatus(InfoSum);
        }
    }
    else if (currentColor.toLowerCase() == thePointerColor.toLowerCase() && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {


        if (theAction == 'click' && theMarkColor != '') {	//单击

            if ((theFrom != "") && (theFrom != "undefined")) {
                //选定记录:底色为选定色

                if (document.all("checkid" + theRowNum) != null) {
                    eval("document.form1.checkid" + theRowNum).checked = true;
                }
                eval(theFrom)(theRowNum, 1);
                //求和
                if (SumCelLenght > 0) {//在TableHead表的单元格读取和存储值
                    //当前列
                    var InfoSum = "";
                    for (var L = 0; L < SumCelLenght; L++) {
                        var NowColValue = SumCelArray[L];								//当前列
                        var NowColName = Number(NowColValue) + Number(MergeRows);					//列名所在列；用于区分有并行和无并行的情况
                        var ColName = TableHead.rows[0].cells[NowColName].innerText; //列名
                        ColName = br_replace(ColName);  //把空格，回车符，换行符去掉  add by zx 20110322
                        //alert (ColName);
                        //ColName=ColName.replace("<br>","");
                        var OldSumAmount = TableHead.rows[0].cells[NowColValue].data;
                        if (typeof(OldSumAmount) == "undefined")
                            OldSumAmount = 0;

                        //var sumAmount=Number(OldSumAmount)+Number(theRow.cells[NowColValue].innerText)*1;	//累加值
                        var ActualCol = theRowNum == 1 ? NowColValue : NowColValue - 2;
                        var CurrentValue = theRow.cells[ActualCol].innerText;
                        CurrentValue = br_replace(CurrentValue);
                        CurrentValue = CurrentValue.replace(/\,/g, "");
                        CurrentValue = CurrentValue == "" ? 0 : CurrentValue;
                        var sumAmount = Number(OldSumAmount) + Number(CurrentValue);	//累加值

                        sumAmount = sumAmount.toFixed(2);
                        TableHead.rows[0].cells[NowColValue].data = sumAmount;
                        InfoSum += ColName + "=" + sumAmount + "   ";
                    }

                    //window.status=InfoSum!=""?"选定行求和：   "+InfoSum:"";
                    showWindowStatus(InfoSum);
                }
            }
            newColor = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        if (theAction == 'click') {
            if ((theFrom != "") && (theFrom != "undefined")) {//取消选定：底色为非选定色，如果鼠标在其上，则为鼠标经过色
                if (document.all("checkid" + theRowNum) != null) {//如果存在则
                    eval("document.form1.checkid" + theRowNum).checked = false;
                }
                eval(theFrom)(theRowNum, -1);
                if (SumCelLenght > 0) {//在TableHead表的单元格读取和存储值
                    //当前列
                    var InfoSum = "";
                    for (var L = 0; L < SumCelLenght; L++) {
                        var NowColValue = SumCelArray[L];								//当前列
                        var NowColName = Number(NowColValue) + Number(MergeRows);		//列名所在列；用于区分有并行和无并行的情况
                        var ColName = TableHead.rows[0].cells[NowColName].innerText;  //列名

                        ColName = br_replace(ColName);  //把空格，回车符，换行符去掉  add by zx 20110322
                        //ColName=ColName.replace("<br>","");
                        var OldSumAmount = TableHead.rows[0].cells[NowColValue].data;
                        if (typeof(OldSumAmount) == "undefined")
                            OldSumAmount = 0;
                        //var sumAmount=Number(OldSumAmount)-Number(theRow.cells[NowColValue].innerText)*1;	//累加值
                        var ActualCol = theRowNum == 1 ? NowColValue : NowColValue - 2;
                        var CurrentValue = theRow.cells[ActualCol].innerText;
                        CurrentValue = br_replace(CurrentValue);
                        CurrentValue = CurrentValue.replace(/\,/g, "");
                        CurrentValue = CurrentValue == "" ? 0 : CurrentValue;
                        var sumAmount = Number(OldSumAmount) - Number(CurrentValue);	//累加值

                        sumAmount = sumAmount.toFixed(2);
                        TableHead.rows[0].cells[NowColValue].data = sumAmount;
                        InfoSum += ColName + "=" + sumAmount + "   ";
                    }
                    //window.status=InfoSum!=""?"选定行求和：   "+InfoSum:"";
                    showWindowStatus(InfoSum);
                }
            }
            else {		//反选非点击事件
                if (SumCelLenght > 0) {//在TableHead表的单元格读取和存储值
                    //当前列
                    var InfoSum = "";
                    for (var L = 0; L < SumCelLenght; L++) {
                        var NowColValue = SumCelArray[L];								//当前列
                        var NowColName = Number(NowColValue) + Number(MergeRows);		//列名所在列；用于区分有并行和无并行的情况
                        var ColName = TableHead.rows[0].cells[NowColName].innerText; //列名

                        ColName = br_replace(ColName);  //把空格，回车符，换行符去掉  add by zx 20110322

                        //ColName=ColName.replace("<br>","");
                        var OldSumAmount = TableHead.rows[0].cells[NowColValue].data;
                        if (typeof(OldSumAmount) == "undefined")
                            OldSumAmount = 0;
                        //var sumAmount=Number(OldSumAmount)-Number(theRow.cells[NowColValue].innerText)*1;	//累加值
                        var ActualCol = theRowNum == 1 ? NowColValue : NowColValue - 2;
                        var CurrentValue = theRow.cells[ActualCol].innerText;
                        CurrentValue = br_replace(CurrentValue);
                        CurrentValue = CurrentValue.replace(/\,/g, "");
                        CurrentValue = CurrentValue == "" ? 0 : CurrentValue;
                        var sumAmount = Number(OldSumAmount) - Number(CurrentValue);	//累加值

                        sumAmount = sumAmount.toFixed(2);
                        TableHead.rows[0].cells[NowColValue].data = sumAmount;
                        InfoSum += ColName + "=" + sumAmount + "   ";
                    }
                    //window.status=InfoSum!=""?"选定行求和：   "+InfoSum:"";
                    showWindowStatus(InfoSum);
                }
            }
            newColor = (thePointerColor != '') ? thePointerColor : theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum]) ? true : null;
        }
    }

    if (newColor) {
        var c = null;
        if (domDetect) {

            for (c = 0; c < rowCellsCnt; c++) {

                //判断是否有并行，如果有，则首行前几列，不变色
                if (theMerge < rowCellsCnt) {	//有并行
                    var MergeCles = rowCellsCnt - theMerge;//并行的列
                    if (c >= MergeCles) {
                        theCells[c].setAttribute('bgcolor', newColor, 0);
                    }
                }
                else {						//无并行
                    theCells[c].setAttribute('bgcolor', newColor, 0);
                }

            }
        }
        else {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].style.backgroundColor = newColor;
            }
        }
    }
    return true;
}

//导入报价
function ToImportPrice() {
    document.form1.action = "cg_cgdsheet_dely_add.php";
    document.form1.target = "_self";
    document.form1.submit();
    document.form1.action = "cg_cgdsheet_read.php";
    document.form1.target = "";
}

//导出报价
function ToExportPrice() {

    var choosedRow = 0;
    var Ids;

    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
            if (choosedRow == 1) {
                Ids = jQuery(this).val();
            } else {
                Ids = Ids + "," + jQuery(this).val();
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    document.form1.action = "cg_cgdsheet_dely_export.php?Ids=" + Ids;
    document.form1.target = "download";
    document.form1.submit();
    document.form1.action = "cg_cgdsheet_read.php";
    document.form1.target = "";
}

// 导出详情
function ToExportAll() {
    var choosedRow = 0;
    var Ids;
    var S = '';

    jQuery('input[name^="checkid"]:checkbox').each(function () {
        choosedRow = choosedRow + 1;
        if (choosedRow == 1) {
            Ids = jQuery(this).val();
        } else {
            Ids = Ids + "," + jQuery(this).val();
        }
    });
    var num = 0;
    jQuery('option:selected').each(function () {
        if (num > 8) {
            return false;
        }
        if (num == 0) {
            S = jQuery(this).html();
        } else {
            S = S + "|" + jQuery(this).html();
        }
        num++;
    });
    document.form1.action = "cg_cgdsheet_full_export.php?Ids=" + Ids + "&S=" + S;
    document.form1.target = "download";
    document.form1.submit();
    document.form1.action = "cg_cgdsheet_read.php";
    document.form1.target = "";
}
function ResetPage_cg(e) {
    switch (e) {
        case 1:
            document.forms["form1"].elements["OrderPO"].value = "";
            document.form1.submit();
            break;
        case 2:
            document.forms["form1"].elements["OrderPO"].value = "";
            document.forms["form1"].elements["TypeId"].value = "";
            document.form1.submit();
            break;
    }
}

</script>
<style>
  .div-mcmain {
    margin-bottom: 40px;
  }

  #menuB2 {
    position: inherit !important;
    float: right;
  }
</style>