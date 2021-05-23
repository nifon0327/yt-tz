<?php   
//电信-EWEN
include "../model/modelhead.php";
echo"<SCRIPT src='../model/addtblist.js' type=text/javascript></script>";
//步骤2：
ChangeWtitle("$SubCompany 删除客户订单");//需处理
$nowWebPage =$funFrom."_delcause";	
$toWebPage  =$funFrom."_del";	
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
   $CheckDelResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.yw1_orderdeleted WHERE POrderId='$POrderId'  AND OrderPO='$OrderPO' and ProductId='$ProductId' AND Estate>0 LIMIT 1",$link_id));
   $CheckDelId=$CheckDelResult["Id"];
	}
//步骤4：
$tableWidth=1050;$tableMenuS=500;
include "subprogram/del_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,Mid,$Mid,OrderNumber,$OrderNumber";
//步骤5：//需处理

$Estate=$CheckDelId==""?-1:5;
if ($Estate==4 || $Estate==0 || $Estate==5){
	switch($Estate){
         case "0":
		$msgError="该订单已出货，不能删除!";
        break;
        case "4":
		$msgError="该订单已生成出货单，不能删除!";
        break;
        case "5":
		$msgError="该订单已有删除记录,请通知审核!";
        break;
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

?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
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


<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
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
    <td colspan="7" class="A0100" valign="bottom"><font color='red'>◆删除订单原因（需填写）</font></td>
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
  <tr>
    <td class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
    <td align="center" class="A0111" bgcolor='<?php    echo $Title_bgcolor?>'>上传附件</td>
    <td colspan="6"  class="A0101">&nbsp;&nbsp;&nbsp;&nbsp;<input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg,pdf" Msg="文件格式不对,请重选"></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
 
</table>
<?php   

$PJinfo="SELECT G.Id,G.StockId,G.POrderId,G.stuffId,G.OrderQty,G.BuyerId,G.CompanyId,D.StuffCname,G.Price 
		         FROM $DataIn.cg1_stocksheet G 
                 LEFT JOIN $DataIn.stuffdata D ON G.StuffId=D.StuffId
                 LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
                 WHERE 1 AND T.mainType<2 AND G.Mid!='0' and(G.FactualQty>'0' OR G.AddQty>'0' ) and G.POrderId='$POrderId' ";
		
		$delResult=mysql_query($PJinfo);
		$Rows=mysql_affected_rows();
?>

<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">

	<tr>
    <td width="10" height="35" class="A0010">&nbsp;</td>
    <td colspan="7" valign="bottom"><font color='red'>◆订单中有<span style="font-size:18px ;color:#0000CC"><?php    echo $Rows?></span>个配件已下采购单，是否先行请采购退回采购单然后再删除</font></td>
    <td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
    </tr> 

  
 <tr bgcolor='<?php    echo $Title_bgcolor?>'>
    <td width="10" class="A0010" bgcolor="#FFFFFF" height="30">&nbsp;</td>
    <td class="A1111" width="120" align="center">待购流水号</td>
    <td class="A1101" width="100" align="center">配件ID</td>
    <td class="A1101" width="250" align="center">配件名称</td>
    <td class="A1101" width="80" align="center">订单数量</td>
    <td class="A1101" width="100" align="center">采购</td>
    <td class="A1101" width="100" align="center">供应商</td>
	<td class="A1101" width="80" align="center">价格</td>
    <td width="10" class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
		<td width="10" class="A0010" height="180">&nbsp;</td>
		<td colspan="7" align="center" class="A0111">
		<div style="width:100%;height:100%;overflow-x:hidden;overflow-y:scroll">
		<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="StuffList">
 		<?php   
		//已下采购单配件单列表

		/*$PJinfo="SELECT G.Id,G.StockId,G.POrderId,G.stuffId,G.OrderQty,G.BuyerId,G.CompanyId,D.StuffCname,G.Price 
		         FROM cg1_stocksheet G 
                 LEFT JOIN stuffdata D ON G.StuffId=D.StuffId
                 LEFT JOIN stufftype T ON T.TypeId=D.TypeId 
                 WHERE 1 AND T.mainType<2 AND G.Mid!='0' and(G.FactualQty>'0' OR G.AddQty>'0' ) and G.POrderId='$POrderId' ";
		
		$delResult=mysql_query($PJinfo);
		$Rows=mysql_affected_rows();
		echo $Rows;*/
		if($delRows=mysql_fetch_array($delResult)){
			do{  
			     $Id=$delRows["Id"];
			     $StockId=$delRows["StockId"];
				 $POrderId=$delRows["POrderId"];
				 $stuffId=$delRows["stuffId"];
				 $OrderQty=$delRows["OrderQty"];
				  
				 $BuyerId=$delRows["BuyerId"];
				 $pResult = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number=$BuyerId ORDER BY Number LIMIT 1",$link_id);
                 if($pRow = mysql_fetch_array($pResult))
				 {
	                 $Buyer=$pRow["Name"];
	              }
								  
				 $CompanyId =$delRows["CompanyId"];
				 $comResult =mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE CompanyId=$CompanyId",$link_id);
				 if($comRow=mysql_fetch_array($comResult))
				  {
				      $Forshort=$comRow["Forshort"];
				   }
				 
				 
				 $StuffCname=$delRows["StuffCname"];
				 $Price=$delRows["Price"];
				 
			    
				echo"<tr>";
				echo"<td width='120' class='A0101' align='center' height='30' >$StockId</td>
					<td width='100' class='A0101' align='center'>$stuffId</td>
					<td width='250' class='A0101' align='center'>$StuffCname</td>
					<td width='80' class='A0101' align='center'>$OrderQty</td>
					<td width='100' class='A0101' align='center'>$Buyer</td>
					<td width='100' class='A0101' align='center'>$Forshort</td>
					<td width='61' class='A0101' align='center'>$Price</td>
					
					</tr>";
				
				}while($delRows=mysql_fetch_array($delResult));
			}
		?>           
    	</table>
		</div>		
		</td>
		<td class="A0001" bgcolor="#FFFFFF">&nbsp;</td>
	</tr>
  
 </table>
<?php   
}
//步骤5：
include "subprogram/del_model_b.php";
?>