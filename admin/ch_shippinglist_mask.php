<?php
//电信-zxq 2012-08-01
include "../basic/chksession.php";
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$ModelCompanyId = " and CompanyId='$DeliveryValue'";
$CompanyId = $CompanyId == "" ? $DeliveryValue : $CompanyId;
//echo $upIds;
$subMitHidden = 0;


include "subprogram/ch_amountshow.php";
?>

<table width="450" border="0" cellspacing="0"><input name="TempValue" type="hidden" id="TempValue">
    <tr>
        <td colspan="2" align="center" valign="top"><h3>出货资料</h3></td>
    </tr>
    <tr>
        <td width="62" height="25" align="center">出货单号</td>
        <td>
            <?php
            //计算最后的Invoice编号
            $date = date("Y-m-d");
            $maxInvoiceNO=mysql_fetch_array(mysql_query("SELECT InvoiceNO FROM $DataIn.ch1_shipmain WHERE Date = '$date' and Sign=1 $ModelCompanyId ORDER BY InvoiceNO desc LIMIT 1",$link_id));
            if($maxInvoiceNO){
                $maxNO=substr($maxInvoiceNO["InvoiceNO"],8);
                $maxNO +=1;
                $NewInvoiceNO=date('Ymd').sprintf("%02d",$maxNO);
            }
            else{
                $NewInvoiceNO=date('Ymd').'01';;
            }
            ?>
            <input name="InvoiceNO" type="text" id="InvoiceNO" value="<?php  echo $NewInvoiceNO?>" size="38" dataType="Require" msg="未填"></td>
    </tr><tr>
        <td width="62" height="25" align="center">车次单号</td>
        <td>
            <?php
            //计算最后的CarNumber编号
            $date = date("Y-m-d");
            $maxCarNumber=mysql_fetch_array(mysql_query("SELECT CarNumber FROM $DataIn.ch1_shipmain WHERE Date = '$date' and Sign=1 $ModelCompanyId ORDER BY CarNumber desc LIMIT 1",$link_id));
            if($maxCarNumber){
                $maxNO=substr($maxCarNumber["CarNumber"],10);
                $maxNO +=1;
                $NewCarNumber=date('Ymd').sprintf("%02d",$maxNO);
            }
            else{
                $NewCarNumber=date('Ymd').'01';;
            }
            ?>
            <input name="CarNumber" type="text" id="CarNumber" value="JL<?php  echo $NewCarNumber?>" size="38" dataType="Require" msg="未填"></td>
    </tr>
  <tr>
    <td height="25" align="center">出货日期</td>
    <td>
      <input name="ShipDate" type="text" id="ShipDate" value="<?php echo date("Y-m-d") ?>" size="38" maxlength="10" dataType="Date" msg="格式不对">
    </td>
  </tr>
<!--  <tr>-->
<!--    <td height="30" align="center" valign="top">选择车辆</td>-->
<!--    <td>-->
<!--        --><?php
//        $SubMit = "<span onClick='Validator.Validate(document.getElementById(document.form1.id),3,\"ch_shippinglist_save\",2)' $onClickCSS>确定</span>&nbsp;";
//        $checkBank = mysql_query("SELECT Id,CarNo FROM $DataIn.cardata ORDER BY Id", $link_id);
//        if ($BankRow = mysql_fetch_array($checkBank)) {
//            echo "&nbsp;<select name='CarNo' id='CarNo' style='width:234px' dataType='Require' msg='未选'>";
//            echo "<option value=''>请选择</option>";
//            $i = 1;
//            do {
//                $Id = $BankRow["Id"];
//                $CarNo = $BankRow["CarNo"];
//                $Checked = $i == 1 ? "checked" : "";
//                echo "<option value='$CarNo' $Checked>$CarNo</option>";
//                $i++;
//            } while ($BankRow = mysql_fetch_array($checkBank));
//            echo "</select>";
//        }
//        else {
//            $SubMit = "";
//            echo "<div class='redB'>此客户车辆的资料不全,不能生成出货单.</div>";
//        }
//        ?>
<!--    </td>-->
<!--  </tr>-->
  <tr>
    <td height="30" align="center" valign="top">文档模板</td>
    <td>
        <?php
        $SubMit = "<span onClick='Validator.Validate(document.getElementById(document.form1.id),3,\"ch_shippinglist_save\",2)' $onClickCSS>确定</span>&nbsp;";
        $checkBank = mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel WHERE 1 $ModelCompanyId ORDER BY Id desc ", $link_id);
        if ($BankRow = mysql_fetch_array($checkBank)) {
            echo "&nbsp;<select name='ModelId' id='ModelId' style='width:234px' dataType='Require' msg='未选'>";
//            echo "<option value=''>请选择</option>";
            $i = 1;
            do {
                $Id = $BankRow["Id"];
                $Title = $BankRow["Title"];
                $Checked = $i == 1 ? "checked" : "";
                echo "<option value='$Id'>$Title</option>";
                $i++;
            } while ($BankRow = mysql_fetch_array($checkBank));
            echo "</select>";
        }
        else {
            $SubMit = "";
            echo "<div class='redB'>此客户出货文档模板的资料不全,不能生成出货单.</div>";
        }
        ?>
    </td>
  </tr>
    <tr>
        <td height="30" align="center" valign="top">交易银行</td>
        <td>
            <?php
            $SubMit = "<span onClick='Validator.Validate(document.getElementById(document.form1.id),3,\"ch_shippinglist_save\",2)' $onClickCSS>确定</span>&nbsp;";
            $checkBank = mysql_query("SELECT Id,Title FROM $DataIn.my2_bankinfo ORDER BY Id", $link_id);
            if ($BankRow = mysql_fetch_array($checkBank)) {
                echo "&nbsp;<select name='BankId' id='BankId' style='width:234px' dataType='Require' msg='未选'>";
//                echo "<option value=''>请选择</option>";
                $i = 1;
                do {
                    $Id = $BankRow["Id"];
                    $Title = $BankRow["Title"];
                    $Checked = $i == 1 ? "checked" : "";
                    echo "<option value='$Id' $Checked>$Title</option>";
                    $i++;
                } while ($BankRow = mysql_fetch_array($checkBank));
                echo "</select>";
            }
            else {
                $SubMit = "";
                echo "<div class='redB'>此客户交易银行的资料不全,不能生成出货单.</div>";
            }
            ?>
        </td>
    </tr>


    <?php if ($subMitHidden == 1) $SubMit = ""; ?>
  <tr valign="bottom">
    <td height="27" colspan="2" align="right"><?php echo $SubMit ?> &nbsp;&nbsp;
      <a href="javascript:closeMaskDiv()">取消 &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; </a></td>
  </tr>
</table>
