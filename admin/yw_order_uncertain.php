<?php   
//电信-EWEN
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 未确定客户订单");//需处理
$fromWebPage=$funFrom."_read";	
$nowWebPage =$funFrom."_uncertain";	
$toWebPage  =$funFrom."_updated";	
$retWebPage  =$funFrom."_read";	

$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$selResult = mysql_query("SELECT 
S.OrderPO,M.OrderDate,M.ClientOrder,M.Id as Mid,M.OrderNumber,
S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.ShipType,S.DeliveryDate,S.Estate,S.scFrom, 
P.cName,P.eCode,C.Forshort
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M on M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
where S.Id=$Id ORDER BY S.OrderNumber DESC",$link_id);
/*
echo "SELECT 
S.OrderPO,M.OrderDate,M.ClientOrder,M.Id as Mid,M.OrderNumber,
S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.ShipType,S.DeliveryDate,S.Estate,S.scFrom, 
P.cName,P.eCode,C.Forshort
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M on M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
where S.Id=$Id ORDER BY S.OrderNumber DESC";
*/

if($selData = mysql_fetch_array($selResult)){
	$Mid=$selData["Mid"];
	$Forshort=$selData["Forshort"];
	$OrderPO=$selData["OrderPO"]==""?"&nbsp;":$selData["OrderPO"];
	$OrderNumber=$selData["OrderNumber"];
	$OrderDate=$selData["OrderDate"];	
	$ClientOrder=$selData["ClientOrder"];
	$ProductId=$selData["ProductId"];
	$POrderId=$selData["POrderId"];
	$cName=$selData["cName"];
	$eCode=$selData["eCode"]==""?"&nbsp;":$selData["eCode"];
	$Qty=$selData["Qty"];
	$Price=$selData["Price"];
	$Amount=sprintf("%.2f",$Qty*$Price);
	$Estate=$selData["Estate"];
	$scFrom=$selData["scFrom"];
	}
//步骤4：
$tableWidth=950;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Mid,$Mid,OrderNumber,$OrderNumber,ActionId,$ActionId";

//步骤5：//需处理
/*
if ($Estate==4 || $Estate==0){
	if ($Estate==0){
		$msgError="该订单已出货！";
	}
	else{
		$msgError="该订单已生成出货单！";
	}
	echo "<SCRIPT LANGUAGE=JavaScript>alert('错误：$msgError');"; 
	echo "ReOpen(\"$retWebPage\");"; 
	echo "</script>";
}
else{
	switch($scFrom){
		case 2:
		  $scFromMsg="<font color='red'>生产中</font>";
		  break;
		case 3:
		  $scFromMsg="<font color='red'>已生产</font>";
		  break;
		default:
		  $scFromMsg="未生产";
		  break;
		  
	}
*/
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="10" class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
    <td colspan="6" class="A0100" valign="bottom">◆主订单信息</td>
    <td width="10" class="A0001" bgcolor="#FFFFFF" height="20">&nbsp;</td>
  </tr>
  <tr>
    <td width="10" height="30" class="A0010">&nbsp;</td>
    <td width="145" align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>客&nbsp;&nbsp;&nbsp;&nbsp;户</td>
    <td width="145" align="center" class="A0100"><?php    echo $Forshort?></td>
    <td width="145" align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>订&nbsp;单&nbsp;PO</td>
    <td width="145" align="center" class="A0100"><?php    echo $OrderPO?></td>
    <td width="145" align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>订单日期</td>
    <td width="145" align="center" class="A0101"><?php    echo $OrderDate?></td>
    <td width="10" class="A0001">&nbsp;</td>
	</tr>
	<tr>
    <td width="10" height="35" class="A0010">&nbsp;</td>
    <td colspan="6" valign="bottom">◆订单产品信息</td>
    <td width="10" class="A0001">&nbsp;</td>
  </tr>
</table>


<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr bgcolor='<?php    echo $Title_bgcolor?>'>
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
    <td align="center" class="A0111"><?php    echo $POrderId?>
    <input name="POrderId" id="POrderId" type="hidden" value="<?php    echo $POrderId?>"></td>
    <td class="A0101" align="center"><?php    echo $ProductId?></td>
    <td class="A0101" align="center"><?php    echo $cName?></td>
    <td class="A0101" align="center"><?php    echo $eCode?></td>
    <td class="A0101" align="center"><?php    echo $Qty?></td>
    <td class="A0101" align="center"><?php    echo $Price?></td>
    <td class="A0101" align="center"><?php    echo $Amount?></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
     <tr>
    <td width="10" class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
    <td colspan="7" class="A0100" valign="bottom">◆订单生产信息:&nbsp;<?php    echo $scFromMsg?></td>
    <td width="10" class="A0001" bgcolor="#FFFFFF" height="20">&nbsp;</td>
  </tr>

  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="35">&nbsp;</td>
    <td colspan="7" class="A0100" valign="bottom"><font color='red'>◆未确定订单原因</font></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
    <td align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>未确定原因</td>
    <td colspan="6"  class="A0101" >&nbsp;&nbsp;&nbsp;&nbsp;<select name="UPType"   id="UPType"  style="width:582px" >
      <?php    
		echo"<option value='' selected>请选择</option>";
		echo"<option value='1'>客户取消订单</option>";
		echo"<option value='2'>产品未确定</option>";
		echo"<option value='3'>自定义(请填写原因)</option>";
		/*
		$result = mysql_query("SELECT Id,TypeName FROM $DataPublic.yw1_orderdeltype WHERE  Estate=1 ORDER BY Id",$link_id);
		if($myrow = mysql_fetch_array($result)){
			do{
				echo"<option value='$myrow[Id]'>$myrow[TypeName]</option>";
				} while ($myrow = mysql_fetch_array($result));
			}
		*/	
		?>
      	</select>
    </td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>

  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
    <td align="center" class="A0111"  >未确定原因(自定义)</td>
    <td colspan="6"  class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Remark" type="text" class="INPUT0000" id="Remark" value="" size="110"></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  
 </table>
<?php   

//步骤5：
include "../model/subprogram/add_model_b.php";
?>

<script>
/*
  function CheckForm(){
	   if (document.getElementById("UPType").value==""){
		    var Remark=document.getElementById("Remark").value;
		    Remark=Remark.replace(/^\s+|\s+$/g,"");//去除两边空格
			if (Remark==""){
			   Message="请填写未确定原因!";	
			   alert(Message);return false;
			}
	   }
  }
 */
</script>
