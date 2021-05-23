<?php   
//电信-zxq 2012-08-01
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 出货订单处理");//需处理
$nowWebPage =$funFrom."_shipout";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="Id,$Id,fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ActionId,$ActionId";
//步骤3：

$upData = mysql_fetch_array(mysql_query("SELECT C.Forshort,M.InvoiceNO FROM $DataIn.ch1_shipmain M
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
WHERE M.Id='$Id'",$link_id));
$Forshort=$upData["Forshort"];
$InvoiceNO=$upData["InvoiceNO"];

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
  <td class="A0010" align="right" height="30" width="300">公&nbsp;&nbsp;司:</td>
  <td class="A0001" ><?php    echo $Forshort?></td>
  </tr>
  <tr>
  <td class="A0010" align="right" height="30">InvoiceNO:</td>
  <td class="A0001" ><?php    echo $InvoiceNO?></td>
  </tr>
  <tr>
    <td class="A0010" align="right" height="30">是否发货&nbsp;</td>
    <td class="A0001"><select name="DeliverySign" Id="DeliverySign" size="1" style="width: 128pt;" dataType="Require" msg="未选择">
      <option value="" selected>请选择</option>
      <option value="1">已发货</option>
      <option value="0">未发货</option>
        </select></td>
  </tr>
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>