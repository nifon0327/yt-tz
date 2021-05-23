<?php
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataIn.ch1_shipmain
$DataIn.ch1_shipsheet
$DataIn.ch2_packinglist
二合一已更新
*/
include "../model/modelhead.php";
$From = $From == "" ? "wait" : $From;
//需处理参数
$ColsNumber = 12;
$tableMenuS = 500;
ChangeWtitle("$SubCompany 当前出货单列表");
$funFrom = "ch_shippinglist";
$nowWebPage = $funFrom . "_wait";
$Th_Col = "选项|60|序号|40|车次单号|100|出货单号|80|客户名称|90|产品数量|80|出货方量|100|出货单|80|出货日期|80|货运信息|120|运输车辆|80|操作人|50";
//$Th_Col="选项|60|序号|40|出货流水号|80|客户|90|Invoice名称|110|Invoice文档|80|外箱标签|60|出货金额|80|出货日期|80|货运信息|120|操作员|50";
$Pagination = $Pagination == "" ? 0 : $Pagination;
$Page_Size = 100;
$ActioToS = "29,3,26,28,35,7,8";
$sumCols = "7";    //求和列,需处理

//步骤3：
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-条件选项
if ($From != "slist") {
    //月份
    $SearchRows = "";
    $SearchRows = " and M.Estate='1'";
    $date_Result = mysql_query("SELECT M.Date FROM $DataIn.ch1_shipmain M WHERE 1 $SearchRows GROUP BY  M.Date  ORDER BY M.Date DESC", $link_id);
    if ($dateRow = mysql_fetch_array($date_Result)) {
        echo "<select name='chooseDate' id='chooseDate' onchange='RefreshPage(\"$nowWebPage\")'>";
        echo "<option value='all' selected>全部时间</option>";
        do {
            $dateValue = $dateRow["Date"];
            $chooseDate = $chooseDate == "" ? $dateValue : $chooseDate;
            if ($chooseDate == $dateValue) {
                echo "<option value='$dateValue' selected>$dateValue</option>";
                $SearchRows .= " and  M.Date='$dateValue' ";
            } else {
                echo "<option value='$dateValue'>$dateValue</option>";
            }
        } while ($dateRow = mysql_fetch_array($date_Result));
        echo "</select>&nbsp;";
    }
    //客户
    $clientResult = mysql_query("SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C 
	WHERE 1 AND C.CompanyId=M.CompanyId $SearchRows GROUP BY M.CompanyId ORDER BY M.CompanyId", $link_id);
    if ($clientRow = mysql_fetch_array($clientResult)) {
        echo "<select name='CompanyId' id='CompanyId' onchange='RefreshPage(\"$nowWebPage\")'>";
        echo "<option value='all' selected>全部客户</option>";
        do {
            $thisCompanyId = $clientRow["CompanyId"];
            $Forshort = $clientRow["Forshort"];
            $CompanyId = $CompanyId == "" ? $thisCompanyId : $CompanyId;
            if ($CompanyId == $thisCompanyId) {
                echo "<option value='$thisCompanyId' selected>$Forshort</option>";
                $SearchRows .= " and M.CompanyId='$thisCompanyId' ";
            } else {
                echo "<option value='$thisCompanyId'>$Forshort</option>";
            }
        } while ($clientRow = mysql_fetch_array($clientResult));
        echo "</select>&nbsp;";
    }

//    //楼栋层
//    $WiseResult = mysql_query("SELECT M.Wise FROM $DataIn.ch1_shipmain M
//LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id
//LEFT JOIN $DataIn.freightdata  F ON F.CompanyId = T.CompanyId
//WHERE 1 $SearchRows GROUP BY M.Wise ORDER BY M.Wise", $link_id);
//    if ($WiseRow = mysql_fetch_array($WiseResult)) {
//        echo "<select name='Wise' id='Wise' onchange='RefreshPage(\"$nowWebPage\")'>";
//        echo "<option value='all' selected>全部栋层</option>";
//        do {
//            $thisWise = $WiseRow["Wise"];
//            $BuildFloorRes=explode("-",$thisWise);
//            $Wise=$Wise==""?$thisWise:$Wise;
//            if ($Wise == $thisWise) {
//                echo "<option value='$thisWise' selected>$BuildFloorRes[0]#  $BuildFloorRes[1]F</option>";
//                $SearchRows .= " and M.Wise='$thisWise' ";
//            }
//            else {
//                echo "<option value='$thisWise'>$BuildFloorRes[0]#  $BuildFloorRes[1]F</option>";
//            }
//        } while ($WiseRow = mysql_fetch_array($WiseResult));
//        echo "</select>&nbsp;";
//    }
//
//		//类型
//    $TypeResult = mysql_query("SELECT  P.TypeId,T.TypeName
//	FROM $DataIn.ch1_shipmain M
//	LEFT JOIN $DataIn.ch1_shipsheet SS ON M.Id=SS.Mid
//LEFT JOIN $DataIn.productdata P ON P.ProductId=SS.ProductId
//INNER JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
//	WHERE 1  $SearchRows GROUP BY P.TypeId ",$link_id);
//    if ($TypeRow = mysql_fetch_array($TypeResult)) {
//        echo "<select name='TypeId' id='TypeId' onchange='RefreshPage(\"$nowWebPage\")'>";
//        echo"<option value='all' selected>全部类型</option>";
//        do {
//            $thisTypeId = $TypeRow["TypeId"];
//            $thisTypeName = $TypeRow["TypeName"];
//            $TypeId = $TypeId == "" ? $thisTypeId : $TypeId;
//            if ($TypeId == $thisTypeId) {
//                echo "<option value='$thisTypeId' selected>$thisTypeName</option>";
//                $SearchRows .= " and P.TypeId='$thisTypeId' ";
//            }
//            else {
//                echo "<option value='$thisTypeId'>$thisTypeName</option>";
//            }
//        } while ($TypeRow = mysql_fetch_array($TypeResult));
//        echo "</select>&nbsp;";
//    }

}
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
echo "&nbsp;<span class='ButtonH_25' onclick='showShipment(this)'>出货</span>&nbsp;";
//echo "&nbsp;<span class='ButtonH_25' onclick='exportDelivery()'>导出出货单</span>";
echo " <div id='winDialog' style=\"position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;min-height:200px\" onDblClick=\"closeWinDialog()\"></div>
        <div id=\"mask\" style=\"text-align: center;vertical-align: middle;display: none;position:fixed;width:160%;height:4500%;background-color: rgba(0,0,0,0.2);z-index: 8;top:-30px;left:-40px\">　</div>
      ";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$d1 = anmaIn("download/invoice/", $SinkOrder, $motherSTR);
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 0);
$mySql = "SELECT  DISTINCT
M.Id,M.CompanyId,M.Number,M.InvoiceNO,M.InvoiceFile,M.Wise,M.Date,M.Estate,M.Locks,M.Sign,M.Operator,M.CarNumber,C.Forshort,C.PayType,S.InvoiceModel,M.CarNo,IFNULL(M.VOL,SUM(IFNULL(L.volume,IFNULL(P.dwgVol,T.DwgVol)))) AS VOL
FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
LEFT JOIN $DataIn.ch8_shipmodel S ON S.Id=M.ModelId 
LEFT JOIN $DataIn.ch1_shipsheet SS ON M.Id=SS.Mid 
LEFT JOIN $DataIn.productdata P ON P.ProductId=SS.ProductId
LEFT JOIN $DataIn.trade_drawing T ON T.Id = P.drawingId 
INNER JOIN $DataIn.producttype PT ON PT.TypeId=P.TypeId 
LEFT JOIN $DataIn.ch1_shipsplit L ON L.ShipId = SS.Id 
WHERE 1 $SearchRows
GROUP BY M.InvoiceNO,M.CarNumber
ORDER BY M.Date DESC";

//echo $mySql;

$myResult = mysql_query($mySql, $link_id);
if ( $myResult && $myRow = mysql_fetch_array($myResult)) {
    do {
        $m = 1;
        $Id = $myRow["Id"];
        $CompanyId = $myRow["CompanyId"];
        $Number = $myRow["Number"];
        $CarNo = $myRow["CarNo"];
        $CarNumber = $myRow["CarNumber"];
        $Forshort = $myRow["Forshort"];
        $InvoiceNO = $myRow["InvoiceNO"];
        $InvoiceFile = $myRow["InvoiceFile"];
        $VOL =$myRow["VOL"];
        $BoxLable = "<div class='redB'>未装箱</div>";
        //检查是否有装箱
        $checkPacking = mysql_query("SELECT Id FROM $DataIn.ch2_packinglist WHERE Mid='$Id' LIMIT 1", $link_id);
        if ($PackingRow = mysql_fetch_array($checkPacking)) {
            //加密参数
            $Parame1 = anmaIn($Id, $SinkOrder, $motherSTR);
            $Parame2 = anmaIn("Mid", $SinkOrder, $motherSTR);
            $BoxLable = $InvoiceFile == 0 ? "&nbsp;" : "<a href='ch_shippinglist_print.php?Parame1=$Parame1&Parame2=$Parame2' target='_blank'>查看</a>";
        }
        //Invoice查看
        //加密参数
        $f1 = anmaIn($InvoiceNO . ".pdf", $SinkOrder, $motherSTR);
        //$InvoiceFile=$InvoiceFile==0?"&nbsp;":"<span onClick='OpenOrLoad(\"$d1\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>查看</span>";
        $InvoiceFile = $InvoiceFile == 0 ? "&nbsp;" : "<a href=\"openorload.php?d=$d1&f=$f1&Type=&Action=6\" target=\"download\">查看</a>";
        if ($CompanyId == 1001) {
            $d2 = anmaIn("invoice/mca", $SinkOrder, $motherSTR);
            //$InvoiceFile.="&nbsp;&nbsp;<span onClick='OpenOrLoad(\"$d2\",\"$f1\",6)' style='CURSOR: pointer;color:#FF6633'>★</span>";
            $InvoiceFile = $InvoiceFile == 0 ? "&nbsp;" : "<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=6\" target=\"download\">★</a>";
        }
        $InvoiceModel = $myRow["InvoiceModel"];
        if ($InvoiceModel == 5) { //出MCA
            $d2 = anmaIn("download/invoice/mca/", $SinkOrder, $motherSTR);
            $InvoiceFile .= "&nbsp;&nbsp;<a href=\"openorload.php?d=$d2&f=$f1&Type=&Action=7\" target=\"download\">★</a>";
        }
        $Wise = $myRow["Wise"] == "" ? "&nbsp;" : $myRow["Wise"];
        $Date = $myRow["Date"];
        $Locks = $myRow["Locks"];
        $Operator = $myRow["Operator"];
        include "../model/subprogram/staffname.php";
        //出货金额
        $checkAmount = mysql_fetch_array(mysql_query("SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$Id'", $link_id));
        $Amount = sprintf("%.2f", $checkAmount["Amount"]);
        $showPurchaseorder = "<img onClick='sOrhOrder(StuffList$i,showtable$i,StuffList$i,\"$Id\",$i);' name='showtable$i' src='../images/showtable.gif' alt='显示或隐藏出货订单明细.' width='13' height='13' style='CURSOR: pointer'>";
        $StuffListTB = "
			<table  border='0' cellspacing='0' id='StuffList$i' style='display:none'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30'><br><div id='showStuffTB$i'>&nbsp;</div><br></td></tr></table>";
        if ($myRow["PayType"] == 1) {
            $BoxLable = "<span class=\"redB\">未收款</span>";
            $OrderSignColor = "bgColor='#F00'";
        }
        // 出货数量+方量
        $sListSql = "SELECT sum(S.Qty) AS Qty,sum(T.Weight) AS Weight,sum(T.CVol) AS CVol
	FROM $DataIn.ch1_shipsheet S 
	LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
	LEFT JOIN $DataIn.trade_drawing T ON T.ProdcutCname = P.cName 
	WHERE S.Mid='$Id' AND S.Type='1'";
        $sListResult = mysql_query($sListSql, $link_id);
        if ($ret = mysql_fetch_array($sListResult)) {
            $tQty = $ret['Qty'];
            $tWeight = $ret['Weight'];
            $CVol = $ret['CVol'];
        };
        if ($Id) {
            $down = "<img src='../images/down.gif' style='' onclick='shipmentDown($Id)'>";
        }
        $ValueArray = array(
            array(0 => $CarNumber, 1 => "align='center'"),
            array(0 => $InvoiceNO, 1 => "align='center'"),
            array(0 => $Forshort, 1 => "align='center'"),
            array(0 => $tQty, 1 => "align='center'"),
            array(0 => $VOL, 1 => "align='center'"),
//			array(0=>$Amount,	1=>"align='right'"),
            array(0 => $down, 1 => "align='center'"),
            array(0 => $Date, 1 => "align='center'"),
            array(0 => $Wise, 1 => "align='center'"),
            array(0 => $CarNo, 1 => "align='center'"),
            array(0 => $Operator, 1 => "align='center'")
        );
        $checkidValue = $Id . '|' . $VOL . '||' . $tWeight;
        include "../model/subprogram/read_model_6.php";
        echo $StuffListTB;
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
<script src='../cjgl/cj_function.js' type=text/javascript></script>
<script src="https://cdn.bootcss.com/layer/2.3/layer.js"></script>
<script>
    //出货
    function showShipment(e) {
        var choosedRow = 0;
        var Ids;
        jQuery('input[name^="checkid"]:checkbox').each(function () {
            if (jQuery(this).prop('checked') == true) {

                choosedRow = choosedRow + 1;
                if (choosedRow == 1) {
                    Ids = jQuery(this).val().split('|')[0];
                } else {
                    Ids = Ids + "," + jQuery(this).val().split('|')[0];
                }
            }
        });

        if (choosedRow == 0) {
            layer.msg("该操作要求选定记录！", function(){});
            return;
        }
        if (choosedRow > 1) {
            layer.msg("该操作只能选取定一条记录!", function(){});
            return;
        }

        openWinDialogWithParas(e, "ch_shippinglist_shipment.php", 400, 200, 'top', Ids);
        jQuery('#mask').show();
    }

    function closeWinDialog() {
        document.getElementById('winDialog').style.display = 'none';
        document.getElementById('mask').style.display = 'none';
    }

    var date = new Date(); //获取一个时间对象
    function format(fmt, date) {
        var o = {
            "M+": date.getMonth() + 1, //月份
            "d+": date.getDate(), //日
            "h+": date.getHours(), //小时
            "m+": date.getMinutes(), //分
            "s+": date.getSeconds(), //秒
            "q+": Math.floor((date.getMonth() + 3) / 3), //季度
            "S": date.getMilliseconds() //毫秒
        };
        if (/(y+)/.test(fmt))
            fmt = fmt.replace(RegExp.$1, (date.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o)
            if (new RegExp("(" + k + ")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    }
    //出货
    function shipment() {
        jQuery('.response').show();
        var need = jQuery('input[name="need"]');
        var CarNo = document.getElementById("CarNo").value;
        if (CarNo) {
            if (CarNo == 'NO') {
                var CarNoIn = jQuery("#CarNoIn").val();
            }
        }else {
            layer.msg('请选择车辆信息', function(){});
            return;
        }

        if (CarNoIn == ''){
            layer.msg('请填写车辆信息', function(){});
            return;
        }

        var CarNumber = jQuery("#CarNumber").val();
        var CarNum = 'JL'+ format('yyyyMMdd', date);
        if (CarNumber == '' || CarNumber == CarNum){
            layer.msg('请填写车次信息', function(){});
            return;
        }

        var GZG = jQuery("#GZG").val();
        var MF = jQuery("#MF").val();
        var num = 1;
        var value = '';
        need.each(function () {
            if (num === 1) {
                value = jQuery(this).val();

            } else {
                value = value + '^^' + jQuery(this).val();
            }
            num++;
        });

        document.form1.action = "ch_shippinglist_updated.php?ActionId=299&ids=" + value + "&CarNo=" + CarNo + "&CarNoIn=" + CarNoIn + "&CarNumber=" + CarNumber + "&GZG=" + GZG + "&MF=" + MF;
        document.form1.submit();
    }

    function CarNoChange() {
        var CarNo = document.getElementById("CarNo").value;
        if (CarNo == 'NO') {
            document.getElementById("CarHide").style.display = "";
        } else {
            document.getElementById("CarHide").style.display = "none";
        }
    }

    //出货单下载
    function shipmentDown(e) {
        document.form1.action = "ch_shippinglist_download.php?Id=" + e;
        document.form1.target = "_blank";
        document.form1.submit();
    }
</script>
