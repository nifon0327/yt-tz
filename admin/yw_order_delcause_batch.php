<?php

/* 显示错误，debug */
ini_set("display_errors", "On");
error_reporting(E_ALL);

//电信-EWEN
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 批量删除客户订单");//需处理
$nowWebPage =$funFrom."_delcause_batch";
$toWebPage  =$funFrom."_del_batch";
$retWebPage  =$funFrom."_read";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$Ids = implode(",",$_REQUEST['checkid']);
//echo $_REQUEST['checkid'];

//echo $Ids ;
/*
$selResult = mysql_query("SELECT 
S.OrderPO,M.OrderDate,M.ClientOrder,M.Id as Mid,M.OrderNumber,
S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.ShipType,S.DeliveryDate,S.Estate,S.scFrom, 
P.cName,P.eCode,C.Forshort
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M on M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
where S.Id=$Id ORDER BY S.OrderNumber DESC",$link_id);
*/
$selSql = "SELECT 
S.Id,S.OrderPO,M.OrderDate,M.ClientOrder,M.Id as Mid,M.OrderNumber,
S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.ShipType,S.DeliveryDate,S.Estate,S.scFrom, 
P.cName,P.eCode,C.Forshort
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M on M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
where S.Id in ($Ids) ORDER BY S.OrderNumber DESC";
//echo $selSql;
$selResult = mysql_query($selSql,$link_id);

//步骤4：
$tableWidth=1050;$tableMenuS=500;
include "subprogram/del_model_t.php";

while ($selData = mysql_fetch_array($selResult)) {
    //echo $selData["Id"];
    $Id = $selData["Id"];
    $Mid = $selData["Mid"];
    $Forshort = $selData["Forshort"];
    $OrderPO = $selData["OrderPO"] == "" ? "&nbsp;" : $selData["OrderPO"];
    $OrderNumber = $selData["OrderNumber"];
    $OrderDate = $selData["OrderDate"];
    $ClientOrder = $selData["ClientOrder"];
    $ProductId = $selData["ProductId"];
    $POrderId = $selData["POrderId"];
    $cName = $selData["cName"];
    $eCode = $selData["eCode"] == "" ? "&nbsp;" : $selData["eCode"];
    $Qty = $selData["Qty"];
    $Price = $selData["Price"];
    $Amount = sprintf("%.2f", $Qty * $Price);
    $Estate = $selData["Estate"];
    $scFrom = $selData["scFrom"];
    $CheckDelResult = mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.yw1_orderdeleted WHERE POrderId='$POrderId'  AND OrderPO='$OrderPO' and ProductId='$ProductId' AND Estate>0 LIMIT 1", $link_id));
    $CheckDelId = $CheckDelResult["Id"];


    $Parameter = "Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Mid,$Mid,OrderNumber,$OrderNumber";
//步骤5：//需处理

    $Estate = $CheckDelId == "" ? -1 : 5;
    if ($Estate == 4 || $Estate == 0 || $Estate == 5) {
        switch ($Estate) {
            case "0":
                $msgError = "该订单已出货，不能删除!";
                break;
            case "4":
                $msgError = "该订单已生成出货单，不能删除!";
                break;
            case "5":
                $msgError = "该订单已有删除记录,请通知审核!";
                break;
        }
        echo "<SCRIPT LANGUAGE=JavaScript>alert('错误：$msgError');";
        echo "ReOpen(\"$retWebPage\");";
        echo "</script>";
    } else {
        switch ($scFrom) {
            case 2:
                $scFromMsg = "<font color='red'>生产中</font>";
                break;
            case 3:
                $scFromMsg = "<font color='red'>已生产</font>";
                break;
            default:
                $scFromMsg = "未生产";
                break;

        }
    }

 ?>
<div style="border:1px solid #0e90d2;width: <?php echo $tableWidth;?>px">
    <table border="0" width="<?php echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
        <tr>
            <td width="10" class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
            <td colspan="6" class="A0100" valign="bottom">◆主订单信息</td>
            <td width="10" class="A0001" bgcolor="#FFFFFF" height="20">&nbsp;</td>
        </tr>
        <tr>
            <td width="10" height="30" class="A0010">&nbsp;</td>
            <td width="145" align="center" class="A0111" bgcolor='<?php echo $Title_bgcolor ?>'>客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
            <td width="145" align="center" class="A0100"><?php echo $Forshort ?></td>
            <td width="145" align="center" class="A0111" bgcolor='<?php echo $Title_bgcolor ?>'>订&nbsp;单&nbsp;PO</td>
            <td width="145" align="center" class="A0100"><?php echo $OrderPO ?></td>
            <td width="145" align="center" class="A0111" bgcolor='<?php echo $Title_bgcolor ?>'>订单日期</td>
            <td width="145" align="center" class="A0101"><?php echo $OrderDate ?></td>
            <td width="10" class="A0001">&nbsp;</td>
        </tr>
        <tr>
            <td width="10" height="35" class="A0010">&nbsp;</td>
            <td colspan="6" valign="bottom">◆订单产品信息</td>
            <td width="10" class="A0001">&nbsp;</td>
        </tr>
    </table>


    <table border="0" width="<?php  echo $tableWidth ?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"
           id="NoteTable">
        <tr bgcolor='<?php  echo $Title_bgcolor ?>'>
            <td width="10" class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
            <td class="A1111" width="90" align="center">订单流水号</td>
            <td class="A1101" width="60" align="center">产品ID</td>
            <td class="A1101" width="250" align="center">产品名称</td>
            <td class="A1101" width="250" align="center">Product Code</td>
            <td class="A1101" width="60" align="center">订购数量</td>
            <td class="A1101" width="70" align="center">售价</td>
            <td class="A1101" width="100" align="center">小计</td>
            <td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        <tr>
            <td class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
            <td align="center" class="A0111"><?php echo $POrderId ?>
                <input name="POrderIdArray[]" id="POrderIdArray[]" type="hidden" value="<?php echo $POrderId ?>"></td>
            <td class="A0101" align="center"><?php echo $ProductId ?></td>
            <td class="A0101" align="center"><?php echo $cName ?></td>
            <td class="A0101" align="center"><?php echo $eCode ?></td>
            <td class="A0101" align="center"><?php echo $Qty ?></td>
            <td class="A0101" align="center"><?php echo $Price ?></td>
            <td class="A0101" align="center"><?php echo $Amount ?></td>
            <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
        </tr>
        <tr>
            <td width="10" class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
            <td colspan="7" class="A0100" valign="bottom">◆订单生产信息:&nbsp;<?php echo $scFromMsg ?> &nbsp; ◆提示:&nbsp;<?php echo $msgError ?></td>
            <td width="10" class="A0001" bgcolor="#FFFFFF" height="20">&nbsp;</td>
        </tr>
    </table>
    </div>
    <br/>
    <?php

}

?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
<tr>
<td class="A0010" bgcolor="#FFFFFF" height="35">&nbsp;</td>
<td colspan="7" class="A0100" valign="bottom"><font color='red'>◆上述订单将被删除，请填写删除订单原因（需填写）</font></td>
<td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
<tr>
<td class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
<td align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>删除原因</td>
<td colspan="6"  class="A0101" >&nbsp;&nbsp;&nbsp;&nbsp;<select name="delType"   id="delType"  style="width:582px" dataType="Require"  msg="未选择删除原因">
  <?php
    echo"<option value='' selected>请选择</option>";
    $result = mysql_query("SELECT Id,TypeName FROM $DataPublic.yw1_orderdeltype WHERE  Estate=1 ORDER BY Id",$link_id);
    if($myrow = mysql_fetch_array($result)){
        do{
            echo"<option value='$myrow[Id]'>$myrow[TypeName]</option>";
            } while ($myrow = mysql_fetch_array($result));
        }
    ?>
      </select>
</td>
<td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
</tr>

<tr>
<td class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
<td align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>备注信息</td>
<td colspan="6"  class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Remark" type="text" class="INPUT0000" id="Remark" value="" size="110"></td>
<td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
<?php /*
    <tr>

<td class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
<td align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>上传附件</td>
<td colspan="6"  class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg,pdf" Msg="文件格式不对,请重选"></td>
<td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
</tr>
*/ ?>
</table>

<?php

//步骤5：
include "subprogram/del_model_b.php";