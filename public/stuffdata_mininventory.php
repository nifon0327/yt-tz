<?php 
//步骤1 2011-08-01 新加页面$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新配件最低库存");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_mininventory";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT S.StuffId,S.TypeId,S.StuffCname,K.mStockQty
FROM $DataIn.stuffdata S 
LEFT JOIN $DataIn.ck9_stocksheet K ON S.StuffId=K.StuffId WHERE S.Id='$Id' LIMIT 1",$link_id));
$StuffId=$upData["StuffId"];
$StuffCname=$upData["StuffCname"];
$mStockQty=$upData["TypeId"];
$mStockQty=$upData["mStockQty"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,StuffId,$StuffId,StuffType,$StuffType,ActionId,98";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="800" border="0" align="center" cellspacing="5">
		<tr>
            <td width="103" align="right" scope="col">配件名称</td><td>(<?php  echo $StuffId?>)<?php  echo $StuffCname?></td>
          </tr>
          <tr>
            <td align="right">最低库存</td>
            <td><input name="mStockQty" type="text" id="mStockQty" value="<?php  echo $mStockQty?>" size="89" dataType="Number" msg="错误的数量"></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>