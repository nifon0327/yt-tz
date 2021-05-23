<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$tableWidth = 400;
$date = date("Y-m-d");
?>

<table width="<?php echo $tableWidth ?>px" border="0" align="left" cellspacing="5">
  <tr>
    <td width="62" height="25" align="center">项目名称</td>
    <td>
        <?php
        $clientResult = mysql_query("
	SELECT M.CompanyId,IFNULL(C.Forshort,'未知项目')  AS Forshort
	FROM $DataIn.ch1_shipsplit SP  
    LEFT JOIN  $DataIn.yw1_ordersheet S  ON S.POrderId=SP.POrderId
	LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	LEFT JOIN  $DataIn.productdata P ON P.ProductId = S.ProductId
	LEFT JOIN  $DataIn.productstock K ON K.ProductId = P.ProductId
	WHERE 1 $SearchRows  AND M.CompanyId IS NOT NULL AND K.tStockQty >= SP.Qty   GROUP BY M.CompanyId 
    UNION
	SELECT S.CompanyId,IFNULL(C.Forshort,'未知项目')  AS Forshort FROM $DataIn.ch5_sampsheet S 
	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=S.CompanyId 
	WHERE 1  and S.Estate='1'
	", $link_id);
        if ($clientRow = mysql_fetch_array($clientResult)) {
            echo "<select name='CompanyId' id='CompanyId' onchange='setValue()'>";
            do {
                $thisCompanyId = $clientRow["CompanyId"];
                $CompanyId = $CompanyId == "" ? $thisCompanyId : $CompanyId;
                $Forshort = $clientRow["Forshort"];
                if ($Forshort !== '未知项目') {
                    if ($CompanyId == $thisCompanyId) {
                        echo "<option value='$thisCompanyId' selected>$Forshort</option>";
                        $ModelCompanyId = " and CompanyId='$thisCompanyId'";
                    } else {
                        echo "<option value='$thisCompanyId'>$Forshort</option>";
                    }
                }
            } while ($clientRow = mysql_fetch_array($clientResult));
            echo "</select>&nbsp;";
        }
        ?>
    </td>
    <tr>
    <td width="62" height="25" align="center">出货单号</td>
    <td>
        <?php
        //计算最后的Invoice编号
        $maxInvoiceNO=mysql_fetch_array(mysql_query("SELECT InvoiceNO FROM $DataIn.ch1_shipmain WHERE Date = '$date' and Sign = 1 $ModelCompanyId ORDER BY InvoiceNO desc LIMIT 1",$link_id));
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
  </tr>

  </tr>
  <tr>
    <td width="62" height="25" align="center">车次单号</td>
    <td>
        <?php
        //计算最后的CarNumber编号
        $maxCarNumber=mysql_fetch_array(mysql_query("SELECT CarNumber FROM $DataIn.ch1_shipmain WHERE Date = '$date' and Sign = 1 $ModelCompanyId ORDER BY CarNumber desc LIMIT 1",$link_id));
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
  <tr>
    <td height="30" align="center" valign="top">文档模板</td>
    <td>
        <?php
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
        }else{
            echo "<div class='redB'>此客户出货文档模板的资料不全,不能生成出货单.</div>";
        }
        ?>
    </td>
  </tr>
  <tr>
    <td height="30" align="center" valign="top">交易银行</td>
    <td>
        <?php
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
        ?>
    </td>
  </tr>
</table>

<table width="<?php echo $tableWidth ?>" border="0" align="center" cellspacing="5">
  <tr>
    <td>&nbsp;</td>
    <td align="center">
      <sapn class='ButtonH_25' id='changeBtn' value='变更' onclick='batchSetTime()'>设置</sapn>
    </td>
    <td align="center">
      <span class='ButtonH_25' id='cancelBtn' value='取消' onclick='closeWinDialog()'>取消</span></td>
    <td>&nbsp;</td>
  </tr>
</table>