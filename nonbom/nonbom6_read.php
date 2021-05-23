<?php
//ewen 2013-03-22 OK
include "../model/modelhead.php";
?>
<script>
    function selectMonth() {
        WdatePicker({dateFmt: 'yyyy-MM'});
    }
</script>
<?php
//步骤2：需处理
$tableMenuS = 500;
ChangeWtitle("$SubCompany 非bom配件采购单");
$funFrom = "nonbom6";
$From = $From == "" ? "read" : $From;
$MergeRows = 13;
$sumCols = "8,10";
//$Th_Col = "请款选项|60|操作|40|下单日期|100|采购|80|供应商|180|采购单号|100|采购备注|60|合同|50|发票|80|发票备注|100|预付金额|60|货款|70|已请款|70|选项|60|行号|30|配件编码|50|配件名称|300|规格|150|申购<br>备注|30|货币|30|增值税率|60|单价|60|申购数量|60|单位|30|金额|60|收货数|50|欠数|50|在库|60|采购库存|60|最低库存|60|请款<br>状态|40|记录<br>状态|40|收货<br>状态|40|申购时间|80|申购人|50";
$Th_Col = "请款选项|60|操作|80|下单日期|100|采购|80|供应商|180|采购单号|100|采购备注|60|预付金额|60|货款|70|已请款|70|选项|60|行号|30|客户项目|120|配件编码|50|配件名称|250|规格|150|申购备注|80|货币|30|增值税率|60|单价|60|申购数量|60|单位|30|金额|60|收货数|50|欠数|50|在库|60|采购库存|60|最低库存|60|请款<br>状态|40|记录<br>状态|40|收货<br>状态|40|申购时间|80|申购人|50";

//必选，分页默认值
$Pagination = $Pagination == "" ? 1 : $Pagination;    //默认分页方式:1分页，0不分页
$Page_Size = 200;                            //每页默认记录数量
$ActioToS = "1,3,27,154,155,7,8,179";
//步骤3：
$nowWebPage = $funFrom . "_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框
if ($From != "slist") {    //非查询：过滤采购、结付方式、供应商、月份
    $SearchRows = "";
    //月份
    $checkResult = mysql_query("SELECT DATE_FORMAT(A.Date,'%Y-%m') AS Month FROM $DataIn.nonbom6_cgmain A  GROUP BY DATE_FORMAT(A.Date,'%Y-%m') ORDER BY A.Date DESC", $link_id);
    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='chooseDate' id='chooseDate' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>选择月份</option>";
        do {
            $Temp_Month = $checkRow["Month"];
            if ($Temp_Month == $chooseDate) {
                echo "<option value='$Temp_Month' selected>$Temp_Month</option>";
                $SearchRows = " AND DATE_FORMAT(E.Date,'%Y-%m')='$Temp_Month'";
            } else {
                echo "<option value='$Temp_Month'>$Temp_Month</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }
    //采购
    $checkResult = mysql_query("SELECT E.BuyerId,C.Name 
							   FROM $DataIn.nonbom6_cgmain E 
							   LEFT JOIN $DataIn.staffmain C ON C.Number=E.BuyerId 
							   WHERE 1 $SearchRows GROUP BY E.BuyerId ORDER BY C.Name", $link_id);
    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='BuyerId' id='BuyerId' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>选择采购</option>";
        do {
            $Temp_BuyerId = $checkRow["BuyerId"];
            $Temp_Name = $checkRow["Name"];
            if ($Temp_BuyerId == $BuyerId) {
                echo "<option value='$Temp_BuyerId' selected>$Temp_Name</option>";
                $SearchRows .= " AND E.BuyerId='$Temp_BuyerId'";
            } else {
                echo "<option value='$Temp_BuyerId'>$Temp_Name</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }

    //供应商
    $checkResult = mysql_query("SELECT Ee.Letter,Ee.Forshort,Ee.CompanyId 
							   FROM $DataIn.nonbom6_cgmain E 
							   LEFT JOIN $DataIn.nonbom3_retailermain Ee ON Ee.CompanyId=E.CompanyId
							   WHERE 1 $SearchRows GROUP BY Ee.Forshort ORDER BY Ee.Letter", $link_id);
    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>选择供应商</option>";
        do {
            $Temp_CompanyId = $checkRow["CompanyId"];
            $Temp_Name = $checkRow["Letter"] . "-" . $checkRow["Forshort"];
            if ($Temp_CompanyId == $CompanyId) {
                echo "<option value='$Temp_CompanyId' selected>$Temp_Name</option>";
                $SearchRows .= " AND E.CompanyId='$Temp_CompanyId'";
            } else {
                echo "<option value='$Temp_CompanyId'>$Temp_Name</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }

    $checkResult = mysql_query("SELECT  E.mainType,Dm.Name  
							   FROM $DataIn.nonbom6_cgmain E 
	                           LEFT JOIN $DataIn.nonbom1_maintype Dm  ON Dm.Id=E.mainType 
							   WHERE 1 $SearchRows AND E.mainType>0 GROUP BY E.mainType", $link_id);

    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='mainType' id='mainType' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>选择主分类</option>";
        do {
            $mainTypeTemp = $checkRow["mainType"];
            $NameTemp = $checkRow["Name"];
            if ($mainType == $mainTypeTemp) {
                echo "<option value='$mainTypeTemp' selected>$NameTemp </option>";
                //$SearchRows.=" AND Dd.mainType='$mainTypeTemp'";
                $SearchRows .= " AND E.mainType='$mainTypeTemp'";
            } else {
                echo "<option value='$mainTypeTemp'>$NameTemp</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }
    //子分类
    $checkResult = mysql_query("SELECT  Dd.Id,Dd.TypeName  
							   FROM $DataIn.nonbom6_cgmain E
							   LEFT JOIN $DataIn.nonbom6_cgsheet F ON E.Id=F.Mid 
							   LEFT JOIN $DataIn.nonbom4_goodsdata D ON D.GoodsId=F.GoodsId 
	                           LEFT JOIN $DataIn.nonbom2_subtype Dd ON Dd.Id=D.TypeId
							   WHERE 1 $SearchRows AND Dd.Id>0 GROUP BY Dd.Id", $link_id);
    if ($checkRow = mysql_fetch_array($checkResult)) {
        echo "<select name='subType' id='subType' onchange='ResetPage(this.name)'>";
        echo "<option value='' selected>选择子分类</option>";
        do {
            $subTypeTemp = $checkRow["Id"];
            $NameTemp = $checkRow["TypeName"];
            if ($subType == $subTypeTemp) {
                echo "<option value='$subTypeTemp' selected>$NameTemp </option>";
                $SearchRows .= " AND D.TypeId='$subTypeTemp'";
            } else {
                echo "<option value='$subTypeTemp'>$NameTemp</option>";
            }
        } while ($checkRow = mysql_fetch_array($checkResult));
        echo "</select>&nbsp;";
    }
    $invoiceStr = "invoiceEstate" . $invoiceEstate;
    $$invoiceStr = "selected";
    echo "<select name='invoiceEstate' id='invoiceEstate' onchange='ResetPage(this.name)'>";
    echo "<option value='0' $invoiceEstate0>全部</option>";
    echo "<option value='1' $invoiceEstate1>有发票</option>";
    echo "<option value='2' $invoiceEstate2>无发票</option>";
    echo "</select>";
    if ($invoiceEstate == 1) {
        $SearchRows .= " AND I.Id>0";
    } else if ($invoiceEstate == 2) {
        $SearchRows .= " AND I.Id IS NULL";
    }


    //$qkEstate= $qkEstate==""?1:$qkEstate;
    $qkEstateStr = "qkEstatestr" . $qkEstate;
    $$qkEstateStr = "selected";
    echo "<select name='qkEstate' id='qkEstate' onchange='ResetPage(this.name)'>";
    echo "<option value='0' $qkEstatestr0>请款状态</option>";
    echo "<option value='1' $qkEstatestr1>未请款</option>";
    echo "<option value='2' $qkEstatestr2>请款中</option>";
    echo "</select>";

}
//检查进入者是否采购
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr 请款月份:<input id='qkMonth' name='qkMonth' type='text' size='16' onfocus='selectMonth()' readonly>";
//步骤5：
include "../model/subprogram/read_model_5.php";
//步骤6：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 0);
switch ($qkEstate) {
    case 1:
        $mySql = "SELECT E.Date AS cgDate,E.PurchaseID,E.Remark AS mainRemark,E.BuyerId,E.Attached AS ContractFile,
			F.Id,F.Mid,F.fromMid,F.GoodsId,F.Qty,F.Price,F.Remark,F.ReturnReasons,F.rkSign,F.Estate,F.Locks,F.Date,T.Forshort AS Company,
			D.GoodsName,D.GoodSpec,D.Attached,D.Unit,Dd.TypeName,Ee.Forshort,Ee.CompanyId,Ff.Name,Gg.wStockQty,Gg.oStockQty,Gg.mStockQty,C.Symbol,F.qkId,H.Name AS AddTaxValueName
		FROM $DataIn.nonbom6_cgmain E
		LEFT JOIN $DataIn.nonbom6_cgsheet F ON E.Id=F.Mid
		LEFT JOIN $DataIn.nonbom4_goodsdata D ON D.GoodsId=F.GoodsId 
		LEFT JOIN $DataIn.nonbom2_subtype Dd ON Dd.Id=D.TypeId
		LEFT JOIN $DataIn.nonbom3_retailermain Ee ON Ee.CompanyId=E.CompanyId 
		LEFT JOIN $DataIn.staffmain Ff ON Ff.Number=F.Operator 
		LEFT JOIN $DataIn.nonbom5_goodsstock Gg ON Gg.GoodsId=F.GoodsId	
		LEFT JOIN $DataIn.currencydata C ON C.Id=Ee.Currency  
		LEFT JOIN $DataIn.provider_addtax H ON H.Id = F.AddTaxValue
		LEFT JOIN $DataIn.nonbom6_invoice I ON I.cgMid = E.Id 
		LEFT JOIN $DataIn.trade_object T ON T.Id = D.TradeId
		WHERE 1 $SearchRows  AND NOT EXISTS( SELECT Q.Mid From  $DataIn.nonbom11_qksheet Q WHERE Q.CgMid=E.Id)  ORDER BY E.Date DESC,E.Id DESC";
        break;
    case 2:
        $mySql = "SELECT E.Date AS cgDate,E.PurchaseID,E.Remark AS mainRemark,E.BuyerId,E.Attached AS ContractFile,
			F.Id,F.Mid,F.fromMid,F.GoodsId,F.Qty,F.Price,F.Remark,F.ReturnReasons,F.rkSign,F.Estate,F.Locks,F.Date,T.Forshort AS Company,
			D.GoodsName,D.GoodSpec,D.Attached,D.Unit,Dd.TypeName,Ee.Forshort,Ee.CompanyId,Ff.Name,Gg.wStockQty,Gg.oStockQty,Gg.mStockQty,C.Symbol ,F.qkId,H.Name AS AddTaxValueName
		FROM $DataIn.nonbom6_cgmain E
		LEFT JOIN $DataIn.nonbom6_cgsheet F ON E.Id=F.Mid
		LEFT JOIN $DataIn.nonbom4_goodsdata D ON D.GoodsId=F.GoodsId 
		LEFT JOIN $DataIn.nonbom2_subtype Dd ON Dd.Id=D.TypeId
		LEFT JOIN $DataIn.nonbom3_retailermain Ee ON Ee.CompanyId=E.CompanyId 
		LEFT JOIN $DataIn.staffmain Ff ON Ff.Number=F.Operator 
		LEFT JOIN $DataIn.nonbom5_goodsstock Gg ON Gg.GoodsId=F.GoodsId	
		LEFT JOIN $DataIn.currencydata C ON C.Id=Ee.Currency 
		LEFT JOIN $DataIn.provider_addtax H ON H.Id = F.AddTaxValue 
		LEFT JOIN $DataIn.nonbom6_invoice I ON I.cgMid = E.Id 
		LEFT JOIN $DataIn.trade_object T ON T.Id = D.TradeId
		WHERE 1 $SearchRows  AND EXISTS( SELECT Q.Mid From  $DataIn.nonbom11_qksheet Q WHERE  Q.Estate>0 AND Q.CgMid=E.Id) ORDER BY E.Date DESC,E.Id DESC";
        break;
    default:
        $mySql = "SELECT E.Date AS cgDate,E.PurchaseID,E.Remark AS mainRemark,E.BuyerId,E.Attached AS ContractFile,
			F.Id,F.Mid,F.fromMid,F.GoodsId,F.Qty,F.Price,F.Remark,F.ReturnReasons,F.rkSign,F.Estate,F.Locks,F.Date,T.Forshort AS Company,
			D.GoodsName,D.GoodSpec,D.Attached,D.Unit,Dd.TypeName,Ee.Forshort,Ee.CompanyId,Ff.Name,Gg.wStockQty,Gg.oStockQty,Gg.mStockQty,C.Symbol ,F.qkId,H.Name AS AddTaxValueName
		FROM $DataIn.nonbom6_cgmain E
		LEFT JOIN $DataIn.nonbom6_cgsheet F ON E.Id=F.Mid
		LEFT JOIN $DataIn.nonbom4_goodsdata D ON D.GoodsId=F.GoodsId 
		LEFT JOIN $DataIn.nonbom2_subtype Dd ON Dd.Id=D.TypeId
		LEFT JOIN $DataIn.nonbom3_retailermain Ee ON Ee.CompanyId=E.CompanyId 
		LEFT JOIN $DataIn.staffmain Ff ON Ff.Number=F.Operator 
		LEFT JOIN $DataIn.nonbom5_goodsstock Gg ON Gg.GoodsId=F.GoodsId	
		LEFT JOIN $DataIn.currencydata C ON C.Id=Ee.Currency 
		LEFT JOIN $DataIn.provider_addtax H ON H.Id = F.AddTaxValue  
		LEFT JOIN $DataIn.nonbom6_invoice I ON I.cgMid = E.Id 
		LEFT JOIN $DataIn.trade_object T ON T.Id = D.TradeId
		WHERE 1 $SearchRows  ORDER BY E.Date DESC,E.Id DESC";
        break;
}
//echo $mySql;
$mainResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($mainRows = mysql_fetch_array($mainResult)) {
    $tbDefalut = 0;
    $midDefault = "";
    $Dir = anmaIn("download/nonbom/", $SinkOrder, $motherSTR);
    $DCPath = anmaIn("download/nonbom_contract/", $SinkOrder, $motherSTR);
    $InvoicePath = anmaIn("download/nonbom_cginvoice/", $SinkOrder, $motherSTR);
    do {
        $m = 1;
        $qkChoose = "&nbsp;";
        $LockRemark = $rkBgColor = $wsBgColor = "";
        $cwSign = 1;
        //主单信息
        $Mid = $mainRows["Mid"];
        //echo "Mid:$Mid";
        $cgDate = substr($mainRows["cgDate"], 0, 10);
        $ContractFile = $mainRows["ContractFile"];

        if ($ContractFile == 1) {
            $f = anmaIn("$Mid" . '.pdf', $SinkOrder, $motherSTR);
            $ContractSTR = "<a href=\"../admin/openorload.php?d=$DCPath&f=$f&Type=&Action=6\" target=\"download\" style='CURSOR: pointer; color:#FF6633'>View</a>";
        } else {
            $ContractSTR = "&nbsp;";
        }

        $PurchaseID = $mainRows["PurchaseID"];
        $Company = $mainRows["Company"];


        $CheckRow = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.nonbom6_invoice 
			     WHERE cgMid ='$Mid'", $link_id));
        $InvoiceFile = $CheckRow["InvoiceFile"];
        $InvoiceNo = $CheckRow["InvoiceNo"];
        $InvoiceRemark = $CheckRow["Remark"];
        $InvoiceAmount = $CheckRow["InvoiceAmount"];
        $InvoiceDate = $CheckRow["InvoiceDate"];

        $InvoiceRemark = $InvoiceRemark == "" ? "&nbsp;" : "<img src='../images/remark.gif' title='$InvoiceRemark' width='18' height='18'>";
        if ($InvoiceFile != "") {
            $f2 = anmaIn($InvoiceFile, $SinkOrder, $motherSTR);
            $InvoiceFile = "<a href=\"../admin/openorload.php?d=$InvoicePath&f=$f2&Type=&Action=6\" target=\"download\" style='CURSOR: pointer; color:#FF6633'>$InvoiceNo</a><img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom6_invoiceupdate\",$Mid)' src='../images/edit.gif' title='更新发票资料' width='13' height='13'>";

        } else {
            $InvoiceFile = "&nbsp;";
        }

        //预付订金
        $checkDj = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS djAmount,Estate AS djEstate FROM $DataIn.nonbom11_djsheet WHERE PurchaseID='$PurchaseID' AND Estate='0'", $link_id));


        $checkHk = mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty*Price),0) AS HKAmount FROM $DataIn.nonbom6_cgsheet WHERE Mid='$Mid' ", $link_id));
        $HKAmount = sprintf("%.2f", $checkHk["HKAmount"]);   //货款


        $checkHavedHk = mysql_fetch_array(mysql_query("SELECT IFNULL(Id,-1) as HkID,IFNULL(SUM(Amount),0) AS HavedAmount,IFNULL(SUM(IF(Estate=3,Amount,0)),0) AS HavePassAmount ,IFNULL(SUM(IF(Estate=0,Amount,0)),0) AS CWAmount  
													 FROM $DataIn.nonbom11_qksheet WHERE CgMid='$Mid' ", $link_id));
        //echo "SELECT IFNULL(SUM(Amount),0) AS HavedAmount,IFNULL(SUM(IF(Estate=0,Amount,0)),0) AS CWAmount  FROM $DataIn.nonbom11_qksheet WHERE CgMid='$Mid'";
        $HavedAmount = sprintf("%.2f", $checkHavedHk["HavedAmount"]);  //已请款：连接请款记录
        $HavePassAmount = $checkHavedHk["HavePassAmount"] == 0 ? "&nbsp;" : sprintf("%.2f", $checkHavedHk["HavePassAmount"]);  //请款通过
        $CWAmount = sprintf("%.2f", $checkHavedHk["CWAmount"]);  //已结付
        $HkID = $checkHavedHk["HkID"];  //已结付ID，有些是0的，所以要判定
        //echo "$Mid:$HkID";

        if ($HavedAmount < $HKAmount) {//全部请款完后不能再请款。。。。
            $qkChoose = "<input name='checkqkid[]' type='checkbox' id='checkqkid$i' value='$Mid' >";
        }
        if ($CWAmount == $HKAmount && $HkID > 0) {  //表示全部结付完成
            $HKAmount = "<div class='greenB'>$HKAmount</div>";
            $HavedAmount = "<a href='nonbom6_qkview.php?Mid=$Mid' target='_blank'>" . "<div class='greenB'  >$HavedAmount</div>" . "</a>";  //已请款：连接请款记录
            $LockRemark = "记录已经结付，强制锁定操作！";
            $cwSign = 0;
        } else {
            if ($CWAmount > 0) {
                $HKAmount = "<div class='yellowB' title='已结付:$CWAmount' >$HKAmount </div>"; //表示部分结付
                $LockRemark = "记录已部分结付，强制锁定操作！";
                $cwSign = 0;
            }
        }

        $HaveqkAmount = $HavedAmount - $CWAmount;
        //echo "$HaveqkAmount=$HavedAmount-$CWAmount";
        if ($HaveqkAmount > 0) {
            if (sprintf("%.2f", $HaveqkAmount) == $HavePassAmount) {  //全部通过，但未结付
                $HavedAmount = "<a href='nonbom6_qkview.php?Mid=$Mid' target='_blank'>" . "<div class='yellowB' title='通过，但未结付' >$HavedAmount</div>" . "</a>";  //已请款：连接请款记录
                $LockRemark = "记录已请款通过，强制锁定操作！";
                $cwSign = 3;
            } else {
                $HavedAmount = "<a href='nonbom6_qkview.php?Mid=$Mid' target='_blank'>" . "<div class='blueB' title='请款中.....'' >$HavedAmount</div>" . "</a>";  //已请款：连接请款记录
                $LockRemark = "记录请款中，强制锁定操作！";
                $cwSign = 2;

            }
        } else {
            if ($HavedAmount == '0.00') {
                $HavedAmount = '&nbsp;';
            }
        }

        if ($CWAmount == 0) $CWAmount = '&nbsp;';
        $MidSTR = anmaIn($Mid, $SinkOrder, $motherSTR);
        $PurchaseID = "<a href='nonbom6_view.php?f=$MidSTR' target='_blank'>$PurchaseID</a>";
        $mainRemark = $mainRows["mainRemark"] == "" ? "&nbsp;" : "<img src='../images/remark.gif' title='$mainRows[mainRemark]' width='18' height='18'>";
        $Operator = $mainRows["BuyerId"];
        include "../model/subprogram/staffname.php";
        $upMian = "<img location.href=\"#\"' style='CURSOR: pointer' onclick='upMainData(\"nonbom6_upmain\",$Mid)' src='../images/edit.gif' title='更新采购主单资料' width='16' height='16'>";
         $upMian .="&nbsp;&nbsp;<a href='nonbom_purchase_export.php?poid=".$Mid."'><img src='../images/downfile.png' width='18' height='18' style='CURSOR:pointer' title='导出采购订单'/></a>";
        //明细资料
        $GoodsId = $mainRows["GoodsId"];
        if ($GoodsId != "") {
            $checkidValue = $mainRows["Id"];
            $GoodsId = $mainRows["GoodsId"];
            $fromMid = $mainRows["fromMid"];
            if ($fromMid > 0) {
                $MidSTR = anmaIn($fromMid, $SinkOrder, $motherSTR);
                $checkSql2 = mysql_fetch_array(mysql_query("SELECT PurchaseID FROM $DataIn.nonbom6_cgmain WHERE Id='$fromMid' ", $link_id));
                $PurchaseID = $checkSql2["PurchaseID"];
                $fromMid = "<a href='nonbom6_view.php?f=$MidSTR' target='_blank'>($PurchaseID)</a>";
            } else {
                $fromMid = "&nbsp;";
            }

            $GoodsName = $mainRows["GoodsName"];
            $GoodSpec = $mainRows["GoodSpec"];
            $Attached = $mainRows["Attached"];
            $Remark = $mainRows["Remark"] == "" ? "&nbsp;" : "<img src='../images/remark.gif' title='$mainRows[Remark]' width='18' height='18'>";
            $Unit = $mainRows["Unit"];
            $Symbol = $mainRows["Symbol"];
            $Price = $mainRows["Price"];
            $AddTaxValueName = $mainRows["AddTaxValueName"];
            $Qty = del0($mainRows["Qty"]);
            $Amount = $Qty * $Price;
            //入库数量
            $rkTemp = mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.nonbom7_insheet WHERE GoodsId='$GoodsId' AND cgId='$checkidValue'", $link_id);
            $rkQty = mysql_result($rkTemp, 0, "Qty");
            $rkQty = $rkQty == "" ? 0 : del0($rkQty);
            $wsQty = $Qty - $rkQty;
            if ($rkQty == $Qty) {
                $rkBgColor = "class='greenB'";
                $rkSign = "<sapn class='greenB'>已收货</span>";
                $rkQty = "<a href='nonbom7_list.php?cgId=$checkidValue' target='_blank' style='color:#093'>$rkQty</a>";
                //更新入库标记
                if ($mainRows["rkSign"] > 0) {
                    $UprkSignSql = "UPDATE $DataIn.nonbom6_cgsheet SET rkSign='0' WHERE Id='$checkidValue' ";
                    $UprkSignResult = mysql_query($UprkSignSql, $link_id);
                }
            } else {
                $rkBgColor = "class='redB'";
                $wsBgColor = "class='redB'";
                if ($rkQty == 0) {
                    $rkSign = "<sapn class='redB'>未收货</span>";
                    $rkQty = "&nbsp;";
                    $rkSignVal = 1;
                    if ($cwSign > 0) $LockRemark = "";
                } else {
                    $rkSign = "<sapn class='yellowB'>部分收货</span>";
                    $rkQty = "<a href='nonbom7_list.php?cgId=$checkidValue' target='_blank' style='color:#F00'>$rkQty</a>";
                    $rkSignVal = 2;
                }
                //更新入库标记
                if ($mainRows["rkSign"] == 0) {
                    $UprkSignSql = "UPDATE $DataIn.nonbom6_cgsheet SET rkSign='$rkSignVal' WHERE Id='$checkidValue' ";
                    $UprkSignResult = mysql_query($UprkSignSql, $link_id);
                }
            }
            $wsQty = $wsQty == 0 ? "&nbsp;" : $wsQty;
            $wStockQty = del0($mainRows["wStockQty"]);
            $oStockQty = del0($mainRows["oStockQty"]);
            $mStockQty = del0($mainRows["mStockQty"]);
            $Attached = $mainRows["Attached"];
            if ($Attached == 1) {
                $Attached = $GoodsId . ".jpg";
                $Attached = anmaIn($Attached, $SinkOrder, $motherSTR);
                $GoodsName = "<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\")' style='CURSOR: pointer;color:#FF6633'>$GoodsName</span>";
            }
            include "../model/subprogram/good_Property.php";//非BOM配件属性
            $Locks = $unLocks == 1 ? 1 : $mainRows["Locks"];

            $qkId = $mainRows["qkId"];
            if ($qkId > 0) $qkEstate = "<span class='greenB'>已请款</span>";
            else $qkEstate = "&nbsp;";


            $Estate = $mainRows["Estate"];
            switch ($Estate) {
                case 1:
                    $Estate = "<span class='greenB'>已审核</span>";
                    break;
                case 4://审核退回
                    $ReturnReasons = $mainRows["ReturnReasons"] == "" ? "审核退回:未填写退回原因" : "审核退回:" . $mainRows["ReturnReasons"];
                    $Estate = "<img src='../images/warn.gif' title='$ReturnReasons' width='18' height='18'>";
                    break;
                default:
                    $LockRemark = "记录有更新，更新审核中，锁定操作！";
                    $Estate = "<span class='redB'>需审核</span>";
                    break;

            }
            $Forshort = $mainRows["Forshort"];
            $Date = $mainRows["Date"];
            $Name = $mainRows["Name"];

            $CompanyId = $mainRows["CompanyId"];
            //加密
            $CompanyId = anmaIn($CompanyId, $SinkOrder, $motherSTR);
            $Forshort = "<a href='nonbom3_view.php?d=$CompanyId' target='_blank'>$Forshort</a>";
            //历史单价
            $Price = "<a href='nonbom4_history.php?GoodsId=$GoodsId' target='_blank'>$Price</a>";
            //配件分析
            $GoodsId = "<a href='nonbom4_report.php?GoodsId=$GoodsId' target='_blank'>$GoodsId</a>";
            //预付金额
            $djAmount = $checkDj["djAmount"];
            $djEstate = $checkDj["djEstate"];
            if ($djAmount > 0) {
                if ($Amount == $djAmount) {
                    $djAmount = "<span class='greenB'>$djAmount</span>";
                } else {
                    $djAmount = "<span class='redB'>$djAmount</span>";
                }
            } else {
                $djAmount = "&nbsp;";
            }
            if ($Locks == 0) {//锁定状态:A一种是可以操作记录（分权限）；B一种是不可操作记录（不分权限）
                if ($Keys & mLOCK) {
                    if ($LockRemark != "") {//财务强制锁定
                        $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'>";
                    } else {
                        $Choose = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/lock.png' width='15' height='15'>";
                    }
                } else {        //A2：无权限对锁定记录操作
                    $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'>";
                }
            } else {
                if (($BuyerId == $Login_P_Number && ($Keys & mUPDATE || $Keys & mDELETE)) || $Keys & mLOCK) {//有权限
                    if ($LockRemark != "") {
                        $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='$LockRemark' width='15' height='15'/>";
                    } else {
                        $Choose = "<input name='checkid[]' type='checkbox' id='checkid$i' value='$checkidValue' disabled><img src='../images/unlock.png' width='15' height='15'/>";
                    }
                } else {//无权限
                    $Choose = "&nbsp;&nbsp;&nbsp;<img src='../images/lock.png' title='锁定操作!' width='15' height='15'/>";
                }
            }
            $Sid = anmaIn($StockId, $SinkOrder, $motherSTR);

            if ($tbDefalut == 0 && $midDefault == "") {//首行
                //并行列
                echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
                echo "<td scope='col' class='A0111' width='$Field[$m]' align='center'>$qkChoose</td>";        //更新
                $unitWidth = $tableWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$upMian</td>";
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$cgDate</td>";            //
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Operator</td>";        //采购
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";        //供应商
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PurchaseID</td>";        //采购单号
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mainRemark</td>";        //备注
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
//                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ContractSTR</td>";        //合同
//                $unitWidth = $unitWidth - $Field[$m];
//                $m = $m + 2;
//                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceFile</td>";        //发票
//                $unitWidth = $unitWidth - $Field[$m];
//                $m = $m + 2;
//                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceRemark</td>";        //备注
//                $unitWidth = $unitWidth - $Field[$m];
//                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$djAmount</td>";      //定金
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;

                echo "<td scope='col' class='A0101' width='$Field[$m]' align='right'>$HKAmount</td>";    //货款
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='right'>$HavedAmount </td>";    //已付款


                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;

                echo "<td width='' class='A0101'>";
                $midDefault = $Mid;
            }
            if ($midDefault != "" && $midDefault == $Mid) {//同属于一个主ID，则依然输出明细表格
                $m = 21;
                echo "<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
                echo "<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
                $unitFirst = $Field[$m] - 1;
                echo "<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose  $showPurchaseorder</td>";//选项
                //echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR> $showPurchaseorder</td>";//选项
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$j</td>";                //序号
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$Company</td>";                //客户
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$GoodsId</td>";        //配件ID
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]'>$GoodsName $fromMid </td>";                    //配件名称
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]'>$GoodSpec </td>";                    //配件名称
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$Remark</td>";        //采购备注
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$Symbol</td>";            //货币
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$AddTaxValueName</td>";
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$Price</td>";            //单价
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";            //采购数量
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$Unit</td>";            //单位
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";        //金额
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$rkQty</div></td>";        //已收货数量
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'><div $wsBgColor>$wsQty</div></td>";        //欠数
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$wStockQty</td>";        //在库
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$oStockQty</td>";            //采购库存
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$mStockQty</td>";        //最低库存
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$qkEstate</td>";        //审核状态
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$Estate</td>";        //审核状态
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$rkSign</td>";        //收货状态
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]'>$Date</td>";            //申购日期
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$Name</td>";        //申购人
                echo "</tr></table>";
                $i++;
                $j++;
            } else {
                //新行开始
                echo "</td></tr></table>";//结束上一个表格
                //并行列
                echo "<table width='$tableWidth' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word; border:1px solid #e7e7e7' bgcolor='#f5f5f5'><tr>";
                echo "<td scope='col' class='A0111' width='$Field[$m]' align='center'>$qkChoose</td>";        //更新
                $unitWidth = $tableWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$upMian</td>";
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$cgDate</td>";            //下单日期
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Operator</td>";        //采购
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$Forshort</td>";        //供应商
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$PurchaseID</td>";        //采购单号
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$mainRemark</td>";        //采购备注
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
//                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$ContractSTR</td>";        //合同
//                $unitWidth = $unitWidth - $Field[$m];
//                $m = $m + 2;
//                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceFile</td>";        //备注
//                $unitWidth = $unitWidth - $Field[$m];
//                $m = $m + 2;
//                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$InvoiceRemark</td>";        //备注
//                $unitWidth = $unitWidth - $Field[$m];
//                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='center'>$djAmount</td>";            //定金
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='right'>$HKAmount</td>";            //货款
                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td scope='col' class='A0101' width='$Field[$m]' align='right'>$HavedAmount </td>";            //已付款
                //$unitWidth=$unitWidth-$Field[$m];	$m=$m+2;
                //echo"<td scope='col' class='A0101' width='$Field[$m]' align='center'>$cwSign</td>";		//货款状态


                $unitWidth = $unitWidth - $Field[$m];
                $m = $m + 2;
                echo "<td width='' class='A0101'>";
                $midDefault = $Mid;
                echo "<table width='100%' border='0' cellspacing='0' id='ListTable$i' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word' bgcolor='#FFFFFF'>";
                echo "<tr onmousedown='ClickKeyCheck(this.parentNode,$i,\"click\",\"$theDefaultColor\",\"$thePointerColor\",\"$theMarkColor\",\"webpage_read\",$ColsNumber);' 
					onmouseover='setPointer(this.parentNode,$i,\"over\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);' 
					onmouseout='setPointer(this.parentNode,$i,\"out\",\"$theDefaultColor\",\"$thePointerColor\",\"\",$ColsNumber);'>";
                $unitFirst = $Field[$m] - 1;
                echo "<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR>$Choose $showPurchaseorder</td>";//选项
                //echo"<td class='A0001' width='$unitFirst'  align='center' $tdBGCOLOR> $showPurchaseorder</td>";//选项
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$j</td>";                //序号
                //echo"<td class='A0001' width='$unitFirst' align='center'>$j</td>";				//序号
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$Company</td>";        //客户
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$GoodsId</td>";        //配件ID
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]'>$GoodsName $fromMid </td>";                    //配件名称
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]'>$GoodSpec </td>";                    //配件名称
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$Remark</td>";        //采购备注
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$Symbol</td>";            //货币
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$AddTaxValueName</td>";
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$Price</td>";            //单价
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$Qty</td>";            //采购数量
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$Unit</td>";            //单位
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$Amount</td>";        //金额
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'><div $rkBgColor>$rkQty</div></td>";        //已收货数量
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'><div $wsBgColor>$wsQty</div></td>";        //欠数
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$wStockQty</td>";        //在库
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$oStockQty</td>";            //采购库存
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='right'>$mStockQty</td>";        //最低库存
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$qkEstate</td>";        //审核状态
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$Estate</td>";        //审核状态
                $m = $m + 2;
                echo "<td class='A0001' width='$Field[$m]' align='center'>$rkSign</td>";        //收货状态
                //$m=$m+2;
                //echo"<td class='A0001' width='$Field[$m]' align='center'>$cwSign</td>";		//货款状态
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]'>$Date</td>";                //申购日期
                $m = $m + 2;
                echo "<td  class='A0001' width='$Field[$m]' align='center'>$Name</td>";            //申购人
                echo "</tr></table>";
                $i++;
                $j++;
            }
            echo $StuffListTB;
        }
    } while ($mainRows = mysql_fetch_array($mainResult));
    echo "</tr></table>";
} else {
    noRowInfo($tableWidth);
}
echo '</div>';
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
