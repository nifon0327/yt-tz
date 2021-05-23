<?php
include "../model/modelhead.php";
echo "<link rel='stylesheet' href='../model/mask.css'>";
//步骤2：需处理
$ColsNumber = 14;
$tableMenuS = 600;
ChangeWtitle("$SubCompany 待出订单列表");
$funFrom = "ch_shippinglist";
$From = $From == "" ? "add" : $From;
$nowWebPage = $funFrom . "_add";
$sumCols = "9,10";      //求和列,需处理
//$Th_Col = "选项|60|序号|40|PO#|80|订单流水号|80|产品Id|50|中文名|220|Product Code/Description|150|售价|60|订单数量|60|金额|60|本次出货|60|产品库存|60|出货方式|60|库位编号|100|转发对象名称|150|产品报关方式|100|订单备注|50|待出备注|110|订单日期|70";
$Th_Col = "选项|60|序号|40|中文名|220|Product Code/Description|150|售价|60|订单数量|60|金额|60|本次出货|60|产品库存|60|出货方式|60|库位编号|100|待出备注|110|订单日期|70";
$Pagination = $Pagination == "" ? 0 : $Pagination;
$Page_Size = 100;

if ($Login_P_Number == "10691" || $Login_P_Number == "10006" || $Login_P_Number == "10341" || $Login_P_Number == "10007" || $Login_P_Number == "10051" || $Login_P_Number == "12198") $ActioToS = "137";
else $ActioToS = "";
//步骤3：
$Operator = $Login_P_Number;
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
//客户
//include"ch_shippinglist_splitCheck.php";

$SearchRows = " and SP.Estate='1' ";
$SearchRows2 = " and S.Estate='1'";
$clientResult = mysql_query("
	SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.ch1_shipsplit SP  
    LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	LEFT JOIN  $DataIn.productdata P ON P.ProductId = S.ProductId
	LEFT JOIN  $DataIn.productstock K ON K.ProductId = P.ProductId
	WHERE 1 $SearchRows  AND M.CompanyId IS NOT NULL AND K.tStockQty >= SP.Qty AND SP.shipSign = 1  GROUP BY M.CompanyId 
    UNION
	SELECT S.CompanyId,C.Forshort FROM $DataIn.ch5_sampsheet S 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
	WHERE 1  and S.Estate='1'
	", $link_id);
if ($clientRow = mysql_fetch_array($clientResult)) {
    echo "<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"ch_shippinglist_add\")'>";
    do {
        $thisCompanyId = $clientRow["CompanyId"];
        $CompanyId = $CompanyId == "" ? $thisCompanyId : $CompanyId;
        $Forshort = $clientRow["Forshort"];
        if ($CompanyId == $thisCompanyId) {
            echo "<option value='$thisCompanyId' selected>$Forshort</option>";
            $SearchRows .= " and M.CompanyId='$thisCompanyId' ";
            $SearchRows2 .= " and S.CompanyId='$thisCompanyId' ";
            $ModelCompanyId = $thisCompanyId;
        }
        else {
            echo "<option value='$thisCompanyId'>$Forshort</option>";
        }
    } while ($clientRow = mysql_fetch_array($clientResult));
    echo "</select>&nbsp;";
}

//增加栋号下拉筛选
$clientResult = mysql_query("
        SELECT M.BuildNo
        FROM $DataIn.ch1_shipsplit SP
        LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
        LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
        LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId
        LEFT JOIN  $DataIn.productdata P ON P.ProductId = S.ProductId
        LEFT JOIN  $DataIn.productstock K ON K.ProductId = P.ProductId
        WHERE 1 $SearchRows  AND S.OrderPO IS NOT NULL AND K.tStockQty >= SP.Qty AND SP.shipSign = 1  GROUP BY M.BuildNo
        ", $link_id);
if ($clientRow = mysql_fetch_array($clientResult)) {
    echo "<select name='BuildNo' id='BuildNo' onchange='RefreshPage(\"ch_shippinglist_add\")'>";
    do {
        $thisBuildNo = $clientRow["BuildNo"];
        $BuildNo = $BuildNo == "" ? $thisBuildNo : $BuildNo;
        if ($BuildNo == $thisBuildNo) {
            echo "<option value='$thisBuildNo' selected>$thisBuildNo 栋</option>";
            $SearchRows .= " and M.BuildNo='$thisBuildNo' ";
        }
        else {
            echo "<option value='$thisBuildNo'>$thisBuildNo 栋</option>";
        }
    } while ($clientRow = mysql_fetch_array($clientResult));
    echo "</select>&nbsp;";
}

//增加业务单号下拉筛选
$clientResult = mysql_query("
        SELECT S.OrderPO
        FROM $DataIn.ch1_shipsplit SP
        LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
        LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
        LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId
        LEFT JOIN  $DataIn.productdata P ON P.ProductId = S.ProductId
        LEFT JOIN  $DataIn.productstock K ON K.ProductId = P.ProductId
        WHERE 1 $SearchRows  AND S.OrderPO IS NOT NULL AND K.tStockQty >= SP.Qty AND SP.shipSign = 1  GROUP BY S.OrderPO
        ", $link_id);
if ($clientRow = mysql_fetch_array($clientResult)) {
    echo "<select name='OrderPO' id='OrderPO' onchange='RefreshPage(\"ch_shippinglist_add\")'>";
    do {
        $thisOrderPO = $clientRow["OrderPO"];
        $OrderPO = $OrderPO == "" ? $thisOrderPO : $OrderPO;
        $PoField=explode("-",$thisOrderPO);
        $PoCount=count($PoField)-1;
        if ($OrderPO == $thisOrderPO) {
            echo "<option value='$thisOrderPO' selected>$PoField[$PoCount] 层</option>";
            $SearchRows .= " and S.OrderPO='$thisOrderPO' ";
        }
        else {
            echo "<option value='$thisOrderPO'>$PoField[$PoCount] 层</option>";
        }
    } while ($clientRow = mysql_fetch_array($clientResult));
    echo "</select>&nbsp;";
}

include "subprogram/ch_pay_check.php";
$PaySignSTR = $PaySign == 0 ? "<span class='redB'>(有货款逾期)<span>" : "";
include "subprogram/ch_amountshow.php";
$otherAction = "";
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr  $MaxStr   &nbsp;&nbsp;&nbsp;货款结付: $PayTerm $PaySignSTR ";
echo " &nbsp;&nbsp;&nbsp;<span onClick='javascript:showMaskDiv(\"$funFrom\",\"$ModelCompanyId\")' class='ButtonH_25' style='width: auto;font-size: 12px;CURSOR: pointer;' value='生成出货单'>生成出货单</span>";


//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理

$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
//List_Title($Th_Col,"1",0);


function List_TitleYW($Th_Col, $Sign, $Height, $ToOutId, $CompanyId, $DataIn, $link_id, $nowWebPage)
{
    if ($Height == 1) {    //高度自动
        $HeightSTR = "";
    }
    else {
        $HeightSTR = "height='25'";
    }
    $Field = explode("|", $Th_Col);
    $Count = count($Field);
    if ($Sign == 1) {
        $tId = "id='TableHead'";
    }
    $tableWidth = 0;
    // add by zx 2011-0326
    for ($i = 0; $i < $Count; $i = $i + 2) {
        $j = $i;
        $k = $j + 1;
        //$tableWidth+=$Field[$j];
        $tableWidth += $Field[$k];
        //$tableWidth=$tableWidth+10;
    }
    if (isFireFox() == 1) {   //是FirFox add by zx 2011-0326  兼容IE,FIREFOX
        //echo "FireFox";
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
        //$tableWidth+=$Field[$j];
        //$tableWidth+=$Field[$k];
        if (isSafari6() == 0) {
            if ($k == ($Count - 1)) {  // add by zx 2011-0326  兼容IE,FIREFOX
                $Field[$k] = "";
            }
        }
        $h = $j + 2;
        if (($Field[$j] == "中文名" && $Field[$h] == "&nbsp;") || $Field[$j] == "&nbsp;") {
            if ($Sign == 1) {
                $Class_Temp = "A1100";
            }
            else {
                $Class_Temp = "A0100";
            }

        }

        if ($Field[$j] == "转发对象名称") {
            $inForT = "";
            $ToOutNameResult = mysql_query("SELECT * from (
						SELECT  D.Id,D.ToOutName as Name ,C.Forshort,O.ToOutId 
						FROM $DataIn.ch1_shipsplit   SP   
						LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
						LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
						LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
						WHERE  O.Mid>0 AND D.Estate=1 AND SP.Estate>0 AND SP.shipSign = 1  AND D.CompanyId='$CompanyId' 
						UNION ALL
						SELECT  D.Id,D.ToOutName as Name ,C.Forshort,O.ToOutId 
						FROM $DataIn.yw7_clientOutData O
						LEFT JOIN  $DataIn.yw1_ordersheet S  ON O.POrderId=S.POrderId
						LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
						LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
						WHERE  O.Mid=0 AND D.Estate=1 AND S.Estate>0   AND D.CompanyId='$CompanyId' 
					) A group by ToOutId", $link_id);
            if ($ToOutNameRow = mysql_fetch_array($ToOutNameResult)) {
                $inForT = "<select name='ToOutId' id='ToOutId' onchange='RefreshPage(\"$nowWebPage\")'>";
                $inForT .= "<option value='' selected> $Field[$j] </option>";
                do {

                    $thisToOutId = $ToOutNameRow["Id"];
                    if ($ToOutId == $thisToOutId) {
                        $inForT .= "<option value='$ToOutNameRow[Id]' selected>$ToOutNameRow[Forshort]-$ToOutNameRow[Name]</option>";
                    }
                    else {
                        $inForT .= "<option value='$ToOutNameRow[Id]'>$ToOutNameRow[Name]</option>";
                    }
                } while ($ToOutNameRow = mysql_fetch_array($ToOutNameResult));
                $inForT .= "</select>&nbsp;";
            }
            else {
                $inForT .= "$Field[$j]";
            }


            $TableStr .= "<td width='$Field[$k]' Class='$Class_Temp'> $inForT </td>";

        }
        else {
            $TableStr .= "<td width='$Field[$k]' Class='$Class_Temp'>$Field[$j]</td>";
        }
    }
    echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $tId><tr $HeightSTR class='' align='center'>" . $TableStr . "</tr></table>";
    if ($Sign == 0) {
        echo "<iframe name=\"download\" style=\"display:none\"></iframe>";
    }
}

//***********************************************
List_TitleYW($Th_Col, "1", 1, $ToOutId, $CompanyId, $DataIn, $link_id, $nowWebPage);

if ($ToOutId != "") {
    $SearchRows .= " AND ((O.ToOutId='$ToOutId' AND O.Mid>0) OR (OP.ToOutId='$ToOutId' AND OP.Mid=0))  ";
    //echo "SearchRows:$SearchRows";
}


$mySql = "SELECT * FROM (
	SELECT M.OrderNumber,M.CompanyId,M.OrderDate,'1' AS Type,
	S.OrderPO,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.dcRemark,S.SeatId,
	P.cName,P.eCode,P.TestStandard,
	SP.Id,SP.ShipType,SP.Qty AS thisQty,SP.OrderSign,K.tStockQty,PI.Leadtime,
	X.name as taxName,R.OrderPO as OutOrderPO 
	FROM $DataIn.ch1_shipsplit SP  
    INNER JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
    LEFT JOIN   $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
	LEFT JOIN   $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	
	LEFT JOIN $DataIn.yw7_clientOutData O ON O.Mid=SP.id
	LEFT JOIN $DataIn.yw7_clientOutData OP ON OP.POrderId=S.POrderId AND OP.Sign=1
	LEFT JOIN $DataIn.yw7_clientOrderPo R ON R.Mid=SP.id
	 
	LEFT JOIN   $DataIn.productdata P ON P.ProductId=S.ProductId 
	LEFT JOIN   $DataIn.productstock K ON K.ProductId = P.ProductId
	LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
	WHERE   S.Estate>0   $SearchRows	AND K.tStockQty >= SP.Qty AND SP.shipSign = 1 
    UNION ALL 
	SELECT '' AS OrderNumber,S.CompanyId,S.Date AS OrderDate,'2' AS Type,
	S.Id,'' AS OrderPO,S.SampId AS POrderId,'' AS ProductId,S.Qty,S.Price,'' AS PackRemark,'' AS SeatId,
	S.SampName AS cName,S.Description AS eCode,'' ASTestStandard,'' AS ShipType,
	'' AS dcRemark,S.Qty AS thisQty ,'' AS OrderSign,S.Qty AS tStockQty ,'' AS Leadtime,'' as taxName,'' as OutOrderPO  
	FROM $DataIn.ch5_sampsheet S WHERE 1 $SearchRows2 
) A  WHERE  1 ORDER BY  A.POrderId";

//echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    do {
        $m = 1;
        $LockRemark = "";
        $theDefaultColor = "#FFFFFF";
        $Id = $myRow["Id"];
        $OrderPO = $myRow["OrderPO"] == "" ? "&nbsp;" : $myRow["OrderPO"];
        $OutOrderPO = $myRow["OutOrderPO"];
        if ($OutOrderPO != "") {
            $OrderPO = "<span title='原单PO：$OrderPO' class=\"redB\">" . $OutOrderPO . "</span>";
        }
        $OrderDate = $myRow["OrderDate"];
        $POrderId = $myRow["POrderId"];
        $ColbgColor = "";
        $checkExpress = mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress 
		WHERE POrderId='$POrderId' AND (Type=1 OR Type=2) ORDER BY Id", $link_id);
        if ($checkExpressRow = mysql_fetch_array($checkExpress)) {
            do {
                if ($Type == 1) {
                    $ColbgColor = "bgcolor='#0066FF'";
                }
                else {
                    $LockRemark = "订单状态异常！";
                    $Locks = 0;//不能操作
                }
            } while ($checkExpressRow = mysql_fetch_array($checkExpress));
        }
        $ProductId = $myRow["ProductId"] == "" ? "&nbsp;" : $myRow["ProductId"];
        $Qty = $myRow["Qty"]; //本次出货数量
        $thisQty = $myRow["thisQty"];  //订单数量
        $tStockQty = $myRow["tStockQty"];
        $qtyColor = $thisQty > $Qty ? " style='color:#FF0000;' " : "";
        $Price = $myRow["Price"];
        $Amount = sprintf("%.2f", $Qty * $Price);
        $PackRemark = $myRow["PackRemark"];
        $PackRemark = $PackRemark == "" ? "&nbsp;" : "<div title='$PackRemark'><img src='../images/remark.gif'></div>";
        $dcRemark = $myRow["dcRemark"] == "" ? "&nbsp;" : $myRow["dcRemark"];
        $cName = $myRow["cName"];
        $eCode = $myRow["eCode"];
        $Description = $myRow["Description"];
        $ToOutName = "";
        $OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
								  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
								  WHERE O.MId='$Id'", $link_id);
        if ($Outmyrow = mysql_fetch_array($OutResult)) {
            $ToOutName = $Outmyrow["ToOutName"];
        }
        else {
            $OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
									  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
									  WHERE  O.POrderId='$POrderId' AND O.Mid=0 ", $link_id);
            if ($Outmyrow = mysql_fetch_array($OutResult)) {
                $ToOutName = $Outmyrow["ToOutName"];
            }
        }

        if ($ToOutName != "" && $enRemark != "") {  //DEASIA不同客户出不同的Delivery Reference NO:
            $enField = explode("|", $enRemark);
            if (count($enField) > 1) {
                $ToOutName = $ToOutName . "(<span class=\"redB\">$enRemark</span>)";
            }
            else {
                $ToOutName = $ToOutName . "($enRemark)";
            }
        }

        if ($ToOutName == "") {
            $ToOutName = "&nbsp;";
        }

        $Type = $myRow["Type"];
        $ShipType = $myRow["ShipType"] == '' ? '7' : $myRow["ShipType"];
        $SeatId = $myRow["SeatId"];
        //出货方式
        if (strlen(trim($ShipType)) > 0) {
            $CheckShipType = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.ch_shiptype WHERE Id='$ShipType'  LIMIT 1", $link_id));
            $ShipName = $CheckShipType["Name"];
            $ShipType = "<image src='../images/ship$ShipType.png' style='width:20px;height:20px;' title='$ShipName' />";
        }
        $OrderSign = $myRow["OrderSign"];
        if ($OrderSign > 0) $theDefaultColor = "#FFAEB9";//#E9FFF5

        $TestStandard = $myRow["TestStandard"];
        include "../admin/Productimage/getPOrderImage.php";

        $OrderPO = $Type == 2 ? "随货项目" : $OrderPO;
        $checkidValue = $Id . "^^" . $Type;
        $Locks = 1;
        if ($Type == 1) {//如果是订单：检查生产数量与需求数量是否一致，如果不一致，不允许选择

            $checkShipRow = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty),0) AS Qty FROM $DataIn.ch1_shipsheet 
            WHERE POrderId='$POrderId'", $link_id));
            $shipQty = $checkShipRow["Qty"];
            if ($shipQty + $Qty == $thisQty) { //最后一次限制
                //检查领料记录 备料总数与领料总数比较
                $CheckblQty = mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty 
					FROM $DataIn.cg1_stocksheet G
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
					LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id = T.mainType 
					WHERE G.POrderId='$POrderId' AND MT.blsign = 1 AND G.Level = 1", $link_id));
                $blQty = $CheckblQty["blQty"];
                $CheckllQty = mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS llQty 
					FROM $DataIn.cg1_stocksheet G 										
					LEFT JOIN  $DataIn.ck5_llsheet K ON K.StockId = G.StockId 
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					LEFT JOIN $DataIn.stuffmaintype MT ON MT.Id = T.mainType 
					WHERE G.POrderId='$POrderId' AND K.Estate=0 AND MT.blsign = 1 AND G.Level = 1", $link_id));

                $llQty = $CheckllQty["llQty"];
                if ($blQty != $llQty) {//领料完毕
                    $LockRemark .= "领料异常！";
                    $Locks = 0;//不能操作
                }
            }


        }

        if ($ShipType == "") {
            $LockRemark .= "业务未填出货方式。";
            $Locks = 0;//不能操作
        }
        if ($PayMode != 2 && $PaySign == 0 && $Login_P_Number != "10691" && $Login_P_Number != "10006" && $Login_P_Number != "10341" && $Login_P_Number != "10007" && $Login_P_Number != "10051") {

            $checkUnLock = mysql_query("SELECT Id FROM $DataIn.ch1_unlock WHERE POrderId='$POrderId' LIMIT 1", $link_id);
            if (mysql_num_rows($checkUnLock) <= 0) {
                $LockRemark .= "客户有货款逾期未付，暂停出货。";
                $Locks = 0;//不能操作
            }
        }

        //检查分批出货总数量是否正确
        $CheckSplitQty = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ch1_shipsplit  WHERE POrderId='$POrderId'", $link_id));
        $TotalSplitQty = $CheckSplitQty["Qty"];
        if ($TotalSplitQty <> $Qty) {
            $LockRemark .= "分批出货总数量与原订单数量不符！";
            $Locks = 0;//不能操作
        }
        $taxName = $myRow["taxName"] == "" ? "&nbsp;" : $myRow["taxName"];

        $showPurchaseorder = "<img onClick='CH_SC_SandH(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i);' name='showtable$i' src='../images/showtable.gif' 
		title='显示或隐藏配件采购明细资料.' width='13' height='13' style='CURSOR: pointer'>";
        $StuffListTB = "
			<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
        $ValueArray = array(
//            array(0 => $OrderPO, 1 => "align='center'"),
//            array(0 => $POrderId, 1 => "align='center'"),
//            array(0 => $ProductId, 1 => "align='center'"),
            array(0 => $TestStandard),
            array(0 => $eCode . $gxQty . "/" . $scQty, 3 => "..."),
            array(0 => $Price, 1 => "align='center'"),
            array(0 => $Qty, 1 => "align='center'"),
            array(0 => $Amount, 1 => "align='center'"),
            array(0 => $thisQty, 1 => "align='center' $qtyColor"),
            array(0 => $tStockQty, 1 => "align='center' "),
            array(0 => $ShipType, 1 => "align='center'"),
            array(0 => $SeatId, 1 => "align='center'"),
//            array(0 => $ToOutName, 1 => "align='center'"),
//            array(0 => $taxName, 1 => "align='left'"),
//            array(0 => $PackRemark, 1 => "align='center'"),
            array(0 => $dcRemark, 1 => "align='left'"),
            array(0 => $OrderDate, 1 => "align='center'", 3 => "..."),
        );
        include "../model/subprogram/read_model_6.php";
        echo $StuffListTB;
    } while ($myRow = mysql_fetch_array($myResult));
}
else {
    noRowInfo($tableWidth);
}
//步骤7：
echo '</div>';
SetMaskDiv();//遮罩初始化
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>

<script>
/////////遮罩层函数/////////////
function showMaskDiv(WebPage, CompanyId, FileDir) {	//显示遮罩对话框
    //检查是否有选取记录
    UpdataIdX = 0;
    var upIds, eValue, eArray;
    upIds = "";
    for (var i = 0; i < form1.elements.length; i++) {
        var e = form1.elements[i];
        if (e.type == "checkbox") {
            var NameTemp = e.name;
            var Name = NameTemp.search("checkid");//防止有其它参数用到checkbox，所以要过滤
            if (e.checked && Name != -1) {
                UpdataIdX = UpdataIdX + 1;
                eValue = e.value;
                eArray = eValue.split("^^");
                upIds += upIds == "" ? eArray[0] : "," + eArray[0];
            }
        }
    }
    //如果没有选记录
    if (UpdataIdX == 0 || CompanyId == "") {
        alert("没有选取记录或公司名称!");
        return false;
    }
    else {
        document.getElementById('divShadow').style.display = 'block';
        divPageMask.style.width = document.body.scrollWidth;
        divPageMask.style.height = document.body.scrollHeight > document.body.clientHeight ? document.body.scrollHeight : document.body.clientHeight;
        document.getElementById('divPageMask').style.display = 'block';
        sOrhDiv("" + WebPage + "", CompanyId, FileDir, upIds);
    }
}

function closeMaskDiv() {	//隐藏遮罩对话框
    document.getElementById('divShadow').style.display = 'none';
    document.getElementById('divPageMask').style.display = 'none';
}

//对话层的显示和隐藏:层的固定名称divInfo,目标页面,传递的参数
function sOrhDiv(WebPage, DeliveryValue, FileDir, upIds) {
    if (DeliveryValue != "") {
        if (FileDir == "public") {
            var url = "../" + FileDir + "/" + WebPage + "_mask.php?DeliveryValue=" + DeliveryValue + "&upIds=" + upIds;
        }
        else {
            var url = "../admin/" + WebPage + "_mask.php?DeliveryValue=" + DeliveryValue + "&upIds=" + upIds;
        }

        //var show=eval("divInfo");
        var ajax = InitAjax();
        ajax.open("GET", url, true);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4) {// && ajax.status ==200
                var BackData = ajax.responseText;
                divInfo.innerHTML = BackData;
            }
        }
        ajax.send(null);
    }
}
</script>