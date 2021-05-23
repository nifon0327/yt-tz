<div id="menudiv592">
<?php
$Th_Col = "序号|30|客户名称|80|业务单号|90|产品名称|80|交期|60|半成品名称|210|生产时间|100|生产数量|55|序号|30|<a onclick='Instead_elects()' style='cursor: pointer' title='点击全选'>选项|30|配件ID|50|配件名称|280|需求单流水号|90|配料人|45|单位|30|需领料数|55|已领料|55|本次备料|55|操作人|80|操作时间|100|操作|50";
$Field = explode("|", $Th_Col);
$Count = count($Field);
$Cols = $Count / 2;
$wField = $Field;
$widthArray = array();
for ($i = 0; $i < $Count; $i++) {
    $i = $i + 1;
    $widthArray[] = $wField[$i];
    $tableWidth += $wField[$i];
}
//if (isSafari6()==1){
$tableWidth = $tableWidth + ceil($Count * 1.5) + 1;
//}
$GysList = "";
$nowInfo = "当前:半成品领料确认单";

if (strlen($tempStuffCname) > 1) {
    $SearchRows .= " AND (D.StuffCname LIKE '%$StuffCname%' OR D.StuffId='$StuffCname') ";
    $GysList1 = "<span class='ButtonH_25'  id='cancelQuery' value='取消查询'   onclick='ResetPage(4,5)'>取消查询</span>";
}
else {

    //增加业务单号下拉筛选
    $ForshortList = "";
    $ForshortResult = mysql_query("SELECT  O.Forshort 
FROM $DataIn.ck5_llsheet L 
INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
INNER JOIN $DataIn.cg1_semifinished  SM ON SM.StockId = SC.StockId
LEFT  JOIN $DataIn.cg1_stocksheet    CG  ON CG.StockId = SC.mStockId
LEFT  JOIN $DataIn.stuffdata         D  ON D.StuffId = SM.mStuffId 
LEFT  JOIN $DataIn.stufftype         T  ON T.TypeId = D.TypeId
LEFT  JOIN $DataIn.cg1_stockmain     M  ON M.Id = CG.Mid 
WHERE  1 $SearchRows GROUP BY O.Forshort ORDER BY DeliveryWeek,Y.OrderPO", $link_id);

    if ($ForshortRow = mysql_fetch_array($ForshortResult)) {
        $ForshortList .= "<select name='khCompanyId' id='khCompanyId' onchange='ResetPage(20,5)'>";
        $ForshortList .= "<option value='all' selected>全部客户</option>";
        do {
            $thisForshort = $ForshortRow["Forshort"];
            $khCompanyId=$khCompanyId==""?$thisForshort:$khCompanyId;
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

    // 生产时间
    $timeList = '';
    $timeResult = mysql_query("select DATE_FORMAT(SC.scDate,'%Y-%m-%d') as created
FROM $DataIn.ck5_llsheet L 
INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
LEFT  JOIN $DataIn.staffmain STM  ON STM.Number = L.creator
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
INNER JOIN $DataIn.cg1_semifinished  SM ON SM.StockId = SC.StockId
LEFT  JOIN $DataIn.cg1_stocksheet    CG  ON CG.StockId = SC.mStockId
LEFT  JOIN $DataIn.stuffdata         D  ON D.StuffId = SM.mStuffId 
LEFT  JOIN $DataIn.stufftype         T  ON T.TypeId = D.TypeId
LEFT  JOIN $DataIn.cg1_stockmain     M  ON M.Id = CG.Mid 
WHERE  1 $SearchRows 
group by DATE_FORMAT(SC.scDate,'%Y-%m-%d')", $link_id);
    if ($timeRow = mysql_fetch_array($timeResult)) {
        $timeList .= "<select name='created' id='created' onchange='ResetPage(1,4)'>";
        $timeList .= "<option value='all' selected>全部</option>";
        do {
            $operation = $timeRow["created"];
            $created = $created == "" ? $operation : $created;
            if ($operation) {
                if ($created == $operation) {
                    $timeList .= "<option value='$operation' selected>$operation</option>";
                    $SearchRows .= " and DATE_FORMAT(SC.scDate,'%Y-%m-%d') = '$operation' ";
                }
                else {
                    $timeList .= "<option value='$operation'>$operation</option>";
                }
            }
        } while ($timeRow = mysql_fetch_array($timeResult));
        $timeList .= "</select>&nbsp;";
    }


    //增加业务单号下拉筛选
    $OrderPOList = "";
    $clientResult = mysql_query("
            SELECT Y.OrderPO
            FROM $DataIn.ck5_llsheet L
            INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
            LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
            LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
            INNER JOIN $DataIn.cg1_semifinished  SM ON SM.StockId = SC.StockId
            LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
            WHERE 1  $SearchRows and Y.OrderPO is not null GROUP BY Y.OrderPO order by Y.OrderPO
            ", $link_id);

    if ($clientRow = mysql_fetch_array($clientResult)) {
        $OrderPOList .= "<select name='OrderPO' id='OrderPO' onchange='ResetPage(20,5)'>";
        $OrderPOList .= "<option value='all' selected>全部PO</option>";
        do {
            $thisOrderPO = $clientRow["OrderPO"];
            $OrderPO = $OrderPO == "" ? $thisOrderPO : $OrderPO;
            if ($OrderPO == $thisOrderPO) {
                $OrderPOList .= "<option value='$thisOrderPO' selected>$thisOrderPO</option>";
                $SearchRows .= " and Y.OrderPO='$thisOrderPO' ";
            }
            else {
                $OrderPOList .= "<option value='$thisOrderPO'>$thisOrderPO</option>";
            }
        } while ($clientRow = mysql_fetch_array($clientResult));
        $OrderPOList .= "</select>&nbsp;";
    }

    // 操作人
    $creatorList = '';
    $creatorResult = mysql_query("select STM.Number,STM.Name 
from $DataIn.ck5_llsheet L 
INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
left join $DataIn.staffmain STM on L.creator = STM.number 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
where 1 $SearchRows group by L.creator", $link_id);
    if ($creatorRow = mysql_fetch_array($creatorResult)) {
        $creatorList .= "<select name='creator' id='creator' onchange='ResetPage(20,5)'>";
        do {
            $Number = $creatorRow["Number"];
            $Name = $creatorRow["Name"];
            if ($_REQUEST['creator'] == $Number) {
                $creatorList .= "<option value='$Number' selected>$Name</option>";
                $SearchRows .= " and STM.Number='$Number' ";
                $stmRows = " where b.Number='$Number' ";
            }
            else {
                $creatorList .= "<option value='$Number'>$Name</option>";
            }
        } while ($creatorRow = mysql_fetch_array($creatorResult));
        $creatorList .= "</select>&nbsp;";
    }

    $GysList1 = "<input name='StuffCname' type='text' id='StuffCname' size='24' value='配件Id或名称'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件Id或名称'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件Id或名称' : this.value;\" style='color:#DDD;'><input class='ButtonH_25' type='button'  id='stuffQuery' value='查询'   onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;ResetPage(4,5);\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}


$GysList1 .= "&nbsp;<input class='ButtonH_25' type='button'  id='ToExportMaterial' name='Submit' value='导出领料信息' onclick='ToexportMaterial()' />&nbsp;&nbsp;&nbsp;<input class='ButtonH_25' type='button'  id='ToExportMaterialAll' name='Submit' value='导出领料汇总' onclick='ToexportMaterialAll()' />";
//步骤5：
echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='" . ($Cols - 6) . "' height='40px' class=''>$llTypeList $GysList $WorkShopList $partTypeList $timeList $ForshortList $OrderPOList $creatorList $GysList1 </td>  <td class='' colspan='2' align='left'> <input class='ButtonH_25' type='button'  id='comfirmBatch' value='确认领料'   onclick='batchPass()'/> </td> <td colspan='4' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr>";
//输出表格标题
for ($i = 0; $i < $Count; $i = $i + 2) {
    $Class_Temp = $i == 0 ? "A1111" : "A1101";
    $j = $i;
    $k = $j + 1;
    echo "<td width='$Field[$k]' class='' height='25px' style='background-color: #F0F5F8'><div align='center'>$Field[$j]</div></td>";
}
echo "</tr></table>";
$DefaultBgColor = $theDefaultColor;
$i = 1;

$mySql = "SELECT  L.POrderId,L.sPOrderId,L.creator,L.created,O.Forshort,SC.Qty, M.PurchaseID,SM.mStockId,
D.StuffId,D.StuffCname,D.Price,D.Picture,D.Gfile,D.Gstate,CG.DeliveryDate,CG.DeliveryWeek,(CG.addQty+CG.FactualQty) AS xdQty,Y.OrderPO,P.cName,STM.Name as sName,SC.scDate
FROM $DataIn.ck5_llsheet L 
INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
LEFT  JOIN $DataIn.staffmain STM  ON STM.Number = L.creator
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = SC.POrderId
LEFT JOIN $DataIn.yw1_ordermain OM ON OM.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
LEFT JOIN $DataIn.trade_object O ON O.CompanyId = OM.CompanyId
INNER JOIN $DataIn.cg1_semifinished  SM ON SM.StockId = SC.StockId
LEFT  JOIN $DataIn.cg1_stocksheet    CG  ON CG.StockId = SC.mStockId
LEFT  JOIN $DataIn.stuffdata         D  ON D.StuffId = SM.mStuffId 
LEFT  JOIN $DataIn.stufftype         T  ON T.TypeId = D.TypeId
LEFT  JOIN $DataIn.cg1_stockmain     M  ON M.Id = CG.Mid 
WHERE  1 $SearchRows GROUP BY SC.sPOrderId ORDER BY DeliveryWeek,Y.OrderPO";
//echo $mySql;
$myResult = mysql_query($mySql, $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    do {
        $m = 1;
        $Id = $myRow["Id"];
        $POrderId = $myRow["POrderId"];
        $sPOrderId = $myRow["sPOrderId"];
        $mStockId = $myRow["mStockId"];
        $Forshort = $myRow["Forshort"];
        $Qty = $myRow["Qty"];
        $xdQty = $myRow["xdQty"];
        $scDate = $myRow["scDate"] != '' ? date('Y-m-d', strtotime($myRow["scDate"])) : '';
        $Relation = $Qty / $xdQty;
        $PurchaseID = $myRow["PurchaseID"];
        $Remark = $myRow["Remark"];
        $Date = $myRow["Date"];
        $StuffId = $myRow["StuffId"];
        $StuffCname = $myRow["StuffCname"];
        $Picture = $myRow["Picture"];
        $creator = $myRow['sName'];
        $created = date("Y-m-d", strtotime($myRow['created']));
        $OrderPO = $myRow["OrderPO"];
        $cName = $myRow["cName"];

        include "../model/subprogram/stuffimg_model.php";
        include "../model/subprogram/stuff_Property.php";//配件属性

        $DeliveryDate = $myRow["DeliveryDate"] == "0000-00-00" ? "" : $myRow["DeliveryDate"];
        include "../model/subprogram/deliverydate_toweek.php";


        $blPrintIMG = "<a href='ck_bl_report1.php?POrderId=$POrderId&mStockId=$mStockId&sPOrderId=$sPOrderId' target='_blank'><img src='../images/printer.png' width='16' height='16'></a>";


        echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
        echo "<td scope='col' class='A0111' width='$Field[$m]' align='center'>$i</td>";
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'  >$Forshort</td>";
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'  >$OrderPO</td>";
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'  onclick='selectAllb1Stuff(this)' onmouseover=\"this.style.cssText='cursor:pointer'\" onmouseover=\"this.style.cssText='cursor:default'\">$cName</td>";
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'  >$DeliveryDate</td>";
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td scope='col' class='A0101' width='$Field[$m]'>$StuffCname $blPrintIMG</td>";
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$scDate</td>";
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Qty</td>";
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td width='' class='A0101'>";

        //订单产品对应的配件信息
        $checkStockSql = mysql_query("SELECT (A.OrderQty*$Relation) AS OrderQty,A.StockId,
		        K.tStockQty,D.StuffId,D.StuffCname,D.Picture,F.Remark,M.Name,
		        P.Forshort,U.Name AS UnitName,U.Decimals
				FROM   $DataIn.cg1_semifinished   A 
                INNER JOIN $DataIn.cg1_stocksheet G  ON G.StockId = A.StockId
				INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=A.StuffId 
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
				LEFT JOIN  $DataIn.staffmain M ON M.Number=G.BuyerId 
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
				LEFT JOIN  $DataIn.base_mposition F ON F.Id=D.SendFloor
				INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				INNER JOIN $DataIn.stuffmaintype MT ON MT.Id=T.mainType
				INNER JOIN $DataIn.stuffunit U ON U.Id=D.Unit
				WHERE A.POrderId='$POrderId' $SearchRows1 AND A.mStockId='$mStockId' AND MT.blSign=1 
        ORDER BY D.SendFloor", $link_id);

        //如果在出库记录不存在的原材料不能做领料确认
        /*
        $checkStockSql=mysql_query("SELECT (A.OrderQty*$Relation) AS OrderQty,A.StockId,
		        K.tStockQty,D.StuffId,D.StuffCname,D.Picture,F.Remark,M.Name,
		        P.Forshort,U.Name AS UnitName,U.Decimals
				FROM   $DataIn.cg1_semifinished   A
                INNER JOIN $DataIn.cg1_stocksheet G  ON G.StockId = A.StockId
				INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=A.StuffId
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId
				LEFT JOIN  $DataIn.staffmain M ON M.Number=G.BuyerId
				LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=G.CompanyId
				LEFT JOIN  $DataIn.base_mposition F ON F.Id=D.SendFloor
				INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				INNER JOIN $DataIn.stuffmaintype MT ON MT.Id=T.mainType
				INNER JOIN $DataIn.stuffunit U ON U.Id=D.Unit
        LEFT JOIN (SELECT M.StuffId,M.StockId,SUM(M.TaskQty) as TaskTotalQty
                   FROM $DataIn.wms_taskout M
                    GROUP BY M.StuffId,M.StockId) as GM on (GM.StuffId=D.StuffId and GM.StockId=A.StockId)
				WHERE A.POrderId='$POrderId' AND A.mStockId='$mStockId' AND MT.blSign=1
              AND  ((GM.TaskTotalQty>=(A.OrderQty*$Relation)  and (T.TypeId in (9018,9019,9021))) OR (T.TypeId not in (9018,9019,9021)))
        ORDER BY D.SendFloor",$link_id);
        */

        if ($checkStockRow = mysql_fetch_array($checkStockSql)) {
            echo "<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
            $j = 1;
            do {
                $Name = $checkStockRow["Name"];
                $Forshort = $checkStockRow["Forshort"];
                $StockId = $checkStockRow["StockId"];
                $StuffId = $checkStockRow["StuffId"];
                $StuffCname = $checkStockRow["StuffCname"];
                $UnitName = $checkStockRow["UnitName"];
                $Picture = $checkStockRow["Picture"];
                $Decimals = $checkStockRow["Decimals"];
                $tStockQty = round($checkStockRow["tStockQty"], $Decimals);
                $OrderQty = round($checkStockRow["OrderQty"], $Decimals);
                $Remark = $checkStockRow["Remark"];
                //检查是否有图片
                $d = anmaIn("download/stufffile/", $SinkOrder, $motherSTR);
                include "../model/subprogram/stuffimg_model.php";
                include "../model/subprogram/stuff_Property.php";//配件属性
                $CheckComboxSql = mysql_query("SELECT A.StockId,A.StuffId,A.OrderQty,D.StuffCname,
					U.Name AS UnitName,K.tStockQty,A.Relation
					FROM $DataIn.cg1_stuffcombox A  
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId = A.StuffId
					LEFT  JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId
					LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
					LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId 
					WHERE  A.mStockId='$StockId'", $link_id);
                $showComboxTable = "";
                $comboxSign = 0;
                if ($CheckComboxRow = mysql_fetch_array($CheckComboxSql)) {
                    $comboxSign = 1;
                    $showComboxTable = "<tr >
		                  <td colspan='11' class='A0100'><div  width='720'><table  cellspacing='1' border='1' align='left' style='margin-left:60px;margin-top:10px;margin-bottom:5px' >";
                    $showComboxTable .= "<tr height='25' style='background:#83b6b9;'>
		                  <td width='50' align='center'>配件ID</td>
		                  <td width='260' align='center' >配件名称</td>
		                  <td width='50' align='center'>单位</td>
		                  <td width='50' align='center'>对应关系</td>
		                  <td width='60' align='center'>需零数量</td>
		                  <td width='60' align='center'>已领数量</td>
		                 
		                  <td width='60' align='center'>本次领料</td>
		                  </tr >";
                    do {

                        $comboxStuffId = $CheckComboxRow["StuffId"];
                        $comboxStockId = $CheckComboxRow["StockId"];
                        $comboxStuffCname = $CheckComboxRow["StuffCname"];
                        $comboxOrderQty = $CheckComboxRow["OrderQty"];
                        $comboxUnitName = $CheckComboxRow["UnitName"];
                        $comboxtStockQty = $CheckComboxRow["tStockQty"];
                        $comboxRelation = $CheckComboxRow["Relation"];
                        $checkcomboxllRow = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$comboxStockId' AND Estate = 1", $link_id));
                        $comboxllQty = $checkcomboxllRow["llQty"];
                        $comboxllQty = $comboxllQty == "" ? 0 : $comboxllQty;

                        $checkcomboxthisllRow = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$comboxStockId' AND Estate = 0", $link_id));
                        $comboxthisllQty = $checkcomboxthisllRow["llQty"];
                        $comboxthisllQty = $comboxthisllQty == "" ? 0 : $comboxthisllQty;

                        $showComboxTable .= "<tr >
		                  <td  align='center'>$comboxStuffId</td>
		                  <td  >$comboxStuffCname</td>
		                  <td align='center'>$comboxUnitName</td>
		                  <td align='center'>$comboxRelation</td>
		                  <td align='right'>$comboxOrderQty</td>
		                  <td align='right'>$comboxllQty</td>
		                  <td align='right' >$comboxthisllQty</td>
		                  </tr >";
                    } while ($CheckComboxRow = mysql_fetch_array($CheckComboxSql));
                    $showComboxTable .= "</table></div></td></tr>";
                }

                //本次领料数
                $UnionSTR3 = mysql_query("SELECT SUM(Qty) AS thisQty FROM $DataIn.ck5_llsheet WHERE   sPOrderId = '$sPOrderId' AND StockId='$StockId' AND Estate='1'", $link_id);
                $thisQty = mysql_result($UnionSTR3, 0, "thisQty");
                $thisQty = $thisQty == "" ? 0 : $thisQty;
                //已备料总数
                $UnionSTR4 = mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE sPOrderId = '$sPOrderId' AND StockId='$StockId' AND Estate='0'", $link_id);
                $llQty = mysql_result($UnionSTR4, 0, "llQty");
                $llQty = $llQty == "" ? 0 : $llQty;
                if ($llQty > $OrderQty) {//领料总数大于订单数,提示出错
                    $llBgColor = "class='redB'";
                }
                else {
                    if ($llQty == $OrderQty) {//刚好全领，绿色
                        $llBgColor = "class='greenB'";
                    }
                    else {        //未领完
                        $llBgColor = "";
                    }
                }
                //确认操作
                $upIMG = "&nbsp;";
                $upImgclick = "";
                $checkRow = "<input type='checkbox' disabled />";
                $RemainQty = $OrderQty - $llQty;//未领料数
                if ($SubAction == 31) {
                    $scnameTemp = preg_replace('[\"|“|”|’|\']', '', $otherCname);
                    $msgStr = "需求单流水号:$StockId|配件名称:$scnameTemp|备料数量:$thisQty";
                    //if ($JobId==14 || $BranchId==7){
                    if ($Login_P_Number == 10005) {
                        if ($thisQty == 0) {
                            $upIMG = "";
                            $upImgclick = "style='color:#F00;'";
                        }
                        else {
                            $upIMG = "<img src='../images/unPass.png' width='30' height='30'>";
                            $upImgclick = "onclick=\"delLLdata(this,$sPOrderId,$StockId,$StuffId,$thisQty,'$msgStr')\"";
                        }
                    }
                    else {

                        if ($thisQty == 0) {
                            $upIMG = "";
                            $upImgclick = "style='color:#F00;'";
                        }
                        else {
//						        //if($thisQty<=$RemainQty){
//						        if(abs($thisQty-$RemainQty)<0.1 || $thisQty<$RemainQty){
                            $upIMG = "<img src='../images/Pass.png' width='30' height='30'>";
                            $upImgclick = "onclick=\"passLLdata(this,$sPOrderId,$StockId,'$msgStr')\" ";
                            $checkRow = "<input type='checkbox' name='checkId$i$j' id='checkId$i$j' value='$sPOrderId|$StockId' />";
//								}
//					            else{
//						           $upIMG="多领";
//								   $upImgclick="style='color:#F00;'";
//					            }
                        }
                    }
                }

                $ShuiPrintIMG = "<a href='slicebom_report.php?POrderId=$POrderId&sPOrderId=$sPOrderId&mStockId=$mStockId&Qty=$Qty' target='_blank'><img src='../images/printer.png' width='16' height='16'></a>";


                echo "<tr height='30'>";
                $unitFirst = $Field[$m] - 1;
                echo "<td class='A0101' width='$unitFirst' align='center' $bgColor>$j</td>";//序号
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$checkRow</td>";//序号
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$StuffId</td>";  //配件ID
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$StuffCname $ShuiPrintIMG</td>";//配件名称
                $m = $m + 2;
                echo "<td  class='A0101' width='$Field[$m]' align='center'>$StockId</td>";  //需求流水号
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$blMan</td>";//配料人
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$UnitName</td>";//单位
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$OrderQty </td>";//需领料数
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'><div $llBgColor>$llQty</div></td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$thisQty</td>";  //本次领料
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$creator</td>";  //操作人
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$created</td>";  //操作时间
                $m = $m + 2;
                echo "<td  class='A0100' width='' align='center' id='updatetd$i$j' $upImgclick>$upIMG</td>";
                echo "</tr>";
                $j++;
                if ($comboxSign == 1) $j++; //存在子配件多一行
                echo $showComboxTable;
            } while ($checkStockRow = mysql_fetch_array($checkStockSql));
            echo "</table>";
        }
        echo "</td></tr></table>";

        $i++;
    } while ($myRow = mysql_fetch_array($myResult));
}
else {
    echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='" . $Cols . "' align='center' height='30' class='A0111' style='background-color: #ffffff'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
}

?>
</div>