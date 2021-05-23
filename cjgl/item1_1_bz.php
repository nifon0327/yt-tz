<?php
//$Th_Col = "选项|30|配件|35|序号|30|客户|80|期限|60|工单流水号|80|产品ID|60|产品名称|280|产品条码|150|打印|50|检讨|30|生管备注|145|数量|60|已生产|60|操作|60";
$Th_Col = "选项|30|配件|35|序号|30|客户名称|100|交期|100|工单流水号|100|产品ID|60|产品名称|200|产品条码|150|生管备注|145|数量|60|已生产|60|操作|60";
$Field = explode("|", $Th_Col);
$Count = count($Field);
$Cols = $Count / 2;
$nowInfo = "当前:脱模入库";
//步骤4：需处理-条件选项
$SearchRows = " AND SC.scFrom>0 AND SC.Estate=1 AND SC.ActionId='$fromActionId'";

if (strlen($tempcName) > 0) {
    $SearchRows .= " AND P.cName LIKE '%$tempcName%' ";
    $searchList1 = "<span class='ButtonH_25'  id='cancelQuery' value='取消' onclick='ResetPage(1,1)'>取消查询</span>";
}
else {
    $searchList1 = "<input type='text' name='tempcName' id='tempcName' value='' width='20'/>  <span class='ButtonH_25' id='okQuery' value='查询' onclick='ResetPage(1,1)'>查询</span>";
}

$checkScSign = 3;//可生产标识

$WorkShopResult = mysql_query("SELECT SC. WorkShopId,W.Name AS WorkShopName 
FROM  $DataIn.yw1_scsheet SC 
INNER JOIN $DataIn.workshopdata W  ON W.Id = SC.WorkShopId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
WHERE 1 $SearchRows AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign GROUP BY SC.WorkShopId  order by SC.WorkShopId DESC ", $link_id);
if ($WorkShopRow = mysql_fetch_array($WorkShopResult)) {
    $ActionList = $ActionList . "<select name='kWorkShopId' id='kWorkShopId' style = 'width:100px' onChange='ResetPage(1,1)'>";
    $i = 1;
    do {
        $theWorkShopId = $WorkShopRow["WorkShopId"];
        $theWorkShopName = $WorkShopRow["WorkShopName"];
        $kWorkShopId = $kWorkShopId == "" ? $theWorkShopId : $kWorkShopId;
        if ($kWorkShopId == $theWorkShopId) {
            $ActionList = $ActionList . "<option value='$theWorkShopId' selected>$theWorkShopName</option>";
            $SearchRows .= " AND SC.WorkShopId='$theWorkShopId'";
        }
        else {
            $ActionList = $ActionList . "<option value='$theWorkShopId'>$theWorkShopName</option>";
        }
        $i++;
    } while ($WorkShopRow = mysql_fetch_array($WorkShopResult));
    $ActionList = $ActionList . "</select>";
}

$ClientList = "";
$ClientResult = mysql_query("SELECT M.CompanyId,C.Forshort 
FROM  $DataIn.yw1_scsheet SC 
INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
INNER JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
WHERE 1 $SearchRows AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign GROUP BY M.CompanyId order by M.CompanyId", $link_id);
if ($ClientRow = mysql_fetch_array($ClientResult)) {
    $ClientList = "<select name='CompanyId' id='CompanyId' onChange='ResetPage(1,1)'>";
    $i = 1;
    do {
        $theCompanyId = $ClientRow["CompanyId"];
        $theForshort = $ClientRow["Forshort"];
        $CompanyId = $CompanyId == "" ? $theCompanyId : $CompanyId;
        if ($CompanyId == $theCompanyId) {
            $ClientList .= "<option value='$theCompanyId' selected>$theForshort</option>";
            $SearchRows .= " AND M.CompanyId='$theCompanyId'";
            $nowInfo .=  $ItemRemark . " - " . $theForshort;
        }
        else {
            $ClientList .= "<option value='$theCompanyId'>$theForshort</option>";
        }
        $i++;
    } while ($ClientRow = mysql_fetch_array($ClientResult));
    $ClientList .= "</select>";
}

// 生产时间
$mySql = "SELECT DATE_FORMAT(SC.scDate,'%Y-%m-%d') as scDate
	FROM  $DataIn.yw1_scsheet SC 
	INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = SC.StockId
	INNER JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
	LEFT  JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=S.POrderId
	LEFT  JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
	WHERE 1  $SearchRows AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign  GROUP BY scDate ORDER BY  scDate DESC";
$clientResult = mysql_query($mySql, $link_id);
if ($clientRow = mysql_fetch_array($clientResult)) {
    $scDateList = "<select name='scDate' id='scDate' onchange='ResetPage(1,1)'>";
    do {
        $thisscDate = $clientRow["scDate"];
        $scDate = $scDate == "" ? $thisscDate : $scDate;
        if ($scDate == $thisscDate) {
            $scDateList .= "<option value='$thisscDate' selected>$thisscDate</option>";
            $SearchRows .= " and SC.scDate='$thisscDate' ";
        }
        else {
            $scDateList .= "<option value='$thisscDate'>$thisscDate</option>";
        }
    } while ($clientRow = mysql_fetch_array($clientResult));
    $scDateList .= "</select>&nbsp;";
}

//分类
$TypeResult = mysql_query("SELECT P.TypeId,T.TypeName
FROM  $DataIn.yw1_scsheet SC 
INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
INNER JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
INNER JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
WHERE 1  $SearchRows AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign  GROUP BY P.TypeId ORDER BY T.mainType,T.TypeId", $link_id);
if ($TypeRow = mysql_fetch_array($TypeResult)) {
    $TypeList = "<select name='ProductTypeId' id='ProductTypeId'  onchange='ResetPage(1,1)'>";
    do {
        $theTypeId = $TypeRow["TypeId"];
        $TypeName = $TypeRow["TypeName"];
        $ProductTypeId = $ProductTypeId == "" ? $theTypeId : $ProductTypeId;
        if ($ProductTypeId == $theTypeId) {
            $TypeList .= "<option value='$theTypeId' selected>$TypeName</option>";
            $SearchRows .= " AND P.TypeId='$theTypeId'";
        }
        else {
            $TypeList .= "<option value='$theTypeId'>$TypeName</option>";
        }
    } while ($TypeRow = mysql_fetch_array($TypeResult));
    $TypeList .= "</select>&nbsp;";
}
//步骤5：
include '../basic/loading.php';
$disabled = '';
if ($Login_JobId == 42) {
  $disabled = "style='display:none'";
}
echo "<table id='ListTable' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'>
	<tr>
	<td colspan='" . ($Cols - 4) . "' height='40px' class=''>$ActionList  $ClientList  $scDateList  $TypeList  $searchList1</td><td colspan='6' align='right' class=''><span class='ButtonH_25' id='batchUpdate' value='保存' onclick='batchOperate();' $disabled>保存</span>&nbsp;&nbsp;&nbsp;&nbsp;<input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr>";
//输出表格标题
for ($i = 0; $i < $Count; $i = $i + 2) {
    $Class_Temp = $i == 0 ? "A1111" : "A1101";
    $j = $i;
    $k = $j + 1;
    echo "<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
}
echo "</tr>";
///////////////////////////////////////////////////////////////
$DefaultBgColor = $theDefaultColor;
$j = 2;
$i = 1;
$mySql = "SELECT O.Forshort,M.CompanyId,M.OrderDate,SC.Id,
    S.POrderId,S.OrderPO,S.Price,S.sgRemark,S.DeliveryDate,S.ShipType,
    SC.Id,SC.sPOrderId,SC.Qty,SC.Estate,SC.ActionId,SC.StockId,SC.Remark,
    P.ProductId,P.cName,P.eCode,P.TestStandard,P.pRemark,
    U.Name AS Unit,PI.Leadtime,PI.Leadweek,D.TypeId
	FROM  $DataIn.yw1_scsheet SC 
	INNER JOIN $DataIn.yw1_ordersheet S ON S.POrderId = SC.POrderId
	INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.trade_object O ON O.CompanyId = M.CompanyId
	INNER JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
	INNER JOIN $DataIn.productunit U ON U.Id=P.Unit
	INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = SC.StockId
	INNER JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
	LEFT  JOIN $DataIn.yw3_pileadtime PI ON PI.POrderId=S.POrderId
	LEFT  JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
	WHERE 1  $SearchRows AND getCanStock(SC.sPOrderId,$checkScSign)=$checkScSign  GROUP BY SC.Id ORDER BY  PI.Leadweek DESC";
//    echo "$mySql"	;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    do {
        $czSign = 1;//操作标记
        $AskDay = "";
        $thisBuyRMB = 0;
        $OrderSignColor = "bgColor='#FFFFFF'";
        $theDefaultColor = $DefaultBgColor;
        $OrderPO = toSpace($myRow["OrderPO"]);

        $Id = $myRow["Id"];
        $Forshort = $myRow['Forshort'];
        $POrderId = $myRow["POrderId"];
        $sPOrderId = $myRow["sPOrderId"];
        $ProductId = $myRow["ProductId"];
        $cName = $myRow["cName"];
        $eCode = toSpace($myRow["eCode"]);
        $TestStandard = $myRow["TestStandard"];
        $Estate = $myRow["Estate"];
        include "../admin/Productimage/getProductImage.php";
        $ShipType = $myRow["ShipType"];
        //出货方式
        if (strlen(trim($ShipType)) > 0) {
            $ShipType = "<image src='../images/ship$ShipType.png' style='width:20px;height:20px;'/>";
        }

        $Qty = $myRow["Qty"];
        $Price = $myRow["Price"];
        $Remark = $myRow["Remark"] == "" ? "&nbsp;" : $myRow["Remark"];
        $OrderDate = $myRow["OrderDate"];
        $Leadtime = $myRow["Leadtime"];
        $Leadweek = $myRow["Leadweek"];
        include "../model/subprogram/PI_Leadweek.php";

        $sumQty = $sumQty + $Qty;
        $StockId = $myRow["StockId"];
        $TypeId = $myRow["TypeId"];

        /*
            **备料,领料情况
            */
        /* $llSign = 0 ; $blSign = 0 ;
         $k = 0 ; $tempblK = 0 ;
         $templlK = 0 ;
         $CheckllResult = mysql_query("SELECT ROUND(G.OrderQty*(S.Qty/$Qty),1) AS OrderQty,S.StockId
         FROM $DataIn.yw1_scsheet  S
         LEFT JOIN $DataIn.cg1_stocksheet G  ON G.StockId = S.StockId
         WHERE S.sPOrderId = $sPOrderId  AND G.Level=1 AND G.blsign = 1 ",$link_id);
         while($CheckllRow  = mysql_fetch_array($CheckllResult)){

             $llOrderQty  =  $CheckllRow["OrderQty"];
             $llStockId   =  $CheckllRow["StockId"];

             //备料情况
             $checkblQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS blQty FROM $DataIn.ck5_llsheet
             WHERE sPOrderId = $sPOrderId AND StockId='$llStockId'",$link_id));
             $thisblQty=$checkblQtyResult["blQty"];
             if($thisblQty >0)$blSign = 1; //部分领料
             if($llOrderQty ==$thisblQty)$tempblK++;

             //领料情况

             $checkllQtyResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet
             WHERE sPOrderId = $sPOrderId AND StockId='$llStockId' AND Estate=0",$link_id));
             $thisllQty=$checkllQtyResult["llQty"];
             if($thisllQty >0)$llSign = 1; //部分领料
             if($llOrderQty ==$thisllQty)$templlK++;
             $k++;
         }

         if($tempblK == $k)$blSign = 2 ;//全部备料
         if($templlK == $k)$llSign = 2 ;//全部领料
         $bgColor="";
         if($blSign ==2 && $llSign >0){
            $bgColor = "bgcolor='#FFB6C1'";
         }else if($blSign ==2 && $llSign ==0){
            $bgColor = "bgcolor='#A2CD5A'";
         }else if($blSign == 1){
            $bgColor = "bgcolor='#BFEFFF'";
         }*/

        $ColbgColor = "";
        //加急订单
        $checkExpress = mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress 
		WHERE POrderId='$POrderId' ORDER BY Id", $link_id);
        if ($checkExpressRow = mysql_fetch_array($checkExpress)) {
            do {
                $Type = $checkExpressRow["Type"];
                switch ($Type) {
                    case 2:
                        $ColbgColor = "bgcolor='#FF0000'";
                        $czSign = 0;
                        break;    //未确定产品
                    case 7:
                        $theDefaultColor = "#FFA6D2";
                        break;    //加急
                }
            } while ($checkExpressRow = mysql_fetch_array($checkExpress));
        }

        //已完成的工序数量
        $CheckscQty = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS scQty 
		FROM $DataIn.sc1_cjtj C 
		WHERE C.POrderId='$POrderId' AND C.sPOrderId = '$sPOrderId' AND C.StockId = $StockId ", $link_id));
        $scQty = $CheckscQty["scQty"];
        $unScQty = $Qty - $scQty;
        //已生产数字显示方式
        switch ($scQty) {
            case 0:
                $scQty = "&nbsp;";
                break;
            default://生产数量非0
                if ($Qty == $scQty) {//生产完成
                    $scQty = "<div class='greenB'>$scQty</div>";
                    $czSign = 0;//不能操作
                }
                else {
                    if ($Qty > $scQty) {//未完成
                        $scQty = "<div class='yellowB'>$scQty</div>";
                    }
                    else {//多完成
                        $scQty = "<div class='redB'>$scQty</div>";
                    }
                }
                break;
        }
        //操作权限:如果权限=31 则可以操作,否则不能操作
        $UpdateIMG = "&nbsp;";
        $UpdateClick = "&nbsp;";
        $PrintIMG = "&nbsp;";
        $PrintClick = "&nbsp;";

        $CheckData = "<input type='checkbox' disabled />";

        if ($czSign == 1) {//可以操作
            if ($SubAction == 31 && $unScQty > 0) {//有权限:需要是该类别下的小组成员，方有权登记
                $UpdateIMG = "<img src='../images/register.png' width='30' height='30'";
                $UpdateClick = "onclick='RegisterQty($POrderId,$sPOrderId,$StockId,1)'";

                $CheckData = "<input type='checkbox' id='checkId$i' name='checkId$i' value='$POrderId|$sPOrderId|$StockId' />";

                $PrintIMG = "<img src='../images/printer.png' width='30' height='30'";
                $PrintClick = "onclick='PrintTasks($POrderId,$sPOrderId)'";
            }
        }

        /* if($blSign ==0){
                $UpdateIMG = "<span class='redB'>未备料</span>";
                $UpdateClick="";
            }else if ($blSign ==1){
                $UpdateIMG = "<span class='blueB'>部分备料</span>";
                $UpdateClick="";
            }else if ($blSign ==2 && $llSign == 0){
                $UpdateIMG = "<span class='greenB'>未领料</span>";
                $UpdateClick="";
            }*/
        /*
          if ($TestStandardSign!==1){  //标准图需要更改等
                 $UpdateIMG="审核标准图";
              $UpdateClick="";
           }
          */
        if ($Estate == 0) {//生产完毕
            $UpdateIMG = "";
            $UpdateClick = "bgcolor='#339900'";

            $CheckData = "<input type='checkbox' disabled />";
        }


        //动态读取配件资料
        $showPurchaseorder = "[ + ]";
        $ListRow = "<tr bgcolor='#D9D9D9' id='ListRow$i' style='display:none'><td class='A0111' height='30' colspan='$Count'><br><div id='ShowDiv$i'>&nbsp;</div><br></td></tr>";
        echo "<tr $bgColor><td class='A0111' align='center'>$CheckData</td><td class='A0101' align='center' id='theCel$i' height='25' valign='middle' $ColbgColor onClick='ShowOrHide(ListRow$i,theCel$i,$i,\"$POrderId\",\"$sPOrderId\",\"showScOrder\",\"finished\");' >$showPurchaseorder</td>";
        echo "<td class='A0101' align='center'>$i</td>";
        echo "<td class='A0101' align='center'>$Forshort</td>";
        echo "<td class='A0101' align='center'>$Leadweek</td>";
        echo "<td class='A0101' align='center'>$sPOrderId</td>";
        echo "<td class='A0101' align='center' >$ProductId</td>";
        echo "<td class='A0101'>$TestStandard</td>";
        echo "<td class='A0101'>$eCode</td>";
//        echo "<td class='A0101' align='center' $PrintClick>$PrintIMG</td>";
//        echo "<td class='A0101' align='center'>$CaseReport</td>";

        echo "<td class='A0101' onclick='InputRemark($j,$sPOrderId)' style='cursor: pointer'>$Remark</td>";

        echo "<td class='A0101' align='right'>$Qty</td>";
        echo "<td class='A0101' align='center'>$scQty</td>";
        echo "<td class='A0101' align='center' $UpdateClick>$UpdateIMG</td>";
        echo "</tr>";
        echo $ListRow;
        $j = $j + 2;
        $i++;
    } while ($myRow = mysql_fetch_array($myResult));
}
else {
    echo "<tr><td colspan='" . $Cols . "' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr>";
}
echo "</table>";
?>
<script>
function batchOperate() {
    jQuery('.response').show();
    //$POrderId|$sPOrderId|$scStockId
    var choosedRow = 0;
    var qtyerror = false;

    var POrderIds;
    var sPOrderIds;
    var StockIds;
    var qtys;
    jQuery('input[name^="checkId"]:checkbox').each(function () {
        if (jQuery(this).prop('checked') == true) {

            var Ids = jQuery(this).val();

            var splitArr = Ids.split('|');
            if (splitArr.length != 3) {
                alert('数据错误!');
                window.location.reload();
                return;
            }

            choosedRow = choosedRow + 1;

            if (choosedRow == 1) {
                POrderIds = splitArr[0];
                sPOrderIds = splitArr[1];
                StockIds = splitArr[2];
            } else {

                POrderIds = POrderIds + '|' + splitArr[0];
                sPOrderIds = sPOrderIds + '|' + splitArr[1];
                StockIds = StockIds + '|' + splitArr[2];
            }
        }
    });

    if (qtyerror) return;
    if (choosedRow == 0) {
        alert("该操作要求选定记录！");
        window.location.reload();
        return;
    }

    var url = "item1_1_scdj_2_ajax.php";
    var ajax = InitAjax();
    ajax.open("POST", url, true);
    ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    ajax.onreadystatechange = function () {
        if (ajax.readyState == 4) {// && ajax.status ==200
            //alert(ajax.responseText);
            if (ajax.responseText.trim() == "Y") {//更新成功
                //document.form1.submit();
                ResetPage(1, 1);
            }
            else {
                alert("保存失败！");
            }
        }
    }
    ajax.send("POrderIds=" + POrderIds + "&sPOrderIds=" + sPOrderIds + "&StockIds=" + StockIds + "&qtys=" + qtys);
}

</script>