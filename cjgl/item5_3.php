<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>
<?php
include '../basic/loading.php';
//$kWorkShopId = $kWorkShopId ==""?"102":$kWorkShopId;
$SearchRows = " AND SC.scFrom >0 AND SC.Estate>0 AND SC.Qty>SC.ScQty";//生产分类里的ID

/*
$WorkShopResult= mysql_query("SELECT SC.WorkShopId,W.Name AS WorkShopName
FROM  $DataIn.yw1_scsheet SC
INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
WHERE 1 $SearchRows GROUP BY SC.WorkShopId  order by SC.WorkShopId DESC ",$link_id);
*/


//    $WorkShopList = "";
//    $WorkShopResult = mysql_query("SELECT ActionId,`Name`
//FROM  $DataIn.workorderaction W  order by ActionId desc ", $link_id);
//
//    if ($WorkShopRow = mysql_fetch_array($WorkShopResult)) {
//        $WorkShopList .= "<select name='kWorkShopId' id='kWorkShopId' style = 'width:100px' onChange='ResetPage(1,5)'>";
//        $i = 1;
//        do {
//            $theWorkShopId = $WorkShopRow["ActionId"];
//            $theWorkShopName = $WorkShopRow["Name"];
//            $kWorkShopId = $kWorkShopId == "" ? $theWorkShopId : $kWorkShopId;
//            if($theWorkShopName == '混凝土搅拌')break;
//            if ($kWorkShopId == $theWorkShopId) {
//                $WorkShopList .= "<option value='$theWorkShopId' selected>$theWorkShopName</option>";
//                $SearchRows .= " AND SC.ActionId='$theWorkShopId'";
//            } else {
//                $WorkShopList .= "<option value='$theWorkShopId'>$theWorkShopName</option>";
//            }
//            $i++;
//        } while ($WorkShopRow = mysql_fetch_array($WorkShopResult));
//        $WorkShopList .= "</select>";
//    }


$WorkShopList = "";
$WorkShopList .= "<select name='kWorkShopId' id='kWorkShopId' style = 'width:100px' onChange='ResetPage(1,5)'>";
$WorkShopResult = mysql_query("SELECT ActionId FROM  $DataIn.workorderaction W ORDER BY ActionId DESC", $link_id);
if ($WorkShopRow = mysql_fetch_array($WorkShopResult)) {
    do {
        $theWorkShopId = $WorkShopRow["ActionId"];
        if ($theWorkShopId == 106) {
          continue;
        }
        $kWorkShopId = $kWorkShopId == "" ? $theWorkShopId : $kWorkShopId;
        if ($kWorkShopId == $theWorkShopId) {

            $SearchRows .= " AND SC.ActionId='$theWorkShopId'";

        }
    } while ($WorkShopRow = mysql_fetch_array($WorkShopResult));
    $a = $kWorkShopId == '103' ? 'selected' : '';
    $b = $kWorkShopId == '104' ? 'selected' : '';
    $c = $kWorkShopId == '106' ? 'selected' : '';
    $d = $kWorkShopId == '101' ? 'selected' : '';
    //$WorkShopList .= "<option value='103' $a>钢筋下料</option>";
    $WorkShopList .= "<option value='104' $b>骨架搭建</option>";

    //$WorkShopList .= "<option value='106' $c>浇捣养护</option>";

    $WorkShopList .= "<option value='101' $d>脱模入库</option>";


    $WorkShopList .= "</select>";
}


//增加客户下拉筛选
$ForshortList = "";
$ForshortResult = mysql_query("SELECT
	O.Forshort 
FROM
	$DataIn.yw1_scsheet SC
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId = SC.mStockId 
WHERE
	1 
	$SearchRows
GROUP BY
	O.ForShort", $link_id);
if ($ForshortRow = mysql_fetch_array($ForshortResult)) {
    $ForshortList .= "<select name='khCompanyId' id='khCompanyId' onchange='ResetPage(1,5)'>";
    //$ForshortList .= "<option value='all' selected>请选择客户</option>";

    do {
        $thisForshort = $ForshortRow["Forshort"];
        $khCompanyId = $khCompanyId == "" ? $thisForshort : $khCompanyId;
        if ($thisForshort == "") {

        }
        else if ($khCompanyId == $thisForshort) {
            $ForshortList .= "<option value='$thisForshort' selected>$thisForshort</option>";
            $SearchRows .= " and O.Forshort='$thisForshort' ";
        }
        else {
            $ForshortList .= "<option value='$thisForshort'>$thisForshort</option>";
        }
    } while ($ForshortRow = mysql_fetch_array($ForshortResult));
    $ForshortList .= "</select>&nbsp;";
}


//增加栋号下拉筛选
if ($ForshortList != "") {
    $BuildNoList = "";
    $BuildNoResult = mysql_query("
    SELECT M.BuildNo 
	FROM $DataIn.yw1_scsheet SC 
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId 
  LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
  LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId = SC.mStockId
	WHERE 1 $SearchRows GROUP BY M.BuildNo", $link_id);
    if ($BuildNoRow = mysql_fetch_array($BuildNoResult)) {
        $BuildNoList .= "<select name='BuildNo' id='BuildNo' onchange='ResetPage(1,5)'>";
        //$BuildNoList .= "<option value='all' selected>请选择栋号</option>";

        do {
            $thisBuildNo = $BuildNoRow["BuildNo"];
            $BuildNo = $BuildNo == "" ? $thisBuildNo : $BuildNo;
            if ($BuildNo == $thisBuildNo) {
                $BuildNoList .= "<option value='$thisBuildNo' selected>$thisBuildNo</option>";
                $SearchRows .= " and M.BuildNo='$thisBuildNo' ";
            }
            else {
                $BuildNoList .= "<option value='$thisBuildNo'>$thisBuildNo</option>";
            }
        } while ($BuildNoRow = mysql_fetch_array($BuildNoResult));
        $BuildNoList .= "</select>&nbsp;";
    }

//增加业务单号下拉筛选
    $OrderPOList = "";
    $clientResult = mysql_query("
        SELECT S.OrderPO
			FROM  $DataIn.yw1_scsheet  SC 
			LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=SC.POrderId
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
			LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
			LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId = SC.mStockId
      WHERE 1  $SearchRows and S.OrderPO is not null  GROUP BY S.OrderPO order by S.OrderPO
        ", $link_id);

    if ($clientRow = mysql_fetch_array($clientResult)) {
        $OrderPOList .= "<select name='OrderPO' id='OrderPO' onchange='ResetPage(1,5)'>";
        /*    $OrderPOList .= "<option value='' selected>请选择PO</option>";
            if ($OrderPO == "All"){
                $OrderPOList .= "<option value='All' selected>全部PO</option>";
            }else{
                $OrderPOList .= "<option value='All'>全部PO</option>";
            }*/
        do {
            $thisOrderPO = $clientRow["OrderPO"];
            // $OrderPO=$OrderPO==""?$thisOrderPO:$OrderPO;
            if ($OrderPO == "" || $OrderPO == null || !$OrderPO) {
                $OrderPOList .= "<option value='$thisOrderPO' selected>$thisOrderPO</option>";
                $SearchRows .= " and S.OrderPO='$thisOrderPO' ";
                $OrderPO = $thisOrderPO;
            }
            else if ($OrderPO == $thisOrderPO) {
                $OrderPOList .= "<option value='$thisOrderPO' selected>$thisOrderPO</option>";
                $SearchRows .= " and S.OrderPO='$thisOrderPO' ";
            }
            else {
                $OrderPOList .= "<option value='$thisOrderPO'>$thisOrderPO</option>";
            }
        } while ($clientRow = mysql_fetch_array($clientResult));
        $OrderPOList .= "</select>&nbsp;";
    }

//分类
$typeList = '';
$typeSql = mysql_query("SELECT DISTINCT
	P.TypeName, PD.TypeId
FROM
	$DataIn.cg1_stocksheet CG
	LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId = CG.POrderId
	LEFT JOIN $DataIn.yw1_scsheet SC ON S.POrderId=SC.POrderId
	INNER JOIN $DataIn.productdata PD ON PD.ProductId = S.ProductId
	INNER JOIN producttype P ON P.TypeId = PD.TypeId
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
  LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	LEFT JOIN $DataIn.stuffdata A ON A.StuffId = CG.StuffId
	LEFT JOIN $DataIn.stuffunit U ON U.Id = A.Unit
	LEFT JOIN $DataIn.stufftype T ON T.TypeId = A.TypeId
WHERE
	1
	$SearchRows
	AND CG.Mid = 0
	AND ( CG.FactualQty + CG.AddQty ) > 0", $link_id);
if ($typeRow = mysql_fetch_array($typeSql)) {
    $typeList .= "<select name='TypeId' id='TypeId' onchange='document.form1.submit();'>";
    $typeList .= "<option value='All' selected>全部类型</option>";
    do {
        $theTypeId = $typeRow["TypeId"];
        $TypeName = $typeRow["TypeName"];
        if ($TypeId == $theTypeId) {
            $typeList .= "<option value='$theTypeId' style= 'font-weight: bold' selected>$TypeName</option>";
            $SearchRows .= " AND PD.TypeId='$theTypeId'";
        }
        else {
            $typeList .= "<option value='$theTypeId' style= 'font-weight: bold'>$TypeName</option>";
        }
    } while ($typeRow = mysql_fetch_array($typeSql));
    $typeList .= "</select>&nbsp;";
}

// 流水线号
    $LineList = "";
    $WorkShopResult = mysql_query("
        SELECT  WA.Name AS workshopName
            FROM  $DataIn.yw1_scsheet  SC 
            LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=SC.POrderId
            LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = S.OrderNumber
            LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
            LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
            LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId = SC.mStockId
            INNER JOIN $DataIn.productdata PD ON PD.ProductId = S.ProductId
            LEFT  JOIN $DataIn.workshopdata WA ON WA.Id = SC.WorkShopId where 1 $SearchRows group by WA.Name
        ", $link_id);
    if ($WorkShopRow = mysql_fetch_array($WorkShopResult)) {
        $LineList .= "<select name='WorkShop' id='WorkShop' onchange='ResetPage(1,5)'>";
        do {
            $thisWorkShop = $WorkShopRow["workshopName"];
            $WorkShop=$WorkShop==""?$thisWorkShop:$WorkShop;
            if ($WorkShop == $thisWorkShop) {
                $LineList .= "<option value='$thisWorkShop' selected>$thisWorkShop</option>";
                $SearchRows .= " and WA.Name='$thisWorkShop' ";
            }
            else {
                $LineList .= "<option value='$thisWorkShop'>$thisWorkShop</option>";
            }
        } while ($WorkShopRow = mysql_fetch_array($WorkShopResult));
        $LineList .= "</select>&nbsp;";
    }

}

if ($kWorkShopId == '101') {
    include "item5_3_1.php"; //组装类
}
else {
    include "item5_3_2.php"; //半成品类的
}
unset($OrderPO);
?>

</form>
</body>
</html>
<script src='showkeyboard.js' type=text/javascript></script>
<script src='taskstyle.js' type=text/javascript></script>
<script src='../model/pagefun.js' type=text/javascript></script>
<script src='cj_function.js' type=text/javascript></script>
<script>
var keyboard = new KeyBoard();
var tasksboard = new TasksBoard();
var QtyArray = new Array();
var IdArray = new Array();
var eArray = new Array();
var eImg = "<img src='../images/register.png' width='30' height='30'>";

function showKeyboard(e, index, j, OrderQty, llQty, StockId, k, comboxSign) {
    var addQtyFun = function () {
        var checkId = "checkId" + index + j;
        var ListCheck = document.getElementById(checkId);
        var eStr = parseFloat(e.innerHTML);
        if (eStr >= 0) {
            ListCheck.checked = true;
        } else {
            ListCheck.checked = false;
        }
        addQty(e, StockId);
        if (comboxSign == 1) {
            addComboxQty(index, j, k, ListCheck.checked);
        }
    };
    keyboard.show(e, OrderQty, '<=', llQty, addQtyFun);
}

function validate(e) {
    var reg = new RegExp("^\\d+(\\.{0,1}\\d+){0,1}$");
    var obj = jQuery(e).html();
    var prepareReal = jQuery(e).parent().find('.prepareReal').html();
    if (obj == 0) {
        alert("备料不可为0");
        jQuery(e).html('');
    }
    else if (!reg.test(obj)) {
        alert("请输入有效的数字!");
        jQuery(e).html('');
    }
    else if (obj > (prepareReal * 1.1)) {
        alert("备料数量超限!");
        jQuery(e).html('');
    }
}

// 单选
function checkId(e, index, i, k, comboxSign) {
    var checkId = "checkId" + index + i;
    var tableId = "ListTable" + index;
    var ListTable = document.getElementById(tableId);
    var ListCheck = document.getElementById(checkId);
    i = i - 1;

    if (ListCheck.checked) {
        addQty(ListTable.rows[i].cells[10], ListCheck.value);
    }

    if (comboxSign == 1) {
        addComboxQty(index, i + 1, k, ListCheck.checked);
    }

}


function addComboxQty(index, i, k, flag, Qty) {

    var tableId = "ListTable" + index;
    var ListTable = document.getElementById(tableId);

    if (flag) {

        for (tempk = 1; tempk < k; tempk++) {
            var comboxblQty = document.getElementById("comboxblQty" + index + i + tempk);
            var comboxRelation = document.getElementById("comboxRelation" + index + i + tempk);
            var blQty = ListTable.rows[i - 1].cells[10].innerHTML;
            comboxblQty.innerHTML = parseFloat(blQty) * parseFloat(comboxRelation.innerHTML);
        }
    } else {
        for (tempk = 1; tempk < k; tempk++) {
            var comboxblQty = document.getElementById("comboxblQty" + index + i + tempk);
            comboxblQty.innerHTML = "";
        }
    }
}


function addQty(e, StockId) {
    var eStr = parseFloat(e.innerHTML);
    if (eStr >= 0) {
        m = ArrayPostion(IdArray, StockId);

        if (m >= 0) {
            QtyArray[m] = eStr;
            BtnDisabled(true);

        }
        else {
            IdArray.unshift(StockId);
            eArray.unshift(e);
            QtyArray.unshift(eStr);
            BtnDisabled(false);
            e.style.color = '#F00';
        }
    }
    else {
        m = ArrayPostion(IdArray, StockId);
        if (m >= 0) {
            IdArray.splice(m, 1);
            QtyArray.splice(m, 1);
            eArray.splice(m, 1);
        }
        e.innerHTML = eImg;
    }
}

function ArrayPostion(Arr, Str) {
    var backValue = -1;
    var sLen = Arr.length;
    if (sLen > 0) {
        for (i = 0; i < sLen; i++) {
            if (Arr[i] == Str) {
                backValue = i;
                break;
            }
        }
        BtnDisabled(false);
    }
    return backValue;
}


function ArrayClear() {
    IdArray = [];
    QtyArray = [];
    var tempj;
    var sLen = eArray.length;
    if (sLen > 0) {
        for (i = 0; i < sLen; i++) {
            eval(eArray[i]).innerHTML = eImg;
        }
    }
    eArray = [];
    var checkId, ListCheck;
    var cIdNumber = parseInt(document.getElementById("cIdNumber").value);

    for (var i = 1; i <= cIdNumber; i++) {

        tempj = document.getElementById("tempj" + i).value;
        for (var k = 1; k < tempj; k++) {

            ListCheck = document.getElementById("checkId" + i + k);
            if (ListCheck != null) {
                if (ListCheck.disabled == false) ListCheck.checked = false;
            }
        }
    }
    BtnDisabled(true);
}

function BtnDisabled(Flag) {
    document.getElementById("saveBtn").disabled = Flag;
    document.getElementById("cancelBtn").disabled = Flag;
}
var scDates;
function saveQty(e) {
    jQuery(e).hide();
    jQuery('.response').show();
    var choosedRow = 0;
    var flag = 0;
    var ll = 0;
    scDates = new Array();

    jQuery('input[name^="checkId"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            var value = jQuery(this).val();
            var splitArr = value.split('|');
            if (splitArr.length != 4) {
                return;
            }
            choosedRow = choosedRow + 1;
            if (choosedRow == 1) {
                if (splitArr[3]==''){
                    ll = 1;
                }
                if (splitArr[2]==''){
                    flag = 1;
                }else{
                    scDates = splitArr[2];
                }
            } else {
                if (splitArr[3]==''){
                    ll = 1;
                }
                if (splitArr[2]==''){
                    flag = 1;
                }else{
                    scDates = scDates + '|' + splitArr[2];
                }
            }
        }
    });


    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    if (IdArray.length <= 0) {
        alert("请先添加领料数量！");
        window.location.reload();
        return false;
    }
    if(ll == 1){
        alert ('请设置台车！');
        window.location.reload();
        return false;
    }
    if(flag == 1){
        alert ('请设置生产时间！');
        window.location.reload();
        return false;
    }
    doSave();
}

function doSave(){
    BtnDisabled(true);
    var Ids = IdArray.join("|");
    var Qty = QtyArray.join("|");
    var fromPage = document.getElementById("fromPage").value;
    //alert(Ids)
    var data = "Id=" + Ids + "&Qty=" + Qty + "&ActionId=31&fromPage=" + fromPage;
    var url = "item5_3_ajax.php";
    //alert(url);
    var ajax = InitAjax();
    ajax.open("post", url, true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4 && ajax.status == 200) {// && ajax.status ==200
            //alert(ajax.responseText)
            if (ajax.responseText == "Y") {
                //document.form1.submit();
                window.location.reload();
            } else {
                window.location.reload();
            }
        }
    };
    ajax.send(data);
}

var showmenuFlag = 0 //new
var showIpad = 1;
function showmenuie5(event) {
    event.preventDefault();

    if (showIpad == 0) showmenuFlag = 2;
    else showmenuFlag = 1; //new

    var menu = $("ie5menu");
    var Color = $("ColorSide");

    menu.style.display = "block";
    menu.style.visibility = "visible";

    var rightedge = table.offsetWidth - event.clientX;
    var bottomedge = table.offsetHeight - event.clientY;

    if (rightedge < menu.offsetWidth) {
        menu.style.left = window.scrollX + event.clientX - menu.offsetWidth + "px";
    }
    else {
        menu.style.left = window.scrollX + event.clientX + "px";
    }
    if (bottomedge < menu.offsetHeight) {
        menu.style.top = window.scrollY + event.clientY - menu.offsetHeight + "px";
    }
    else {
        menu.style.top = window.scrollY + event.clientY + "px";
    }
    Color.style.height = menu.offsetHeight + "px";
    return false;
}

function hidemenuie5() {
    var menu = $("ie5menu");
    menu.style.display = "none";
    menu.style.visibility = "hidden";
    showmenuFlag = 0; //new
}

function $(objName) {

    if (document.getElementById) {
        return document.getElementById(objName);
    }
    else if (document.layers) {
        return eval("document.layers['" + objName + "']");
    }
    else {
        return eval('document.all.' + objName);
    }
}

function myover(obj) {
    obj.className = "itemshovor";
}

function myout(obj) {
    obj.className = "menuitems";
}

var table = document.getElementById("menudiv531");
if (table !== null) {
    table.oncontextmenu = showmenuie5;

    table.onclick = function () {  //new
        if (showmenuFlag == 1) {
            hidemenuie5();
        }

    };
}


function MYAll_elects() {
    jQuery('input[name^="checkId"]:checkbox').each(function () {

        if (jQuery(this).attr("disabled") == "disabled") {
        } else {
            if (this.checked == false) {

                jQuery(this).click();
            }
        }
    });
    // hidemenuie5();
}

function All_elects() {
    jQuery('input[name^="checkId"]:checkbox').each(function () {

        if (jQuery(this).attr("disabled") == "disabled") {
        } else {
            if (this.checked == false) {

                jQuery(this).click();
            }
        }
    });
    hidemenuie5();
}

function MYInstead_elects() {
    jQuery('input[name^="checkId"]:checkbox').each(function () {

        if (jQuery(this).attr("disabled") == "disabled") {
        } else {
            jQuery(this).click();
        }
    });
    // hidemenuie5();
}

function Instead_elects() {
    jQuery('input[name^="checkId"]:checkbox').each(function () {

        if (jQuery(this).attr("disabled") == "disabled") {
        } else {
            jQuery(this).click();
        }
    });
    hidemenuie5();
}

var selectIds;
var selectsPOrderIds;

function batchChangeWorkShop(e) {
    var choosedRow = 0;
    selectIds = new Array();

    jQuery('input[name^="checkId"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            var value = jQuery(this).val();
            var splitArr = value.split('|');
            if (splitArr.length != 4) {
                return;
            }
            choosedRow = choosedRow + 1;
            selectIds[choosedRow - 1] = splitArr[0];

            if (choosedRow == 1) {
                selectsPOrderIds = splitArr[1];
            } else {
                selectsPOrderIds = selectsPOrderIds + '|' + splitArr[1];
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }

    openWinDialog(e, "item4_6_change2.php", 405, 300, 'bottom');
}

var selectIds;
var selectsPOrderIds;
function batchChangescTime(e) {
    var choosedRow = 0;
    selectIds = new Array();

    jQuery('input[name^="checkId"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            var value = jQuery(this).val();
            var splitArr = value.split('|');
            if (splitArr.length != 4) {
                return;
            }
            choosedRow = choosedRow + 1;
            selectIds[choosedRow - 1] = splitArr[0];

            if (choosedRow == 1) {
                selectsPOrderIds = splitArr[1];
            } else {
                selectsPOrderIds = selectsPOrderIds + '|' + splitArr[1];
            }
        }
    });


    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }
    openWinDialog(e, "item4_6_change3.php", 405, 300, 'bottom');

}
function getDate(e) {
    jQuery(e).attr("value", jQuery(e).val());
}
function batchChangescTime1() {
    jQuery.ajax({
        url: 'set_time_for_product.php',
        type: 'post',
        data: {
            date: jQuery('#scDate').val(),
            POrderId: selectsPOrderIds
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

function batchChangeWorkshop() {
    var changeWorkShop = document.getElementById("changeWorkShopId");
    var changeWorkShopId = changeWorkShop.value;
    var htmlValue = changeWorkShop.options[changeWorkShop.options.selectedIndex].text;
    if (changeWorkShopId > 0) {
        jQuery.ajax({
            url: 'item4_6_ajax2.php',
            type: 'post',
            data: {
                changeWorkShopId: changeWorkShopId,
                sPOrderIds: selectsPOrderIds,
                fromPage: '5_3'
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
}
</script>