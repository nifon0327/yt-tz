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
/*
分开已更新
*/
//步骤1电信---yang 20120801
include "../model/modelhead.php";
include "../model/subprogram/UpdateCode.php"; //更新条码 add by zx 20100701
include "../model/subprogram/business_authority.php";//看客户权限
//步骤2：需处理
$czTest = $_REQUEST["cz"];

$tableMenuS = 850;
ChangeWtitle("$SubCompany 产品列表");
$funFrom = "productdata";
$From = $From == "" ? "read" : $From;
$TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId=$Login_P_Number LIMIT 1", $link_id);
if ($TRow = mysql_fetch_array($TResult)) {
    $Th_Col = "选项|40|序号|35|客户|70|产品ID|50|产品属性|60|中文名|200|&nbsp;|30|Product Code|160|App图|40|海关编码|100|材质|80|用途|80|客户<br>授权书|50|QC图|40|品牌<br>提供图|50|单品重<br>(g)|50|成品重<br>(g)|40|误差值<br>(±g)|40|Price|60|利润|100|装箱<br>单位|40|外箱<br>条码|30|已出数量<br>(下单次数)|60|退货<br>数量|50|产品<br>库存|50|最后出货<br>月份|50|交货<br>均期|50|产品<br>备注|50|属电<br>子类|40|所属分类|100|报关方式|60|客户<br>验货|30|状态|30|外箱<br>标签|30|退回原因|180|认证下载|200|带包装<br>高清图|50|不带包装<br>高清图|50|产品尺寸|150|报价规则|350";//|背卡<br>条码|30|PE袋<br>条码|30|白盒<br>坑盒|30
    $myTask = 1;
    include "../model/subprogram/sys_parameters.php";
    $ColsNumber = 37;
} else {
//	$Th_Col="选项|40|序号|35|客户|70|产品ID|50|产品属性|60|中文名|200|&nbsp;|30|Product Code|160|App图|40|海关编码|100|材质|80|用途|80|品牌<br>授权书|50|QC图|40|客户<br>提供图|50|单品重<br>(g)|50|成品量<br>(g)|40|误差值<br>(±g)|40|Price|60|装箱<br>单位|40|外箱<br>条码|30|已出数量<br>(下单次数)|60|退货<br>数量|50|产品<br>库存|50|最后出货<br>月份|50|交货<br>均期|50|产品<br>备注|50|属电<br>子类|40|所属分类|100|报关方式|60|客户<br>验货|30|状态|30|外箱<br>标签|30|退回原因|180|认证下载|200|带包装<br>高清图|50|不带包装<br>高清图|50|产品尺寸|150|报价规则|350"; //|背卡<br>条码|30|PE袋<br>条码|30|白盒<br>坑盒|30
    $Th_Col = "选项|40|序号|35|客户|70|产品ID|50|产品属性|60|中文名|150|&nbsp;|30|Product Code|150|替换单号|120|原因|120|单品重<br>(g)|70|成品量<br>(g)|70|误差值<br>(±g)|70|Price|70|装箱单位|70|外箱条码|70|已出数量<br>(下单次数)|70|退货数量|70|产品库存|70|所属分类|100"; //|背卡<br>条码|30|PE袋<br>条码|30|白盒<br>坑盒|30
    $myTask = 0;
    $ColsNumber = 37;
}
//必选，分页默认值
//$ColsNumber=35;
$Pagination = $Pagination == "" ? 1 : $Pagination;    //默认分页方式:1分页，0不分页
$Page_Size = 300;                            //每页默认记录数量
if ($ProfitType != "") $Pagination = 0;
$ActioToS = "1,2,3,4,5,6,7,8,13,40,58,81,80,74,172,168,38";                //功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消
//步骤3：
$nowWebPage = $funFrom . "_read";
include "../model/subprogram/read_model_3.php";
//步骤4：需处理-可选条件下拉框AND M.Estate=1

if ($From != "slist") {
    $SearchRows = "";
    $result = mysql_query("SELECT M.CompanyId,M.Forshort,M.Letter 
	     FROM $DataIn.productdata P 
	     LEFT JOIN $DataIn.trade_object M ON P.CompanyId = M.CompanyId
         WHERE 1 AND M.Estate=1 AND M.ObjectSign IN (1,2) AND MOD(M.CompanySign,7)=0 $ClientStr 
         GROUP BY P.CompanyId ORDER BY M.Letter", $link_id);
    if ($myrow = mysql_fetch_array($result)) {
        echo "<select name='CompanyId' id='CompanyId' onchange='ResetPages(\"CompanyId\")'>";
        do {
            $theCompanyId = $myrow["CompanyId"];
            $theForshort = $myrow["Forshort"];
            $theLetter = $myrow["Letter"];
            $CompanyId = $CompanyId == "" ? $theCompanyId : $CompanyId;
            if ($CompanyId == $theCompanyId) {
                echo "<option value='$theCompanyId' selected>$theLetter-$theForshort</option>";
                $SearchRows = " AND P.CompanyId=" . $theCompanyId;
                $nowForshort = $theForshort;
            } else {
                echo "<option value='$theCompanyId'>$theLetter-$theForshort</option>";
            }
        } while ($myrow = mysql_fetch_array($result));
        echo "</select>";
    }

    //栋层
    $result = mysql_query("SELECT substring_index( P.cName, '-', 1 ) AS build,substring_index((substring_index( P.cName, '-', 2 ) ), '-', -1 ) as floor
		FROM $DataIn.pands A
		LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
		LEFT JOIN $DataIn.trade_object M ON M.CompanyId=P.CompanyId  
		WHERE M.Estate>0 $SearchRows GROUP BY build+0,Floor+0 ", $link_id);

    if ($myrow = mysql_fetch_array($result)) {
        echo "<select name='buildFloor' id='buildFloor' onchange='ResetPages(\"buildFloor\")'>";
//        echo "<option value='all' selected>全部栋层</option>";
        do {
            $theBuild = $myrow["build"];
            $theFloor = $myrow["floor"];
            $thebuildFloor = $theBuild . '-' . $theFloor;
            $buildFloor = $buildFloor == "" ? $thebuildFloor : $buildFloor;
            if ($buildFloor == $thebuildFloor) {
                echo "<option value='$thebuildFloor' selected>$theBuild 栋 $theFloor 层</option>";
                $SearchRows .= " AND P.cName like '$buildFloor-%'";
            } else {
                echo "<option value='$thebuildFloor'>$theBuild 栋 $theFloor 层</option>";
            }
        } while ($myrow = mysql_fetch_array($result));
    }
    echo "</select>&nbsp; ";


    $result = mysql_query("SELECT P.TypeId,T.TypeName
	FROM $DataIn.productdata P
	LEFT JOIN $DataIn.ProductType T ON T.TypeId=P.TypeId
	LEFT JOIN $DataIn.productmaintype C ON C.Id=T.mainType
	WHERE T.Estate=1 $SearchRows GROUP BY P.TypeId ORDER BY T.mainType DESC,T.Letter", $link_id);
    echo "<select name='ProductType' id='ProductType' onchange='ResetPages(\"ProductType\")'>";
    echo "<option value='all' selected>全部类型</option>";
    while ($myrow = mysql_fetch_array($result)) {
        $TypeId = $myrow["TypeId"];
        $TypeName = $myrow["TypeName"];
        $ProductType = $ProductType == "" ? $TypeId : $ProductType;
        if ($ProductType == $TypeId) {
            echo "<option value='$TypeId'  selected> $TypeName</option>";
            $SearchRows .= " AND P.TypeId=" . $ProductType;
        } else {
            echo "<option value='$TypeId' > $TypeName</option>";
        }
    }
    echo "</select>&nbsp;";

//	   $buyResult = mysql_query("SELECT P.buySign,B.Name
//	   FROM $DataIn.productdata P
//	   LEFT JOIN $DataIn.product_property B ON B.Id = P.buySign
//       WHERE 1 $SearchRows  GROUP BY P.buySign ORDER BY B.Id",$link_id);
//
//	   if($buyRow = mysql_fetch_array($buyResult)){
//	   echo "<select name='buySign' id='buySign' onchange='ResetPage(this.name)'>";
//	   echo "<option value='' selected>全部</option>";
//		do{
//			$thebuySign=$buyRow["buySign"];
//			$theName=$buyRow["Name"];
//			if($buySign==$thebuySign){
//				echo"<option value='$thebuySign' selected>$theName</option>";
//				$SearchRows.=" AND P.buySign=".$thebuySign;
//				}
//			 else{
//			 	echo"<option value='$thebuySign'>$theName</option>";
//				}
//			}while($buyRow = mysql_fetch_array($buyResult));
//			echo"</select>";
//		}

    $TempProfitSTR = "ProfitTypeStr" . strval($ProfitType);
    $$TempProfitSTR = "selected";
    echo "<select name='ProfitType' id='ProfitType' onchange='ResetPage(this.name)'>";
    echo "<option value='' $ProfitTypeStr>全部净利</option>
		<option value='1' style= 'color:#FF00CC;' $ProfitTypeStr1>0以下</option>
		<option value='2' style= 'color:#FF0000;' $ProfitTypeStr2>0-7%</option>
		<option value='3' style= 'color:#FF6633;' $ProfitTypeStr3>8-15%</option>
		<option value='4' style= 'color:#009900;' $ProfitTypeStr4>16%以上</option>
		<option value='5' style= 'color:#BB0000;' $ProfitTypeStr5>未设定</option>
	</select>&nbsp;";

    $TempProfitSTR = "LastMStr" . strval($LastM);
    $$TempProfitSTR = "selected";
    echo "<select name='LastM' id='LastM' onchange='ResetPage(this.name)'>";
    echo "<option value='' $LastMStr>全部出货日期</option>
		<option value='1' style= 'color:#090;' $LastMStr1>半年内</option>
		<option value='2' style= 'color:#f60;' $LastMStr2>半年至1年</option>
		<option value='3' style= 'color:#f00;' $LastMStr3>1年以上</option>
	</select>&nbsp;";
    switch ($LastM) {
        case 1://<6
            $ShipMonthStr = " AND (E.Months<6 OR E.Months IS NULL)";
            break;
        case 2://6<=  <12
            $ShipMonthStr = " AND E.Months>5 AND E.Months<12 AND E.Months IS NOT NULL";
            break;
        case 3://>=12
            $ShipMonthStr = " AND E.Months>11 AND E.Months IS NOT NULL";
            break;
        default://全部
            $ShipMonthStr = "";
            break;
    }
}
echo "<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>$CencalSstr";
echo "<span class='ButtonH_25' id='Replace' onclick='Replace(\"replace\")'>替 板</span>";
echo "<span class='ButtonH_25' id='ReplaceBack' onclick='ReplaceBack()'>撤销替板</span>";

//$NowYear = date("Y");
//$NowMonth = date("m");
//$searchtable = "productdata|P|cName|0|1"; //快速搜索的表名，字段名. 表名|别名|字段|1  1表示带Estate字段
//$searchfile = "../model/subprogram/QuickSearch_ajax.php";
//include "../model/subprogram/QuickSearch.php";

//步骤5：
include "../model/subprogram/read_model_5.php";
if ($NameRule != "") {
    echo "<table border='0' cellspacing='0' style='TABLE-LAYOUT: fixed; WORD-WRAP:       break-word' bgcolor='#FFFFFF' width='$tableWidth' ><tr ><td height='25' class='A0011' ><span style='color:red'>命名规则:</span>$NameRule</td></tr></table>";
}

include "../model/subprogram/CurrencyList.php";
echo "<div id='Jp' style='position:absolute; left:1020px; top:229px; width:480px; height:50px;z-index:1;visibility:hidden;' tabIndex=0><input name='ActionTableId' type='hidden' id='ActionTableId'><input name='ActionRowId' type='hidden' id='ActionRowId'><input name='ObjId' type='hidden' id='ObjId'>
		<div class='out'>
			<div class='in' id='infoShow'>
			</div>
		</div>
	</div>";


//步骤6：需处理数据记录处理
$i = 1;
$KillRecord = 0;
$j = ($Page - 1) * $Page_Size + 1;
List_Title($Th_Col, "1", 1);
$mySql = "SELECT P.Id,P.ProductId,P.cName,P.eCode,P.Price,P.Unit,P.Moq,P.Weight,P.MisWeight,P.CompanyId,P.Description,P.Remark,P.pRemark,P.bjRemark,P.dzSign,P.productsize,P.TestStandard,P.Img_H,P.Date,P.PackingUnit,P.Estate,P.Locks,P.Code,P.Operator,P.ReturnReasons,P.InspectionSign,T.TypeName,C.Forshort,D.Rate,D.Symbol,D.PreChar,M.Name AS MaterialQ,W.Name AS UseWay,
P.MainWeight,E.Months,E.LastMonth,BG.Name AS bgName,S.tStockQty,H.HSCode,B.Name AS buySign,C.Id AS CID 
FROM $DataIn.productdata P
LEFT JOIN $DataIn.producttype T ON T.TypeId=P.TypeId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=P.CompanyId
LEFT JOIN $DataIn.customscode H ON H.ProductId = P.ProductId
LEFT JOIN $DataIn.productmq M ON M.Id = P.MaterialQ
LEFT JOIN $DataIn.productuseway W ON W.Id = P.UseWay
LEFT JOIN $DataIn.currencydata D ON D.Id=C.Currency
LEFT JOIN $DataIn.taxtype BG ON BG.Id = P.taxtypeId
LEFT JOIN $DataIn.productstock S ON S.ProductId = P.ProductId
LEFT JOIN $DataIn.product_property B ON B.Id = P.buySign
LEFT JOIN (
		    SELECT DATE_FORMAT(MAX(M.Date),'%Y-%m') AS LastMonth,TIMESTAMPDIFF(MONTH,MAX(M.Date),now()) AS Months,S.ProductId            
            FROM $DataIn.ch1_shipmain M 
            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
            WHERE 1 GROUP BY S.ProductId ORDER BY M.Date DESC
    ) E ON E.ProductId=P.ProductId
LEFT JOIN $DataIn.productstandimg PM ON PM.ProductId=P.ProductId
WHERE 1 $SearchRows  $ShipMonthStr ORDER BY Estate DESC,Id DESC";
//echo $mySql;
$myResult = mysql_query($mySql . " $PageSTR", $link_id);
if ($myRow = mysql_fetch_array($myResult)) {
    $d = anmaIn("download/productfile/", $SinkOrder, $motherSTR);
    $dirforstuff = anmaIn("download/stufffile/", $SinkOrder, $motherSTR);
    do {
        $m = 1;
        $Months = $myRow["Months"];
        $LastMonth = $myRow["LastMonth"];
        $Id = $myRow["Id"];
        $ProductId = $myRow["ProductId"];
        $Rate = $myRow["Rate"];
        $Symbol = $myRow["Symbol"];
        $Client = $myRow["Forshort"];
        $cName = $myRow["cName"];
        $PreChar = $myRow["PreChar"];
        $eCode = $myRow["eCode"] == "" ? "&nbsp;" : $myRow["eCode"];
        $HSCode = $myRow["HSCode"] == "" ? "&nbsp;" : $myRow["HSCode"];
        $Remark = trim($myRow["Remark"]) == "" ? "&nbsp;" : "<img src='../images/remark.gif' title='$myRow[Remark]' width='18' height='18'>";
        $pRemark = trim($myRow["pRemark"]) == "" ? "&nbsp;" : "<img src='../images/remark.gif' title='$myRow[pRemark]' width='18' height='18'>";
        $bjRemark = "";
        $bjRemark = trim($myRow["bjRemark"]) == "" ? "&nbsp;" : $myRow["bjRemark"];
        $bgName = $myRow["bgName"] == "" ? "&nbsp;" : $myRow["bgName"];

        if ($czTest == 5) {
            $bjRemark = $bjRemark == "&nbsp;" ? "" : $bjRemark;
            $bjRemarkId = $ProductId . "_bj";
            $bjRemark = "<input type='text' style='width:300px;' name='' id='$bjRemarkId' value='" . $bjRemark . "'>";
            $bjRemark .= "<input type='button' value='修改' onClick='alterBJ($ProductId)' ";
        }

        $Description = $myRow["Description"];
        $Price = $myRow["Price"];
        $Moq = $myRow["Moq"] == 0 ? "&nbsp;" : $myRow["Moq"];
        $tStockQty = $myRow["tStockQty"] == 0 ? "&nbsp;" : $myRow["tStockQty"];
        $Weight = $myRow["Weight"] == 0 ? "&nbsp;" : $myRow["Weight"];
        $MisWeight = $myRow["MisWeight"] == 0 ? "&nbsp;" : $myRow["MisWeight"];
        $MainWeight = $myRow["MainWeight"] == 0 ? "&nbsp;" : $myRow["MainWeight"];
        $MaterialQ = $myRow["MaterialQ"] == "" ? "&nbsp;" : $myRow["MaterialQ"];
        $UseWay = $myRow["UseWay"] == "" ? "&nbsp;" : $myRow["UseWay"];
        if ($myRow["ReturnReasons"] != "") {
            $OrderSignColor = "bgColor='#FFD700'";
        } else {
            $OrderSignColor = "";
        }
        $ReturnReasons = $myRow["ReturnReasons"] == "" ? "&nbsp;" : "<span class='redB'>" . $myRow["ReturnReasons"] . "</span>";
        $TestStandard = $myRow["TestStandard"];

        //echo "SELECT CompanyId AS SCOM,FileName FROM $DataIn.doc_standarddrawing  WHERE CompanyId = '$CompanyId' AND FileRemark = '$cName'";
        $myRowImg = mysql_fetch_array(mysql_query("SELECT CompanyId AS SCOM,FileName FROM $DataIn.doc_standarddrawing  WHERE CompanyId = '$CompanyId' AND FileRemark = '$cName'", $link_id));
        $FileName = $myRowImg["SCOM"];
        $CId = $myRow["CID"];
        $FileName = $myRowImg["FileName"];
        include "../admin/Productimage/getProductImage.php";

        $I_FilePath = "download/teststandard/";
        $I_td = anmaIn("$I_FilePath", $SinkOrder, $motherSTR);
        $Img_H1 = "";
        $Img_H2 = "";
        $Img_HResult = mysql_query("SELECT Picture,Type FROM $DataIn.productimg  WHERE  ProductId=$ProductId", $link_id);
        while ($Img_HRow = mysql_fetch_array($Img_HResult)) {
            $Img_HType = $Img_HRow["Type"];
            $Img_HPicture = $Img_HRow["Picture"];
            switch ($Img_HType) {
                case "1": //带包装的高清图
                    $I_Field1 = anmaIn($Img_HPicture, $SinkOrder, $motherSTR);
                    $Img_H1 = "<a href=\"../admin/openorload.php?d=$I_td&f=$I_Field1&Type=&Action=6\" target=\"download\">H1</a>";
                    break;
                case "2": //不带包装的高清图
                    $I_Field2 = anmaIn($Img_HPicture, $SinkOrder, $motherSTR);
                    $Img_H2 = "<a href=\"../admin/openorload.php?d=$I_td&f=$I_Field2&Type=&Action=6\" target=\"download\">H2</a>";
                    break;
            }
        }
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
        } else {
            $ClientFileSTR = "&nbsp;";
        }

        //  客户授权书
        include "../model/subprogram/product_clientproxy.php";
        //认证下载
        $CerSql = "SELECT Picture,Remark FROM $DataIn.product_certification WHERE ProductId='$ProductId'";
        $CerResult = mysql_query($CerSql, $link_id);
        $CerImg = "&nbsp;";
        $CerPicture = array();
        $index = 0;
        $Cer_FilePath = "download/productcer/";
        if ($CerRow = mysql_fetch_array($CerResult)) {
            do {
                $CerPicture[$index] = $CerRow["Picture"];
                $CerRemark[$index] = $CerRow["Remark"];
                $Cer_Field = anmaIn($CerPicture[$index], $SinkOrder, $motherSTR);
                $Cer_td = anmaIn("$Cer_FilePath", $SinkOrder, $motherSTR);
                $CerDownload = "<a href=\"../admin/openorload.php?d=$Cer_td&f=$Cer_Field&Type=&Action=6\" target=\"download\">$CerRemark[$index]</a>";
                $CerImg .= $CerDownload . "</br>";
                $index++;
            } while ($CerRow = mysql_fetch_array($CerResult));
        }
        //include "subprogram/product_teststandard.php";
        $Code = $myRow["Code"] == "" ? "&nbsp;" : "<img src='../images/remark.gif' title='$myRow[Code]' width='18' height='18'>";
        $Estate = $myRow["Estate"];
        switch ($Estate) {
            case 1:
                $Estate = "<div class='greenB'>√</div>";
                break;
            case 2:
                $Estate = "<div class='yellowB'>√.</div>";
                break;
            case 3:
                $Estate = "<div class='yellowB'>×.</div>";
                break;
            default:
                $Estate = "<div class='redB'>×</div>";
                break;
        }
        $PackingUnit = $myRow["PackingUnit"];
        $uResult = mysql_query("SELECT Name FROM $DataPublic.packingunit WHERE Id=$PackingUnit order by Id Limit 1", $link_id);
        if ($uRow = mysql_fetch_array($uResult)) {
            $PackingUnit = $uRow["Name"];
        }
        $Unit = $myRow["Unit"];
        $Date = $myRow["Date"];
        $Locks = $myRow["Locks"];
        $dzSign = $myRow["dzSign"] == "1" ? "√" : "&nbsp;";
        $InspectionSign = $myRow["InspectionSign"] == "1" ? "√" : "&nbsp;";
        $buySign = $myRow["buySign"];

        $productsize = $myRow["productsize"] == "" ? "&nbsp;" : $myRow["productsize"];
        //操作员姓名
        $Operator = $myRow["Operator"];
        include "../model/subprogram/staffname.php";
        $thisCId = $myRow["CompanyId"];
        $TypeName = $myRow["TypeName"];
        $saleRMB = sprintf("%.2f", $Price * $Rate);//产品销售RMB价格
        $GfileStr = "";
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
                } else {//整数对应关系
                    $thisRMB = sprintf("%.4f", $stuffRate * $stuffPrice * $OppositeRelation[0]);
                }
                $buyRMB = $buyRMB + $thisRMB;    //总成本
                if ($CurrencyTemp != 2) {        //非外购
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
            } else {
                if ($profitRMBPC >= 3) {
                    $ViewSign = $ProfitType == 3 ? 1 : 0;
                    $profitRMB = "<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='yellowB'>$profitRMB($profitRMBPC%)</sapn></a>";
                } else {
                    if ($profitRMB < 0) {
                        $ViewSign = $ProfitType == 1 ? 1 : 0;
                        $profitRMB = "<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='purpleB'>$profitRMB($profitRMBPC%)</sapn></a>";
                    } else {
                        $ViewSign = $ProfitType == 2 ? 1 : 0;
                        $profitRMB = "<a href='pands_profit.php?From=task&Cid=$ProductId' target='_blank'><span class='redB'>$profitRMB($profitRMBPC%)</sapn></a>";
                    }
                }
            }
        } else {
            $ViewSign = $ProfitType == 5 ? 1 : 0;
            $profitRMB = "<div class='redB'>未设定</div>";
        }


        //单号
        $ReplaceResult = mysql_query("SELECT ReplaceNO,Reason FROM $DataIn.ck_substitute WHERE YProductId = $ProductId ", $link_id);
        if ($ReplaceRow = mysql_fetch_array($ReplaceResult)) {
            $ReplaceNO = $ReplaceRow["ReplaceNO"];
            $Reason = $ReplaceRow["Reason"];
        }else{
            $ReplaceNO = "";
            $Reason = "";
        }


        if ($ProfitType == "") $ViewSign = 1;

        if ($ViewSign == 1) {
            //交货期
            include "../model/subprogram/product_chjq.php";
            //订单总数
            $checkAllQty = mysql_query("
								  SELECT SUM(ALLQTY) AS ALLQTY,count(*) AS Orders FROM( 
									SELECT SUM(S.Qty) AS AllQty FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
									WHERE P.eCode LIKE (SELECT eCode FROM $DataIn.productdata WHERE ProductId='$ProductId') GROUP BY OrderPO
									)A
								  ", $link_id);
            $AllQtySum = toSpace(mysql_result($checkAllQty, 0, "AllQty"));
            $Orders = mysql_result($checkAllQty, 0, "Orders");
            //已出货数量
            $checkShipQty = mysql_query("SELECT SUM(Qty) AS ShipQty FROM $DataIn.ch1_shipsheet WHERE ProductId='$ProductId'", $link_id);
            $ShipQtySum = toSpace(mysql_result($checkShipQty, 0, "ShipQty"));
            //最后出货日期
            if ($Months != NULL) {
                if ($Months < 6) {//6个月内绿色
                    $LastShipMonth = "<div class='greenB'>" . $LastMonth . "</div>";
                } else {
                    if ($Months < 12) {//6－12个月：橙色
                        $LastShipMonth = "<div class='yellowB'>" . $LastMonth . "</div>";
                    } else {//红色
                        $LastShipMonth = "<div class='redB'>" . $LastMonth . "</div>";
                    }
                }

            } else {//没有出过货
                $LastShipMonth = "&nbsp;";
            }
            //百分比
            $TempInfo = "style='CURSOR: pointer;' onclick='ViewChart($ProductId,1)'";
            $TempPC = $AllQtySum == 0 ? 0 : ($ShipQtySum / $AllQtySum) * 100;
            $TempPC = $TempPC >= 1 ? (round($TempPC) . "%") : (sprintf("%.2f", $TempPC) . "%");
            if ($AllQtySum > 0) {
                $TempInfo .= "title='订单总数:$AllQtySum,已出数量占:$TempPC'";
            }
//退货数量
            $checkReturnedQty = mysql_query("SELECT SUM(Qty) AS ReturnedQty FROM $DataIn.product_returned WHERE ProductId='$ProductId'", $link_id);
            $ReturnedQty = toSpace(mysql_result($checkReturnedQty, 0, "ReturnedQty"));


            if ($ReturnedQty > 0 && $ShipQtySum > 0) {
                //退货百分比
                $ReturnedPercent = sprintf("%.1f", (($ReturnedQty / $ShipQtySum) * 1000));
                if ($ReturnedPercent >= 5) {
                    $ReturnedQty = "<span class=\"redB\">" . $ReturnedQty . "</span>";
                } else {
                    if ($ReturnedPercent >= 2) {
                        $ReturnedQty = "<span class=\"yellowB\">" . $ReturnedQty . "</span>";
                    } else {
                        $ReturnedQty = "<span class=\"greenB\">" . $ReturnedQty . "</span>";
                    }
                }
                $ReturnedP =
                $TempInfo2 = "style='CURSOR: pointer;' onclick='ViewChart($ProductId,2)' title=\"退货率：$ReturnedPercent ‰\"";
            } else {
                $ReturnedQty = "&nbsp;";
                $TempInfo2 = "";
            }
            //高清图片检查
            $mProductId = $ProductId;
            $checkImgSQL = mysql_query("SELECT Picture FROM $DataIn.productimg WHERE ProductId=$ProductId", $link_id);
            if ($checkImgRow = mysql_fetch_array($checkImgSQL)) {
                $Picture = $checkImgRow["Picture"];
                //echo $Picture;
                $f = anmaIn($Picture, $SinkOrder, $motherSTR);
                $ProductId = "<a href='openorload.php?d=$d&f=$f&Type=zip'>$ProductId</a>";
            }
            $ShipQtySum = "<span class='yellowB'>" . $ShipQtySum . "</span>";
            $GfileStr = $GfileStr == "" ? "&nbsp;" : $GfileStr;
            $TableId = "ListTable$i";

            //出货数量和下单次数
            if ($Orders > 0) {
                if ($Orders < 2) {
                    $ShipQtySum = $ShipQtySum . "<span class=\"redB\">($Orders)</span>";
                } else {
                    if ($Orders > 4) {
                        $ShipQtySum = $ShipQtySum . "<span class=\"greenB\">($Orders)</span>";
                    } else {
                        $ShipQtySum = $ShipQtySum . "<span class=\"yellowB\">($Orders)</span>";
                    }
                }
            }
            //array(0=>$MainWeight,		1=>"align='center'"),
            if ($myTask == 1) {
                $ValueArray = array(
                    array(0 => $Client),
                    array(0 => $ProductId, 1 => "align='center'"),
                    array(0 => $buySign, 1 => "align='center'"),
                    array(0 => $TestStandard, 2 => "onmousedown='window.event.cancelBubble=true;'", 3 => "line"),
                    array(0 => $CaseReport, 1 => "align='center'"),
                    array(0 => $eCode, 1 => " title=\"$Description\" "),
                    array(0 => $AppFileSTR, 1 => "align='center'"),
                    array(0 => $HSCode,),
                    array(0 => $MaterialQ, 1 => "align='center'"),
                    array(0 => $UseWay, 1 => "align='center'"),
                    array(0 => $clientproxy, 1 => "align='center'"),
                    array(0 => $QCImage, 1 => "align='center'"),
                    array(0 => $ClientFileSTR, 1 => "align='center'"),
                    array(0 => $MainWeight, 1 => "align='center'"),
                    array(0 => $Weight, 1 => "align='center'"),
                    array(0 => $MisWeight, 1 => "align='center'"),

                    array(0 => $PreChar . $Price . "&nbsp;", 1 => "align='right'"),
                    array(0 => $profitRMB, 1 => "align='center'"),
                    array(0 => $PackingUnit, 1 => "align='center'"),
                    array(0 => $Code, 1 => "align='center'"),
                    array(0 => $ShipQtySum, 1 => "align='center'", 2 => $TempInfo),
                    array(0 => $ReturnedQty, 1 => "align='center'", 2 => $TempInfo2),
                    array(0 => $tStockQty, 1 => "align='center'"),
                    array(0 => $LastShipMonth, 1 => "align='center'"),
                    array(0 => $JqAvg, 1 => "align='center'"),
                    array(0 => $pRemark, 1 => "align='center'"),
                    array(0 => $dzSign, 1 => "align='center'"),
                    array(0 => $TypeName),
                    array(0 => $bgName, 1 => "align='center'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,30,$mProductId,1)' style='CURSOR: pointer'"),
                    array(0 => $InspectionSign, 1 => "align='center'"),
                    array(0 => $Estate, 1 => "align='center'"),
                    /*array(0=>$CodeFile,			1=>"align='center'"),
                    array(0=>$LableFile,		1=>"align='center'"),
                    array(0=>$WhiteFile,			1=>"align='center'"),*/
                    array(0 => $BoxFile, 1 => "align='center'"),
                    array(0 => $ReturnReasons),
                    array(0 => $CerImg, 1 => "align='center'"),
                    array(0 => $Img_H1, 1 => "align='center'", 2 => "onmousedown='window.event.cancelBubble=true;'"),
                    array(0 => $Img_H2, 1 => "align='center'", 2 => "onmousedown='window.event.cancelBubble=true;'"),
                    array(0 => $productsize, 2 => "onmousedown='window.event.cancelBubble=true;' onclick='aiaxUpdate($i,38,$mProductId,3)' style='CURSOR: pointer'"),
                    array(0 => $bjRemark, 1 => "align='left'", 2 => "onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,39,$mProductId,2)' style='CURSOR: pointer'")
                );

            } else {
                //1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,8,$mProductId,4)' style='CURSOR: pointer'"
                $ValueArray = array(
                    array(0 => $Client),
                    array(0 => $ProductId, 1 => "align='center'"),
                    array(0 => $buySign, 1 => "align='center'"),
                    array(0 => $TestStandard, 2 => "onmousedown='window.event.cancelBubble=true;'", 3 => "line"),
                    array(0 => $CaseReport, 1 => "align='center'"),
                    array(0 => $eCode, 1 => "title=\"$Description\""),
                    array(0 => $ReplaceNO, 1 => "align='center'"),
                    array(0 => $Reason, 1 => "align='center'"),
//				array(0=>$AppFileSTR,		1=>"align='center'"),
//				array(0=>$HSCode,		    ),
//				array(0=>$MaterialQ,		1=>"align='center'"),
//				array(0=>$UseWay,		    1=>"align='center'"),
//				array(0=>$clientproxy,		1=>"align='center'"),
//
//                array(0=>$QCImage,		    1=>"align='center'"),
//                array(0=>$ClientFileSTR,	1=>"align='center'"),
                    array(0 => $MainWeight, 1 => "align='center'"),
                    array(0 => $Weight, 1 => "align='center'"),
                    array(0 => $MisWeight, 1 => "align='center'"),
                    array(0 => $PreChar . $Price, 1 => "align='right'"),
                    array(0 => $PackingUnit, 1 => "align='center'"),
                    array(0 => $Code, 1 => "align='center'"),
                    array(0 => $ShipQtySum, 1 => "align='center'", 2 => $TempInfo),
                    array(0 => $ReturnedQty, 1 => "align='center'", 2 => $TempInfo2),
                    array(0 => $tStockQty, 1 => "align='center'"),
//				array(0=>$LastShipMonth,	1=>"align='center'"),
//				array(0=>$JqAvg,			1=>"align='center'"),
//				array(0=>$pRemark,			1=>"align='center'"),
//				array(0=>$dzSign,			1=>"align='center'"),
                    array(0 => $TypeName),
//				array(0=>$bgName,			1=>"align='center'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='updateJq($i,30,$mProductId,1)' style='CURSOR: pointer'"),
//				array(0=>$InspectionSign,			1=>"align='center'"),
//				array(0=>$Estate,			1=>"align='center'"),
//				/*array(0=>$CodeFile,		1=>"align='center'"),
//				array(0=>$LableFile,		1=>"align='center'"),
//				array(0=>$WhiteFile,		1=>"align='center'"),*/
//				array(0=>$BoxFile,			1=>"align='center'"),
//				array(0=>$ReturnReasons),
//				array(0=>$CerImg,			1=>"align='center'"),
//				array(0=>$Img_H1,			1=>"align='center'",	2=>"onmousedown='window.event.cancelBubble=true;'"),
//				array(0=>$Img_H2,			1=>"align='center'",	2=>"onmousedown='window.event.cancelBubble=true;'"),
//				array(0=>$productsize,2=>"onmousedown='window.event.cancelBubble=true;' onclick='aiaxUpdate($i,38,$mProductId,3)' style='CURSOR: pointer'"),
//				array(0=>$bjRemark,1=>"align='left'",2=>"onmousedown='window.event.cancelBubble=true;' onclick='aiaxUpdate($i,39,$mProductId,2)' style='CURSOR: pointer'"),

                );
            }
            $checkidValue = $Id;
            include "../model/subprogram/read_model_6.php";
        } else {
            $KillRecord += 1;
        }
    } while ($myRow = mysql_fetch_array($myResult));
}
if ($i == 1) {
    noRowInfo($tableWidth);
}
//步骤7：
include "../model/subprogram/ColorInfo.php";
echo '</div>';
$myResult = mysql_query($mySql, $link_id);
$RecordToTal = mysql_num_rows($myResult);
$RecordToTal -= $KillRecord;
pBottom($RecordToTal, $i - 1, $Page, $Pagination, $Page_Size, $timer, $Login_WebStyle, $tableWidth);
include "../model/subprogram/read_model_menu.php";
?>
<div id='winDialog'
     style="position:absolute;display:none;z-index:9;-moz-border-radius:12px;-webkit-border-radius:12px;border: 2px solid #333;background:#CCC;"
     onDblClick="closeWinDialog()"></div>
<div id="mask"
     style="text-align: center;vertical-align: middle;display: none;position:absolute;width:130%;height:11500%;background-color: rgba(0,0,0,0.2);z-index: 8;top:-30px;left:-40px">
    　
</div>
<script src='../cjgl/cj_function.js' type=text/javascript></script>
<script src='../model/IE_FOX_MASK.js' type=text/javascript></script>
<script type="text/javascript" src="../plugins/layer/layer.js"></script>
<script language="JavaScript" type="text/JavaScript">
    UTF8 = {
        encode: function (s) {
            for (var c, i = -1, l = (s = s.split("")).length, o = String.fromCharCode; ++i < l;
                 s[i] = (c = s[i].charCodeAt(0)) >= 127 ? o(0xc0 | (c >>> 6)) + o(0x80 | (c & 0x3f)) : s[i]
            ) ;
            return s.join("");
        },
        decode: function (s) {
            for (var a, b, i = -1, l = (s = s.split("")).length, o = String.fromCharCode, c = "charCodeAt"; ++i < l;
                 ((a = s[i][c](0)) & 0x80) &&
                 (s[i] = (a & 0xfc) == 0xc0 && ((b = s[i + 1][c](0)) & 0xc0) == 0x80 ?
                     o(((a & 0x03) << 6) + (b & 0x3f)) : o(128), s[++i] = "")
            ) ;
            return s.join("");
        }
    };

    function ViewChart(Pid, OpenType) {
        document.form1.action = "productdata_chart.php?Pid=" + Pid + "&Type=" + OpenType;
        document.form1.target = "_blank";
        document.form1.submit();
        document.form1.target = "_self";
        document.form1.action = "";
    }

    function updateJq(TableId, RowId, ProductId, toObj) {//行即表格序号;列，流水号，更新源
        showMaskBack();
        var InfoSTR = "";
        var buttonSTR = "";
        var theDiv = document.getElementById("Jp");
        var tempTableId = document.form1.ActionTableId.value;
        theDiv.style.top = event.clientY + document.body.scrollTop + 'px';
        if (toObj == 25) {
            theDiv.style.left = event.clientX + document.body.scrollLeft + 'px';
        } else {
            theDiv.style.left = event.clientX + document.body.scrollLeft - parseInt(theDiv.style.width) + 'px';
        }
        if (theDiv.style.visibility == "hidden" || toObj != ObjId || TableId != tempTableId) {
            document.form1.ActionTableId.value = TableId;
            document.form1.ActionRowId.value = RowId;
            document.form1.ObjId.value = toObj;
            switch (toObj) {

                case 1://报关方式
                    InfoSTR = "产品ID为:<input name='ProductId' type='text' id='ProductId' value='" + ProductId + "' size='8' class='TM0000' readonly/>的报关方式:<select id='tmptaxtypeId' name='tmptaxtypeId' style='width:150px;'><option value='' 'selected'>请选择</option>";
                <?PHP
                $OutInfo = "";
                $taxtypeResult = mysql_query("SELECT * FROM $DataIn.taxtype WHERE Estate=1 order by Id  ", $link_id);
                if ($ToOutNameRow = mysql_fetch_array($taxtypeResult)) {
                    do {
                        $ptId = $ToOutNameRow["Id"];
                        $ptName = $ToOutNameRow["Name"];
                        $OutInfo .= "<option value='$ptId' >  $ptName </option>";
                    } while ($ToOutNameRow = mysql_fetch_array($taxtypeResult));
                }
                ?>
                    InfoSTR = InfoSTR + "<?PHP echo $OutInfo; ?>" + "</select><br>";
                    break;
                    break;
                case 2:	//报价规则
                    InfoSTR = "更新产品ID为<input name='ProductId' type='text' id='ProductId' value='" + ProductId + "' size='8' class='TM0000' readonly>的报价规则<textarea name='bjRemark' cols = '50' rows='3' id='bjRemark'></textarea><br>";
                    break;
                case 3:	//产品尺寸
                    InfoSTR = "更新产品ID为<input name='ProductId' type='text' id='ProductId' value='" + ProductId + "' size='8' class='TM0000' readonly>的产品尺寸<textarea name='productsize' cols = '50' rows='3' id='productsize'></textarea><br>";
                    break;

                case 4:	//产品海关编码
                    InfoSTR = "更新产品ID为<input name='ProductId' type='text' id='ProductId' value='" + ProductId + "' size='8' class='TM0000' readonly>的海关编码<textarea name='HSCode' cols = '50' rows='3' id='HSCode'></textarea><br>";
                    break;
            }


            var buttonSTR = "&nbsp;<div align='right'><input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='更新' onclick='aiaxUpdate()'>&nbsp;<input type='button' name='Submit' class=btn1_mouseout onmouseover='this.className=\"btn1_mouseover\"' value='取消' onclick='CloseDiv()'>";


            infoShow.innerHTML = InfoSTR + buttonSTR;
            theDiv.className = "moveRtoL";
            if (isIe()) {  //只有IE才能用   add by zx 加入庶影   20110323  IE_FOX_MASK.js
                theDiv.filters.revealTrans.apply();//防止错误
                theDiv.filters.revealTrans.play(); //播放
            } else {
                theDiv.style.opacity = 0.9;
            }
            theDiv.style.visibility = "";
            theDiv.style.display = "";
        }
    }

    function CloseDiv() {
        var theDiv = document.getElementById("Jp");
        theDiv.className = "moveLtoR";
        if (isIe()) {  //只有IE才能用 add by zx 加入庶影   20110323  IE_FOX_MASK.js
            theDiv.filters.revealTrans.apply();
            //theDiv.style.visibility = "hidden";
            theDiv.filters.revealTrans.play();
        }
        theDiv.style.visibility = "hidden";
        //theDiv.filters.revealTrans.play();
        infoShow.innerHTML = "";
        closeMaskBack();
    }


    function aiaxUpdate() {
        var ObjId = document.form1.ObjId.value;
        var tempTableId = document.form1.ActionTableId.value;
        var tempRowId = document.form1.ActionRowId.value;
        var tempProductId = document.form1.ProductId.value;
        switch (ObjId) {

            case "1": //产品报关方式
                var taxtypeId = document.form1.tmptaxtypeId.value;
                //alert(taxtypeId);
                var taxtypeObj = document.form1.tmptaxtypeId;
                var taxtypeNameText = "";
                for (i = 1; i < taxtypeObj.length; i++) {
                    if (taxtypeObj[i].selected == true) {
                        taxtypeNameText = taxtypeObj[i].innerText;
                    }
                }

                myurl = "productdata_updated_ajax.php?ProductId=" + tempProductId + "&taxtypeId=" + taxtypeId + "&ActionId=taxtype";
                var ajax = InitAjax();
                ajax.open("GET", myurl, true);
                ajax.onreadystatechange = function () {
                    if (ajax.readyState == 4) {// && ajax.status ==200
                        if (taxtypeId.length > 0)
                            eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = taxtypeNameText;
                        else
                            eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "&nbsp;";
                        CloseDiv();
                    }
                }
                ajax.send(null);
                break;


            case "2"://更新报价规则
                var tempbjRemark = document.form1.bjRemark.value;
                var tempbjRemark1 = encodeURIComponent(tempbjRemark);//传输中文
                myurl = "productdata_updated_ajax.php?ProductId=" + tempProductId + "&bjRemark=" + tempbjRemark1 + "&ActionId=bjRemark";
                var ajax = InitAjax();
                ajax.open("GET", myurl, true);
                ajax.onreadystatechange = function () {
                    if (ajax.readyState == 4) {// && ajax.status ==200
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title=''><NOBR>&nbsp;" + tempbjRemark + "</NOBR></DIV>";
                        CloseDiv();
                    }
                }
                ajax.send(null);
                break;

            case "3"://更新产品尺寸
                var tempproductsize = document.form1.productsize.value;
                var tempproductsize1 = encodeURIComponent(tempproductsize);//传输中文
                myurl = "productdata_updated_ajax.php?ProductId=" + tempProductId + "&productsize=" + tempproductsize1 + "&ActionId=productsize";
                var ajax = InitAjax();
                ajax.open("GET", myurl, true);
                ajax.onreadystatechange = function () {
                    if (ajax.readyState == 4) {// && ajax.status ==200
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title=''><NOBR>&nbsp;" + tempproductsize + "</NOBR></DIV>";
                        CloseDiv();
                    }
                }
                ajax.send(null);
                break;

            case "4"://更新海关编码
                var tempHSCode = document.form1.HSCode.value;
                var tempHSCode1 = encodeURIComponent(tempHSCode);//传输中文
                myurl = "productdata_updated_ajax.php?ProductId=" + tempProductId + "&HSCode=" + tempHSCode1 + "&ActionId=HSCode";
                var ajax = InitAjax();
                ajax.open("GET", myurl, true);
                ajax.onreadystatechange = function () {
                    if (ajax.readyState == 4) {// && ajax.status ==200
                        eval("ListTable" + tempTableId).rows[0].cells[tempRowId].innerHTML = "<DIV STYLE='width:$Field[$m] px;overflow: hidden; text-overflow:ellipsis' title=''><NOBR>&nbsp;" + tempHSCode + "</NOBR></DIV>";
                        CloseDiv();
                    }
                }
                ajax.send(null);
                break;
        }
    }

    // 刷新
    function ResetPages(e) {
        switch (e) {
            case 'CompanyId':
                document.forms["form1"].elements["buildFloor"].value = "";
                document.forms["form1"].elements["ProductType"].value = "";
                document.form1.submit();
                break;
            case 'buildFloor':
                document.forms["form1"].elements["ProductType"].value = "";
                document.form1.submit();
                break;
            case 'ProductType':
                document.form1.submit();
                break;
        }
    }

    // 替板
    function Replace(e) {
        jQuery("#Replace").on("click",function () {


            var choosedRow = 0;
            var value = '';
            jQuery('input[name^="checkid[]"]:checkbox').each(function () {
                if (jQuery(this).prop('checked') == true) {
                    choosedRow = choosedRow + 1;
                    if (choosedRow === 1) {
                        value = jQuery(this).val();
                    }
                }

            });
            var CompanyId = jQuery("#CompanyId").val();
            value = value + "&action=" + e + "&CompanyId=" + CompanyId;
            if (choosedRow == 0) {
                layer.msg("该操作要求选定记录！", function () {
                });
                return;
            }
            if (choosedRow > 1) {
                layer.msg("该操作只能选取定一条记录!", function () {
                });
                return;
            }

            openWinDialogWithParas(this, "productdata_replace.php", 600, 750, 'center', value);
        })
    }

    function RepairSave() {
        var ids = jQuery(" input[ name='ids' ] ").val();

        //替换
        var refund = jQuery(" input[ name='refund' ] ").val();

        // var buildFloor = jQuery("#BF").val();
        // if (buildFloor == '') {
        //     layer.msg('请选择替换楼层！', function () {
        //     });
        //     return;
        // }

        var GID = jQuery("#GID").val();
        if (GID == '') {
            layer.msg('请选择替换构件！', function () {
            });
            return;
        }

        var THNO = jQuery("#THNO").val();
        if (THNO == '') {
            layer.msg('请填入替换单号！', function () {
            });
            return;
        }

        var reason = jQuery("#reason").val();
        // if (reason == '') {
        //     layer.msg('请填写替换原因！', function () {
        //     });
        //     return;
        // }


        var formFile = new FormData();
        formFile.append("ActionId", refund);
        formFile.append("Ids", ids);
        formFile.append("GID", GID);
        formFile.append("THNO", THNO);
        formFile.append("Reason", reason);

        var ajax = InitAjax();
        ajax.open("POST", 'productdata_updated_ajax.php', true);
        ajax.onreadystatechange = function (result) {
            if (ajax.readyState == 4 && ajax.status == 200) {// && ajax.status ==200
                //alert(ajax.responseText);
                //el.innerHTML=tempWeeks;
                if (ajax.responseText == "Y") {
                    layer.msg('构件替换成功！', {icon: 1}, function () {
                        RefreshPage("<?php echo $nowWebPage; ?>")
                    });
                } else {
                    //layer.msg(ajax.responseText, {icon: 2}, function () {
                    //    RefreshPage("<?php //echo $nowWebPage; ?>//")
                    //});
                    layer.confirm(ajax.responseText, {
                        btn: ['确定'] //按钮
                        ,icon: 2
                    }, function(index){
                        layer.close(index);
                        //RefreshPage("<?php //echo $nowWebPage; ?>//")
                    })
                }
            }
        };
        ajax.send(formFile);
    }

    //选择替换构件
    function ChooseG() {

        var CompanyId = jQuery("#CompanyId").val();

        layer.open({
            type: 2,
            title: '替换构件',
            area: ['800px', '500px'],
            btn: ['确定', '取消'],
            fixed: false, //不固定
            // maxmin: true,
            offset: '150px',
            content: 'productdata_choose.php?companyId=' + CompanyId,
            success: function (layero) {
                layero.find('.layui-layer-btn').css('text-align', 'center')
            },
            yes: function (index) {//

                var chooses = 0;
                var val = '';
                var GName = '';
                var Field = '';
                var $table = layer.getChildFrame('.ListTable tbody', index);
                $table.find('input[type=checkbox]:checked').each(function () {
                    chooses = chooses + 1;
                    if (chooses === 1) {
                        val = jQuery(this).val().split("|");

                        Field = val[1].split("-");

                        for (var i = 2; i < Field.length - 1; i++) {
                            if (i == 2) {
                                GName = Field[i];
                            } else {
                                GName += '-' + Field[i];
                            }

                        }
                    }
                });


                if (chooses == 0) {
                    layer.msg("该操作要求选定记录！", function () {
                    });
                    return;
                }
                if (chooses > 1) {
                    layer.msg("该操作只能选取定一条记录!", function () {
                    });
                    return;
                }

                jQuery("#GID").val(val[0]);
                jQuery("#GIdShow").val(' ' + Field[0] + '# ' + Field[1] + 'F      ' + GName);
                // jQuery("#GIdShow").before(' <input type="text" class="ButtonH_X" disabled value="666" required="required"/>  ');
                layer.close(index);
            },
            btn2: function (index) {//layer.alert('aaa',{title:'msg title'});  ////点击取消回调
                layer.close(index);
            }

        });
    }

    // 撤销替板
    function ReplaceBack() {
        var choosedRow = 0;
        var value = '';
        jQuery('input[name^="checkid[]"]:checkbox').each(function () {
            if (jQuery(this).prop('checked') == true) {
                choosedRow = choosedRow + 1;
                if (choosedRow === 1) {
                    value = jQuery(this).val();
                } else {
                    value = value + '|' + jQuery(this).val();
                }
            }

        });


        if (choosedRow == 0) {
            layer.msg("该操作要求选定记录！", function () {
            });
            return;
        }
        if (choosedRow > 1) {
            layer.msg("该操作只能选取定一条记录!", function () {
            });
            return;
        }
        layer.open({
            type: 1
            ,title: false //不显示标题栏
            ,closeBtn: false
            ,area: '300px;'
            ,offset: '150px'
            ,shade: 0.8
            ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
            ,resize: false
            ,btn: ['确定', '取消']
            ,btnAlign: 'c'
            ,moveType: 1 //拖拽模式，0或者1
            ,content: '<div style="padding: 30px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;font-size: medium;">您确定撤销替板？</div>'
            ,success: function(layero){
                var btn = layero.find('.layui-layer-btn');
                btn.find('.layui-layer-btn0').attr({
                    href: 'javascript:;'
                    ,onclick:'back('+value+')'
                });
            }
        });
    }
    function back(e) {
        var formFile = new FormData();
        formFile.append("ActionId", 'back');
        formFile.append("Ids", e);

        var ajax = InitAjax();
        ajax.open("POST", 'productdata_updated_ajax.php', true);
        ajax.onreadystatechange = function (result) {
            if (ajax.readyState == 4 && ajax.status == 200) {// && ajax.status ==200
                //alert(ajax.responseText);
                //el.innerHTML=tempWeeks;
                var str = ajax.responseText.replace(/\s+/g,"");
                if (str == "Y") {
                    layer.msg('撤销替板成功！', {icon: 1}, function () {
                        RefreshPage("<?php echo $nowWebPage; ?>")
                    });
                } else {
                    //layer.msg(ajax.responseText, {icon: 2},{time:5000}, function () {
                    //    RefreshPage("<?php //echo $nowWebPage; ?>//")
                    //});
                    layer.confirm(str, {
                        btn: ['确定'] //按钮
                        ,icon: 2
                    }, function(index){
                        layer.close(index);
                        RefreshPage("<?php echo $nowWebPage; ?>")
                    })
                }
            }
        };
        ajax.send(formFile);
    }

</script>

