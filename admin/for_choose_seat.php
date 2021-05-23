<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$tableWidth = 400;
$SeatIdResult = mysql_query("SELECT SeatId
    FROM wms_seat
    WHERE WareHouse='成品仓库' order by SeatId", $link_id);
if ($SeatIdRow = mysql_fetch_array($SeatIdResult)) {
    $SeatIdList = "<select name='SeatId1' id='SeatId1' style='height:30px'>";
    do {
        $theSeatId = $SeatIdRow["SeatId"];
        $SeatId1 = $SeatId1 == '' ? $theSeatId : $SeatId1;
        if ($SeatId1 == $theSeatId) {
            $SeatIdList .= "<option value='$theSeatId' selected>$theSeatId</option>";
        }
        else {
            $SeatIdList .= "<option value='$theSeatId' >$theSeatId</option>";
        }
    } while ($SeatIdRow = mysql_fetch_array($SeatIdResult));
    $SeatIdList .= "</select>";
}

?>
<style>
  .grey_table td {
    background-color: #ccc;
    border: none
  }

  .grey_table tr {
    border: none
    background-color: #ccc;
  }

  .grey_table {
    border-radius: 12px;
    background-color: #ccc;
    border: none
  }
</style>
<table width="<?php echo $tableWidth ?>" class="grey_table" border="0" align="center" cellspacing="5">
  <tr>
    <td align="center">
      　　请选择库位：<?php echo $SeatIdList; ?>
    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>

  </tr>
</table>

<table width="<?php echo $tableWidth ?>" class="grey_table" border="0" align="center" cellspacing="5">
  <tr>
    <td>&nbsp;</td>
    <td align="center">
      <span class='ButtonH_25' id='changeBtn' value='变更' onclick='doPassRkData(1)'>设置</span>
    </td>
    <td align="center">
      <span class='ButtonH_25' id='cancelBtn' value='取消' onclick='closeWinDialog()'>取消</span></td>
    <td>&nbsp;</td>
  </tr>
</table>
