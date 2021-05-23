<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$tableWidth = 400;
$SeatIdResult= mysql_query("SELECT SeatId
    FROM wms_seat
    WHERE WareHouse='成品仓库' order by SeatId",$link_id);
if ($SeatIdRow = mysql_fetch_array($SeatIdResult)){
    $SeatIdList="<select name='SeatId' id='SeatId' style='height:30px'>";
    do{
        $theSeatId=$SeatIdRow["SeatId"];

        $SeatIdList.="<option value='$theSeatId'>$theSeatId</option>";
    }while($SeatIdRow = mysql_fetch_array($SeatIdResult));
    $SeatIdList.="</select>";
}

?>


<table width="<?php echo $tableWidth ?>" border="0" align="center" >
  <tr>
    <td  align="center" colspan="2">
      　　请选择库位：<?php echo $SeatIdList; ?>
    </td>

  </tr>
    <tr>
        <td  align="center" colspan="2" style="padding: 15px 0">
            　　入库单号：<input type="text" name="storageNO" id="storageNO" width="20" required="required" placeholder="请填写入库单号" value="">
        </td>

    </tr>
    <tr>
        <td  align="center" colspan="2" style="padding:0 0 15px 0">
            　　入库垛号：<input type="text" name="stackNO" id="stackNO" width="20" required="required" placeholder="请填写入库垛号" value="">
        </td>

    </tr>
  <tr>
    <td align="center">
      <span class='ButtonH_25' id='changeBtn' value='变更' onclick='doPassRkData(1)'>设置</span>
    </td>
    <td align="center">
      <span class='ButtonH_25' id='cancelBtn' value='取消' onclick='closeWinDialog()'>取消</span></td>
  </tr>
</table>
