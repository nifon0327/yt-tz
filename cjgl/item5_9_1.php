<?php
$Th_Col = "序号|30|工单流水号|80|产品名称|160|生产数量|55|序号|30|选项|30|配件ID|50|配件名称|215|需求单流水号|90|配料人|45|单位|30|需领料数|55|已领料|55|本次备料|55|操作|50";
$Field = explode("|", $Th_Col);
$Count = count($Field);
$wField = $Field;
$widthArray = array();
for ($i = 0; $i < $Count; $i++) {
    $i = $i + 1;
    $widthArray[] = $wField[$i];
    $tableWidth += $wField[$i];
}
if (isSafari6() == 1) {
    $tableWidth = $tableWidth + ceil($Count * 1.5) + 1;
}
$GysList = "";
$nowInfo = "当前:车间领料确认单";
if (strlen($tempStuffCname) > 1) {
    $SearchRows .= " AND (D.StuffCname LIKE '%$StuffCname%' OR D.StuffId='$StuffCname') ";
    $GysList1 = "<span class='ButtonH_25'  id='cancelQuery' value='取消查询'   onclick='ResetPage(4,5)'>取消查询</span>";
}
else {
    $ClientResult = mysql_query("SELECT M.CompanyId,C.Forshort 
		 FROM  $DataIn.ck5_llsheet L 
		 INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
		 INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=SC.POrderId  
		 INNER JOIN $DataIn.yw1_ordermain M  ON M.OrderNumber=Y.OrderNumber 
		 INNER JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId
		 WHERE 1  $SearchRows  GROUP BY M.CompanyId order by M.CompanyId", $link_id);

    if ($ClientRow = mysql_fetch_array($ClientResult)) {
        $GysList .= "<select name='CompanyId' id='CompanyId' onchange='ResetPage(9,5)' style='width:100px;'>";
        do {
            $theCompanyId = $ClientRow["CompanyId"];
            $theForshort = $ClientRow["Forshort"];
            $CompanyId = $CompanyId == "" ? $theCompanyId : $CompanyId;
            if ($CompanyId == $theCompanyId) {
                $GysList .= "<option value='$theCompanyId' selected>$theForshort</option>";
                $SearchRows .= " AND  M.CompanyId='$theCompanyId'";
                $DefaultClient = $theForshort;
            }
            else {
                $GysList .= "<option value='$theCompanyId'>$theForshort</option>";
            }
        } while ($ClientRow = mysql_fetch_array($ClientResult));
        $GysList .= "</select>&nbsp;";
    }
    //分类
    $TypeResult = mysql_query("SELECT P.TypeId,T.TypeName 
		FROM  $DataIn.ck5_llsheet L 
		INNER JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
	    INNER JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=SC.POrderId    
        INNER JOIN $DataIn.yw1_ordermain M  ON M.OrderNumber=Y.OrderNumber 
        INNER JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId 
        INNER JOIN $DataIn.producttype T ON T.TypeId=P.TypeId 
        WHERE 1  $SearchRows  GROUP BY P.TypeId ORDER BY T.mainType,T.TypeId", $link_id);

    $oneTypeId = "";
    $SearchType = "";
    if ($TypeRow = mysql_fetch_array($TypeResult)) {
        $TypeList = "<select name='pTypeId' id='pTypeId' onchange='ResetPage(9,5)'>";
        do {
            $theTypeId = $TypeRow["TypeId"];
            $TypeName = $TypeRow["TypeName"];
            if ($theTypeId != "") {
                if ($oneTypeId == "") $oneTypeId = $theTypeId;
                if ($pTypeId == $theTypeId) {
                    $TypeList .= "<option value='$theTypeId' selected>$TypeName</option>";
                    $SearchType = " AND P.TypeId='$theTypeId'";
                }
                else {
                    $TypeList .= "<option value='$theTypeId'>$TypeName</option>";
                }
            }
        } while ($TypeRow = mysql_fetch_array($TypeResult));
        $TypeList .= "</select>&nbsp;";
        if ($SearchType == "") $SearchType = " AND P.TypeId='$oneTypeId'";
        $SearchRows .= $SearchType;
    }
    $GysListId = "<input name='StuffCname' type='text' id='StuffCname' size='20' value='配件Id或名称'   oninput='CnameChanged(this)' onfocus=\"this.value=this.value=='配件Id或名称'?'' : this.value;\"  onblur= \"this.value=this.value=='' ? '配件Id或名称' : this.value;\" style='color:#DDD;'><input class='ButtonH_25' type='button'  id='stuffQuery' value='查询'   onclick=\" document.getElementById('tempStuffCname').value=document.getElementById('StuffCname').value;ResetPage(4,5);\" disabled/><input name='tempStuffCname' type='hidden' id='tempStuffCname'/>";
}

$GysList1 = "&nbsp;<span class='ButtonH_25' id='comfirmBatch' value='确认领料'   onclick='batchPass()'>确认领料</span>";

//步骤5：
echo "<table width='$tableWidth' border='0' cellspacing='0'  style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='8' height='40px' class=''>$llTypeList $WorkShopList $GysList  $TypeList </td><td colspan = '3' align='center' class=''>$GysList1</td> <td colspan='3' align='right' class=''><input name='NowInfo' type='text' id='NowInfo' value='$nowInfo' class='text' disabled></td></tr></table>";
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
$mySql = "SELECT SC.POrderId,SC.sPOrderId,SC.Qty AS scQty ,Y.Qty,P.cName,Y.OrderPO,P.TestStandard,P.ProductId 
FROM $DataIn.ck5_llsheet L 
LEFT JOIN $DataIn.yw1_scsheet SC ON SC.sPOrderId = L.sPOrderId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=SC.POrderId 
LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber = Y.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
WHERE 1 $SearchRows  Group BY SC.sPOrderId ORDER BY SC.sPOrderId DESC";
//echo $mySql;
$mainResult = mysql_query($mySql, $link_id);
if ($mainRows = mysql_fetch_array($mainResult)) {
    do {    //主单信息
        $m = 1;
        $POrderId = $mainRows["POrderId"];
        $OrderPO = $mainRows["OrderPO"];
        $cName = $mainRows["cName"];
        $Qty = $mainRows["Qty"];
        $ProductId = $mainRows["ProductId"];
        $TestStandard = $mainRows["TestStandard"];
        include "../admin/Productimage/getOnlyPOrderImage.php";
        $Estate = $mainRows["Estate"];
        $sPOrderId = $mainRows["sPOrderId"];
        $scQty = $mainRows["scQty"];


        //$blPrintIMG="<a href='ck_bl_report1.php?POrderId=$POrderId&mStockId=$mStockId&sPOrderId=$sPOrderId' target='_blank'><img src='../images/printer.png' width='16' height='16'></a>";

        echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
        echo "<td scope='col' class='A0111' width='$Field[$m]' align='center' >$i</td>";//编号
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$sPOrderId</td>";//订单PO
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td scope='col' class='A0101' width='$Field[$m]'>$TestStandard $blPrintIMG</td>";    //产品名称
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$scQty</td>";//工单生产数量
        $unitWidth = $unitWidth - $Field[$m];
        $m = $m + 2;
        echo "<td width='' class='A0101'>";
        //订单产品对应的配件信息
        $j = 1;
        $tabbgColor = ($j + 1) % 2 == 0 ? "bgcolor='#FFF'" : "bgcolor='#EEE'";
        $checkStockSql = mysql_query("SELECT G.OrderQty*(S.Qty/$Qty) AS OrderQty,G.StockId,K.tStockQty,D.StuffId,D.StuffCname,
		        D.Picture,F.Remark,M.Name,P.Forshort,U.Name AS UnitName,U.Decimals  
				FROM $DataIn.yw1_scsheet  S 
				INNER JOIN $DataIn.cg1_stocksheet G ON G.POrderId = S.POrderId 
				LEFT  JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				LEFT  JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT  JOIN $DataIn.staffmain M ON M.Number=G.BuyerId 
				LEFT  JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
				LEFT  JOIN $DataIn.base_mposition F ON F.Id=D.SendFloor
				LEFT  JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				LEFT  JOIN $DataIn.stuffmaintype MT ON MT.Id = T.mainType
				LEFT  JOIN $DataIn.stuffunit U ON U.Id=D.Unit
				WHERE S.sPOrderId='$sPOrderId' AND MT.blsign = 1 AND G.Level=1", $link_id);
        if ($checkStockRow = mysql_fetch_array($checkStockSql)) {
            echo "<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' $tabbgColor>";
            do {
                $Name = $checkStockRow["Name"];
                $Forshort = $checkStockRow["Forshort"];
                $StockId = $checkStockRow["StockId"];
                $StuffId = $checkStockRow["StuffId"];
                $StuffCname = $checkStockRow["StuffCname"];
                $UnitName = $checkStockRow["UnitName"];
                $otherCname = $StuffCname;
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
					LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
					LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
					LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId 
					WHERE  A.mStockId='$StockId'", $link_id);
                $showComboxTable = "";
                $comboxSign = 0;
                if ($CheckComboxRow = mysql_fetch_array($CheckComboxSql)) {
                    $comboxSign = 1;
                    $showComboxTable = "<tr >
		                  <td colspan='10' class='A0100'><div  width='720'><table  cellspacing='1' border='1' align='left' style='margin-left:60px;margin-top:10px;margin-bottom:5px' >";
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
                        $checkcomboxllRow = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$comboxStockId' AND Estate = 0", $link_id));
                        $comboxllQty = $checkcomboxllRow["llQty"];
                        $comboxllQty = $comboxllQty == "" ? 0 : $comboxllQty;

                        $checkcomboxthisllRow = mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$comboxStockId' AND Estate = 1", $link_id));
                        $comboxthisllQty = $checkcomboxthisllRow["llQty"];
                        $comboxthisllQty = $comboxthisllQty == "" ? 0 : $comboxthisllQty;

                        $showComboxTable .= "<tr >
		                  <td  align='center'>$comboxStuffId</td>
		                  <td  onclick='selectAllb1Stuff(this)' onmouseover=\"this.style.cssText='cursor:pointer'\" onmouseover=\"this.style.cssText='cursor:default'\">$comboxStuffCname</td>
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

                $UnionSTR3 = mysql_query("SELECT SUM(Qty) AS thisQty FROM $DataIn.ck5_llsheet WHERE sPOrderId = '$sPOrderId' AND StockId='$StockId' AND Estate='1'", $link_id);
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
                    else {                //未领完
                        $llBgColor = "";
                    }
                }
                //确认操作
                $upIMG = "&nbsp;";
                $upImgclick = "";
                $RemainQty = $OrderQty - $llQty;//未领料数

                $checkRow = "<input type='checkbox' disabled />";
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
                            if (abs($thisQty - $RemainQty) < 0.1 || $thisQty < $RemainQty) {
                                $upIMG = "<img src='../images/Pass.png' width='30' height='30'>";
                                $upImgclick = "onclick=\"passLLdata(this,$sPOrderId,$StockId,'$msgStr')\" ";
                                $checkRow = "<input type='checkbox' name='checkId$i$j' id='checkId$i$j' value='$sPOrderId|$StockId' />";
                            }
                            else {
                                $upIMG = "多领";
                                $upImgclick = "style='color:#F00;'";
                            }
                        }
                    }
                }

                echo "<tr height='30'>";
                $unitFirst = $Field[$m] - 1;
                echo "<td class='A0101' width='$unitFirst' align='center' $bgColor>$j</td>";//序号
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$checkRow</td>";//选项
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$StuffId</td>";    //配件ID
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$StuffCname</td>";//配件名称
                $m = $m + 2;
                echo "<td  class='A0101' width='$Field[$m]' align='center'>$StockId</td>";    //需求流水号
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$blMan</td>";//配料人
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$UnitName</td>";//单位
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$OrderQty </td>";//需领料数
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'><div $llBgColor>$llQty</div></td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$thisQty</td>";    //本次领料
                $m = $m + 2;
                echo "<td  class='A0100' width='' align='center' id='updatetd$i$j' $upImgclick>$upIMG</td>";
                echo "</tr>";
                $j++;
                if ($comboxSign == 1) $j++; //存在子配件多一行
                echo $showComboxTable;
            } while ($checkStockRow = mysql_fetch_array($checkStockSql));
            echo "</table>";
        }
        $i++;
        echo "</td></tr></table>";

    } while ($mainRows = mysql_fetch_array($mainResult));
}
else {
    echo "<table width='$tableWidth' border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word'><tr><td colspan='14' align='center' height='30' class='A0111'><div class='redB' style='background-color: #ffffff'>没有记录!</div></td></tr></table>";
}
?>