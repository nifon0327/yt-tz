<?php
//ewen 2013-03-04 OK
include "../model/subprogram/s1_model_1.php";
//步骤2：需处理
$Th_Col = "采购单号|60|下单日期|70|采购|60|供应商|100|选项|40|序号|40|流水号|60|配件编号|60|非bom配件名称|200|申购数量|60|未收数量|60|单价|60|金额|80|备注|200|申购人|60";
$ColsNumber = 12;
$tableMenuS = 600;
$Page_Size = 100;                            //每页默认记录数量
$Parameter .= ",Jid,$Jid,Bid,$Bid";
//步骤3：
include "../model/subprogram/s1_model_3.php";
//步骤4：可选，其它预设选项
$sSearch = $From != "slist" ? "" : $sSearch;
$sSearch .= $Jid == "" ? "" : " AND A.CompanyId='$Jid'";
$sSearch .= $Bid == "" ? "" : " AND A.BuyerId='$Bid'";

echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>";
echo $CencalSstr;
//步骤5：
include "../model/subprogram/s1_model_5.php";
//步骤6：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 0);
$mySql = "SELECT A.PurchaseID,A.Date,
	B.Mid,B.Id,B.GoodsId,B.Qty,B.Remark,B.Price,
	C.GoodsName,C.BarCode,C.TypeId,C.Attached,C.Unit,
	D.Forshort,E.Name,F.Name AS Buyer,C.CkId
	FROM $DataIn.nonbom6_cgmain A
	LEFT JOIN $DataIn.nonbom6_cgsheet B ON A.Id=B.Mid
	LEFT JOIN $DataPublic.nonbom4_goodsdata C ON C.GoodsId=B.GoodsId
	LEFT JOIN $DataPublic.nonbom3_retailermain D ON D.CompanyId=A.CompanyId 
	LEFT JOIN $DataPublic.staffmain E ON E.Number=B.Operator
	LEFT JOIN $DataPublic.staffmain F ON F.Number=A.BuyerId
	WHERE 1 AND B.rkSign>0 and B.Mid>0 $sSearch ORDER BY A.Id
	";
//echo $mySql;
$mainResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($mainRows = mysql_fetch_array($mainResult)) {
    $tbDefalut = 0;
    $midDefault = "";
    do {
        $m = 1;
        $Mid = $mainRows["Mid"];
        $Buyer = $mainRows["Buyer"];
        $Forshort = $mainRows["Forshort"];
        $PurchaseID = $mainRows["PurchaseID"];
        $Date = $mainRows["Date"];
        $Remark = $mainRows["Remark"];
        //明细资料
        $GoodsId = $mainRows["GoodsId"];
        if ($GoodsId != "") {
            //$Forshort=$mainRows["Forshort"];
            $Id = $mainRows["Id"];
            $GoodsName = $mainRows["GoodsName"];
            $Qty = $mainRows["Qty"];
            $Name = $mainRows["Name"];
            $Price = $mainRows["Price"];
            $CkId = $mainRows["CkId"];
            $Amount = number_format($Qty * $Price, 2);
            //收货数量计算
            $ReQty_Temp = mysql_query("SELECT SUM(Qty) AS a1 FROM $DataIn.nonbom7_insheet WHERE cgId='$Id'", $link_id);;
            $ReQty = mysql_result($ReQty_Temp, 0, "a1");
            $Unreceive = $Qty - $ReQty;

            //收货数量计算
            if ($tbDefalut == 0 && $midDefault == "") {//首行
                //输出并行列
                echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
                echo "<tr>";
                echo "<td class='A0111' width='$Field[$m]' align='center'>$PurchaseID</td>";
                $unitWiath = $unitWiath - $Field[$m];
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$Date</td>";
                $unitWiath = $unitWiath - $Field[$m];
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$Buyer</td>";
                $unitWiath = $unitWiath - $Field[$m];
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$Forshort</td>";
                $unitWiath = $unitWiath - $Field[$m];
                $m = $m + 2;
                echo "<td class='A0101' width='$unitWiath'>";
                $midDefault = $Mid;
            }
            $PropertyResult = mysql_query("SELECT Id FROM $DataPublic.nonbom4_goodsproperty WHERE GoodsId=$GoodsId AND Property=7", $link_id);
            if ($PropertyRow = mysql_fetch_array($PropertyResult)) {
                $PropertySign = 1;
            }
            else  $PropertySign = 0;
            $ValueSTR = "$Id^^$GoodsId^^$GoodsName^^$Qty^^$Unreceive^^$PropertySign^^$CkId";
            if ($Unreceive == 0) {
                $chooseStr = "&nbsp;";
            }
            else {
                $chooseStr = "<input name='checkid[$i]' type='checkbox' id='checkid$i' value='$ValueSTR' disabled>";
            }
            //输出明细|供应商|100|流水号|90|非bom配件编号|100|非bom配件名称|190|申购数量|60|未收数量|60|申购人|60";
            if ($midDefault != "" && $midDefault == $Mid) {//同属于一个主ID，则依然输出明细表格
                $m = 9;
                echo "<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
                echo "<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
                $unitFirst = $Field[$m] - 2;
                echo "<td class='A0101' align='center' width='$unitFirst'>$chooseStr</td>";
                $m = $m + 2;
                echo "<td class='A0101'  align='center' width='$Field[$m]'>$i</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$Id</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$GoodsId</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$GoodsName</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$Qty</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$Unreceive</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$Price</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$Amount</td>";
                $m = $m + 2;
                echo "<td class='A0100' width='$Field[$m]' align='left'>$Remark</td>";
                $m = $m + 2;
                echo "<td class='A0100' width='$Field[$m]' align='right'>$Name</td>";
                echo "</tr></table>";
                $i++;
            }
            else {
                //新行开始
                echo "</td></tr></table>";//结束上一个表格
                //输出并行列
                echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'>";
                echo "<tr>";
                echo "<td class='A0111' width='$Field[$m]' align='center'>$PurchaseID</td>";
                $unitWiath = $unitWiath - $Field[$m];
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$Date</td>";
                $unitWiath = $unitWiath - $Field[$m];
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$Buyer</td>";
                $unitWiath = $unitWiath - $Field[$m];
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$Forshort</td>";
                $unitWiath = $unitWiath - $Field[$m];
                $m = $m + 2;
                echo "<td class='A0101' width='$unitWiath'>";
                $midDefault = $Mid;
                //输出明细
                echo "<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
                echo "<tr onmousedown='chooseRow(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
                $unitFirst = $Field[$m] - 2;
                echo "<td class='A0101' align='center' width='$unitFirst'>$chooseStr</td>";
                $m = $m + 2;
                echo "<td class='A0101' align='center' width='$Field[$m]'>$i</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$Id</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='center'>$GoodsId</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]'>$GoodsName</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$Qty</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$Unreceive</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$Price</td>";
                $m = $m + 2;
                echo "<td class='A0101' width='$Field[$m]' align='right'>$Amount</td>";
                $m = $m + 2;
                echo "<td class='A0100' width='$Field[$m]' align='left'>$Remark</td>";
                $m = $m + 2;
                echo "<td class='A0100' width='$Field[$m]' align='right'>$Name</td>";
                echo "</tr></table>";
                $i++;
            }
        }
    } while ($mainRows = mysql_fetch_array($mainResult));
    echo "</tr></table>";
}
else {
    noRowInfo($tableWidth);
}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
?>