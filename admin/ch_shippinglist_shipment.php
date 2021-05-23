<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
?>
<input type="hidden" id="input_ids" value="">
<div  style="min-height:200px">
<table width="<?php echo $tableWidth ?>px" border="0" align="center" cellspacing="0" bgcolor="white">
  <tr bgcolor="#87ceeb" style="height:30px;border:none">
    　
        <td style="text-align: center;font-weight: bold;font-size: 15px">出货资料</td>
    </tr>
    <tr style="height: 40px">
        <td style="text-align: center;height: 30px;width: 350px;">出货单号：
            <?php
            //计算最后的Invoice编号
            $date = date("Y-m-d");
            $maxInvoiceNO=mysql_fetch_array(mysql_query("SELECT InvoiceNO,Id,CarNumber FROM $DataIn.ch1_shipmain WHERE Id = '$ids' ORDER BY InvoiceNO desc LIMIT 1",$link_id));
            if($maxInvoiceNO){
                $InvoiceNO=$maxInvoiceNO["InvoiceNO"];
                $Id=$maxInvoiceNO["Id"];
                 $CarNumber=$maxInvoiceNO["CarNumber"];
                echo "<input type='hidden' name='need' value=\"$Id\">";
            }
            ?>
            
            <input name="InvoiceNO" type="text" id="InvoiceNO" value="<?php  echo $InvoiceNO?>" size="38" dataType="Require" msg="未填" style="width: 150px"></td>
    </tr>
    <tr style="height:40px">
        <td style="height: 30px;text-align: center;">车次单号：
            <input name="CarNumber" type="text" id="CarNumber" value="<?php  echo $CarNumber ?>" size="38" dataType="Require" style="width: 150px">
        </td>
    </tr>
    <tr style="height: 40px">
        <td height="25" align="center">出货日期：
            <input name="ShipDate" type="date" id="ShipDate" value="<?php echo date("Y-m-d") ?>" size="38" maxlength="10" dataType="Date" msg="格式不对" style="width: 150px">
        </td>
    </tr>
    <tr style="height:40px">
            <td style="height: 30px;text-align: center;">选择车辆：
                <?php
                $SubMit = "<span onClick='Validator.Validate(document.getElementById(document.form1.id),3,\"ch_shippinglist_save\",2)' $onClickCSS>确定</span>&nbsp;";
                $checkBank = mysql_query("SELECT Id,CarNo FROM $DataIn.cardata ORDER BY Id", $link_id);
                if ($BankRow = mysql_fetch_array($checkBank)) {
                    echo "&nbsp;<select name='CarNo' id='CarNo' style='width:150px' dataType='Require' msg='未选' onchange='CarNoChange()'>";
                    echo "<option value=''>请选择</option>";
                    $i = 1;
                    do {
                        $Id = $BankRow["Id"];
                        $CarNo = $BankRow["CarNo"];
                        $Checked = $i == 1 ? "checked" : "";
                        echo "<option value='$CarNo' $Checked>$CarNo</option>";
                        $i++;
                    } while ($BankRow = mysql_fetch_array($checkBank));
                    echo "<option value='NO'>没有相关车辆</option>";
                    echo "</select>";
                }
                else {
                    $SubMit = "";
                    echo "<div class='redB'>此客户车辆的资料不全,不能生成出货单.</div>";
                }
                ?>
            </td>
    </tr>
    <tr style="height:40px;display: none;" id="CarHide">
        <td style="height: 30px;text-align: center;">输入车辆：
            <input name="CarNoIn" type="text" id="CarNoIn" value size="38" placeholder="请输入车辆信息" dataType="Require" style="width: 150px">
        </td>
    </tr>
    <tr style="height:40px;display: none">
        <td style="height: 30px;text-align: center;">工 字 钢：
            <input name="GZG" type="text" id="GZG" value size="38" placeholder="请输入工字钢数量" dataType="Require"
                   style="width: 150px">
        </td>
    </tr>
    <tr style="height:40px;display: none">
        <td style="height: 30px;text-align: center;">木　　方：
            <input name="MF" type="text" id="MF" value size="38" placeholder="请输入木方数量" dataType="Require"
                   style="width: 150px">
        </td>
    </tr>
    <tr style="height:20px"><td></td></tr>
</table>
</div>
<table width="<?php echo $tableWidth ?>" border="0" align="center" cellspacing="5" >
  <tr>
    <td style="width: 100px">
      <span class='ButtonH_25' id='changeBtn' value='生成' onclick='shipment()'>生成</span>
    </td>
    <td align="center">
      <span class='ButtonH_25' id='cancelBtn' value='取消' onclick='closeWinDialog()'>取消</span></td>
  </tr>
</table>
