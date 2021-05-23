<style>
  i {
    color: #949598;
}
i:hover {
    cursor: pointer;
}

</style>
<?php
//电信-EWEN
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
echo "<html><head><META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
<link rel='stylesheet' href='../model/css/read_line.css'>
<link rel='stylesheet' href='../model/css/sharing.css'>
<link rel='stylesheet' href='../model/Totalsharing.css'>
<link rel='stylesheet' href='../model/keyright.css'>
<link rel='stylesheet' href='../model/tableborder.css'>
<link rel='stylesheet' href='../model/scrollbar.css'>
<link rel='stylesheet' href='../model/css/ac_ln.css'>
<script src='../model/pagefun_yw.js' type=text/javascript></script>
<script src='../cjgl/cj_function.js' type=text/javascript></script>
<script src='../model/pagefun.js' type=text/javascript></script>
<script src='../model/lookup.js' type=text/javascript></script>
<script src='../model/checkform.js' type=text/javascript></script>
<script language='javascript' type='text/javascript' src='../model/js/jquery-1.11.1.js'></script>
<script language='javascript' type='text/javascript' src='../model/js/float_headtable.js'></script>
<script language='javascript' type='text/javascript' src='../model/DatePicker/WdatePicker.js'></script></head>";
echo "<link rel='stylesheet' href='../model/shadow.css'>";
?>
<script language="JavaScript" type="text/JavaScript">
    function searchSpliteBill(companyId, productId) {
        window.open("../admin/yw_order_read.php?From=slist&SearchRows= and M.CompanyId=" + companyId + " and P.ProductId = " + productId + "  &operation=splite");
    }
</script>
<?php
include "../model/subprogram/sys_parameters.php";
include "../model/subprogram/business_authority.php";//看客户权限
include '../basic/loading.php';

$From = $From == "" ? "read" : $From;
//
//需处理参数
$tableMenuS = 800;
$funFrom = "yw_order";
$helpFile = 0;//有帮助文件
$nowWebPage = $funFrom . "_read";
$nullable = ' AND O.Forshort != \'南京上坊\' ';
$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId=$Login_P_Number LIMIT 1", $link_id);
if ($TRow = mysql_fetch_array($TResult)) {
    $unColorCol = 25;//不着色列
    $Th_Col = "选项|55|序号|30|业务单号|120|产品名称|210|&nbsp;|30|产品条码|200|已出<br>(下单次数)|70|退货|50|成品重<br>(g)|50|装箱单位|60|单价|55|数量|50|金额|70|利润|80|订单利润|60|订单备注|110|英文备注|110|生管备注|110|采购备注|110|待出备注|110|装运方式|60|采购|60|备料|60|组装|45|待出|60|交期|80|操作人|55|期限|60|报价规则|450|转发对象名称|150|产品报关方式|80";
    $ColsNumber = 29;
    $myTask = 1;
    $sumCols = "11,12,13";
}
else {
    $unColorCol = 24;//不着色列
    $Th_Col = "选项|55|序号|30|要货日期|80|业务单号|120|产品名称|210|&nbsp;|30|产品条码|200|初始台车|50|生产台车|50|生产日期|80|已出<br>(下单次数)|70|退货|50|成品重<br>(g)|50|单价|55|数量|50|金额|70|订单备注|110|英文备注|110|生管备注|110|采购备注|110|待出备注|110|装运方式|60|采购|60|备料|60|组装|45|待出|60|交期|80|操作人|55|期限|60|报价规则|450|转发对象名称|100|产品报关方式|80";

    $ColsNumber = 26;
    $myTask = 0;
    $sumCols = "11,12";
}

if ($taxtypeId > 1) {
    $Th_Col = "选项|55|序号|30|客户名称|80|业务单号|120|产品名称|210|&nbsp;|30|产品条码|200|已出<br>(下单次数)|70|装箱单位|60|单价|55|数量|50|金额|70|采购备注|110|产品报关方式|80|操作人|55";
    $ColsNumber = 13;
    $myTask = 2;
    $sumCols = "9,10";
}
//更新
$Pagination = $Pagination == "" ? 0 : $Pagination;
$Page_Size = 100;
//步骤3：
include "../model/subprogram/read_model_3.php";
$subTableWidth = $tableWidth - 30;
//步骤4：需处理-条件选项
$Temptoday = date("Y-m-d");
if ($From != "slist") {
    if ($taxtypeId > 1) {
        $SearchRows = "";
        $ClientResult = mysql_query("SELECT M.CompanyId,C.Forshort,SUM(S.Qty*S.Price*R.Rate) AS Amount,R.Symbol
           FROM $DataIn.yw1_ordermain M 
           INNER JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber 
           INNER JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
           INNER JOIN $DataIn.currencydata R ON R.Id=C.Currency
           WHERE S.Estate>0  $SearchRows $nullable GROUP BY M.CompanyId ORDER by Amount DESC,M.CompanyId ASC", $link_id);
        if ($ClientRow = mysql_fetch_array($ClientResult)) {
            echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
            echo "<option value='' selected>全部</option>";
            do {
                $theCompanyId = $ClientRow["CompanyId"];
                $theForshort = $ClientRow["Forshort"];
                $theSymbol = $ClientRow["Symbol"];
                //$theForshort=$theForshort."(".$theSymbol.")";
                //$CompanyId=$CompanyId==""?$theCompanyId:$CompanyId;
                if ($CompanyId == $theCompanyId) {
                    $nowForshort = $theForshort;
                    echo "<option value='$theCompanyId' selected>$theForshort</option>";
                    $SearchRows .= " and M.CompanyId='$theCompanyId' ";
                    $DefaultClient = $theForshort;
                }
                else {
                    echo "<option value='$theCompanyId'>$theForshort</option>";
                }
            } while ($ClientRow = mysql_fetch_array($ClientResult));
            echo "</select>&nbsp;";

            $SearchRows .= " AND (S.Estate>1 OR S.taxtypeId>1) ";
        }


    }
    else {
        $SearchRows = "";
        $ClientResult = mysql_query("SELECT P.CompanyId, C.Id, C.Forshort FROM yw1_ordersheet S LEFT JOIN productdata P ON P.ProductId = S.ProductId LEFT JOIN trade_object C ON C.CompanyId = P.CompanyId WHERE S.Estate > 0 AND C.Id IS NOT NULL GROUP BY P.CompanyId ORDER BY C.Forshort", $link_id);
        if ($ClientRow = mysql_fetch_array($ClientResult)) {
            echo "<select name='CompanyId' id='CompanyId' onchange='RefreshPages(\"CompanyId\")'>";
            do {
                $theCompanyId = $ClientRow["CompanyId"];
                $theForshort = $ClientRow["Forshort"];
//                $theSymbol = $ClientRow["Symbol"];
                //$theForshort=$theForshort."(".$theSymbol.")";
                $CompanyId = $CompanyId == "" ? $theCompanyId : $CompanyId;
                if ($CompanyId == $theCompanyId) {
                    $nowForshort = $theForshort;
                    echo "<option value='$theCompanyId' selected>$theForshort</option>";
                    $SearchRows = " and P.CompanyId='$theCompanyId' ";
                    $DefaultClient = $theForshort;
                } else {
                    echo "<option value='$theCompanyId'>$theForshort</option>";
                }
            } while ($ClientRow = mysql_fetch_array($ClientResult));
            echo "</select>&nbsp;";
        }

        //楼栋-选择
        $BuildNoResult = mysql_query("SELECT P.BuildingNo AS BuildNo FROM yw1_ordersheet S LEFT JOIN productdata P ON P.ProductId = S.ProductId LEFT JOIN trade_object C ON C.CompanyId = P.CompanyId WHERE S.Estate > 0 AND C.Id IS NOT NULL $SearchRows GROUP BY P.BuildingNo ORDER BY P.BuildingNo+0", $link_id);

        if ($BuildNoResult && $BuildNoRow = mysql_fetch_array($BuildNoResult)) {
            echo "<select name='BuildNo' id='BuildNo' onchange='RefreshPages(\"BuildNo\")'>";
            //echo"<option value='' >全部</option>";

            do {
                $thisBuildNo = $BuildNoRow["BuildNo"];
                $BuildNo = $BuildNo == "" ? $thisBuildNo : $BuildNo;
//                if ($thisBuildNo != "999" && $thisBuildNo != NULL) {
                    if ($BuildNo == $thisBuildNo) {
                        echo "<option value='$thisBuildNo' selected>$thisBuildNo 栋</option>";
                        $SearchRows .= " AND P.BuildingNo='$thisBuildNo'";
                    } else {
                        echo "<option value='$thisBuildNo'>$thisBuildNo 栋</option>";
                    }
//                }
            } while ($BuildNoRow = mysql_fetch_array($BuildNoResult));

            echo "</select>&nbsp;";
        }


        //PO-选择
        $poResult = mysql_query("SELECT P.FloorNo, S.OrderPO FROM yw1_ordersheet S LEFT JOIN productdata P ON P.ProductId = S.ProductId LEFT JOIN trade_object C ON C.CompanyId = P.CompanyId LEFT JOIN $DataIn.trade_info TI ON TI.TradeId = C.Id 
        where S.OrderPO = CONCAT_WS( '-', TI.TradeNo, P.BuildingNo, P.FloorNo )  AND S.Estate > 0 AND C.Id IS NOT NULL $SearchRows GROUP BY P.FloorNo ORDER BY P.FloorNo+0", $link_id);

        if ($poResult && $poRow = mysql_fetch_array($poResult)) {
            echo "<select name='OrderPO' id='OrderPO' onchange='RefreshPages(\"OrderPO\")'>";
            // echo"<option value='all' >全部楼层</option>";

            do {
                $thisOrderPO = $poRow["OrderPO"];
                $thisFloor = $poRow["FloorNo"];
                $OrderPORes = explode("-", $thisOrderPO);
                $count = count($OrderPORes) - 1;
                $OrderPO = $OrderPO == "" ? $thisOrderPO : $OrderPO;

                if ($OrderPO == $thisOrderPO && $OrderPO != 'all') {
                    echo "<option value='$thisOrderPO' selected>$thisFloor 层</option>";
                    $SearchRows .= " AND S.OrderPO='$thisOrderPO'";
                } else {
                    echo "<option value='$thisOrderPO'>$thisFloor 层</option>";
                }
            } while ($poRow = mysql_fetch_array($poResult));
            echo "</select>&nbsp;";
        }

        //分类
        $TypeResult = mysql_query("SELECT P.TypeId, PT.NameRule, PT.TypeName FROM yw1_ordersheet S LEFT JOIN productdata P ON P.ProductId = S.ProductId LEFT JOIN trade_object C ON C.CompanyId = P.CompanyId LEFT JOIN producttype PT ON PT.TypeId = P.TypeId WHERE S.Estate > 0 AND C.Id IS NOT NULL $SearchRows GROUP BY P.TypeId", $link_id);

        if ($TypeRow = mysql_fetch_array($TypeResult)) {
            echo "<select name='TypeId' id='TypeId' onchange='RefreshPages(\"TypeId\")'>";
            echo "<option value='all' selected>全部类型</option>";
            do {
                $theTypeId = $TypeRow["TypeId"];
                $TypeName = $TypeRow["TypeName"];
                $TypeId = $TypeId == "" ? $theTypeId : $TypeId;
                if ($TypeId == $theTypeId) {
                    echo "<option value='$theTypeId'  selected>$TypeName</option>";
                    $SearchRows .= " AND P.TypeId='$theTypeId'";
                } else {
                    echo "<option value='$theTypeId'>$TypeName</option>";
                }
            } while ($TypeRow = mysql_fetch_array($TypeResult));
            echo "</select>&nbsp;";
        }
        $TempProfitSTR = "ProfitTypeStr" . strval($ProfitType);
        $TempProfitSTR = "selected";
 //        echo "<select name='ProfitType' id='ProfitType' onchange='ResetPage(this.name)'>";
 //        echo "<option value='' $ProfitTypeStr>全部净利</option>
	// 	<option value='1' style= 'color:#FF00CC;' $ProfitTypeStr1>0以下</option>
	// 	<option value='2' style= 'color:#FF0000;' $ProfitTypeStr2>0-7%</option>
	// 	<option value='3' style= 'color:#FF6633;' $ProfitTypeStr3>8-15%</option>
	// 	<option value='4' style= 'color:#009900;' $ProfitTypeStr4>16%以上</option>
	// </select>&nbsp;";
        //状态/标记选择
        //$SignType=$SignType==""?0:$SignType;
        $TempEstateSTR = "SignTypeStr" . strval($SignType);
        $$TempEstateSTR = "selected";
        // echo "<select name='SignType' id='SignType' onchange='ResetPage(this.name)'>";
        // echo "<option value='' $SignTypeStr0>全部订单</option>";
        // echo "<option value='2' $SignTypeStr2>未确定订单</option>";
        // //echo"<option value='7' $SignTypeStr7>加急订单</option>";
        // echo "<option value='11' $SignTypeStr11>需修改标准图</option>";
        // echo "<option value='8' $SignTypeStr8>交期前一周</option>";
        // echo "<option value='9' $SignTypeStr9>交期已过</option>";
        // //echo "<option value='12' $SignTypeStr12>可备料订单</option>";
        // echo "<option value='13' $SignTypeStr13>待组装订单</option>";
        // echo "<option value='3' $SignTypeStr3>待出订单</option></select>";

        // echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";

        //过滤
        switch ($SignType) {
            case 0:
            case 2:
            break;
            case 3:
            $SearchRows .= " AND S.Estate=2";
            break;
            case 11:
            $SearchRows .= " AND I.Type='9'";
            break;
            case 8 :
                $SearchRows .= "  AND TIMESTAMPDIFF( DAY ,  '$Temptoday', PI.Leadtime )<=7  AND TIMESTAMPDIFF( DAY ,  '$Temptoday', PI.Leadtime )>0";//离PI交期7天之内
                break;
                case 9:
                $SearchRows .= " AND TIMESTAMPDIFF( DAY, PI.Leadtime ,  '$Temptoday' )>=0";//超过PI交期的
                break;
                case 12:
                $blSignType = 12;
                break;
                case 13:
                $blSignType = 13;
                break;
                default:
                $SearchRows .= " AND T.Type='$SignType'";
            }


        }

    }
    else {
    //查询的条件
        if ($operation != "splite") {
            echo "<input name='SearchRows' type='hidden' id='SearchRows' value='$SearchRows'>";
        }
        else {
            $SearchRows = $_GET['SearchRows'];
        }

    }
//增加快带查询Search按钮
    if ($operation != "splite") {
        echo "$CencalSstr";
    $searchtable = "productdata|P|cName|0|0"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段,其它值无
}

if ($taxtypeId > 1) {
    echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='yw_order_toexcel_declare.php?TypeId=$taxtypeId&CompanyId=$CompanyId' target='_blank'>To Excel</a>";
}

echo "&nbsp;&nbsp;<span class='ButtonH_25' style='margin-right: 50px;' onClick='RefreshPages(this)'>查询</span>";
echo "<input type='text' id='cx' name='cx' style='display: none;'/>";

//设置交期
if ($myTask == 0 || $myTask == 1) {
    echo "&nbsp;&nbsp;<span class='ButtonH_25' onClick='ToSetDelivery(this)'>设置交期</span>";
}

echo "&nbsp;&nbsp;<span class='ButtonH_25' onClick='ToImportPrice()'>导入</span>";
echo "&nbsp;&nbsp;<span class='ButtonH_25' onClick='ToExportPrice()'>导出</span>";
echo "&nbsp;&nbsp;<span class='ButtonH_26' onClick='showSetTimeFrame(this)'>导入设置日期和台车号</span>";
if ($Login_BranchId == '115' || ( $Login_BranchId == '7' && $Login_JobId == '39')){
    echo "&nbsp;&nbsp;<span class='ButtonH_25' onClick='QRCode(this)'>二维码导出</span></div>";
    echo "&nbsp;&nbsp;<span class='ButtonH_25' onClick='Transfer(this)'>流转单导出</span></div>";
}
echo "<div id='winDialog' style=\"position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;\" onDblClick=\"closeWinDialog()\"></div>";
echo "&nbsp;&nbsp;<span class='ButtonH_25' onClick='yh(this)'>设置要货日期</span>";
if ($CompanyId == 100426) { //研砼上海


}
//步骤5：
include "../model/subprogram/read_model_5.php";
include "../model/subprogram/CurrencyList.php";
echo "<div id='Jp' style='position:absolute; left:341px; top:229px; width:420px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
<div class='out'>
<div class='in' id='infoShow'>
</div>
</div>
</div>";
//步骤6：需处理数据记录处理FILTER: revealTrans(transition=7,duration=0.5) blendTrans(duration=0.5);
$sumQty = 0;
$sumSaleAmount = 0;
$sumTOrmb = 0;
$DefaultBgColor = $theDefaultColor;
$i = 1;
$sRow = 1;
$j = ($Page - 1) * $Page_Size + 1;

List_Title($Th_Col, "1", 1);
//***************************
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
            $Class_Temp = $i == 0 ? "" : "";
        }
        else {
            $Class_Temp = $i == 0 ? "" : "";
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
        if (($Field[$j] == "产品名称" && $Field[$h] == "&nbsp;") || $Field[$j] == "&nbsp;") {
            if ($Sign == 1) {
                $Class_Temp = "A1100";
            }
            else {
                $Class_Temp = "A0100";
            }

        }

    }
    echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $tId><tr $HeightSTR class='' align='center'>" . $TableStr . "</tr></table>";
    if ($Sign == 0) {
        echo "<iframe name=\"download\" style=\"display:none\"></iframe>";
    }
}

//***********************************************
//List_TitleYW($Th_Col,"1",1,$ToOutId,$CompanyId,$DataIn,$link_id,$nowWebPage);

if ($ToOutId != "") {
    $SearchRows .= " AND O.ToOutId='$ToOutId' AND O.Mid=0 ";
}
if ($cx) {
    switch ($SignType) {
        case 11:
        $mySql = "SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.MainWeight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime,PI.Remark AS PIRemark,S.dcRemark,X.name as taxName
        FROM $DataIn.yw1_ordermain M
        INNER JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
        INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
        INNER JOIN $DataIn.packingunit U ON U.Id=P.PackingUnit 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
        LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
        LEFT JOIN $DataIn.yw2_orderteststandard I ON I.POrderId=S.POrderId
        LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
        WHERE 1 and S.Estate>0 $SearchRows ORDER BY M.CompanyId,M.OrderDate ASC,M.Id DESC";
        break;

        case 2:
        $mySql = "SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.MainWeight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime,PI.Remark AS PIRemark,S.dcRemark,X.name as taxName
        FROM $DataIn.yw1_ordermain M
        INNER JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
        INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
        INNER JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
        LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId 
        LEFT JOIN (SELECT LEFT(GL.StockId,12) AS POrderId,GL.Locks FROM $DataIn.cg1_lockstock GL,$DataIn.cg1_stocksheet G WHERE GL.Locks=0 AND GL.StockId=G.StockId GROUP BY POrderId) K ON K.POrderId=S.POrderId 
        LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
        WHERE 1 and S.Estate>0  AND (T.Type='2' OR K.Locks=0) $SearchRows ORDER BY M.CompanyId,M.OrderDate ASC,M.Id DESC";
        break;

        case 12:
        $mySql = "SELECT * FROM (
        SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark,S.dcRemark
        ,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.MainWeight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime,PI.Remark AS PIRemark,SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,
        SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2,X.name as taxName
        FROM $DataIn.yw1_ordermain M
        INNER JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
        INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
        INNER JOIN $DataIn.packingunit U ON U.Id=P.PackingUnit 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
        LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
        LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
        LEFT JOIN (
        select S.POrderId,G.StuffId,sum(G.OrderQty) as OrderQty from $DataIn.yw1_ordersheet S 
        INNER JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
        WHERE 1 and S.Estate>0  GROUP by G.POrderId,G.StuffId
        )  G ON G.POrderId=S.POrderId  

        LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
        LEFT JOIN (
        SELECT G.POrderId,G.StuffID,IFNULL(SUM(L.Qty),0) AS Qty FROM $DataIn.yw1_ordersheet S 
        LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
        LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
        WHERE 1  AND S.Estate>0  GROUP BY G.POrderId,G.StuffID 
        ) L ON L.POrderId=G.POrderId  AND L.StuffId=G.StuffId

        INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
        INNER JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
        WHERE 1 and S.Estate>0 AND ST.mainType<2  $SearchRows  GROUP BY S.POrderId ) A 
        WHERE A.K1>=A.K2 ORDER BY A.CompanyId,A.OrderDate ASC,A.Id DESC";
        break;
        default:

        $mySql = "SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.delivery,
        S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,
        P.Weight,P.MainWeight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,PI.Leadtime,S.dcRemark,PI.Remark AS PIRemark,X.name as taxName,C.Forshort,C.Id AS CID ,S.liningNo,S.RealLining 
        FROM $DataIn.yw1_ordermain M
        INNER JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
        INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
        INNER JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
        INNER JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
        LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
        LEFT JOIN $DataIn.yw2_orderexpress T ON T.POrderId=S.POrderId
        LEFT JOIN $DataIn.clientsub B ON B.Id=M.SubClientId
        LEFT JOIN $DataIn.yw7_clientOutData O ON O.POrderId=S.POrderId AND O.Sign=1
        LEFT JOIN $DataIn.taxtype X ON X.Id=S.taxtypeId
        WHERE 1 and S.Estate>0 $SearchRows ORDER BY M.CompanyId,M.OrderDate ASC,M.Id DESC";
        break;
    }
}
// echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    $thisTOrmbOUTsum = 0;
    do {
        //初始化计算的参数
        $OrderCgRemark = "";
        $OrderRemark = "";

        $m = 1;
        $AskDay = "";
        $thisBuyRMB = 0;
        $OrderSignColor = "bgColor='#fff'";
        $theDefaultColor = $DefaultBgColor;
        $OrderPO = toSpace($myRow["OrderPO"]);
        //加密参数
        $Id = $myRow["Id"];
        $POrderId = $myRow["POrderId"];
        $delivery = $myRow["delivery"];

        $liningNo = $myRow["liningNo"];
        $RealLining = $myRow["RealLining"];
        $OrderDate = $myRow["OrderDate"];

        include "order_date.php";

        if (($blSignType == 13 && $sc_cycle == "&nbsp;") || $blSignType == 13 && $sc_Sign == 1) {
            continue;
        }
        if ($blSignType == 12 && $blSign == 1) {
            continue;
        }
        $PI = $myRow["PI"];
        if ($PI != "") {
            $f1 = anmaIn($PI . ".pdf", $SinkOrder, $motherSTR);
            $d1 = anmaIn("download/pipdf/", $SinkOrder, $motherSTR);
            $PI = "<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>查看</a>";
            $PIoId = $POrderId . "|" . $Id;//弹出DIV传值用
        }
        else {
            $PI = "&nbsp;";
            $PIoId = "$POrderId|N";
        }
        //echo "POrderId:$POrderId";
        $ClientOrder = $myRow["ClientOrder"];
        if ($ClientOrder != "") {//原单在序号列显示
            $f2 = anmaIn($ClientOrder, $SinkOrder, $motherSTR);
            $d2 = anmaIn("download/clientorder/", $SinkOrder, $motherSTR);
            $ClientOrder = "<a href=\"openorload.php?d=$d2&f=$f2&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$i</a>";
        }
        else {
            $ClientOrder = $i;
        }
        $ProductId = $myRow["ProductId"];
        $cName = $myRow["cName"];
        $eCode = toSpace($myRow["eCode"]);
        /////////

        //订单总数
        $checkAllQty = mysql_query("
          SELECT SUM(ALLQTY) AS ALLQTY,count(*) AS Orders FROM( 
          SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
          INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
          WHERE P.eCode LIKE (SELECT eCode FROM $DataIn.productdata WHERE ProductId='$ProductId') GROUP BY OrderPO
      )A", $link_id);

        $AllQtySum = toSpace(mysql_result($checkAllQty, 0, "AllQty"));
        $Orders = mysql_result($checkAllQty, 0, "Orders");

        //已出货数量
        $checkShipQty = mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'", $link_id);
        $ShipQtySum = toSpace(mysql_result($checkShipQty, 0, "ShipQty"));
        //$ShipQtySum
        //百分比
        $TempInfo = "style='CURSOR: pointer;' onclick='ViewChart($ProductId,1)'";
        $TempPC = $AllQtySum == 0 ? 0 : ($ShipQtySum / $AllQtySum) * 100;
        $TempPC = $TempPC >= 1 ? (round($TempPC) . "%") : (sprintf("%.2f", $TempPC) . "%");
        if ($AllQtySum > 0) {
            $TempInfo .= "title='订单总数:$AllQtySum,已出数量占:$TempPC'";
        }
        $GfileStr = $GfileStr == "" ? "&nbsp;" : $GfileStr;
        $Weight = zerotospace($Weight);
        //退货数量
        $checkReturnedQty = mysql_query("SELECT SUM(Qty) AS ReturnedQty FROM $DataIn.product_returned WHERE eCode='$eCode'", $link_id);
        $ReturnedQty = toSpace(mysql_result($checkReturnedQty, 0, "ReturnedQty"));
        if ($ReturnedQty > 0 && $ShipQtySum > 0) {
            //退货百分比
            $ReturnedPercent = sprintf("%.1f", (($ReturnedQty / $ShipQtySum) * 1000));
            if ($ReturnedPercent >= 5) {
                $ReturnedQty = "<span class=\"redB\">" . $ReturnedQty . "</span>";
            }
            else {
                if ($ReturnedPercent >= 2) {
                    $ReturnedQty = "<span class=\"yellowB\">" . $ReturnedQty . "</span>";
                }
                else {
                    $ReturnedQty = "<span class=\"greenB\">" . $ReturnedQty . "</span>";
                }
            }
            $ReturnedP =
            $TempInfo2 = "style='CURSOR: pointer;' onclick='ViewChart($ProductId,2)' title=\"退货率：$ReturnedPercent ‰\"";

        }
        else {
            $ReturnedQty = "&nbsp;";
            $TempInfo2 = "";
        }
        $ShipQtySum = "<span class='yellowB'>" . $ShipQtySum . "</span>";

        /* by.lwh 20180416 下载 */
        $myRowImg = mysql_fetch_array(mysql_query("SELECT CompanyId AS SCOM,FileName FROM $DataIn.doc_standarddrawing  WHERE CompanyId = '$CompanyId' AND FileRemark = '$cName'", $link_id));
        $FileName = $myRowImg["SCOM"];
        $CId = $myRow["CID"];
        $FileName = $myRowImg["FileName"];

        ////////
        $Weight = $myRow["Weight"];
        $MainWeight = $myRow["MainWeight"] == 0 ? "&nbsp;" : $myRow["MainWeight"];
        $TestStandard = $myRow["TestStandard"];
        include "../admin/Productimage/getPOrderImage.php";
        $Unit = $myRow["Unit"];
        $Qty = $myRow["Qty"];
        $Price = sprintf("%.3f", $myRow["Price"]);
        $PackRemark = $myRow["PackRemark"];
        $sgRemark = $myRow["sgRemark"];
        $DeliveryDate = $myRow["DeliveryDate"] == "0000-00-00" ? "" : $myRow["DeliveryDate"];


        //如果超过30天
        $AskDay = AskDay($OrderDate);
        $BackImg = $AskDay == "" ? "" : "background='../images/$AskDay'";

        $OrderDate = CountDays($OrderDate, 0);
        $Estate = $myRow["Estate"];
        $LockRemark = $Estate == 4 ? "已生成出货单." : "";
        $Locks = $myRow["Locks"];
        $PIRemark = $myRow["PIRemark"];
        $Leadtime = $myRow["Leadtime"];
        $LeadbgColor = "";
        $OldLeadtime = "";
        $hasLeadtimeSign = "";
        if ($Leadtime == "") {
            $checkTimeResult = mysql_fetch_array(mysql_query("SELECT Leadtime FROM $DataIn.yw3_pileadtime WHERE POrderId='$POrderId' LIMIT 1", $link_id));
            $Leadtime = $checkTimeResult["Leadtime"];
            $LeadbgColor = $Leadtime == "" ? $LeadbgColor : " bgColor='#F7E200' ";
            $Leadtime = $Leadtime == "" ? "&nbsp;" : $Leadtime;
        }
        include "../model/subprogram/PI_Leadtime.php";
        if ($weekName != "") {
            $OldLeadtime = $weekName;
            $hasLeadtimeSign = "YES";
        }
        //更改PI，显示title 提示主管审核
        $UpdateLeadtimeRemarkStr = "";
        $CheckUpdateResult = mysql_fetch_array(mysql_query("SELECT  UpdateLeadtime,Remark ,OldLeadtime FROM $DataIn.yw3_pileadtimechange WHERE POrderId=$POrderId LIMIT 1", $link_id));
        $UpdateLeadtime = $CheckUpdateResult["UpdateLeadtime"];
        $UpdateLeadtimeRemark = $CheckUpdateResult["Remark"];
        if ($UpdateLeadtime != "") {
            $LeadbgColor = " bgColor='#54BCE5' ";
            $UpdateLeadtime = GetWeek($UpdateLeadtime, $link_id);
            $OldLeadtime = $CheckUpdateResult["OldLeadtime"] == "" ? $OldLeadtime : GetWeek($CheckUpdateResult["OldLeadtime"], $link_id);
            $UpdateLeadtimeRemarkStr = "更改前的PI交期：$OldLeadtime\n更改后的PI交期：$UpdateLeadtime,更改原因：$UpdateLeadtimeRemark";
        }
        $PIRemark = $PIRemark . $UpdateLeadtimeRemarkStr;
        $Leadtime = $PIRemark == "" ? $Leadtime : "<div title='$PIRemark' style='color:#FF0000' >$Leadtime</div>";
        $pRemark = $myRow["pRemark"] == "" ? "&nbsp;" : $myRow["pRemark"];
        $cgRemark = $myRow["cgRemark"] == "" ? "&nbsp;" : $myRow["cgRemark"];
        $bjRemark = $myRow["bjRemark"] == "" ? "&nbsp;" : $myRow["bjRemark"];
        $dcRemark = $myRow["dcRemark"] == "" ? "&nbsp;" : $myRow["dcRemark"];

        include "../model/subprogram/order_shiptype.php";
        //读取操作员姓名
        $Operator = $myRow["Operator"];
        include "../model/subprogram/staffname.php";

        $thisSaleAmount = sprintf("%.2f", $Qty * $Price);//本订单卖出金额
        $sumSaleAmount = sprintf("%.2f", $sumSaleAmount + $thisSaleAmount);
        $sumQty = $sumQty + $Qty;
        //交货期
        //include "../model/subprogram/product_chjq.php";
        /*利润计算*////////////
        $CompanyId = $myRow["CompanyId"];
        if ($CompanyId == 1044) {
            $psValue = 0.95;
        }
        else {
            $psValue = 1;
        }
        $currency_Temp = mysql_query("SELECT A.Rate,A.Symbol FROM $DataPublic.currencydata A 
           INNER JOIN $DataIn.trade_object B ON A.Id=B.Currency WHERE  B.CompanyId=$CompanyId ORDER BY B.CompanyId LIMIT 1", $link_id);
        if ($RowTemp = mysql_fetch_array($currency_Temp)) {
            $Rate = $RowTemp["Rate"];//汇率
            $Symbol = $RowTemp["Symbol"];//货币符号
        }
        $thisTOrmbOUT = sprintf("%.3f", $thisSaleAmount * $Rate);//转成人民币的卖出金额******************************
        $thisTOrmbOUTsum += $thisTOrmbOUT;
        //产品RMB$saleRMB_P=sprintf("%.4f",$Price*$Rate);

        /*
		//配件成本计算:只计算需要采购的部分
		$cbAmountUSD=0;$cbAmountRMB=0;$llcbAmountUSD=0;$llcbAmountRMB=0;//初始化
		$CostResult=mysql_query("SELECT SUM((A.AddQty+A.FactualQty)*A.Price*IFNULL(C.Rate,1)) AS oTheCost,SUM(A.OrderQty*A.Price*IFNULL(C.Rate,1)) AS oTheCost2,IFNULL(C.Symbol,'RMB') AS Symbol,B.ProviderType
			FROM $DataIn.cg1_stocksheet A
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId
			LEFT JOIN $DataIn.trade_object B ON A.CompanyId=B.CompanyId
			LEFT JOIN $DataIn.currencydata C ON B.Currency=C.Id
			WHERE 1 AND A.Level = 1 AND S.POrderId='$POrderId'
			GROUP BY C.Id ORDER BY A.Id DESC",$link_id);
		if($CostRow= mysql_fetch_array($CostResult)){
			do{
				$cbAmount=sprintf("%.3f",$CostRow["oTheCost"]);
				$TempSymbol=$CostRow["Symbol"];
				$TempoTheCost=$CostRow["oTheCost"];
				$TempoTheCost2=$CostRow["oTheCost2"];
					$AmountTemp="cbAmount".strval($TempSymbol);
					$$AmountTemp+=sprintf("%.0f",$TempoTheCost);//利润成本
					$AmountTemp2="llcbAmount".strval($TempSymbol);
				$$AmountTemp2+=sprintf("%.0f",$TempoTheCost2);//理论成本
				}while ($CostRow= mysql_fetch_array($CostResult));
			}


		$GrossProfitp=sprintf("%.3f",$thisTOrmbOUT-$cbAmountUSD-$cbAmountRMB);

		//单品利润
		$profitRMB=sprintf("%.3f",$GrossProfitp/$Qty);

		//理论净利
		$profitRMB2=sprintf("%.3f",($thisTOrmbOUT-$llcbAmountUSD-$llcbAmountRMB-$llcbAmountRMB*$HzRate)/$Qty);
		//echo "($thisTOrmbOUT-$llcbAmountUSD-$llcbAmountRMB-$llcbAmountRMB*$HzRate)/$Qty)";

		$GrossProfit=$GrossProfitp;
		//$profitRMB=$profitRMB<=0.3?"<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='redB'>$profitRMB</sapn></a>":$profitRMB=$profitRMB<=0.7?"<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='yellowB'>$profitRMB</sapn></a>":"<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='greenB'>$profitRMB</sapn></a>";
		$profitRMB2PC=$Price==0?0:sprintf("%.0f",($profitRMB2*100)/($Price*$Rate));
		*/
        include "../model/subprogram/getOrderProfit.php";
        //订单利润保存
        $ReadProfitSign = "SAVE";
        include "subprogram/yw_order_profit.php";
        //净利分类
        if ($profitRMB2PC > 10) {
            $ViewSign = $ProfitType == 4 ? 1 : 0;
            $profitRMB2 = "<a href='../public/pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='greenB'>$profitRMB2($profitRMB2PC%)</sapn></a>";
        }
        else {
            if ($profitRMB2PC >= 3) {
                $ViewSign = $ProfitType == 3 ? 1 : 0;
                $profitRMB2 = "<a href='../public/pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='yellowB'>$profitRMB2($profitRMB2PC%)</span></a>";
            }
            else {
                if ($profitRMB2 < 0) {
                    $ViewSign = $ProfitType == 1 ? 1 : 0;
                    $profitRMB2 = "<a href='../public/pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='purpleB'>$profitRMB2($profitRMB2PC%)</span></a>";
                }
                else {
                    $ViewSign = $ProfitType == 2 ? 1 : 0;
                    $profitRMB2 = "<a href='../public/pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='redB'>$profitRMB2($profitRMB2PC%)</span></a>";
                }
            }
        }

        //采购交期设置
        $ReduceWeeks = "";
        $CheckResult = mysql_query("SELECT ReduceWeeks FROM $DataIn.yw2_cgdeliverydate WHERE POrderId='$POrderId' LIMIT 1", $link_id);
        if ($CheckRow = mysql_fetch_array($CheckResult)) {
            $ReduceWeeks = $CheckRow["ReduceWeeks"];
        }

        if ($ViewSign == 1 || $ProfitType == "") {
            //订单状态色：有未下采购单，则为白色,属性为可供的除外
            $checkColor = mysql_query("SELECT G.Id,G.StockId FROM $DataIn.cg1_stocksheet G 
             INNER JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
             INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
             INNER JOIN $DataIn.stuffmaintype TM ON TM.Id=T.mainType  
             WHERE 1 AND TM.blSign=1 AND G.Mid='0' and (G.FactualQty>'0' OR G.AddQty>'0') and G.PorderId='$POrderId'", $link_id);
            if ($checkColorRow = mysql_fetch_array($checkColor)) {
                $OrderSignColor = "bgColor='#fff'";//有未下需求单
            }
            else {//已全部下单
                $OrderSignColor = "bgColor='#339900'";  //设默认绿色
                //生产数量与工序数量不等时，黄色

                //入库数量
                $CheckscQty = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS rkQty FROM $DataIn.yw1_orderrk  WHERE POrderId='$POrderId'", $link_id));
                $rkQty = $CheckscQty["rkQty"];

                if ($Qty != $rkQty) {
                    $OrderSignColor = "bgColor='#FFCC00'";
                }
            }


            //采购单锁定
            $OrderCgRemark = "";
            $cgRemarked = 0;
            $TmpCgRemark = "";
            $CheckStockSql = mysql_query("SELECT * FROM $DataIn.cg1_lockstock K WHERE K.StockId LIKE '$POrderId%' AND K.Locks=0 AND exists (SELECT StockId FROM $DataIn.cg1_stocksheet WHERE StockId=K.StockId AND POrderId='$POrderId')", $link_id);
            while ($CheckStockRow = mysql_fetch_array($CheckStockSql)) {
                if ($CheckStockRow["Remark"] != "") {
                    $OrderCgRemark .= $OrderCgRemark == "" ? "原因:" . $CheckStockRow["Remark"] : "," . $CheckStockRow["Remark"];
                    $TmpCgRemark .= $OrderCgRemark == "" ? "" . $CheckStockRow["Remark"] : "," . $CheckStockRow["Remark"];
                }
                $cgRemarked = 1;
                $OrderSignColor = "bgColor='#0099FF'";
                break; //找到一个跳出当前循环
            }
            if ($cgRemarked == 1 && $OrderCgRemark == "") {
                $OrderCgRemark = "未填写原因";
                $TmpCgRemark = $OrderCgRemark;
            }


            $ColbgColor = "";

            //检查是否工单锁定
            $CheckSignSql = mysql_query("SELECT L.Id,L.Remark 
             FROM $DataIn.yw1_scsheet S 
             LEFT JOIN $DataIn.yw1_sclock L ON L.sPOrderId=S.sPOrderId 
             WHERE S.POrderId ='$POrderId' AND S.ActionId=101 AND L.Locks=0 LIMIT 1", $link_id);
            if ($CheckSignRow = mysql_fetch_array($CheckSignSql)) {
                $OrderRemark = $CheckSignRow["Remark"] . '/工单锁定;        ';
                $ColbgColor = "bgColor='#CCCCCC'";
            }

            //加急订单
            $checkExpress = mysql_query("SELECT Type,Remark,Estate FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' ORDER BY Id", $link_id);
            if ($checkExpressRow = mysql_fetch_array($checkExpress)) {
                do {
                    $Type = $checkExpressRow["Type"];
                    $UPRemark = $checkExpressRow["Remark"];
                    $UPEstate = $checkExpressRow["Estate"];
                    //echo $UPRemark;
                    switch ($Type) {
                        case 1:
                        $ColbgColor = "bgcolor='#0066FF'";
                            break;  //自有产品标识
                            case 2:
                            if ($UPEstate == 1) {
                                $ColbgColor = "bgcolor='#FF0000'";
                                $OrderRemark .= $UPRemark;
                            }
                            else {
                                $ColbgColor = "bgcolor='#CD2990'";
                                $OrderRemark .= $UPRemark . "/主管已审核锁定";
                            }
                            //$LockRemark=$OrderRemark="未确定产品 ".$UPRemark ;
                            break;    //未确定产品
                        }
                    } while ($checkExpressRow = mysql_fetch_array($checkExpress));
                }
            //动态读取 $thisTOrmbINo
                if ($OrderRemark != "") {
                    $TempStrtitle = $OrderRemark;
                }
                else {
                    $TempStrtitle = $TmpCgRemark;
                }

                $TempStrtitle = $TempStrtitle == "" ? "显示或隐藏配件采购明细资料" : $TempStrtitle;
                $showPurchaseorder = "<img onClick='ShowOrHide(StuffList$i,showtable$i,StuffList$i,\"$POrderId\",$i,\"\");' name='showtable$i' src='../images/showtable.gif' 
                title='$TempStrtitle' width='13' height='13' style='CURSOR: pointer'>";
                $StuffListTB = "<table width='$tableWidth' border='0' cellspacing='0' id='StuffList$i' style='display:none'>
                <tr bgcolor='#B7B7B7'>
                <td class='A0111' height='30'><br><div id='showStuffTB$i' width='$subTableWidth'>&nbsp;</div><br></td></tr></table>";
            //0:内容	1：对齐方式		2:单元格属性		3：截取
            //检查权限:订单备注、出货方式的权限
                $Weight = zerotospace($Weight);
            //出货数量和下单次数
                if ($Orders > 0) {
                    if ($Orders < 2) {
                        $ShipQtySum = $ShipQtySum . "<span class=\"redB\">($Orders)</span>";
                    }
                    else {
                        if ($Orders > 4) {
                            $ShipQtySum = $ShipQtySum . "<span class=\"greenB\">($Orders)</span>";
                        }
                        else {
                            $ShipQtySum = $ShipQtySum . "<span class=\"yellowB\">($Orders)</span>";
                        }
                    }
                }


                $checkIdSql = "SELECT NewQty,NewPrice FROM $DataIn.yw1_orderupdate WHERE POrderId ='$POrderId' AND Estate=1";
                $checkIdResult = mysql_query($checkIdSql, $link_id);
                $checkIdRow = mysql_fetch_array($checkIdResult);
                $NewQty = $checkIdRow["NewQty"];
                $NewPrice = $checkIdRow["NewPrice"];
                if ($NewPrice != $Price && $NewPrice > 0) {
                    $Price = "<span class='redB' title='原价:$Price,更新后的价格为:$NewPrice'>$Price</span>";
                }

                if ($NewQty != $Qty && $NewQty > 0) {
                    $Qty = "<span class='redB' title='原数量:$Qty,更新后的数量为:$NewQty'>$Qty</span>";
                }

                $ShipQtyStr = "";
                $tempStr = "";
                $titleShipQty = mysql_query("SELECT S.Qty AS ShipQty ,M.Date
                   FROM $DataIn.ch1_shipsheet  S
                   LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
                   WHERE S.POrderId='$POrderId'", $link_id);
                while ($titleRow = mysql_fetch_array($titleShipQty)) {
                    $ShipDate = $titleRow["Date"];
                    $titleQty = $titleRow["ShipQty"];
                    if ($tempStr == "") $tempStr = "出货时间:$ShipDate,出货数量:$titleQty";
                    else $tempStr = $tempStr . "<br>" . "出货时间:$ShipDate,出货数量:$titleQty";
                }
                if ($tempStr != "") $ShipQtyStr = "title='$tempStr'";


            //订单备注

                $enRemark = "&nbsp;";
                $RemarkResult = mysql_query("SELECT Remark FROM $DataIn.yw2_orderremark WHERE POrderId='$POrderId' AND Type=1 ORDER BY ID DESC LIMIT 1", $link_id);
                if ($RemarkRow = mysql_fetch_array($RemarkResult)) {
                    $enRemark = $RemarkRow["Remark"];
                }

            //$ToOutName=$myRow["ToOutName"];
                $ToOutName = "&nbsp;";

                $OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
                 LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
                 WHERE  O.POrderId='$POrderId' AND O.Mid=0 ", $link_id);
            //echo "";
                if ($Outmyrow = mysql_fetch_array($OutResult)) {
                //删除数据库记录
                //$Forshort=$myRow["Forshort"];
                    $ToOutName = $Outmyrow["ToOutName"];
                }

                $taxName = $myRow["taxName"] == "" ? "&nbsp;" : $myRow["taxName"];

                if ($From == "slist") {
                    $cnameTitle = $myRow["Forshort"];
                }
                $eCode .= '&nbsp;<img src="../images/printer.png" width="30" height="30" >';

            //生产时间
                $scDate = "&nbsp;";
                $scDateResult = mysql_query("SELECT DATE_FORMAT( scDate, '%Y-%m-%d' ) AS scDate FROM $DataIn.yw1_scsheet WHERE POrderId='$POrderId' GROUP BY POrderId", $link_id);
                if ($scDateRow = mysql_fetch_array($scDateResult)) {
                    $scDate = $scDateRow["scDate"];
                }

                switch ($myTask) {
                    case 0:
                    $ValueArray = array(
                        array(0 => $delivery, 1 => "align='center'"),
                        array(0 => $OrderPO, 2 => " onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,3,$POrderId,25)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $TestStandard, 1 => " title='$cnameTitle'", 3 => "line"),
                        array(0 => $CaseReport, 1 => "align='center'"),
                        array(0 => $eCode, 2 => "onmousedown='window.event.cancelBubble=true;' onclick='showPrintCodeWin($CompanyId,$Id)' style='CURSOR: pointer'"),
                        array(0 => $liningNo, 1 => "align='center' "),
                        array(0 => $RealLining, 1 => "align='center' style='font-weight:bold'" , 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,8,\"$POrderId\",36)' style='CURSOR: pointer'"),
                        array(0 => $scDate, 1 => "align='center'"),
                        array(0 => $ShipQtySum, 1 => "align='right'", 2 => $TempInfo),
                        array(0 => $ReturnedQty, 1 => "align='right'", 2 => $TempInfo2),
                        array(0 => $Weight, 1 => "align='right'"),
                        array(0 => $Price, 1 => "align='right'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,12,\"$POrderId|$Price\",35)' style='CURSOR: pointer'"),
                        array(0 => $Qty, 1 => "align='right'"),
                        array(0 => $thisSaleAmount, 1 => "align='right'"),
                        array(0 => $PackRemark, 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,15,$POrderId,2)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $enRemark, 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,16,$POrderId,9)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $sgRemark, 1 => "style='color:#F00;'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,17,$POrderId,6)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $cgRemark, 1 => "align='right'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,18,$POrderId,5)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $dcRemark, 1 => "align='right'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,19,$POrderId,8)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $ShipType, 1 => "align='right'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,20,$POrderId,3)' style='CURSOR: pointer'"),
                        array(0 => $wl_cycle, 1 => "align='right'"),
                        array(0 => $bl_cycle, 1 => "align='right'"),
                        array(0 => $sc_cycle, 1 => "align='right'"),
                        array(0 => $sctj_date, 1 => "align='right'"),
                        array(0 => $Leadtime, 1 => "align='right'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='set_weekdate(this,\"$PIoId\",\"$ReduceWeeks\",\"$hasLeadtimeSign\")'   $LeadbgColor"),
                        array(0 => $Operator, 1 => "align='right'"),
                        array(0 => $OrderDate, 1 => "align='right' $BackImg"),
                        array(0 => $bjRemark, 3 => "..."),
                        array(0 => $ToOutName, 1 => "align='right'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,29,$POrderId,33)' style='CURSOR: pointer'"),
                        array(0 => $taxName, 1 => "align='right'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,30,$POrderId,34)' style='CURSOR: pointer'"),

                    );
                    break;
                    case 1:
                    $GrossProfit = sprintf("%.0f", $GrossProfit);
                    $GrossProfitSUM = $GrossProfitSUM + $GrossProfit;
                    if ($GrossProfit < 0) {
                        $GrossProfit = -1 * $GrossProfit;
                        $GrossProfit = "<div class='redB'>-$GrossProfit</div>";
                    }
                    //array(0=>$MainWeight,1=>"align='right'"),
                    $ValueArray = array(
                        array(0 => $OrderPO, 2 => " onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,2,$POrderId,25)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $TestStandard, 1 => " title='$cnameTitle'", 3 => "line"),
                        array(0 => $CaseReport, 1 => "align='center'"),
                        array(0 => $eCode, 3 => "..."),
                        array(0 => $ShipQtySum, 1 => "align='right'", 2 => $TempInfo),
                        array(0 => $ReturnedQty, 1 => "align='center'", 2 => $TempInfo2),
                        array(0 => $Weight, 1 => "align='right'"),
                        array(0 => $Unit, 1 => "align='center'"),
                        array(0 => $Price, 1 => "align='right'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,10,\"$POrderId|$Price\",35)' style='CURSOR: pointer'"),

                        array(0 => $Qty, 1 => "align='right' $ShipQtyStr"),
                        array(0 => $thisSaleAmount, 1 => "align='right'"),
                        array(0 => $profitRMB2, 1 => "align='right'"),
                        array(0 => $GrossProfit, 1 => "align='right'"),

                        array(0 => $PackRemark, 2 => "onmousedown='window. event.cancelBubble=true;' onclick='updateJq($i,15,$POrderId,2)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $enRemark, 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,16,$POrderId,9)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $sgRemark, 1 => "style='color:#F00;'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,17,$POrderId,6)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $cgRemark, 1 => "align='center'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,18,$POrderId,5)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $dcRemark, 1 => "align='center'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,19,$POrderId,8)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $ShipType, 1 => "align='left'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,20,$POrderId,3)' style='CURSOR: pointer'"),
                        array(0 => $wl_cycle, 1 => "align='center'"),
                        array(0 => $bl_cycle, 1 => "align='center'"),
                        array(0 => $sc_cycle, 1 => "align='center'"),
                        array(0 => $sctj_date, 1 => "align='center'"),
                        array(0 => $Leadtime, 1 => "align='center' ", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='set_weekdate(this,\"$PIoId\",\"$ReduceWeeks\",\"$hasLeadtimeSign\")'   $LeadbgColor"),
                        array(0 => $Operator, 1 => "align='center'"),
                        array(0 => $OrderDate, 1 => "align='center' $BackImg"),
                        array(0 => $bjRemark, 3 => "..."),
                        array(0 => $ToOutName, 1 => "align='center'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,29,$POrderId,33)' style='CURSOR: pointer'"),
                        array(0 => $taxName, 1 => "align='center'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,30,$POrderId,34)' style='CURSOR: pointer'"),

                    );
                    break;
                    case 2:
                    $Forshort = $myRow['Forshort'];
                    $ValueArray = array(
                        array(0 => $Forshort),
                        array(0 => $OrderPO, 2 => " onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,3,$POrderId,25)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $TestStandard, 3 => "line"),
                        array(0 => $CaseReport, 1 => "align='center'"),
                        array(0 => $eCode, 3 => "..."),
                        array(0 => $ShipQtySum, 1 => "align='right'", 2 => $TempInfo),
                        array(0 => $Unit, 1 => "align='center'"),
                        array(0 => $Price, 1 => "align='right'"),
                        array(0 => $Qty, 1 => "align='right'"),
                        array(0 => $thisSaleAmount, 1 => "align='right'"),
                        array(0 => $cgRemark, 1 => "align='center'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,12,$POrderId,5)' style='CURSOR: pointer'", 3 => "..."),
                        array(0 => $taxName, 1 => "align='center'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,13,$POrderId,34)' style='CURSOR: pointer'"),
                        array(0 => $Operator, 1 => "align='center'"),

                    );
                    break;
                }
                $checkidValue = $Id;
                include "subprogram/read_model_6_yw.php";
                echo $StuffListTB;

        }//净利分类显示结束
    } while ($myRow = mysql_fetch_array($myResult));
    //合计
    switch ($myTask) {
        case 0:
        $m = 1;
        $ClientPC = sprintf("%.0f", $GrossProfitSUM * 100 / $thisTOrmbOUTsum);
        $ValueArray = array(
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => $sumQty, 1 => "align='right'"),
            array(0 => $sumSaleAmount, 1 => "align='right'"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
        );
        $ShowtotalRemark = "合计";
        $isTotal = 1;
        include "../model/subprogram/read_model_total.php";
        break;
        case 1:
        $m = 1;
        $ClientPC = $thisTOrmbOUTsum > 0 ? sprintf("%.0f", $GrossProfitSUM * 100 / $thisTOrmbOUTsum) : "";
        $ValueArray = array(
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => $sumQty, 1 => "align='right'"),
            array(0 => $sumSaleAmount, 1 => "align='right'"),
            array(0 => "&nbsp;"),
            array(0 => $GrossProfitSUM, 1 => "align='right'"),
            array(0 => "毛利率：" . $ClientPC . "%", 1 => "align='left'"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
            array(0 => "&nbsp;"),
        );

        $ShowtotalRemark = "合计";
        $isTotal = 1;
        include "../model/subprogram/read_model_total.php";
        break;
    }

}
else {
    noRowInfo($tableWidth);
}
//步骤7：
include "../model/subprogram/ColorInfo.php";
echo '</div>';

//$myResult1 = mysql_query($mySql,$link_id);
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
ChangeWtitle($SubCompany . $DefaultClient . "客户未出明细列表");
if ($From != "slist") {
    if ($SignType != 12) {
        $ActioToS = "1,2,3,88,182,23,11,38,39,60,74,91,103,104,131";
    }
    else {
        $ActioToS = "1,2,3,88,182,23,11,38,39,60,74,91,103,104,146,147,131";
    }
}
else {
    if ($operation != "splite") {
        $ActioToS = "1,2,3,88,182,23,11,38,39,60,74,103,104,131";  //22,
    }
    else {
        $ActioToS = "2,3,88,182,23,11,38,39,60,74,103,104,131";
    }
}
$codeCompanyId = array('1004', '1059', '100024', '1093', '1046', '2397', '1103', '2553', '100035', '100113', '1097');
if (in_array($CompanyId, $codeCompanyId)) {
    $ActioToS = $ActioToS . ",168";
}

include "../model/subprogram/read_model_menu.php";
?>
<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
<script src='../model/IE_FOX_MASK.js' type="text/javascript"></script>
<script src='../model/weekdate.js' type="text/javascript"></script>
<script language="JavaScript" type="text/JavaScript">
   var selectIds;
   function yh(e) {
    var choosedRow = 0;

    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
            if (choosedRow == 1) {
                selectIds = jQuery(this).val();
            } else {
                selectIds = selectIds + "," + jQuery(this).val();
            }
        }
    });


    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }
    openWinDialog(e, "yw_order_read_yh.php", 405, 300, 'bottom');

}


function batchChangescTime1() {
    jQuery.ajax({
        url: 'yw_order_ajax_yh.php',
        type: 'post',
        data: {
            date: jQuery('#yhDate').val(),
            Ids: selectIds
        },
        dataType: 'json',
        beforeSend: function () {
            jQuery('.response').show();
        },
        success: function (result) {
            if (result.rlt === true) {
                window.location.reload();
            }
        }
    }).done(function () {
        window.location.reload();

    });
}

var weekdate = new WeekDate();
var staffNumber = <?php echo $Login_P_Number?>;
function ViewChart(Pid, OpenType) {
    document.form1.action = "../public/productdata_chart.php?Pid=" + Pid + "&Type=" + OpenType;
    document.form1.target = "_blank";
    document.form1.submit();
    document.form1.target = "_self";
    document.form1.action = "";
}


function updateJq(TableId, RowId, runningNum, toObj) {//行即表格序号;列，流水号，更新源

    if (RowId == 10) {
        //价格设定
        if (document.getElementById("checkid" + TableId)) {
            //存在
        } else {
            return;
        }
    }

    showMaskBack();
    var InfoSTR = "";
    var buttonSTR = "";
    var theDiv = document.getElementById("Jp");
    var ObjId = document.form1.ObjId.value;
    var tempTableId = document.form1.ActionTableId.value;
    theDiv.style.top = event.clientY + document.body.scrollTop + 'px';
    if (toObj == 25) {
        theDiv.style.left = event.clientX + document.body.scrollLeft + 'px';
    }
    else {
        theDiv.style.left = event.clientX + document.body.scrollLeft - parseInt(theDiv.style.width) + 'px';
    }
    //theDiv.style.left=event.clientX + document.body.scrollLeft-parseInt(theDiv.style.width)+'px';
    if (theDiv.style.visibility == "hidden" || toObj != ObjId || TableId != tempTableId) {
        document.form1.ActionTableId.value = TableId;
        document.form1.ActionRowId.value = RowId;
        document.form1.ObjId.value = toObj;
        switch (toObj) {
            case 1:	//采购单交货期
            InfoSTR = "<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='14' class='TM0000' readonly>的采购单交货期:<input name='DeliveryDate' type='text' id='DeliveryDate' size='10' maxlength='10' class='INPUT0100' onFocus='WdatePicker()' readonly>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
            break;
            case 2:	//订单备注
            InfoSTR = "更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='TM0000' readonly>的订单备注<input name='PackRemark' type='text' id='PackRemark' size='50' class='INPUT0100'><br>";
            break;
            case 3://出货方式
            InfoSTR = "<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='TM0000' readonly/>订单出货方式<select id='ShipType' name='ShipType' style='width:150px;'><option value='' 'selected'>请选择</option>";

            <?PHP
            $shipTypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.ch_shiptype WHERE  Estate=1 ORDER BY Id", $link_id);
            if ($TypeRow = mysql_fetch_array($shipTypeResult)) {
                do {
                    $echoInfo .= "<option value='$TypeRow[Id]'>$TypeRow[Name]</option>";
                } while ($TypeRow = mysql_fetch_array($shipTypeResult));
            }
            ?>
            InfoSTR = InfoSTR + "<?PHP echo $echoInfo; ?>" + "</select><br>";
            break;
            case 4://PI交期
            var runNum = runningNum.split("|");
            InfoSTR = "更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='" + runNum[0] + "' size='12' class='TM0000' readonly><input name='PIoId' type='hidden' id='PIoId' value='" + runNum[1] + "'>的PI交货日期<input name=PIDate' type='text' id='PIDate' size='10' maxlength='10' class='INPUT0100' onfocus='WdatePicker({minDate:\"%y-%M-%d\",isShowWeek:true,onpicked:function() {document.getElementById(\"PIWeek\").innerHTML=$dp.cal.getP(\"W\",\"WW\")+\"周\";}} )'  readonly><span id='PIWeek' style='margin-left:5px;color:#0000ff'></span>";
            break;
            case 5://采购备注
            InfoSTR = "更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='INPUT0000' readonly>的采购备注<input name='cgRemark' type='text' id='cgRemark' size='50' class='INPUT0100'>";
            break;
            case 6://生管备注
            InfoSTR = "更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='INPUT0000' readonly>的生管备注<input name='sgRemark' type='text' id='sgRemark' size='50' class='INPUT0100'>";
            break;
            case 8://待出备注
            InfoSTR = "更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='INPUT0000' readonly>的待出备注<input name='dcRemark' type='text' id='dcRemark' size='50' class='INPUT0100'>";
            break;
            case 9://英文备注
            InfoSTR = "更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='INPUT0000' readonly>的英文备注<input name='enRemark' type='text' id='enRemark' size='50' class='INPUT0100'>";
            break;
            case 25:	//订单备注
            InfoSTR = "更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='INPUT0000' readonly> 的PO:<input name='OrderPO' type='text' id='OrderPO' size='20' class='INPUT0100'><br>";
            break;

            case 33://转发对象名称
            InfoSTR = "<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='TM0000' readonly/>转发对象名称<select id='ToOutName' name='ToOutName' style='width:150px;'><option value='' 'selected'>请选择</option>";
                //var CompanyId=document.getElementById("CompanyId").value;
                <?PHP
                $OutInfo = "";
                $ToOutNameResult = mysql_query("SELECT  D.Id,D.ToOutName as Name ,C.Forshort 
                   FROM $DataIn.yw7_clientToOut D 
                   LEFT JOIN $DataIn.trade_object C ON C.CompanyId=D.CompanyId
                   WHERE   D.Estate=1   AND D.CompanyId='$CompanyId'  ", $link_id);

                if ($ToOutNameRow = mysql_fetch_array($ToOutNameResult)) {
                    do {
                        $OutInfo .= "<option value='$ToOutNameRow[Id]'>$ToOutNameRow[Forshort]-$ToOutNameRow[Name]</option>";
                    } while ($ToOutNameRow = mysql_fetch_array($ToOutNameResult));
                }
                ?>
                InfoSTR = InfoSTR + "<?PHP echo $OutInfo; ?>" + "</select><br>";
                break;

            case 34://产品报关方式
            InfoSTR = "<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='TM0000' readonly/>产品报关方式<select id='tmptaxtypeId' name='tmptaxtypeId' style='width:150px;'><option value='' 'selected'>请选择</option>";
                //var CompanyId=document.getElementById("CompanyId").value;
                <?PHP
                $OutInfo = "";
                $taxtypeResult = mysql_query("SELECT * FROM $DataIn.taxtype WHERE Estate=1 order by Id  ", $link_id);
                if ($ToOutNameRow = mysql_fetch_array($taxtypeResult)) {
                    do {
                        $ptId = $ToOutNameRow["Id"];
                        $ptName = $ToOutNameRow["Name"];
                        $OutInfo .= "<option value='$ptId' >  $ptName </option>";
                    } while ($ToOutNameRow = mysql_fetch_array($taxtypeResult));
                }
                ?>
                InfoSTR = InfoSTR + "<?PHP echo $OutInfo; ?>" + "</select><br>";
                break;

            case 35://价格
            var runNum = runningNum.split("|");
            InfoSTR = "更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='" + runNum[0] + "' size='12' class='INPUT0000' readonly>的价格<input name='Price' type='text' id='Price' value='" + runNum[1] + "' size='50' class='INPUT0100'>";
            break;

            case 36://台车
            InfoSTR = "更新订单流水号为<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='12' class='TM0000' readonly>的台车号<input name='liningNo' type='text' id='liningNo' size='10' maxlength='10' class='INPUT0100'>";
            break;

        }
        if (toObj > 1) {
            var buttonSTR = "&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";
        }
        infoShow.innerHTML = InfoSTR + buttonSTR;
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
    if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
        theDiv.filters.revealTrans.apply();
        //theDiv.style.visibility = "hidden";
        theDiv.filters.revealTrans.play();
    }
    theDiv.style.visibility = "hidden";
    //theDiv.filters.revealTrans.play();
    infoShow.innerHTML = "";
    closeMaskBack();    //add by zx 关闭庶影   20110323   add by zx 加入庶影   20110323  IE_FOX_MASK.js
}

function aiaxUpdate() {
    var ObjId = document.form1.ObjId.value;
    var tempTableId = document.form1.ActionTableId.value;
    var tempRowId = document.form1.ActionRowId.value;
    var temprunningNum = document.form1.runningNum.value;

    switch (ObjId) {
        case "1":		//更新采购单交货期:
        var tempDeliveryDate = document.form1.DeliveryDate.value;
        myurl = "purchaseorder_updated.php?StockId=" + temprunningNum + "&DeliveryDate=" + tempDeliveryDate + "&ActionId=jq";
        retCode = openUrl(myurl);
        if (retCode != -2) {
                //更新成功,隐藏DIV，并且重新写该单元格的内容	或 重新动态更新需求单列表
                if (tempDeliveryDate == "") {
                    tempDeliveryDate = "未确定";
                }
                var ColorDate = Number(DateDiff(tempDeliveryDate));
                if (ColorDate < 2) {
                    eval("ListTB" + tempTableId).rows[tempRowId].cells[15].innerHTML = "<div class='redB'>" + tempDeliveryDate + "</div>";
                }
                else {
                    if (ColorDate < 5) {
                        eval("ListTB" + tempTableId).rows[tempRowId].cells[15].innerHTML = "<div class='yellowB'>" + tempDeliveryDate + "</div>";
                    }
                    else {
                        eval("ListTB" + tempTableId).rows[tempRowId].cells[15].innerHTML = "<div class='greenB'>" + tempDeliveryDate + "</div>";
                    }
                }
                CloseDiv();
            }
            break;
        case "2":		//订单说明 PackRemark
        var tempPackRemark0 = document.form1.PackRemark.value;
        var tempPackRemark1 = encodeURIComponent(tempPackRemark0);
        myurl = "yw_order_updated.php?POrderId=" + temprunningNum + "&tempPackRemark=" + tempPackRemark1 + "&ActionId=PackRemark";
        var ajax = InitAjax();
        ajax.open("GET", myurl, true);
        ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;" + tempPackRemark0 + "</NOBR></DIV>";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;
        case "3":		//出货方式
        var tempShipType0 = document.form1.ShipType.value;
        var tempShipType1 = encodeURIComponent(tempShipType0);
        myurl = "yw_order_updated.php?POrderId=" + temprunningNum + "&tempShipType=" + tempShipType1 + "&ActionId=ShipType";
            //alert(myurl);
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    if (tempShipType0.length > 0)
                    //eval("ListTable"+tempTableId).rows[0].cells[tempRowId].innerHTML="<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;"+tempShipType0+"</NOBR></DIV>";
                eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<img src='../images/ship" + tempShipType0 + ".png' style='width:20px;height:20px;'/>";
                else
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;" + tempShipType0 + "</NOBR></DIV>";
                CloseDiv();
            }
        }
        ajax.send(null);
        break;
        case "4":		//PI交期
        var tempPIDate = document.form1.PIDate.value;
        var tempPIoId = document.form1.PIoId.value;
        myurl = "yw_order_updated.php?POrderId=" + temprunningNum + "&PIoId=" + tempPIoId + "&PIDate=" + tempPIDate + "&ActionId=PIDate";
            //alert(myurl);
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;" + tempPIDate + "</NOBR></DIV>";
                    if (tempPIDate.length >= 10) eval("ListTable" + tempTableId).rows[0].cells[tempRowId].style.bgColor = "#F7E200";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;
        case "5"://更新采购备注
        var tempcgRemark = document.form1.cgRemark.value;
            var tempcgRemark1 = encodeURIComponent(tempcgRemark);//传输中文
            myurl = "yw_order_updated.php?POrderId=" + temprunningNum + "&cgRemark=" + tempcgRemark1 + "&ActionId=cgRemark";
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;" + tempcgRemark + "</NOBR></DIV>";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;
        case "6"://更新生管备注
        var tempsgRemark = document.form1.sgRemark.value;
            var tempsgRemark1 = encodeURIComponent(tempsgRemark);//传输中文
            myurl = "yw_order_updated.php?POrderId=" + temprunningNum + "&sgRemark=" + tempsgRemark1 + "&ActionId=sgRemark";
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;" + tempsgRemark + "</NOBR></DIV>";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;

        case "8"://更新待出备注
        var tempdcRemark = document.form1.dcRemark.value;
            var tempdcRemark1 = encodeURIComponent(tempdcRemark);//传输中文
            myurl = "yw_order_updated.php?POrderId=" + temprunningNum + "&dcRemark=" + tempdcRemark1 + "&ActionId=dcRemark";
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;" + tempdcRemark + "</NOBR></DIV>";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;

        case "9"://更新英文备注
        var tempenRemark = document.form1.enRemark.value;
            var tempenRemark1 = encodeURIComponent(tempenRemark);//传输中文
            myurl = "yw_order_updated.php?POrderId=" + temprunningNum + "&enRemark=" + tempenRemark1 + "&ActionId=enRemark";
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title='$Value0'><NOBR>&nbsp;" + tempenRemark + "</NOBR></DIV>";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;

        case "25":		//更新PO
        var OrderPO = jQuery('input#OrderPO').val();
        var tempOrderPO = encodeURIComponent(OrderPO);
        myurl = "yw_order_updated.php?POrderId=" + temprunningNum + "&tempOrderPO=" + tempOrderPO + "&ActionId=OrderPO";
        var ajax = InitAjax();
        ajax.open("GET", myurl, true);
        ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' ><NOBR>" + OrderPO + "</NOBR></DIV>";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;

        case "33":		//客户出货指定转发对象
        var tempToOutName0 = document.form1.ToOutName.value;
        var ToOutNameObj = document.form1.ToOutName;
        var ToOutNameText = "";
        for (i = 1; i < ToOutNameObj.length; i++) {
            if (ToOutNameObj[i].selected == true) {
                ToOutNameText = ToOutNameObj[i].innerText;
            }
        }
            //alert(ToOutNameText);
            var tempToOutName1 = encodeURIComponent(tempToOutName0);
            myurl = "ch_shippinglist_splitupdated.php?POrderId=" + temprunningNum + "&tempToOutName=" + tempToOutName1 + "&ActionId=ToOutName" + "&sId=0"; //sId=0 表示来处yw_order_read
            //alert (myurl);
            //return ;
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    if (tempToOutName0.length > 0)
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = ToOutNameText;
                    else
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "&nbsp;";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;

        case "34":		//
        var taxtypeId = document.form1.tmptaxtypeId.value;
            //alert(taxtypeId);
            var taxtypeObj = document.form1.tmptaxtypeId;
            var taxtypeNameText = "";
            for (i = 1; i < taxtypeObj.length; i++) {
                if (taxtypeObj[i].selected == true) {
                    taxtypeNameText = taxtypeObj[i].innerText;
                }
            }
            //alert(ToOutNameText);
            var tempToOutName1 = encodeURIComponent(tempToOutName0);
            myurl = "ch_shippinglist_splitupdated.php?POrderId=" + temprunningNum + "&taxtypeId=" + taxtypeId + "&ActionId=taxtype";
            //alert (myurl);
            //return ;
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    if (taxtypeId.length > 0)
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = taxtypeNameText;
                    else
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "&nbsp;";
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;


        case "35"://更新价格

        var tempPrice = document.form1.Price.value;
        if (!/^\d+(\.\d+)?$/.test(tempPrice)) {
            alert("请输入正确的价格");
            return;
        }

        myurl = "yw_order_updated.php?POrderId=" + temprunningNum + "&Price=" + tempPrice + "&ActionId=Price";
        var ajax = InitAjax();
        ajax.open("GET", myurl, true);
        ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = tempPrice;
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;

        case "36"://更新台车

        var templiningNo = document.form1.liningNo.value;
        if (!/^\d+(\.\d+)?$/.test(templiningNo)) {
            alert("请输入正确的台车");
            return;
        }

        myurl = "yw_order_updated.php?POrderId=" + temprunningNum + "&liningNo=" + templiningNo + "&ActionId=liningNo";
        var ajax = InitAjax();
        ajax.open("GET", myurl, true);
        ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = templiningNo;
                    CloseDiv();
                }
            }
            ajax.send(null);
            break;

        }
    }

    function ChangeStuff(tempTableId, tempRowId, TypeId, toObj, StockId) {
        var num = Math.random();
        BackStockId = window.open("yw_order_choosestuff.php?r=" + num + "&TypeId=" + TypeId + "&SearchNum=1", "BackStockId", "dialogHeight =800px;dialogWidth=500px;center=yes;scroll=yes");
        if (BackStockId) {
        var FieldArray = BackStockId.split("^^");//配件名称，配件单价
        var tempStuffId = FieldArray[0];
        var tempStuffCname = FieldArray[1];
        var tempPrice = FieldArray[2];
        //更新配件
        myurl = "yw_order_updated.php?StockId=" + StockId + "&StuffId=" + tempStuffId + "&Price=" + tempPrice + "&ActionId=ChangeStuff";
        retCode = openUrl(myurl);
        if (retCode != -2) {
            eval("ListTB" + tempTableId).rows[tempRowId].cells[3].innerHTML = "<div class='greenB'>" + FieldArray[1] + "</div>";
            eval("ListTB" + tempTableId).rows[tempRowId].cells[4].innerHTML = FieldArray[2];
        }
    }
}


</script>
<script language="JavaScript" type="text/JavaScript">

//只供yw_order_Ajax add by zx 2011-01-09
function updateLock(TableCellId, runningNum, Locks) {//行即表格序号;列，流水号，更新源
    showMaskBack();
    var InfoSTR = "";
    var buttonSTR = "";
    var theDiv = document.getElementById("Jp");
    var tempTableId = document.form1.ActionTableId.value;
    theDiv.style.top = event.clientY + document.body.scrollTop + 'px';
    if (theDiv.style.visibility == "hidden") {
        //document.form1.ActionTableId.value=TableCellId;//表格名称
        InfoSTR = "<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='14' class='TM0000' readonly>&nbsp;<select name='myLock' id='myLock' size='1'> <option value='1'>解锁</option><option value='0'>锁定</option> </select> &nbsp;&nbsp;&nbsp;<br><br>锁定原因:<input name='myLockRemark' type='text' id='myLockRemark' style='width:320px;' class='INPUT0100'/>&nbsp;&nbsp;&nbsp;<br><div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdateLock(" + TableCellId + ")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'></div>";

        infoShow.innerHTML = InfoSTR;
        theDiv.className = "moveRtoL";
        if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
            theDiv.filters.revealTrans.apply();//防止错误
            theDiv.filters.revealTrans.play(); //播放
        }
        theDiv.style.visibility = "";
        theDiv.style.display = "";
    }
}

function aiaxUpdateLock(TableCellId) {
    //var tempTableId=document.form1.ActionTableId.value;
    var temprunningNum = document.form1.runningNum.value;
    var tempmyLock = document.form1.myLock.value;
    var tempLockRemark = document.form1.myLockRemark.value;
    if (tempmyLock == "0" && tempLockRemark == "") {
        alert("请输入锁定原因!");
        return;
    }
    var tempLockRemark1 = encodeURIComponent(tempLockRemark);//传输中文
    myurl = "../admin/yw_order_ajax_updated.php?StockId=" + temprunningNum + "&myLock=" + tempmyLock + "&LockRemark=" + tempLockRemark1 + "&Action=Lock";

    var ajax = InitAjax();
    ajax.open("GET", myurl, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4) {// && ajax.status ==200
            if (tempmyLock == "1") {
                //eval(tempTableId).rows[RowId].cells[0]
                eval(TableCellId).innerHTML = "<div title='采购未锁定'> <img src='../images/unlock.png' width='15' height='15'> </div>";
            }
            else {
                eval(TableCellId).innerHTML = "<div style='background-color:#ff0000' title='采购已锁定," + tempLockRemark + "' > <img src='../images/lock.png' width='15' height='15'></div>";
            }
            CloseDiv();
        }
    }
    ajax.send(null);
}

function set_weekdate(el, PIoId, ReduceWeeks, hasLeadtimeSign) {
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
            var tmpArray = PIoId.split("|");
            ReduceWeeks = document.getElementById("ReduceWeeks").value;
            updateWeekRemark = encodeURIComponent(document.getElementById("updateWeekRemark").value);
            if (hasLeadtimeSign == "YES" && updateWeekRemark == "") {
                alert("改变交期，请填写改变交期的原因，并告知主管审核，新的交期才能生效");
                return false;
            }
            if (hasLeadtimeSign == "YES" && updateWeekRemark != "") {
                alert("改变交期，请告知主管审核，新的交期才能生效");
            }
            myurl = "yw_order_updated.php?POrderId=" + tmpArray[0] + "&PIoId=" + tmpArray[1] + "&PIDate=" + tempPIDate + "&ReduceWeeks=" + ReduceWeeks + "&updateWeekRemark=" + updateWeekRemark + "&hasLeadtimeSign=" + hasLeadtimeSign + "&ActionId=PIDate";
            //alert(myurl); el.innerHTML=tempWeeks;return;
            var ajax = InitAjax();
            ajax.open("GET", myurl, true);
            ajax.onreadystatechange = function () {
                if (ajax.readyState == 4) {// && ajax.status ==200
                    el.innerHTML = tempWeeks;
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

}


function updatescLock(TableCellId, runningNum, Locks) {//行即表格序号;列，流水号，更新源
    showMaskBack();
    var InfoSTR = "";
    var buttonSTR = "";
    var theDiv = document.getElementById("Jp");
    theDiv.style.top = event.clientY + document.body.scrollTop + 'px';
    if (theDiv.style.visibility == "hidden") {
        InfoSTR = "<input name='runningNum' type='text' id='runningNum' value='" + runningNum + "' size='14' class='TM0000' readonly>&nbsp;<select name='scLock' id='scLock' size='1'> <option value='1'>解锁</option><option value='0'>锁定</option> </select> &nbsp;&nbsp;&nbsp;<br><br>锁定原因:<input name='scLockRemark' type='text' id='scLockRemark' style='width:320px;' class='INPUT0100'/>&nbsp;&nbsp;&nbsp;<br><div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdateScLock(" + TableCellId + ")'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'></div>";

        infoShow.innerHTML = InfoSTR;
        theDiv.className = "moveRtoL";
        if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
            theDiv.filters.revealTrans.apply();//防止错误
            theDiv.filters.revealTrans.play(); //播放
        }
        theDiv.style.visibility = "";
        theDiv.style.display = "";
    }
}

function aiaxUpdateScLock(TableCellId) {
    var temprunningNum = document.form1.runningNum.value;
    var tempmyLock = document.form1.scLock.value;
    var tempLockRemark = document.form1.scLockRemark.value;
    if (tempmyLock == "0" && tempLockRemark == "") {
        alert("请输入锁定原因!");
        return;
    }
    var tempLockRemark1 = encodeURIComponent(tempLockRemark);//传输中文
    myurl = "../admin/semifinished_order_ajax_updated.php?sPOrderId=" + temprunningNum + "&myLock=" + tempmyLock + "&LockRemark=" + tempLockRemark1 + "&Action=Lock";

    var ajax = InitAjax();
    ajax.open("GET", myurl, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4) {// && ajax.status ==200
            if (tempmyLock == "1") {
                eval(TableCellId).innerHTML = "<div title='工单未锁定'> <img src='../images/unlock.png' width='15' height='15'> </div>";
            }
            else {
                eval(TableCellId).innerHTML = "<div style='background-color:#ff0000' title='工单已锁定," + tempLockRemark + "' > <img src='../images/lock.png' width='15' height='15'></div>";
            }
            CloseDiv();
        }
    }
    ajax.send(null);
}

function ToSetDelivery(el) {
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
            //alert(updateWeekRemark);
            myurl = "yw_order_updated2.php?Ids=" + Ids + "&PIDate=" + tempPIDate + "&updateWeekRemark=" + updateWeekRemark + "&ReduceWeeks=" + ReduceWeeks;
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
        }
    };

    weekdate.show(el, 1, saveFun, "");
    if (ReduceWeeks == "" || ReduceWeeks == "-1") {
        weekdate.addOtherWeekInfo("&nbsp;&nbsp;采购交期:<select name='ReduceWeeks'  id='ReduceWeeks' style='width:80px'><option value='-1' selected>前一周</option><option value='0'>同周</option></select>");
    }
    else {
        weekdate.addOtherWeekInfo("&nbsp;&nbsp;采购交期:<select name='ReduceWeeks'  id='ReduceWeeks' style='width:80px'><option value='-1'>前一周</option><option value='0' selected>同周</option></select>");
    }
}

//导入报价
function ToImportPrice() {
    document.form1.action = "yw_order_price_add.php";
    document.form1.target = "_self";
    document.form1.submit();
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
    document.form1.action = "yw_order_price_export.php?Ids=" + Ids;
    document.form1.target = "download";
    document.form1.submit();
}

/* 打印 by.lwh 20180413 */

function showPrintCodeWin(CompanyId, id) {
    var url = 'yw_order_read_print.php?CompanyId=' + CompanyId + '&id=' + id;
    window.open(url, 'newwindow', 'height=400, width=450, top=100, left=100, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=n o, status=no')
}

/* 打印 */
function RefreshPages(e) {
    switch (e) {
        case 'CompanyId':
        document.forms["form1"].elements["BuildNo"].value = "";
        document.forms["form1"].elements["OrderPO"].value = "";
        document.forms["form1"].elements["TypeId"].value = "";
        document.forms["form1"].elements["cx"].value = "";
        document.form1.submit();
        break;
        case 'BuildNo':
        document.forms["form1"].elements["OrderPO"].value = "";
        document.forms["form1"].elements["TypeId"].value = "";
        document.forms["form1"].elements["cx"].value = "";
        document.form1.submit();
        break;
        case 'OrderPO':
        document.forms["form1"].elements["TypeId"].value = "";
        document.forms["form1"].elements["cx"].value = "";
        document.form1.submit();
        break;
        case 'TypeId':
        document.forms["form1"].elements["cx"].value = "";
        document.form1.submit();
        break;
        default:
        document.forms["form1"].elements["cx"].value = "cx";
        document.form1.submit();
        break;
    }
}

    // 显示设置时间对话框
    function showSetTimeFrame(e){
        openWinDialog(e,"yw_order_settime.php",405,300,'bottom');
    }

  // 批量设置时间
  function batchSetTime() {
      jQuery('.response').show();
      var tempTime = jQuery('#setTime').val();
      jQuery.ajax({
          url: 'yw_order_do_settime.php',
          data: {
              tempTime: tempTime
          },
          type: 'post',
          dataType: 'json'
      });
      window.location.reload();


  }

  function QRCode(e) {
    var choosedRow = 0;
    var Ids;

    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
            if (choosedRow == 1) {
                Ids = jQuery(this).val();
            } else {
                Ids = Ids + "|" + jQuery(this).val();
            }
        }
    });


    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    // 批量生成二维码
    jQuery.ajax({
        url: 'yw_order_QRCode.php',
        type: 'post',
        data: {
            Ids : Ids
        },
        async: false,
        dataType: 'json'

    });

    var Forshort = jQuery('#CompanyId option:selected').html();


    document.form1.action = "yw_order_qrcode_excel.php?Ids=" + Ids+"&Forshort="+Forshort;
    document.form1.target = "download";
    document.form1.submit();

}


function Transfer(e) {
    var choosedRow = 0;
    var Ids;

    jQuery('input[name^="checkid"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
            if (choosedRow == 1) {
                Ids = jQuery(this).val();
            } else {
                Ids = Ids + "|" + jQuery(this).val();
            }
        }
    });


    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }
            // 批量生成二维码
            jQuery.ajax({
                url: 'yw_order_QRCode.php',
                type: 'post',
                data: {
                    Ids: Ids
                },
                async: false,
                dataType: 'json'

            });

            var Forshort = jQuery('#CompanyId option:selected').html();


            document.form1.action = "yw_order_flow_excel.php?Ids=" + Ids + "&Forshort=" + Forshort;
            document.form1.target = "download";
            document.form1.submit();

        }

    </script>