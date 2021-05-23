<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$tableWidth = 500;

$clientResult = mysql_query("SELECT M.CompanyId,C.Forshort 
	FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C 
	WHERE 1 AND C.CompanyId=M.CompanyId $SearchRows GROUP BY M.CompanyId ORDER BY M.CompanyId", $link_id);
if ($clientRow = mysql_fetch_array($clientResult)) {
    $clientList = "<select name='Company' id='Company' style='width:125px;height:22px;color:#009900;'>";
    do {
        $thisCompanyId = $clientRow["CompanyId"];
        $Forshort = $clientRow["Forshort"];
        if ($CompanyId == $thisCompanyId) {
            $clientList .= "<option value='$thisCompanyId' selected>$Forshort</option>";
        }
        else {
            $clientList .= "<option value='$thisCompanyId'>$Forshort</option>";
        }
    } while ($clientRow = mysql_fetch_array($clientResult));
    $clientList .= "</select>&nbsp;";
}
?>

<table width="<?php echo $tableWidth ?>px" border="0" align="left" cellspacing="5">
  <tr>
    <td>
      　　　　客户：<?php echo $clientList ?>
    </td>
    <td>
    </td>
  </tr>
  <tr>
    <td>
      　　起始日期：<input type="date" id="start_date" name="start_date" value="">
    </td>
    <td>
      　　结束日期：<input type="date" id="end_date" name="end_date" value="">
    </td>
  </tr>
</table>

<table width="<?php echo $tableWidth ?>" border="0" align="center" cellspacing="5">
  <tr>
    <td>&nbsp;</td>
    <td align="center">
      <sapn class='ButtonH_25' id='changeBtn' value='导出' onclick='exportState()'>导出</sapn>
    </td>
    <td align="center">
      <span class='ButtonH_25' id='cancelBtn' value='取消' onclick='closeWinDialog()'>取消</span></td>
    <td>&nbsp;</td>
  </tr>
</table>
