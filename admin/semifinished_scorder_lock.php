<?php   
//电信-EWEN
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 未确定客户订单");//需处理
$fromWebPage=$funFrom."_read";	
$nowWebPage =$funFrom."_lock";	
$toWebPage  =$funFrom."_updated";	
$retWebPage  =$funFrom."_read";	

$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$myResult = mysql_query("SELECT S.Id,S.POrderId,S.sPOrderId,S.Qty,S.ScFrom,S.ActionId,S.StockId,S.Remark,S.mStockId,
D.StuffId,D.StuffCname,D.Picture,M.PurchaseID,W.Name AS WorkShopName,U.Name AS UnitName
FROM  $DataIn.yw1_scsheet S
LEFT JOIN $DataIn.workshopdata W  ON W.Id = S.WorkShopId
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.mStockId
LEFT JOIN $DataIn.cg1_stockmain M ON M.Id = G.Mid
LEFT JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
LEFT JOIN $DataIn.stuffunit  U ON U.Id = D.Unit
WHERE  S.Id=$Id ",$link_id);


if($myRow = mysql_fetch_array($myResult)){
	$POrderId=$myRow["POrderId"];
		$sPOrderId=$myRow["sPOrderId"];
		$WorkShopName=$myRow["WorkShopName"];
		$mStockId=$myRow["mStockId"];  //半成品StockId
		$StuffId=$myRow["StuffId"];
		$Picture=$myRow["Picture"];
		$StuffCname=$myRow["StuffCname"];
		$UnitName=$myRow["UnitName"];
		$Qty=$myRow["Qty"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=600;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,sPOrderId,$sPOrderId,ActionId,$ActionId,OrderAction,$OrderAction";

?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
    <td width="10" height="35" class="A0010">&nbsp;</td>
    <td colspan="5" valign="bottom">◆工单产品信息</td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
</table>


<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr bgcolor='<?php    echo $Title_bgcolor?>'>
    <td width="10" class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
    <td class="A1111" width="100" align="center">订单流水号</td>
    <td class="A1101" width="100" align="center">工单流水号</td>
    <td class="A1101" width="60" align="center">半成品ID</td>
    <td class="A1101" width="" align="center">半成品名称</td>
    <td class="A1101" width="80" align="center">生产数量</td>
    <td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="20">&nbsp;</td>
    <td align="center" class="A0111"><?php    echo $POrderId?>
    <input name="POrderId" id="POrderId" type="hidden" value="<?php    echo $POrderId?>"></td>
    <input name="sPOrderId" id="sPOrderId" type="hidden" value="<?php    echo $sPOrderId?>"></td>
    <td class="A0101" align="center"><?php    echo $sPOrderId?></td>
     <td class="A0101" align="center"><?php    echo $StuffId?></td>
    <td class="A0101" align="center"><?php    echo $StuffCname?></td>
    <td class="A0101" align="center"><?php    echo $Qty?></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
 

  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="35">&nbsp;</td>
    <td colspan="5" class="A0100" valign="bottom"><font color='red'>◆未确定订单原因</font></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>

  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
    <td align="center" class="A0111"  >未确定原因</td>
    <td colspan="4"  class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<textarea  name="LockRemark"  id="LockRemark" cols="80" rows="6" ></textarea></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  
 </table>
<?php   

//步骤5：
include "../model/subprogram/add_model_b.php";
?>
