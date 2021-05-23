<?php
$nowInfo = "当前:生产入库确认";

$SearchRows = "";

//客户筛选
$ForshortResult= mysql_query("SELECT O.Forshort
	FROM $DataIn.sc1_cjtj  S 
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId 
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=Y.POrderId
	WHERE S.Estate in (1,2,3)  $SearchRows AND G.Level = 1 GROUP BY O.Forshort",$link_id);
if ($ForshortRow = mysql_fetch_array($ForshortResult)){
    $ForshortList="<select name='khCompanyId' id='khCompanyId'  onChange='ResetPage(1,5)'>";
    do{
        $theForshort=$ForshortRow["Forshort"];
        $khCompanyId=$khCompanyId==""?$theForshort:$khCompanyId;
        if($khCompanyId==$theForshort){
            $ForshortList.="<option value='$theForshort' selected>$theForshort</option>";
            $SearchRows.=" AND O.Forshort='$theForshort'";
            $nowInfo.=" - ".$theForshort;
        }
        else{
            $ForshortList.="<option value='$theForshort'>$theForshort</option>";
        }
    }while($ForshortRow = mysql_fetch_array($ForshortResult));
    $ForshortList.="</select>";
}

$WorkShopResult = mysql_query("SELECT SC.WorkShopId,W.Name AS WorkShopName 
	FROM $DataIn.sc1_cjtj  S 
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId 
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=Y.POrderId
	WHERE S.Estate in (1,2,3)  $SearchRows AND G.Level = 1 GROUP BY SC.WorkShopId  order by SC.WorkShopId", $link_id);
if ($WorkShopRow = mysql_fetch_array($WorkShopResult)) {
    $WorkShopList = "<select name='WorkShopId' id='WorkShopId' style = 'width:100px' onChange='ResetPage(1,4)'>";
    $i = 1;
    do {
        $theWorkShopId = $WorkShopRow["WorkShopId"];
        $theWorkShopName = $WorkShopRow["WorkShopName"];
        $WorkShopId = $WorkShopId == "" ? $theWorkShopId : $WorkShopId;
        if ($WorkShopId == $theWorkShopId) {
            $WorkShopList .= "<option value='$theWorkShopId' selected>$theWorkShopName</option>";
            $SearchRows .= " AND SC.WorkShopId='$theWorkShopId'";
        }
        else {
            $WorkShopList .= "<option value='$theWorkShopId'>$theWorkShopName</option>";
        }
        $i++;
    } while ($WorkShopRow = mysql_fetch_array($WorkShopResult));
    $WorkShopList .= "</select>";
}

// Date
$dataResult = mysql_query("SELECT S.Date
	FROM $DataIn.sc1_cjtj  S 
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId 
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=Y.POrderId
	WHERE S.Estate in (1,2,3)  $SearchRows AND G.Level = 1 GROUP BY S.Date ORDER BY Date DESC", $link_id);
if ($dataRow = mysql_fetch_array($dataResult)) {
    $dataList = "<select name='chooseDate' id='chooseDate'  onChange='ResetPage(1,4)'>";
    $dataList .= "<option value='all' >全部日期</option>";
    do {
        $theDate = $dataRow["Date"];
        $chooseDate = $chooseDate == "" ? $theDate : $chooseDate;
        if ($chooseDate == $theDate) {
            $dataList .= "<option value='$theDate' selected>$theDate</option>";
            $SearchRows .= " AND S.Date='$theDate'";
        }
        else {
            $dataList .= "<option value='$theDate'>$theDate</option>";
        }
    } while ($dataRow = mysql_fetch_array($dataResult));
    $dataList .= "</select>";
}

// OrderPO
$mySql = "SELECT Y.OrderPO
	FROM $DataIn.sc1_cjtj  S 
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId 
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=Y.POrderId
	WHERE S.Estate in (1,2,3)  $SearchRows AND G.Level = 1 GROUP BY Y.OrderPO";
$OrderPOResult = mysql_query($mySql);
if ($OrderPORow = mysql_fetch_array($OrderPOResult)) {
    $OrderPOList = "<select name='OrderPO' id='OrderPO'  onChange='ResetPage(1,4)'>";
    $OrderPOList .= "<option value='all' >全部业务单号</option>";
    do {
        $theOrderPO = $OrderPORow["OrderPO"];
        $OrderPO = $OrderPO == "" ? $theOrderPO : $OrderPO;
        if ($OrderPO == $theOrderPO) {
            $OrderPOList .= "<option value='$theOrderPO' selected>$theOrderPO</option>";
            $SearchRows .= " AND Y.OrderPO='$theOrderPO'";
        }
        else {
            $OrderPOList .= "<option value='$theOrderPO'>$theOrderPO</option>";
        }
    } while ($OrderPORow = mysql_fetch_array($OrderPOResult));
    $OrderPOList .= "</select>";
}

// Type
$mySql = "SELECT
	P.TypeId,T.TypeName
FROM
	$DataIn.sc1_cjtj S
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId = Y.ProductId
	INNER JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
WHERE
	S.Estate IN ( 1, 2, 3 ) 
$SearchRows
	AND G.LEVEL = 1 
GROUP BY
	P.TypeId";
//echo $mySql;
$TypeResult = mysql_query($mySql);
if ($TypeRow = mysql_fetch_array($TypeResult)) {
    $TypeList = "<select name='TypeId' id='TypeId' onchange='ResetPage(1.4)'>";
    $TypeList .= "<option value='all' selected>全部类型</option>";
    do {
        $theTypeId = $TypeRow["TypeId"];
        $TypeName = $TypeRow["TypeName"];
        if ($TypeId == $theTypeId) {
            $TypeList .= "<option value='$theTypeId'  selected>$TypeName</option>";
            $SearchRows .= " AND P.TypeId='$theTypeId'";
        }
        else {
            $TypeList .= "<option value='$theTypeId' >$TypeName</option>";
        }
    } while ($TypeRow = mysql_fetch_array($TypeResult));
    $TypeList .= "</select>&nbsp;";
}

// QCstate
$mySql = "SELECT S.Estate
	FROM $DataIn.sc1_cjtj  S 
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId
	INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = S.sPOrderId
	INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId 
	INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=Y.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	LEFT JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=Y.POrderId
	WHERE S.Estate in (1,2,3)  $SearchRows AND G.Level = 1 GROUP BY S.Estate";

$EstateResult = mysql_query($mySql);
if ($EstateRow = mysql_fetch_array($EstateResult)) {
    $EstateList = "<select name='Estate' id='Estate'  onChange='ResetPage(1,4)'>";
    $EstateList .= "<option value='all' >全部质检状态</option>";
    do {
        $theEstate = $EstateRow["Estate"];
        $theCEstate = ($theEstate == 1 ? '未审核' : ($theEstate == 2 ? '合格' : '不合格'));
        $Estate = $Estate == "" ? $theEstate : $Estate;
        if ($Estate == $theEstate) {
            $EstateList .= "<option value='$theEstate' selected>$theCEstate</option>";
            $SearchRows .= " AND S.Estate='$theEstate'";
        }
        else {
            $EstateList .= "<option value='$theEstate'>$theCEstate</option>";
        }
    } while ($EstateRow = mysql_fetch_array($EstateResult));
    $EstateList .= "</select>";
}

include "item4_7_1.php"; //组装类入库确认


?>
</form>
</body>

<div id='divShadow' class="divShadow" style="display:none;z-index:2;" onDblClick="closeMaskDiv()"></div>
<div id="divPageMask" class="divPageMask" style="display:none; background-color:rgba(0,0,0,0.6);"></div>
<!--<div id='winDialog' style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;" onDblClick="closeWinDialog()"></div>-->
</html>
<script src="../model/js/jquery-1.11.1.js"></script>
<script language="JavaScript">
function passRkdata(e, POrderId, StockId, level) {
    var msg = "确定当前生产数量入库?";
    if (confirm(msg)) {
        var chooseDate = document.getElementById("chooseDate").value;

        if (level == 1) {

            var index = e.id.replace('updateTd', '');
            var SeatId = document.getElementById("SeatId" + index).value;

            if (SeatId == "") {
                alert("该选择库位编号！");
                return false;
            }

            var url = "item4_7_ajax.php?POrderId=" + POrderId + "&StockId=" + StockId + "&chooseDate=" + chooseDate + "&level=" + level + "&SeatId=" + SeatId;
        } else {
            var url = "item4_7_ajax.php?POrderId=" + POrderId + "&StockId=" + StockId + "&chooseDate=" + chooseDate + "&level=" + level;
        }
        var ajax = InitAjax();
        ajax.open("GET", url, true);
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                //更新该单元格内容
                //alert(ajax.responseText);
                if (ajax.responseText == "Y") {
                    document.form1.submit();
                    //e.innerHTML="<div style='background-color:#FF0000'>已确认</div>";
                } else {
                    alert("生产记录确认失败！");
                }
            }
        };
        ajax.send(null);
    }
}
function batchPassQC(level) {

    var choosedRow = 0;
    var POrderIds;
    var StockIds;
    var SeatIds;
    var pDatess;

    var checkSeatId = true;

    jQuery('input[name^="checkId"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {

            choosedRow = choosedRow + 1;
            var Ids = jQuery(this).val().split('|');
            if (choosedRow == 1) {
                POrderIds = Ids[0];
                StockIds = Ids[1];
                pDates = Ids[2];
            } else {
                POrderIds = POrderIds + "|" + Ids[0];
                StockIds = StockIds + "|" + Ids[1];
                pDates = pDates + "|" + Ids[2];
            }
        }
    });

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        return;
    }
    var sign = '';
    if (level == 3) {
        sign = '不';
    }
    var msg = "确定当前生产质检" + sign + "合格?";
    if (confirm(msg)) {
        //var chooseDate = document.getElementById("chooseDate").value;
        var url = "item4_7_batch_ajax.php";
        var params = "POrderIds=" + POrderIds + "&StockIds=" + StockIds + "&chooseDates=" + pDates + "&level=" + level;

        var ajax = InitAjax();
        ajax.open("POST", url, true);
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                //更新该单元格内容
                //alert(ajax.responseText);
                if (ajax.responseText == "Y") {
                    document.form1.submit();
                    //e.innerHTML="<div style='background-color:#FF0000'>已确认</div>";
                } else {
                    alert("确认失败！");
                }
            }
        };
        ajax.send(params);
    }
}


function batchPassRkdata(e) {
    var choosedRow = 0;
    jQuery('input[name^="checkId"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            choosedRow = choosedRow + 1;
        }

    });
    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        // window.location.reload();
        return;
    }
    openWinDialog(e, "for_choose_seat.php", 405, 300, 'bottom');

}
function doPassRkData(level) {

    jQuery('.response').show();
    var choosedRow = 0;
    var POrderIds;
    var StockIds;
    var SeatIds;
    var pDates;

    var checkSeatId = true;
    var storageNOVal = true;
var stackNOVal = true;

    jQuery('input[name^="checkId"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {

            choosedRow = choosedRow + 1;
            var Ids = jQuery(this).val().split('|');
            if (choosedRow == 1) {
                POrderIds = Ids[0];
                StockIds = Ids[1];
                pDates = Ids[2];
            } else {
                POrderIds = POrderIds + "|" + Ids[0];
                StockIds = StockIds + "|" + Ids[1];
                pDates = pDates + "|" + Ids[2];
            }

            if (level == 1) {
                var SeatId = jQuery("#SeatId option:selected").text();
                var storageNO = jQuery(" input[ name='storageNO' ] ").val();
var stackNO = jQuery(" input[ name='stackNO' ] ").val();
                if (level == 1) {
                    params1 = "&SeatId=" + SeatId + "&storageNO=" + storageNO + "&stackNO=" + stackNO;
                }
                if (SeatId == "") {
                    checkSeatId = false;
                    return false;
                }
                if (storageNO == "") {
                    storageNOVal = false;
                    return false;
                }
if (stackNO == "") {
                    stackNOVal = false;
                    return false;
                }

            }
        }
    });

    if (!checkSeatId) {
        alert("该选择库位编号！");
        window.location.reload();
        return;
    }

    if (!storageNOVal) {
        alert("该填写入库单号！");
        window.location.reload();
        return;
    }
if (!stackNOVal) {
        alert("该填写入库垛号！");
        window.location.reload();
        return;
    }

    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        window.location.reload();
        return;
    }

    var msg = "确定当前生产数量入库?";
    if (confirm(msg)) {
        //var chooseDate = document.getElementById("chooseDate").value;
        var url = "item4_7_batch_ajax.php";
        var params = "POrderIds=" + POrderIds + "&StockIds=" + StockIds + "&chooseDates=" + pDates + "&level=" + level + params1;


        var ajax = InitAjax();
        ajax.open("POST", url, true);
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        ajax.onreadystatechange = function () {
            if (ajax.readyState == 4 && ajax.status == 200) {
                //更新该单元格内容
                //alert(ajax.responseText);
                if (ajax.responseText.trim() == "Y") {
                    document.form1.submit();
                    //e.innerHTML="<div style='background-color:#FF0000'>已确认</div>";
                } else {
                    alert("生产记录确认失败！");
                    window.location.reload();


                }
            }
        };
        ajax.send(params);
    }
}

function showPrintCodeWin(POrderId) {
    document.getElementById("divShadow").innerHTML = "";
    var url = "item4_7_print.php?POrderId=" + POrderId;

    var ajax = InitAjax();
    ajax.open("GET", url, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4 && ajax.status == 200) {
            document.getElementById("divShadow").innerHTML = ajax.responseText;
        }
    };
    ajax.send(null);
    //定位对话框
    divShadow.style.left = window.pageXOffset + (window.innerWidth - 440) / 2 + "px";
    divShadow.style.top = window.pageYOffset + (window.innerHeight - 300) / 2 + "px";
    document.getElementById('divPageMask').style.display = 'block';
    document.getElementById('divShadow').style.display = 'block';
    document.getElementById('divPageMask').style.width = document.body.scrollWidth;
    document.getElementById('divPageMask').style.height = document.body.offsetHeight + "px";
}

function toPrint() {
    var oldstr = document.body.innerHTML;
    var bdhtml = window.document.body.innerHTML;
    var sprnstr = "<!--startprint-->";
    var eprnstr = "<!--endprint-->";
    var headstr = "<html><head><title></title></head><body>";  //打印头部
    var footstr = "</body></html>";  //打印尾部
    var prnhtml = bdhtml.substr(bdhtml.indexOf(sprnstr) + 17);
    prnhtml = prnhtml.substring(0, prnhtml.indexOf(eprnstr));
    window.document.body.innerHTML = headstr + prnhtml + footstr;
    window.print();
    document.body.innerHTML = oldstr;
    return false;
}


function chooseRow() {

    var num = 0;
    var totalStatusBar = document.getElementById("TotalStatusBar");
    jQuery('input[name^="checkId"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {
            num++;
        }
    });

    if (num != 0) {
        totalStatusBar.innerHTML = "&nbsp;&nbsp;&nbsp;选定行求和：" + num ;
        totalStatusBar.style.display = "";
    } else {
        totalStatusBar.style.display = "none";
    }
}
</script>
