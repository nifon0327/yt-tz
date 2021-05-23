<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="item6_2_shipUpdate.css" type="text/css" media="screen" charset="utf-8">
	<script language="javascript" src="showDialog/showDialog.js" type="text/javascript"></script>
	<script type="text/javascript" src="cj_function.js"></script>
	<script type="text/javascript" charset="utf-8" src="item6_2_shipUpdate.js" ></script>
</head>
<body>
<?php
//OK
include "../../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$Id = $_GET["Id"];
$shipEstate = $_GET["shipEstate"];

$funFrom="item6_2";
$saveWebPage=$funFrom . "_ajax.php?ActionId=1";

$upResult = mysql_query("SELECT M.Sign,M.ShipType,M.CompanyId,M.ModelId,M.BankId,M.Number,M.InvoiceNO,M.Wise,M.Notes,M.Terms,M.PaymentTerm,M.PreSymbol,M.Date,C.Forshort
FROM $DataIn.ch1_shipmain M,$DataIn.trade_object C WHERE M.Id='$Id' AND M.CompanyId=C.CompanyId LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$ShipSign=$upData["Sign"];
	$ShipType=$upData["ShipType"];
	$CompanyId=$upData["CompanyId"];
	$thisModelId=$upData["ModelId"];
	$BankId=$upData["BankId"];
	$Number=$upData["Number"];
	$InvoiceNO=$upData["InvoiceNO"];
	$Wise=$upData["Wise"];

	$Notes=$upData["Notes"];
	$Terms=$upData["Terms"];
	$PaymentTerm=$upData["PaymentTerm"];
	$PreSymbol=$upData["PreSymbol"];
	$Date=$upData["Date"];
	$Forshort=$upData["Forshort"];
	}
//文档模板
$SelectCode="<select name='ModelId' id='ModelId' style='width: 150px; height:20px;'>";
$checkBank=mysql_query("SELECT Id,Title FROM $DataIn.ch8_shipmodel WHERE 1 ORDER BY Id",$link_id);
while($BankRow=mysql_fetch_array($checkBank)){
	 $MId=$BankRow["Id"];
	 $Title=$BankRow["Title"];
	 if($thisModelId==$MId)$SelectCode.="<option value='$MId' selected>$Title</option>";
	 else $SelectCode.="<option value='$MId'>$Title</option>";
}
$SelectCode.="</select>";
?>
<form action="<?php  echo $saveWebPage?>" method="post"  enctype="multipart/form-data" target="FormSubmit" name="saveForm" id="saveForm" onsubmit= "return CheckForm();"><input id="ShipId" name="ShipId" value="<?php  echo $Id?>" type="hidden"/><input id="CompanyId" name="CompanyId" value="<?php  echo $CompanyId?>" type="hidden"/><input id="ShipSign" name="ShipSign" value="<?php  echo $ShipSign?>" type="hidden"/><input id="ShipType" name="ShipType" value="<?php  echo $ShipType?>" type="hidden"/> <input id="ShipEstate" type="hidden" value="<?php  echo $shipEstate ?>" />
 <table width="100%" border="1" align="center" cellspacing="0" id="headTable">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
		<tr >
            <td width="64px" align="center">文档模板</td>
            <td width="180px" align="center"><?php  echo $SelectCode?></td>
             <td width="80"align="center" >收款账号</td>
            <td align="center" width="167px">
			<?php
		  $checkBank=mysql_query("SELECT Id,Title FROM $DataPublic.my2_bankinfo WHERE Id='$BankId' ORDER BY Id LIMIT 1",$link_id);
		  if($BankRow=mysql_fetch_array($checkBank)){
			$Title=$BankRow["Title"];
			echo $Title;
			}
		   ?>
		</td>
		<td width="80" align="center">InvoiceNO</td>
            <td><input name="InvoiceNO" type="text" id="InvoiceNO" value="<?php  echo $InvoiceNO?>" size="10" class="inputStyle"  disabled></td>
            <td width="80" align="center">出货日期</td>
            <td width="100px"><input name="ShipDate" type="text" id="ShipDate" value="<?php  echo $Date?>" size="10" class="inputStyle" disabled></td>
          </tr>
           <tr>
            <td width="50" align="center" >Notes</td>
            <td colspan='3'><input name="Notes" type="text" id="Notes" size="40" class="inputStyle"></td>
             </td>
			 <td width="50" align="center" >Terms</td>
             <td colspan='3'><input name="Terms" type="text" id="Terms" size="35" class="inputStyle"></td>
			 </tr>
			 <tr>
			 <td width="50" align="center" >PaymentTerm</td>
             <td colspan='3' width="200px"><input name="PaymentTerm" type="text" id="PaymentTerm" size="40" class="inputStyle"></td>
			 <td colspan='4' align="center">
              <input name="OrderQuery"  class='ButtonH_25' type="button" id="OrderQuery" value="加入定单" onClick="viewOrderdata()" >
        </tr>
   </table>
   <table border="1" width="100%" cellpadding="0" cellspacing="0" align="center">
	       <tr bgcolor='#EEEEEE'>
		<td width="10" class="A0010" height="25" bgcolor='#CCC'>&nbsp;</td>
		<td class="A1111" width="50" align="center">操作</td>
		<td class="A1101" width="50" align="center">序号</td>
		<td class="A1101" width="120" align="center">PO</td>
		<td class="A1101" width="250" align="center">产品名称</td>
		<td class="A1101" width="210" align="center">Product Code</td>
		<td class="A1101" width="80" align="center">售价</td>
		<td class="A1101" width="80" align="center">出货数量</td>
		<td width="10" class="A0000" bgcolor='#CCC'>&nbsp;</td>
	</tr>
    <tr>
		<td width="10" class="A0010" height="20">&nbsp;</td>
		<td class='A0111' colspan="7">
		<div style="width:100%;height:249px;overflow-x:hidden;overflow-y:scroll">
		<table border="1" width="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" align="left" id="OrderList">
		<?php
		//明细信息
	 	//产品订单列表
		$sheetResultP = mysql_query("SELECT S.Id,S.POrderId,O.OrderPO,P.cName,P.eCode,S.Qty,S.Price,S.Type,S.YandN 
				FROM $DataIn.ch1_shipsheet S 
				LEFT JOIN $DataIn.yw1_ordersheet O ON O.POrderId=S.POrderId 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=O.ProductId 
				WHERE S.Mid='$Id' AND S.Type='1'
			UNION ALL
				SELECT S.Id,S.POrderId,'' AS OrderPO,O.SampName AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN 
				FROM $DataIn.ch1_shipsheet S
				LEFT JOIN $DataIn.ch5_sampsheet O ON O.SampId=S.POrderId
				WHERE S.Mid='$Id' AND S.Type='2'
			UNION ALL
				SELECT S.Id,S.POrderId,'' AS OrderPO,O.Description AS cName,O.Description AS eCode,S.Qty,S.Price,S.Type,S.YandN 
				FROM $DataIn.ch1_shipsheet S
				LEFT JOIN $DataIn.ch6_creditnote O ON O.Number=S.POrderId
				WHERE S.Mid='$Id' AND S.Type='3'
			",$link_id);
		$k=1;
		if($sheetRowP = mysql_fetch_array($sheetResultP)){
			do{
				$sId=$sheetRowP["Id"];
				$POrderId=$sheetRowP["POrderId"];
				$OrderPO=$sheetRowP["OrderPO"]==""?"&nbsp;":$sheetRowP["OrderPO"];
				$Price=$sheetRowP["Price"];
				$Date=$sheetRowP["Date"];
				$Qty=$sheetRowP["Qty"];
				$Remark=$sheetRowP["Remark"];
				$cName=$sheetRowP["cName"];
				$eCode=$sheetRowP["eCode"];
				echo"<tr class= 'listStyle' >
				<td width='50'  align='center' height='20'><a href='#' onclick='deleteRow(this.parentNode,OrderList,$POrderId)' title='取消此出货项目'>×</a></td>
				<td width='50'  align='center'>$k</td>
				<td width='120'  align='center'>$OrderPO</td>
				<td width='250'  align = 'center' >$cName</td>
				<td width='210' >$eCode</td>
				<td width='80'  align='center'><input name='Price' class='INPUT0000' type='text' Id='Price' value='$Price' size='6' maxlength='10' onblur='changePrice(this,$sId)' disabled></td>
				<td width='79'  align='center'>$Qty</td>
				</tr>";
				$k++;
				}while($sheetRowP = mysql_fetch_array($sheetResultP));
			}
		?>
		</table>
		</div>
		</td>
		<td width="10" class="A0000">&nbsp;</td>
	</tr>
  </table>
    <table border="1" width="100%" cellpadding="0" cellspacing="0" align="center">
	       <tr bgcolor='#EEEEEE'>
		<td width="10" class="A0010" height="25" bgcolor='#CCC'>&nbsp;</td>
		<td class="A1111" width="50" align="center">操作</td>
		<td class="A1101" width="50" align="center">序号</td>
		<td class="A1101" width="120" align="center">PO</td>
		<td class="A1101" width="250" align="center">产品名称</td>
		<td class="A1101" width="210" align="center">Product Code</td>
		<td class="A1101" width="80" align="center">售价</td>
		<td class="A1101" width="80" align="center">出货数量</td>
		<td width="10" class="A0000" bgcolor='#CCC'>&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="159">&nbsp;</td>
		<td colspan="7" align="center" class="A0111">
		<div style="width:860;height:100%;overflow-x:hidden;overflow-y:scroll;background:#FFF;">
			<table width='100%' cellpadding="0" cellspacing="0"  id="ListTable">
			<input name="TempValue" type="hidden" id="TempValue"><input name='AddIds' type='hidden' id="AddIds">
			</table>
		</div>
		</td>
        <td width="10" class="A0000" height="159">&nbsp;</td>
	</tr>
</table>
</body>
</html>