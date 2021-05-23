<?php
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col = "选项|40|序号|40|分类|90|配件Id|45|配件名称|280|含税价|60|成本价|60|单位|40|可用库存|60|实物库存|60|默认供应商|100|送货</br>楼层|40|采购|50|规格|30|备注|30|状态|40|更新日期|70|操作|50";
$ColsNumber = 14;
$tableMenuS = 800;
$Page_Size = 20;                            //每页默认记录数量
$isPage = 1;//是否分页
//已传入参数：目的查询页面，来源页面，可选记录数，动作，类别uType

switch ($fSearchPage) {
    case"cg_cgdsheet"://添加特采单,过滤采购
        if ($Bid != "") {
            $Parameter .= ",Bid,$Bid";
            $sSearch .= " AND B.BuyerId='$Bid'";
        }
        break;
    case "processbom":
        if ($ProductId != "") $sSearch .= " AND A.ProductId='$ProductId' ";
        $sSearch .= " AND T.mainType='" . $APP_CONFIG['PANDS_FINAL_MAINTYPE'] . "'  AND S.StuffId NOT IN (SELECT StuffId FROM  $DataIn.process_bom GROUP BY StuffId)";
        break;

    case "stuffcombox_pand": //来自于子母配件BOM
        if ($Action == 3) {//选择子配件
            $sSearch .= " AND PA.Property='10' AND S.StuffId NOT IN (SELECT DISTINCT StuffId  FROM $DataIn.stuffcombox_bom ) ";
        } else if ($Action == 6) { //选择母配件
            $sSearch .= " AND PA.Property='9' AND S.StuffId NOT IN (SELECT DISTINCT mStuffId  FROM $DataIn.stuffcombox_bom )";
        }
        break;
    case "clientorder":
        $sSearch .= " AND S.Estate='1' ";
        break;
    case "semifinishedbom"://选择半成品配件
        if ($Action == 6) {// 只显示半成品
            $sSearch .= " AND T.mainType='" . $APP_CONFIG['SEMI_MAINTYPE'] . "'  AND NOT EXISTS (SELECT E.mStuffId  FROM $DataIn.semifinished_bom E WHERE E.mStuffId=S.StuffId ) ";
        } else if ($Action == 3) {
                $sSearch .= " AND S.StuffId != '$mStuffId'";
                //分类
            $TypeRows = "";
            $result = mysql_query("SELECT T.TypeId,T.TypeName,T.Letter 
	         FROM $DataIn.stuffdata D  
             LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
             WHERE D.Estate>0 AND D.TypeId IN ('9021','9019','9018','9013','9009','9006','9002') GROUP BY T.TypeId ",$link_id);
            if($myrow = mysql_fetch_array($result)){
                $TypeRows .= "<select name='StuffType' id='StuffType' onchange='ResetPage(this.name)'><option value='' selected>--全部--</option>";
                $NameRule="";
                do{
                    $theTypeId=$myrow["TypeId"];
                    $TypeName=$myrow["Letter"]."-".$myrow["TypeName"];
                    if ($StuffType==$theTypeId){
                        $TypeRows .=  "<option value='$theTypeId' selected >$TypeName</option>";
                        $SearchRows .=" AND S.TypeId='$theTypeId' ";
                    }
                    else{
                        $TypeRows .=  "<option value='$theTypeId'  >$TypeName</option>";
                    }
                }while ($myrow = mysql_fetch_array($result));
                $TypeRows .= "</select>&nbsp;";
            }
        } else if ($Action == 1) {

            $sSearch .= " AND T.mainType='" . $APP_CONFIG['SEMI_MAINTYPE'] . "'";
        }
        break;
    case "stuffchange2":
        if ($fromAction == 1) {
            $sSearch .= "  AND T.TypeId = 7100 ";
        }
        break;
    case "semistuffchange2":
        if ($fromAction == 1) {
            $sSearch .= "  AND T.TypeId != 7100 and T.mainType=3 ";
        }
        break;

    default:
        $sSearch .= $uType == "" ? "" : " AND S.TypeId=$uType";
        $sSearch .= "AND (PA.Property !=10  OR   PA.Property IS NULL)";
        break;
}

$Parameter .= ",mStuffId,$mStuffId";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo $TypeRows;
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//快速搜索 add by ckt 2017-12-14
$From = $From == "" ? "s1" : $From;
echo "<input name='From' type='hidden' id='From' value='$From'>";
$searchtable = array(
    0 => array(
        'name' => '分类',
        'table' => 'T',
        'field' => 'TypeName'
    ),
    1 => array(
        'name' => '配件ID',
        'table' => 'S',
        'field' => 'StuffId'
    ),
    2 => array(
        'name' => '配件名称',
        'table' => 'S',
        'field' => 'StuffCname'
    ),
    3 => array(
        'name' => '供应商',
        'table' => 'P',
        'field' => 'Forshort'
    )
);
//多重查询开关
$multUd = false;
//include "../model/subprogram/QuickSearch.php";
if ($FromSearch == "FromSearch") {  //来自快速搜索
    $SearchRows = '';
    foreach ($searchtable as $key => $value) {
        $RName = 'search' . (string)$key;
        $searchValue = $$RName;
        if ($searchValue != '') {
            $TAsName = $value['table'];
            $TField = $value['field'];
            $SearchRows .= "  AND $TAsName.$TField like '%$searchValue%'  ";
        }
    }
}
//
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 1);
switch ($fSearchPage) {

    case "pands":
        $mySql = "SELECT S.Id,S.StuffId,S.StuffCname,S.Picture,S.Estate,S.Price,S.CostPrice,M.Number,P.CompanyId,P.Forshort,
    P.Currency,M.Name,S.Spec,S.Remark,
    S.SendFloor,S.Date,S.Operator,S.Locks,T.TypeName,U.Name AS Unit
	FROM $DataIn.stuffdata S
	LEFT JOIN $DataIn.stuffunit U ON U.Id=S.Unit
	LEFt JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
	WHERE 1 $SearchRows AND  S.Estate>0  $sSearch order by Id DESC";
        break;
    case "semifinishedbom" :
        if ($Action == 6) {
            $mySql = "SELECT A.mStuffId, S.Id, S.StuffId, S.StuffCname, S.Picture , S.Estate, S.Price, S.CostPrice, S.Spec, 
    S.Remark , S.SendFloor, S.Date, S.Operator, S.Locks, U.NAME AS Unit , S.TypeId ,T.TypeName ,TT.Currency  
    FROM $DataIn.semifinished_bom A
	LEFT JOIN $DataIn.stuffdata S ON S.StuffId = A.mStuffId
	LEFT JOIN $DataIn.stuffcostprice C ON C.StuffId = A.mStuffId
	LEFT JOIN $DataIn.stuffunit U ON U.Id = S.Unit
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
	LEFT JOIN $DataIn.semifinished_deliverydate V ON V.mStuffId = A.mStuffId
	LEFT JOIN $DataIn.pands P ON S.StuffId = P.StuffId
	LEFT JOIN $DataIn.productdata PD ON P.ProductId = PD.ProductId 
	LEFT JOIN $DataIn.trade_object TT ON TT.CompanyId=PD.CompanyId
    WHERE 1 AND  S.StuffId > 0 
    GROUP BY A.mStuffId ORDER BY S.Id DESC";
        } else if ($Action == 3) {
            $mySql = "SELECT S.Id,S.StuffId,S.StuffCname,S.Picture,S.Estate,S.Price,S.CostPrice,M.Number,P.CompanyId,P.Forshort,
    P.Currency,M.Name,S.Spec,S.Remark,S.SendFloor,S.Date,S.Operator,S.Locks,T.TypeName,
    U.Name AS Unit,R.Picture as RPicture,R.Estate AS REstate,R.IsType,L.StuffId as RStuffId,S.TypeId
	FROM $DataIn.stuffdata S
	LEFT JOIN $DataIn.stuffunit U ON U.Id=S.Unit
	LEFt JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 	
	LEFT JOIN $DataIn.stuffreach R ON R.TypeId=T.TypeId
	LEFT JOIN $DataIn.stuffreachlink L ON  L.StuffId=S.StuffId	
    LEFT JOIN  $DataIn.stuffproperty  PA ON PA.StuffId=S.StuffId 
	WHERE 1 $SearchRows AND S.TypeId != '9017' AND  S.Estate>0 $sSearch GROUP BY S.StuffId order by Id DESC";
        } else{
            $mySql = "SELECT S.Id,S.StuffId,S.StuffCname,S.Picture,S.Estate,S.Price,S.CostPrice,M.Number,P.CompanyId,P.Forshort,
    P.Currency,M.Name,S.Spec,S.Remark,S.SendFloor,S.Date,S.Operator,S.Locks,T.TypeName,
    U.Name AS Unit,R.Picture as RPicture,R.Estate AS REstate,R.IsType,L.StuffId as RStuffId,S.TypeId
	FROM $DataIn.stuffdata S
	LEFT JOIN $DataIn.stuffunit U ON U.Id=S.Unit
	LEFt JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 	
	LEFT JOIN $DataIn.stuffreach R ON R.TypeId=T.TypeId
	LEFT JOIN $DataIn.stuffreachlink L ON  L.StuffId=S.StuffId	
    LEFT JOIN  $DataIn.stuffproperty  PA ON PA.StuffId=S.StuffId 
	WHERE 1 $SearchRows AND  S.Estate>0 $sSearch GROUP BY S.StuffId order by Id DESC";
        }
        break;
    default:
        $mySql = "SELECT S.Id,S.StuffId,S.StuffCname,S.Picture,S.Estate,S.Price,S.CostPrice,M.Number,P.CompanyId,P.Forshort,
    P.Currency,M.Name,S.Spec,S.Remark,S.SendFloor,S.Date,S.Operator,S.Locks,T.TypeName,
    U.Name AS Unit,R.Picture as RPicture,R.Estate AS REstate,R.IsType,L.StuffId as RStuffId,S.TypeId
	FROM $DataIn.stuffdata S
	LEFT JOIN $DataIn.stuffunit U ON U.Id=S.Unit
	LEFt JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 	
	LEFT JOIN $DataIn.stuffreach R ON R.TypeId=T.TypeId
	LEFT JOIN $DataIn.stuffreachlink L ON  L.StuffId=S.StuffId	
    LEFT JOIN  $DataIn.stuffproperty  PA ON PA.StuffId=S.StuffId 
	WHERE 1 $SearchRows AND  S.Estate>0 $sSearch GROUP BY S.StuffId order by Id DESC";//记录可用中
        break;
}
//echo $mySql;
if ($FromSearch == "FromSearch") {//来自快速搜索，页码归一 by ckt 2017-12-15
    $myResult = mysql_query($mySql . " limit 0,$Page_Size", $link_id);
} else {
    $myResult = mysql_query($mySql . " $PageSTR", $link_id);
}
if ($myRow = mysql_fetch_array($myResult)) {
    do {
        $m = 1;
        $Id = $myRow["Id"];
        $StuffId = $myRow["StuffId"];
        $Unit = $myRow["Unit"];
        $StuffCname = $myRow["StuffCname"];
        $TypeId = $myRow["TypeId"];
        $TypeName = $myRow["TypeName"];
        $Forshort = $myRow["Forshort"] == "" ? "&nbsp;" : $myRow["Forshort"];
        $Buyer = $myRow["Name"] == "" ? "&nbsp;" : $myRow["Name"];
        $Price = $myRow["Price"];
        $CostPrice = $myRow["CostPrice"];
        $checkStock = mysql_fetch_array(mysql_query("SELECT oStockQty,tStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' LIMIT 1", $link_id));

        $oStockQty = $checkStock["oStockQty"] == "" ? 0 : $checkStock["oStockQty"];
        $tStockQty = $checkStock["tStockQty"] == "" ? 0 : $checkStock["tStockQty"];
        switch ($Action) {
            case "2"://多配件操作,如清除BOM配件
                $Bdata = $StuffId;
                break;
            case "3"://产品配件关系设定
                $Currency = $myRow["Currency"];
                $checkRate = mysql_fetch_array(mysql_query("SELECT Rate FROM $DataPublic.currencydata WHERE Id='$Currency'", $link_id));
                $Rate = $checkRate["Rate"] == "" ? 1 : $checkRate["Rate"];
                $Amount = sprintf("%.4f", $Price * $Rate);
                $CostAmount = sprintf("%.4f", $CostPrice * $Rate);
                $Bdata = $TypeName . "^^" . $StuffId . "^^" . $StuffCname . "^^" . $Buyer . "^^" . $Forshort . "^^" . $Amount . "^^" . $Currency . "^^" . $CostAmount;
                break;
            case "4"://需求单配件置换
                $Number = $myRow["Number"];
                $CompanyId = $myRow["CompanyId"];
                $Bdata = $StuffId . "^^" . $StuffCname . "^^" . $Price . "^^" . $Number . "^^" . $Buyer . "^^" . $CompanyId . "^^" . $Forshort;
                break;
            case "5":
                $CompanyId = $myRow["CompanyId"];
                $Bdata = $StuffId . "^^" . $StuffCname . "^^" . $Price . "^^" . $CompanyId . "^^" . $Forshort;
                break;
            case "1":
            case "6":
                $Bdata = $StuffId . "^^" . $StuffCname;
                break;
            case "7":
                $Bdata = $StuffId . "^^" . $StuffCname . "^^" . $Price . "^^" . $oStockQty . "^^" . $Buyer . "^^" . $Forshort;
                break;
            case "8"://报废
                $Bdata = $StuffId . "^^" . $StuffCname . "^^" . $oStockQty;
                break;
            case "9"://工序BOM
                $Bdata = $StuffId . "^^" . $StuffCname . "^^" . $TypeId;
                break;
            case "12"://配件订单
                $Bdata = $StuffId . "^^" . $StuffCname . "^^" . $Price;
                break;
        }
        $Spec = $myRow["Spec"] == "" ? "&nbsp;" : "<img src='../images/remark.gif' alt='$myRow[Spec]' width='18' height='18'>";
        $Remark = $myRow["Remark"] == "" ? "&nbsp;" : "<img src='../images/remark.gif' alt='$myRow[Remark]' width='18' height='18'>";

        $Picture = $myRow["Picture"];
        if ($Picture == 1) {
            $Picture = $StuffId . ".jpg";
            $File = anmaIn($Picture, $SinkOrder, $motherSTR);
            $Dir = "stufffile";
            $Dir = anmaIn($Dir, $SinkOrder, $motherSTR);
            $Picture = "<span onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;color:#FF6633'>View</span>";
        } else {
            $Picture = "&nbsp";
        }
        $Estate = $myRow["Estate"];
        //echo "Estate:$Estate";
        switch ($Estate) {
            case 0:
                $Estate = "<div class='redB'>×</div>";
                break;
            case 1:
                $Estate = "<div class='greenB'>√</div>";
                break;
            case 2://配件名称审核中
                $Estate = "<div class='yellowB' title='配件名称审核中'>√.</div>";
                break;
        }
        $SendFloor = $myRow["SendFloor"];
        include "../model/subprogram/stuff_GetFloor.php";
        $SendFloor = $SendFloor = "" ? "&nbsp" : $SendFloor;
        $StuffCname = $myRow["StuffCname"];
        //$Estate=$myRow["Estate"]==1?"<div class='greenB'>√</div>":"<div class='redB'>×</div>";
        $Date = substr($myRow["Date"], 0, 10);
        $Operator = $myRow["Operator"];
        include "../model/subprogram/staffname.php";
        include "../model/subprogram/stuff_Property.php";//配件属性
        $Locks = 1;
        $LockRemark = "";
        if ($Action == 8) {
            if ($oStockQty == 0 || $tStockQty == 0) {
                $LockRemark = "库存不足";
            }
        }
        $oStockQty = $oStockQty == 0 ? "&nbsp;" : $oStockQty;
        $tStockQty = $tStockQty == 0 ? "&nbsp;" : $tStockQty;
        $ValueArray = array(
            array(0 => $TypeName . "-" . $Locks,
                1 => "align='center'"),
            array(0 => $StuffId,
                1 => "align='center'"),
            array(0 => $StuffCname),
            array(0 => $CostPrice,
                1 => "align='center'"),
            array(0 => $Price,
                1 => "align='center'"),
            array(0 => $Unit,
                1 => "align='center'"),
            array(0 => $oStockQty,
                1 => "align='center'"),
            array(0 => $tStockQty,
                1 => "align='center'"),
            array(0 => $Forshort),
            array(0 => $SendFloor, 1 => "align='center'"),
            array(0 => $Buyer,
                1 => "align='center'"),
            array(0 => $Spec),
            array(0 => $Remark,
                1 => "align='center'"),
            array(0 => $Estate,
                1 => "align='center'"),
            array(0 => $Date,
                1 => "align='center'"),
            array(0 => $Operator,
                1 => "align='center'")
        );
        $checkidValue = $Bdata;
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