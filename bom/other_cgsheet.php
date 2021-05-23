<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
?>
<link rel="stylesheet" href="../model/css/sharing.css">
<script src='../model/jquery_corners/jquery-1.8.3.js'></script>
<input type="hidden" id="input_Ids" value="">

<table width="<?php echo $tableWidth ?>px" border="0" align="center" cellspacing="0" bgcolor="white">
  <tr bgcolor="#87ceeb" style="height:30px;border:none">
    　
    <td align="center" width="10">　</td>
    <td align="center" width="110">栋号</td>
    　
    <td align="center" width="120">配件类型</td>
    　
    <td align="center" width="120">名称</td>
    　
    <td align="center" width="120">总数</td>
    　
    <td align="center" width="120">总额</td>
  </tr>
    <?php
    $ids = explode('|', $_GET['Ids']);
    $tableWidth = 600;
    $sql = "
SELECT B.BuildingNo, B.StuffType,B.TradeId, 
SD.StuffCname, SD.StuffId,
B.Quantity,B.Loss, SD.Price 
FROM $DataIn.bom_info B
LEFT JOIN $DataIn.stuffdata SD ON B.MaterNo = SD.StuffId ";

    // 添加查询条件
    if (is_array($ids)) {
        foreach ($ids as $k => $id) {
            if ($k === 0) {
                $mySql .= "($sql WHERE B.id = $id)";
            }
            else {
                $mySql .= " UNION ALL ($sql WHERE B.id = $id)";
            }
        }
    }
    $mysql = "SELECT GROUP_CONCAT(BuildingNo) AS BuildingNo,TradeId,
		StuffType,
		StuffCname,
		StuffId,
		sum(Quantity) as Quantity,
		sum(Loss) as Loss,
		Price FROM ($mySql) as a GROUP BY StuffCname";
//        if ($Login_P_number = '10058') echo $mysql;
    $ret = mysql_query($mysql, $link_id);
    if ($res = mysql_fetch_array($ret)) {
        do {
            // 获取数据
            $BuildingNo = $res['BuildingNo'];
            $TradeId = $res['TradeId'];
            $StuffType = $res['StuffType'];
            $StuffId = $res['StuffId'];
            $StuffCname = $res['StuffCname'];
            $Quantity = $res['Quantity'];
            $Loss = $res['Loss'];
            $Price = $res['Price'];
            // 数据处理及验证
            $total = round($Quantity + $Loss, 3);
            $totalAmount = round(($Quantity + $Loss) * $Price, 3);
            echo "<tr class='ListTable' style=\"height:30px;border:none\">";
            echo "<td class='A0110' align=\"center\"><input type='hidden' name='need' value=\"$StuffId|$total|$Price|$TradeId\"></td>";
            echo "<td class='A0101' align=\"center\">$BuildingNo</td>";
            echo "<td class='A0101' align=\"center\">$StuffType</td>";
            echo "<td class='A0101' align=\"center\">$StuffCname</td>";
            echo "<td class='A0101' align=\"center\" name='total' contenteditable='true' onblur=changeTotal(this) style='position:relative'>$total <img src='../images/edit.gif' title='可编辑' style='position:absolute;top:5px;right:5px;'></td>";
            echo "<td class='A0101' align=\"center\">$totalAmount</td>";
            echo "</tr>";
        } while ($res = mysql_fetch_array($ret));
    }
    ?>
</table>

<!--<table width="--><?php //echo $tableWidth ?><!--" border="0" align="center" cellspacing="5" >-->
<!--  <tr>-->
<!--    <td>&nbsp;</td>-->
<!--    <td align="center">-->
<!--      <span class='ButtonH_25' id='changeBtn' value='生成' onclick='doOtherCgsheet()'>生成</span>-->
<!--    </td>-->
<!--    <td align="center">-->
<!--      <span class='ButtonH_25' id='cancelBtn' value='取消' onclick='closeWinDialog()'>取消</span></td>-->
<!--    <td>&nbsp;</td>-->
<!--  </tr>-->
<!--</table>-->
<script>
    function changeTotal(e){
        var need = jQuery(e).parent().find("input[name='need']");
        var val = need.val();
        var ehtml = (e.innerText);
        var total = val.split('|');
        total.splice(1,1,ehtml);
        need.val(total.join('|'));
        var price = total.splice(2,1);
        var totalPrice = parseFloat(ehtml)*parseFloat(price);
        jQuery(e).next().html(totalPrice.toFixed(3));
        // alert('修改成功！');

    }
</script>