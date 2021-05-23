<style type="text/css">
  .moveLtoR {
    filter: revealTrans(Transition=6, Duration=0.3)
  }

  ;
  .moveRtoL {
    filter: revealTrans(Transition=7, Duration=0.3)
  }

  ;
  /* 为 DIV 加阴影 */
  .out {
    position: relative;
    background: #006633;
    margin: 10px auto;
    width: 400px;
  }

  .in {
    background: #FFFFE6;
    border: 1px solid #555;
    padding: 10px 5px;
    position: relative;
    top: -5px;
    left: -5px;
  }

  /* 为 图片 加阴影 */
  .imgShadow {
    position: relative;
    background: #bbb;
    margin: 10px auto;
    width: 220px;
  }

  .imgContainer {
    position: relative;
    top: -5px;
    left: -5px;
    background: #fff;
    border: 1px solid #555;
    padding: 0;
  }

  .imgContainer img {
    display: block;
  }

  .glow1 {
    filter: glow(color=#FF0000, strengh=2)
  }

  .list {
    position: relative;
    color: #FF0000;
  }

  .list span img { /*CSS for enlarged image*/
    border-width: 0;
    padding: 2px;
    width: 100px;
  }

  .list span {
    position: absolute;
    padding: 3px;
    border: 1px solid gray;
    visibility: hidden;
    background-color: #FFFFFF;
  }

  .list:hover {
    background-color: transparent;
  }

  .list:hover span {
    visibility: visible;
    top: 0;
    left: 28px;
  }
</style>
<?php
//步骤1电信---yang 20120801
include "../model/modelhead.php";
//步骤2：需处理
$tableMenuS = 600;
$ColsNumber = 22;
$From = $From == "" ? "m" : $From;
ChangeWtitle("$SubCompany 产品资料待审核列表");
$funFrom = "productdata";
$Th_Col = "选项|60|序号|35|客户|70|产品ID|50|中文名|200|Product Code|160|海关编码|120|材质|80|用途|80|参考<br>售价|60|货币<br>符号|30|描述|30|利润|80|装箱<br>单位|40|外箱<br>条码|30|产品<br>备注|30|包装<br>说明|30|报关方式|60|所属分类|120|产品属性|80|状态|30|操作员|50|报价规则|250|上次退回原因|180";

//必选，分页默认值
$Pagination = 0;  //默认分页方式:1分页，0不分页
$Page_Size = 500;              //每页默认记录数量
$ActioToS = "17,15";
//步骤3：
$nowWebPage = $funFrom . "_m";
include "../model/subprogram/read_model_3.php";
if ($From != "slist") {
    $SearchRows = " AND P.Estate=2";

    $result = mysql_query("SELECT C.CompanyId,C.Forshort,C.Id FROM 
	$DataIn.productdata P
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
	WHERE C.Estate=1 AND MOD(C.CompanySign,7)=0 $SearchRows GROUP BY P.CompanyId ORDER BY C.Id", $link_id);
    if ($myrow = mysql_fetch_array($result)) {
        echo "<select name='CompanyId' id='CompanyId' onchange='ResetPage(this.name)'>";
        do {
            $theCompanyId = $myrow["CompanyId"];
            $theForshort = $myrow["Forshort"];
            $Id = $myrow["Id"];
            $CompanyId = $CompanyId == "" ? $theCompanyId : $CompanyId;
            if ($CompanyId == $theCompanyId) {
                echo "<option value='$theCompanyId' selected>$theForshort</option>";
                $SearchRows .= " AND P.CompanyId=" . $theCompanyId;
                $CId = $Id;
            }
            else {
                echo "<option value='$theCompanyId'>$theForshort</option>";
            }
        } while ($myrow = mysql_fetch_array($result));
        echo "</select>&nbsp;&nbsp;";
    }

    //楼栋
    $result = mysql_query("SELECT TD.BuildingNo FROM $DataIn.productdata P
LEFT JOIN $DataIn.trade_drawing TD ON TD.Id = P.drawingId 
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
WHERE 1 $SearchRows AND TD.TradeId=$CId GROUP BY  TD.BuildingNo ", $link_id);
    if ($myrow = mysql_fetch_array($result)) {
        echo "<select name='BuildNo' id='BuildNo' onchange='ResetPage(this.name)'>";
        do {
            $theBuildNo = $myrow["BuildingNo"];
            $BuildNo = $BuildNo == "" ? $theBuildNo : $BuildNo;
            if ($BuildNo == $theBuildNo) {
                echo "<option value='$theBuildNo' selected>$theBuildNo</option>";
                $SearchRows .= " AND TD.BuildingNo=" . $theBuildNo;
            }
            else {
                echo "<option value='$theBuildNo'>$theBuildNo</option>";
            }
        } while ($myrow = mysql_fetch_array($result));
        echo "</select>&nbsp;&nbsp;";
    }


    echo "<select name='ProductType' id='ProductType' onchange='ResetPage(this.name)'>";
    $result = mysql_query("SELECT P.TypeId,T.TypeName,T.Letter 
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.trade_drawing TD ON TD.Id = P.drawingId 
	WHERE T.Estate=1 $SearchRows GROUP BY P.TypeId ORDER BY T.Letter", $link_id);
    echo "<option value='' selected>全部</option>";
    while ($myrow = mysql_fetch_array($result)) {
        $TypeId = $myrow["TypeId"];
        if ($ProductType == $TypeId) {
            echo "<option value='$TypeId' selected>$myrow[Letter]-$myrow[TypeName]</option>";
        }
        else {
            echo "<option value='$TypeId'>$myrow[Letter]-$myrow[TypeName]</option>";
        }
    }
    echo "</select>&nbsp;&nbsp;";
    $TypeIdSTR = $ProductType == "" ? "" : " AND P.TypeId=" . $ProductType;
    $SearchRows .= $TypeIdSTR;

}
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
//步骤4：
$TitlePre = "<br>&nbsp;&nbsp;退回原因:<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
include "../model/subprogram/read_model_5.php";
include "../model/subprogram/sys_parameters.php";
//步骤5：需处理数据记录处理
$i = 1;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 1);
$mySql = "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.CompanyId,P.Description,P.Remark,P.pRemark,P.bjRemark,
P.buySign,P.Weight,P.MisWeight,P.MainWeight,P.TestStandard,P.Date,P.PackingUnit,P.Estate,P.Locks,P.Code,P.Operator,
T.TypeName,C.Forshort,D.Rate,D.Symbol,P.ReturnReasons,BG.Name AS bgName,M.Name AS MaterialQ,W.Name AS UseWay,H.HSCode,B.Name AS buySign
FROM $DataIn.productdata P
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.currencydata D ON D.Id=C.Currency
LEFT JOIN $DataIn.trade_drawing TD ON TD.Id = P.drawingId 
LEFT JOIN $DataIn.customscode H ON H.ProductId = P.ProductId
LEFT JOIN $DataIn.productmq M ON M.Id = P.MaterialQ
LEFT JOIN $DataIn.productuseway W ON W.Id = P.UseWay
LEFT JOIN $DataIn.taxtype BG ON BG.Id = P.taxtypeId
LEFT JOIN $DataIn.product_property B ON B.Id = P.buySign
where 1 $SearchRows order by Estate DESC,Id DESC";

//echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    do {
        $m = 1;
        $Id = $myRow["Id"];
        $ProductId = $myRow["ProductId"];
        $Rate = $myRow["Rate"];
        $Symbol = $myRow["Symbol"];
        $Client = $myRow["Forshort"];
        $cName = $myRow["cName"];
        $eCode = $myRow["eCode"] == "" ? "&nbsp;" : $myRow["eCode"];
        $Remark = trim($myRow["Remark"]) == "" ? "&nbsp;" : "<img src='../images/remark.gif' Title='$myRow[Remark]' width='18' height='18'>";
        $pRemark = trim($myRow["pRemark"]) == "" ? "&nbsp;" : "<img src='../images/remark.gif' Title='$myRow[pRemark]' width='18' height='18'>";
        $Description = $myRow["Description"] == "" ? "&nbsp;" : "<img src='../images/remark.gif' Title='$myRow[Description]' width='18' height='18'>";
        $ReturnReasons = $myRow["ReturnReasons"] == "" ? "&nbsp;" : $myRow["ReturnReasons"];
        $Price = $myRow["Price"];
        $Moq = $myRow["Moq"] == 0 ? "&nbsp;" : $myRow["Moq"];
        $tStockQty = $myRow["tStockQty"] == 0 ? "&nbsp;" : $myRow["tStockQty"];
        $Weight = $myRow["Weight"] == 0 ? "&nbsp;" : $myRow["Weight"];
        $MisWeight = $myRow["MisWeight"] == 0 ? "&nbsp;" : $myRow["MisWeight"];
        $MainWeight = $myRow["MainWeight"] == 0 ? "&nbsp;" : $myRow["MainWeight"];
        $bjRemark = $myRow["bjRemark"] == "&nbsp;" ?: $myRow["bjRemark"];
        $bgName = $myRow["bgName"] == "" ? "&nbsp;" : $myRow["bgName"];
        $MaterialQ = $myRow["MaterialQ"] == "" ? "&nbsp;" : $myRow["MaterialQ"];
        $UseWay = $myRow["UseWay"] == "" ? "&nbsp;" : $myRow["UseWay"];
        $HSCode = $myRow["HSCode"] == "" ? "&nbsp;" : $myRow["HSCode"];
        $TestStandard = $myRow["TestStandard"];
        include "../admin/Productimage/getProductImage.php";
        //产品QC检验标准图
        $QCImage = "";
        include "../admin/subprogram/product_qcfile.php";
        $QCImage = $QCImage == "" ? "&nbsp;" : $QCImage;
        include "../model/subprogram/product_appview.php";//App图
        //客户提供的产品图

        $ClientFilePath = "../download/productClient/" . $ProductId . ".jpg";
        if (file_exists($ClientFilePath)) {
            $noStatue = "onMouseOver=\"window.status='none';return true\"";
            $ClientFileSTR = "<span class='list' >View<span><img src='$ClientFilePath' $noStatue/></span></span>";
        }
        else {
            $ClientFileSTR = "&nbsp;";
        }

        //  客户授权书
        include "../model/subprogram/product_clientproxy.php";
        $Code = $myRow["Code"] == "" ? "&nbsp;" : "<img src='../images/remark.gif' Title='$myRow[Code]' width='18' height='18'>";
        $Estate = $myRow["Estate"];
        switch ($Estate) {
            case 1:
                $Estate = "<div class='greenB'>√</div>";
                break;
            case 2:
                $Estate = "<div class='yellowB'>√.</div>";
                break;
            default:
                $Estate = "<div class='redB'>×</div>";
                break;
        }
        $buySign = $myRow["buySign"];
        $PackingUnit = $myRow["PackingUnit"];
        $uResult = mysql_query("SELECT Name FROM $DataPublic.packingunit WHERE Id=$PackingUnit order by Id Limit 1", $link_id);
        if ($uRow = mysql_fetch_array($uResult)) {
            $PackingUnit = $uRow["Name"];
        }
        $Unit = $myRow["Unit"];
        $Date = $myRow["Date"];
        $Locks = $myRow["Locks"];
        //操作员姓名
        $Operator = $myRow["Operator"];
        include "../model/subprogram/staffname.php";
        $thisCId = $myRow["CompanyId"];
        $TypeName = $myRow["TypeName"];
        //查产品配件关系表
        $saleRMB = sprintf("%.2f", $Price * $Rate);//产品销售RMB价
        $StuffResult = mysql_query("SELECT A.Relation,E.Rate,D.Currency,B.Price
		FROM $DataIn.pands A
		LEFT JOIN $DataIn.stuffdata B ON B.StuffId=A.StuffId
		LEFT JOIN $DataIn.bps C ON C.StuffId=B.StuffId
		LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=B.TypeId
		LEFT JOIN $DataIn.trade_object D ON D.CompanyId=C.CompanyId		
		LEFT JOIN $DataIn.currencydata E ON E.Id=D.Currency		
		where A.ProductId=$ProductId order by A.Id", $link_id);
        if ($StuffmyRow = mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
            $buyRMB = 0;
            $buyHZsum = 0;
            do {
                $stuffPrice = $StuffmyRow["Price"];
                $stuffRelation = $StuffmyRow["Relation"];
                $stuffRate = $StuffmyRow["Rate"] == "" ? 1 : $StuffmyRow["Rate"];
                $CurrencyTemp = $StuffmyRow["Currency"];
                //成本
                $OppositeRelation = explode("/", $stuffRelation);
                if ($OppositeRelation[1] != "") {//非整数对应关系
                    $thisRMB = sprintf("%.4f", $stuffRate * $stuffPrice * $OppositeRelation[0] / $OppositeRelation[1]);
                }
                else {//整数对应关系
                    $thisRMB = sprintf("%.4f", $stuffRate * $stuffPrice * $OppositeRelation[0]);
                }
                $buyRMB = $buyRMB + $thisRMB;  //总成本
                if ($CurrencyTemp != 2) {    //非外购
                    $buyHZsum += $thisRMB;
                }
            } while ($StuffmyRow = mysql_fetch_array($StuffResult));
            $profitRMB = sprintf("%.2f", $saleRMB - $buyRMB - $buyHZsum * $HzRate);
            if ($saleRMB != 0) {
                $profitRMBPC = sprintf("%.0f", ($profitRMB * 100 / $saleRMB));
            }
            //净利分类
            $ViewSign = 0;
            if ($profitRMBPC > 10) {
                $ViewSign = $ProfitType == 4 ? 1 : 0;
                $profitRMB = "<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='greenB'>$profitRMB($profitRMBPC%)</sapn></a>";
            }
            else {
                if ($profitRMBPC >= 3) {
                    $ViewSign = $ProfitType == 3 ? 1 : 0;
                    $profitRMB = "<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='yellowB'>$profitRMB($profitRMBPC%)</sapn></a>";
                }
                else {
                    if ($profitRMB < 0) {
                        $ViewSign = $ProfitType == 1 ? 1 : 0;
                        $profitRMB = "<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='purpleB'>$profitRMB($profitRMBPC%)</sapn></a>";
                    }
                    else {
                        $ViewSign = $ProfitType == 2 ? 1 : 0;
                        $profitRMB = "<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='redB'>$profitRMB($profitRMBPC%)</sapn></a>";
                    }
                }
            }
        }
        else {
            $ViewSign = $ProfitType == 5 ? 1 : 0;
            $profitRMB = "<div class='redB'>未设定</div>";
        }
        //若为旧product则为黄底
        $OrderSignColor = "";
        $hasOrderSql = mysql_query("Select * From $DataIn.yw1_ordersheet Where ProductId = '$ProductId'");
        if (mysql_num_rows($hasOrderSql) > 0) {
            $OrderSignColor = " bgcolor='#FFCC00'";
        }
        $URL = "productdata_bom_ajax";
        $theParam = "$ProductId";
        $ListId = getRandIndex();
        $showPurchaseorder = "<img onClick='ShowDropTable(ShowTable$ListId,ShowGif$ListId,ShowDiv$ListId,\"$URL\",\"$theParam\",\"public\");' name='ShowGif$ListId' src='../images/showtable.gif' 
			title='显示或隐藏的配件资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' >";
        $StuffListTB = "<table width='$tableWidth' border='0' cellspacing='0' id='ShowTable$ListId' style='display:none;'>
			<tr bgcolor='#B7B7B7'>
			<td class='A0111' height='30' align='left'><br><div id='ShowDiv$ListId'>&nbsp;</div><br></td></tr></table>";
        $ValueArray = array(
            array(0 => $Client),
            array(0 => $ProductId, 1 => "align='center'"),
            array(0 => $TestStandard, 2 => "onmousedown='window.event.cancelBubble=true;'"),
            array(0 => $eCode, 3 => "..."),
            array(0 => $HSCode,),
            array(0 => $MaterialQ, 1 => "align='center'"),
            array(0 => $UseWay, 1 => "align='center'"),
            array(0 => $Price . "&nbsp;", 1 => "align='right'"),
            array(0 => $Symbol, 1 => "align='center'"),
            array(0 => $Description, 1 => "align='center'"),
            array(0 => $profitRMB, 1 => "align='center'"),
            array(0 => $PackingUnit, 1 => "align='center'"),
            array(0 => $Code, 1 => "align='center'"),
            array(0 => $pRemark, 1 => "align='center'"),
            array(0 => $Remark, 1 => "align='center'"),
            array(0 => $bgName, 1 => "align='center'"),
            array(0 => $TypeName, 1 => "align='center'"),
            array(0 => $buySign, 1 => "align='center'"),
            array(0 => $Estate, 1 => "align='center'"),
            array(0 => $Operator, 1 => "align='center'"),
            array(0 => $bjRemark, 1 => "align='left'"),
            array(0 => $ReturnReasons, 1 => "align='left'"),
        );
        $checkidValue = $Id;
        include "../model/subprogram/read_model_6.php";
        echo $StuffListTB;
    } while ($myRow = mysql_fetch_array($myResult));
}
else {
    noRowInfo($tableWidth);
}
//步骤7：
echo '</div>';
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>