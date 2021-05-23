<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth = 500;
$funFrom = "productdata_replace";

$Ids = str_replace("|", ",", "$ids");
//构件
$GJSql = "SELECT
	P.Id,
	P.ProductId,
	P.cName,
	P.eCode,
	P.CompanyId,
	P.Estate,
	T.TypeName,
	C.Forshort
FROM
	$DataIn.productdata P
	LEFT JOIN $DataIn.producttype T ON T.TypeId = P.TypeId
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId = P.CompanyId
	LEFT JOIN $DataIn.customscode H ON H.ProductId = P.ProductId
	LEFT JOIN $DataIn.productmq M ON M.Id = P.MaterialQ
	LEFT JOIN $DataIn.productuseway W ON W.Id = P.UseWay
	LEFT JOIN $DataIn.currencydata D ON D.Id = C.Currency
	LEFT JOIN $DataIn.taxtype BG ON BG.Id = P.taxtypeId
	LEFT JOIN $DataIn.productstock S ON S.ProductId = P.ProductId
	LEFT JOIN $DataIn.product_property B ON B.Id = P.buySign
	LEFT JOIN (
	SELECT
		DATE_FORMAT( MAX( M.Date ), '%Y-%m' ) AS LastMonth,
		TIMESTAMPDIFF( MONTH, MAX( M.Date ), now( ) ) AS Months,
		S.ProductId 
	FROM
		$DataIn.ch1_shipmain M
		LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id 
	WHERE
		1 
	GROUP BY
		S.ProductId 
	ORDER BY
		M.Date DESC 
	) E ON E.ProductId = P.ProductId
	LEFT JOIN $DataIn.productstandimg PM ON PM.ProductId = P.ProductId 
WHERE
	P.Id IN ($Ids)
ORDER BY
	Id DESC";

$result = mysql_query($GJSql, $link_id);
$i = 1;
while ($myrow = mysql_fetch_array($result)) {
    $eCode = $myrow["eCode"];
    $TypeName = $myrow["TypeName"];
    $Forshort = $myrow["Forshort"];
    $GJ .= "<tr>
            <td align='center'>$i</td>
            <td align='center'>$Forshort</td>
            <td align='center'>$TypeName</td>
            <td align='center'>$eCode</td>
        </tr>";
    $i++;
}

?>
<style>
    .input {
        height: 30px;
        line-height: 1.3;
        border-width: 1px;
        border-style: solid;
        background-color: #fff;
        border-radius: 2px;
    }

    .ButtonH_25 {
        font: 14px 思源雅黑;
        color: #fff !important;
        background-color: #48bbcb;
        border-radius: 2px;
        height: 28px;
        line-height: 28px;
        width: 60px;
        padding-left: 8px;
        padding-right: 8px;
        text-align: center;
        display: inline-block;
    }

    .ButtonH_X {
        font: 12px 思源雅黑;
        color: #fff !important;
        background-color: #6e6e6e;
        border-radius: 3px;
        height: 27px;
        line-height: 27px;
        width: 40px;
        padding-left: 8px;
        padding-right: 8px;
        text-align: center;
        display: inline-block;
    }

    .tr {
        height: 45px;
    }
</style>
<script>
    document.oncontextmenu = function () {
        return false;
    };
</script>
<script src='../model/jquery_corners/jquery-1.8.3.js'></script>
<script src="../plugins/layer/layer.js"></script>
<form enctype="multipart/form-data" id="repairForm">
    <div style="width: 100%;text-align: center">
        <table width="80%" border="1" align="center" cellspacing="0" style="margin-top: 10px;">
            <?php echo $GJ ?>
        </table>
        <table width="<?php echo $tableWidth ?>" border="0" align="center" cellspacing="5">
            <tr class="tr">
                <td align="center" height="30">替换构件</td>
                <td align="left">
                    <!--                    <select name="BF" id="BF" style="width: 7em;" class="input" dataType="Require" msg="未选择返修单位"-->
                    <!--                            required="required">-->
                    <!--                        --><?php
                    //                        //楼栋层
                    //                        $BFSql = "SELECT substring_index( P.cName, '-', 1 ) AS build,substring_index((substring_index( P.cName, '-', 2 ) ), '-', -1 ) as floor
                    //		FROM $DataIn.pands A
                    //		LEFT JOIN $DataIn.productdata P ON A.ProductId=P.ProductId
                    //		LEFT JOIN $DataIn.trade_object M ON M.CompanyId=P.CompanyId
                    //		WHERE M.Estate>0 AND P.CompanyId = '$CompanyId' GROUP BY build+0,Floor+0 ";
                    //                        $BFResult = mysql_query($BFSql);
                    //                        echo "<option value='' selected>请选择</option>";
                    //                        if ($BFRow = mysql_fetch_array($BFResult)) {
                    //                            do {
                    //                                $theBuild = $BFRow["build"];
                    //                                $theFloor = $BFRow["floor"];
                    //                                $thebuildFloor = $theBuild . '-' . $theFloor;
                    //                                echo "<option value='$thebuildFloor'>$theBuild 栋 $theFloor 层</option>";
                    //                            } while ($BFRow = mysql_fetch_array($BFResult));
                    //                        }
                    //                        ?>
                    <!--                    </select>-->
                    <input type="text" name="GID" id="GID" class="input" dataType="Require"  style="display: none"
                           required="required"/>
                    <input type="text" name="GIdShow" id="GIdShow" class="input" dataType="Require" onclick="ChooseG()"
                           required="required"/>
                </td>
            </tr>
            <tr class="tr">
                <td align="center">替换单号</td>
                <td><input type="text" name="THNO" id="THNO" class="input" dataType="Require"
                           required="required"/></td>
            </tr>
            <tr class="tr">
                <td align="center">替换原因</td>
                <td><textarea name="reason" cols="38" rows="3" id="reason" dataType="Require"
                              required="required"></textarea></td>
            </tr>
        </table>
        <table width="<?php echo $tableWidth ?>" border="0" align="center" cellspacing="5">
            <tr class="tr">
                <td>&nbsp;</td>
                <td align="center"><span class='ButtonH_25' onclick='RepairSave()'>保存</span></td>
                <td align="center"><span class='ButtonH_25' id='cancelBtn' onclick='closeWinDialog()'>取消</span></td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>
    <input type="hidden" id="ids" name="ids" value="<?php echo $ids; ?>">
    <input type="hidden" id="refund" name="refund" value="<?php echo $action; ?>">
</form>
