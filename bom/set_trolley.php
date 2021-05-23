<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
?>
<input type="hidden" id="input_ids" value="">
<div  style="min-height:450px">
<table width="<?php echo $tableWidth ?>px" border="0" align="center" cellspacing="0" bgcolor="white">
  <tr bgcolor="#87ceeb" style="height:30px;border:none">
    　
    <td align="center" width="10">　</td>
    <td align="center" width="110">顺序号</td>
    　
    <td align="center" width="120">模具类别</td>
    　
    <td align="center" width="120">模具编号</td>
    　
    <td align="center" width="120">台车号</td>
    　
  </tr>
    <?php
    $ids = explode('|', $_GET['ids']);
    $tableWidth = 600;
    $sql = "
SELECT A.Id,A.MouldCat, A.MouldNo,A.TrolleyId
FROM $DataIn.bom_mould A
";

    // 添加查询条件
    if (is_array($ids)) {
        foreach ($ids as $k => $id) {
            if ($k === 0) {
                $mySql .= "($sql WHERE A.id = $id)";
            }
            else {
                $mySql .= " UNION ALL ($sql WHERE A.id = $id)";
            }
        }
    }

//        if ($Login_P_number = '10058') echo $mysql;
    $ret = mysql_query($mySql, $link_id);
    if ($res = mysql_fetch_array($ret)) {
        do {
            // 获取数据
            $Id = $res['Id'];
            $MouldCat = $res['MouldCat'];
            $MouldNo = $res['MouldNo'];
            $TrolleyId = $res['TrolleyId'];
            // 数据处理及验证
            echo "<tr style=\"height:30px;border:none\">";
            echo "<td align=\"center\"><input type='hidden' name='need' value=\"$Id\"></td>";
            echo "<td align=\"center\">$Id</td>";
            echo "<td align=\"center\">$MouldCat</td>";
            echo "<td align=\"center\">$MouldNo</td>";
            echo "<td align=\"center\" name='trolleyId' contenteditable='true' style='position:relative'>$TrolleyId <img src='../images/edit.gif' title='可编辑' style='position:absolute;top:5px;right:5px;'></td>";
            echo "</tr>";
        } while ($res = mysql_fetch_array($ret));
    }
    ?>
</table>
</div>
<table width="<?php echo $tableWidth ?>" border="0" align="center" cellspacing="5" >
  <tr>
    <td>&nbsp;</td>
    <td align="center">
      <span class='ButtonH_25' id='changeBtn' value='确认' onclick='setTrolleyconfirm()'>确认</span>
    </td>
    <td align="center">
      <span class='ButtonH_25' id='cancelBtn' value='取消' onclick='closeWinDialog()'>取消</span></td>
    <td>&nbsp;</td>
  </tr>
</table>
