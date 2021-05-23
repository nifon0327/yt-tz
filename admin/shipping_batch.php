<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$tableWidth = 400;
$date = date("Y-m-d");
$Company = $_GET['Company'];

$ClientResult = mysql_query("
	SELECT M.CompanyId,IFNULL(C.Forshort,'未知项目')  AS Forshort
	FROM $DataIn.ch1_shipsplit SP  
    LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	LEFT JOIN  $DataIn.productdata P ON P.ProductId = S.ProductId
	LEFT JOIN  $DataIn.productstock K ON K.ProductId = P.ProductId
	WHERE 1 $SearchRows  AND M.CompanyId IS NOT NULL AND K.tStockQty >= SP.Qty GROUP BY M.CompanyId 
    UNION
	SELECT S.CompanyId,IFNULL(C.Forshort,'未知项目')  AS Forshort FROM $DataIn.ch5_sampsheet S 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
	WHERE 1  and S.Estate='1'
	", $link_id);
            if ($ClientRow = mysql_fetch_array($ClientResult)) {
                $company =  "&nbsp;<select name='Company' id='Company' style='width:284px' onchange='valueChange(this)'>";
                do {
                    $theCompanyId = $ClientRow["CompanyId"];
                    $theForshort = $ClientRow["Forshort"];
//                    $company .= "<option value='$theCompanyId'>$theForshort</option>";

                    $Company = $Company == "" ? $theCompanyId : $Company;
                    if ($Company == $theCompanyId) {
                        $company .=  "<option value='$theCompanyId' selected>$theForshort</option>";
                    } else {
                        $company .=  "<option value='$theCompanyId'>$theForshort</option>";
                    }

                } while ($ClientRow = mysql_fetch_array($ClientResult));
                $company .= "</select>&nbsp;";
            }

?>

<table width="<?php echo $tableWidth ?>px" border="0" align="left" cellspacing="5">
    <tr>
        <td>
            　　项目名称: <?php echo $company?>
        </td>
        </tr>
    <tr>
        <td>
            　　设置交期：<textarea name="cname" id="cname" cols="40" rows="10" placeholder="楼栋 楼层 构件号,每个构件以空格或换行隔开"></textarea>
        </td>
    </tr>
</table>

<table width="<?php echo $tableWidth ?>" border="0" align="center" cellspacing="5">
    <tr>
        <td>&nbsp;</td>
        <td align="center">
            <sapn class='ButtonH_25' id='changeBtn' value='变更' onclick='batchInput()'>导出二维码</sapn>
        </td>
        <td align="center">
            <span class='ButtonH_25' id='cancelBtn' value='取消' onclick='closeWinDialog()'>取消</span></td>
        <td>&nbsp;</td>
    </tr>
</table>