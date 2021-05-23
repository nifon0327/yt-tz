<?php
//MC代码不一样，BOM回传参数加入成本和货币ID$DataIn.电信---yang 20120801
//步骤1
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col = "选项|40|序号|40|分类|60|配件Id|45|配件名称|150|数量|150|参考买价|60|单位|40|QC图|50|认证内容|50|在库|60|可用库存|60|默认供应商|100|送货</br>楼层|40|采购|50|规格|30|备注|30|状态|40|更新日期|70|操作|50";
$ColsNumber = 16;
$tableMenuS = 800;
$Page_Size = 20;                            //每页默认记录数量
$isPage = 1;//是否分页
$buyerId = $_REQUEST['buyerId'];
//已传入参数：目的查询页面，来源页面，可选记录数，动作，类别uType

switch ($fSearchPage) {
    case"cg_cgdsheet"://添加特采单,过滤采购
        if ($Bid != "") {
            $Parameter .= ",Bid,$Bid";
            $sSearch .= " AND B.BuyerId = $buyerId";
        }
        break;
    case "processbom":
        if ($ProductId != "") $sSearch .= "AND A.ProductId='$ProductId' ";
        break;
    case "stuffcombox_pand": //来自于子母配件BOM
        if ($Action == 3) {//选择子配件
            $sSearch .= "AND PA.Property='10' ";
        } else if ($Action == 6) { //选择母配件
            $sSearch .= "AND PA.Property='9' ";
        }
        break;
    case "stuff_die":
        $sSearch .= "  AND S.StuffId NOT IN (SELECT StuffId FROM cut_die)";
        break;

    case "ck_bp":
        $sSearch .= "  AND IFNULL(PA.Property,0)!='9' ";
        break;
    case "ck_bf":
        $sSearch .= "  AND IFNULL(PA.Property,0)!='9' AND K.oStockQty>0 AND K.tStockQty>0 ";
        break;

    case "pands":
        // 分类
        $TypeList = '';
        $mysql = "SELECT T.TypeId,T.TypeName,T.Letter 
	FROM $DataIn.stuffdata S
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
	WHERE 1 AND S.Estate=1 $sSearch and S.TypeId IN ('9017','9013','9011') group by S.TypeId";
        $res = mysql_query($mysql);
        if ($ret = mysql_fetch_array($res)) {
            $TypeList = "&nbsp;&nbsp;<select name='StuffType' id='StuffType'  onChange='ResetPage(this.name)' style='color: #949598;background-color: #F5F5F5; font: 12px 思源雅黑;' >";
            do {
                $TypeName = $ret["TypeName"];
                $theTypeId = $ret["TypeId"];
                $StuffType = $StuffType == "" ? $theTypeId : $StuffType;
                if ($StuffType == $theTypeId) {
                    $TypeList .= "<option value='$theTypeId' selected>$TypeName</option>";
                    $sSearch .= " AND S.TypeId='$theTypeId' ";
                } else {
                    $TypeList .= "<option value='$theTypeId'>$TypeName</option>";
                }
            } while ($ret = mysql_fetch_array($res));
            $TypeList .= "</select>";

        }
        if ($sokeyword) {
            $sSearch .= " AND S.StuffCname LIKE '%$sokeyword%' ";
        }
        break;
    default:
        $sSearch .= $uType == "" ? "" : " AND S.TypeId=$uType";
        break;
}
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
echo $TypeList;
echo "&nbsp;&nbsp;<select name='Pagination' id='Pagination' onchange='CencelPage()' style='border: 0;
    color: #949598;
    background-color: #F5F5F5;
    font:12px 思源雅黑;'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
//模糊查询
echo "<input name='sokeyword' type='text' id='sokeyword' value='' placeholder='请输入查询配件' /><span class='ButtonH_25' onclick='sokeyword()'>查询</span>";
echo $CencalSstr;
if ($bom == 1) {
    $joinsql = ' RIGHT JOIN (SELECT MaterNo,sum(Quantity+Loss) as total FROM bom_info GROUP BY MaterNo) R ON R.MaterNo = S.StuffId';
} elseif ($bom == 0) {
    $joinsql = ' RIGHT JOIN (SELECT MaterNo,sum(Quantity+Loss) as total FROM bom_info GROUP BY MaterNo) R ON R.MaterNo <> S.StuffId';
} else {
    $joinsql = '';
}
//快速搜索 add by ckt 2017-12-15
if ($fSearchPage == 'pands') {
    $From = $From == "" ? "s1" : $From;
    echo "<input name='From' type='hidden' id='From' value='$From'>";
    $searchtable = array(
        0 => array(
            'name' => '分类',
            'table' => 'T',
            'field' => 'TypeName',
        ),
        1 => array(
            'name' => '配件ID',
            'table' => 'S',
            'field' => 'StuffId',
        ),
        2 => array(
            'name' => '配件名称',
            'table' => 'S',
            'field' => 'StuffCname',
        ),
        3 => array(
            'name' => '供应商',
            'table' => 'P',
            'field' => 'Forshort',
        ),
    );
    //多重查询开关
    $multUd = false;
//    include "../model/subprogram/QuickSearch.php";
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
}

if ($Type == "钢筋半成品") {
    $idSql = "SELECT Id FROM trade_object WHERE CompanyId = $xmId";
    $IdRow = mysql_fetch_array(mysql_query($idSql, $link_id));
    $TradeId = $IdRow["Id"];
    $sSearch .= " AND S.Spec LIKE '%-$TradeId' ";
}
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 1, 1);
switch ($fSearchPage) {
    case "processbom":
        $mySql = "SELECT  S.Id,S.StuffId,S.StuffCname,S.Picture,S.Estate,S.Price,M.Number,P.CompanyId,P.Forshort,
   P.Currency,M.Name,S.Spec,S.Remark,S.SendFloor,S.Date,S.Operator,S.Locks,T.TypeName,U.Name AS Unit
	,R.Picture as RPicture,R.Estate AS REstate,R.IsType,L.StuffId as RStuffId
	FROM $DataIn.pands A 
    LEFT JOIN $DataIn.stuffdata S ON S.StuffId=A.StuffId 
	LEFT JOIN $DataIn.stuffunit U ON U.Id=S.Unit 
	LEFt JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId 
	LEFT JOIN $DataIn.stuffreach R ON R.TypeId=T.TypeId
	LEFT JOIN $DataIn.stuffreachlink L ON  L.StuffId=S.StuffId	
	WHERE 1 AND S.Estate=1 $sSearch GROUP BY S.StuffId  order by Id DESC";
        break;
    case "pands":
        $mySql = "SELECT S.Id,S.StuffId,S.StuffCname,S.Picture,S.Estate,S.Price,M.Number,P.CompanyId,P.Forshort,
    P.Currency,M.Name,S.Spec,S.Remark,S.SendFloor,S.Date,S.Operator,S.Locks,T.TypeName,U.Name AS Unit,T.mainType,
	R.Picture as RPicture,R.Estate AS REstate,R.IsType,L.StuffId as RStuffId
	FROM $DataIn.stuffdata S
	LEFT JOIN $DataIn.stuffunit U ON U.Id=S.Unit
	LEFt JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
	LEFT JOIN $DataIn.stuffreach R ON R.TypeId=T.TypeId
	LEFT JOIN $DataIn.stuffreachlink L ON  L.StuffId=S.StuffId	
	WHERE 1 $SearchRows  $sSearch  and not exists(SELECT PA.StuffId FROM $DataIn.stuffproperty PA WHERE PA.Property='10' AND PA.StuffId=S.StuffId ) GROUP BY S.StuffId order by Id DESC";
        break;
    default:
        $mySql = "SELECT S.Id,S.StuffId,S.StuffCname,S.Picture,S.Estate,S.Price,M.Number,P.CompanyId,P.Forshort,P.Currency,M.Name,S.Spec,
S.Remark,S.SendFloor,S.Date,S.Operator,S.Locks,T.TypeName,U.Name AS Unit,
	R.Picture as RPicture,R.Estate AS REstate,R.IsType,L.StuffId as RStuffId,S.TypeId,I.total
	FROM $DataIn.stuffdata S
	LEFT JOIN $DataIn.stuffunit U ON U.Id=S.Unit
	LEFt JOIN $DataIn.bps B ON B.StuffId=S.StuffId
	LEFT JOIN $DataIn.staffmain M ON M.Number=B.BuyerId
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId
	LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = S.StuffId
	LEFT JOIN $DataIn.stufftype T ON T.TypeId=S.TypeId
	LEFT JOIN $DataIn.stuffreach R ON R.TypeId=T.TypeId
	LEFT JOIN $DataIn.stuffreachlink L ON  L.StuffId=S.StuffId
    LEFT JOIN $DataIn.stuffproperty  PA ON PA.StuffId=S.StuffId
    LEFT JOIN (SELECT MaterNo,sum(Quantity+Loss) as total FROM bom_info GROUP BY MaterNo) I ON I.MaterNo = S.StuffId
	WHERE 1 AND S.Estate>=1 $sSearch GROUP BY S.StuffId order by S.StuffId desc";//记录可用中
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
        $LockRemark = "";
        $Id = $myRow["Id"];
        $StuffId = $myRow["StuffId"];
        $total = $myRow["total"];
        $Unit = $myRow["Unit"];
        $StuffCname = $myRow["StuffCname"];
        $Forshort = $myRow["Forshort"];
        $TypeName = $myRow["TypeName"];
        $Buyer = $myRow["Name"];
        $Price = $myRow["Price"];
        $TypeId = $myRow["TypeId"];

        switch ($Action) {
            case "2"://多配件操作,如清除BOM配件
                $Bdata = $StuffId;
                break;
            case "3"://产品配件关系设定
                $Currency = $myRow["Currency"];
                $mainType = $myRow["mainType"];
                $checkRate = mysql_fetch_array(mysql_query("SELECT Rate FROM $DataPublic.currencydata WHERE Id='$Currency'", $link_id));
                $Rate = $checkRate["Rate"] == "" ? 1 : $Rate;
                $Amount = sprintf("%.4f", $Price * $Rate);
                $Bdata = $TypeName . "^^" . $StuffId . "^^" . $StuffCname . "^^" . $Buyer . "^^" . $Forshort . "^^" . $Amount . "^^" . $Currency . "^^" . $mainType;
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
            case "6"://选择配件以便进行操作:其它操作
                $Bdata = $StuffId . "^^" . $StuffCname;

                break;
            case "7"://客户订单》配件需求单异动
                $checkStock = mysql_query("SELECT oStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' LIMIT 1", $link_id);
                $oStockQty = mysql_result($checkStock, 0, "oStockQty");
                $oStockQty = $oStockQty == "" ? 0 : $oStockQty;
                $Bdata = $StuffId . "^^" . $StuffCname . "^^" . $Price . "^^" . $oStockQty . "^^" . $Buyer . "^^" . $Forshort;
                break;
            case "8"://报废
                $checkStock = mysql_query("SELECT oStockQty,tStockQty FROM $DataIn.ck9_stocksheet WHERE StuffId='$StuffId' LIMIT 1", $link_id);
                $oStockQty = mysql_result($checkStock, 0, "oStockQty");
                $oStockQty = $oStockQty == "" ? 0 : $oStockQty;
                $tStockQty = mysql_result($checkStock, 0, "tStockQty");
                $tStockQty = $tStockQty == "" ? 0 : $oStockQty;
                $Bdata = $StuffId . "^^" . $StuffCname . "^^" . $oStockQty;
                break;
        }
        $Spec = $myRow["Spec"] == "" ? "&nbsp;" : "<img src='../images/remark.gif' title='$myRow[Spec]' width='18' height='18'>";
        $Remark = $myRow["Remark"] == "" ? "&nbsp;" : "<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";

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
        switch ($Estate) {
            case 0:
                $Estate = "<div class='redB'>×</div>";
                $LockRemark = "配件已禁用,强制锁定!";
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

        $IsType = $myRow["IsType"];
        $FileName = $myRow["RPicture"];
        $RStuffId = $myRow["RStuffId"];
        if (($RStuffId != "" && $FileName != "") || $IsType == 1) {
            $REstate = $myRow["REstate"];
            if ($REstate == 2) {
                $REstateSTR = "审核中";
                $ClassColor = "blueB";
            } else {
                $REstateSTR = "查阅";
                $ClassColor = "yellowB";
            }
            $File = anmaIn($FileName, $SinkOrder, $motherSTR);
            $Dir = download . "/stuffreach/";
            $Dir = anmaIn($Dir, $SinkOrder, $motherSTR);
            //$Picture="<span onClick='OpenOrLoad(\"$Dir\",\"$File\")' style='CURSOR: pointer;' class='$ClassColor'>$EstateSTR</span>";
            $RPicture = "<a href=\"openorload.php?d=$Dir&f=$File&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>$REstateSTR</a>";
        } else {
            $RPicture = "&nbsp;";
        }

        //配件QC检验标准图
        $QCImage = "";
        include "../model/subprogram/stuffimg_qcfile.php";
        include "../model/subprogram/stuff_Property.php";//配件属性
        $QCImage = $QCImage == "" ? "&nbsp;" : $QCImage;
        $Date = substr($myRow["Date"], 0, 10);
        $Operator = $myRow["Operator"];
        include "../model/subprogram/staffname.php";
        $Locks = 1;
        if ($Action == 8) {
            if ($oStockQty == 0 || $tStockQty == 0) {
                $LockRemark = "库存不足";
            }
        }
        $ValueArray = array(
            array(0 => $TypeName . "-" . $Locks, 1 => "align='center'"),
            array(0 => $StuffId, 1 => "align='center'"),
            array(0 => $StuffCname),
            array(0 => $total, 1 => "align='center'"),
            array(0 => $Price, 1 => "align='center'"),
            array(0 => $Unit, 1 => "align='center'"),
            array(0 => $QCImage, 1 => "align='center'"),
            array(0 => $RPicture, 1 => "align='center'"),
            array(0 => $tStockQty, 1 => "align='center'"),
            array(0 => $oStockQty, 1 => "align='center'"),
            array(0 => $Forshort),
            array(0 => $SendFloor, 1 => "align='center'"),
            array(0 => $Buyer, 1 => "align='center'"),
            array(0 => $Spec),
            array(0 => $Remark, 1 => "align='center'"),
            array(0 => $Estate, 1 => "align='center'"),
            array(0 => $Date, 1 => "align='center'"),
            array(0 => $Operator, 1 => "align='center'"),
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
<script type="text/javascript">
    jQuery(".sokeyword").keypress(function (e) {
        if (e.which == 13) {

            document.form1.submit();
        }
    });

    function sokeyword() {
        document.form1.submit();
    }
</script>
