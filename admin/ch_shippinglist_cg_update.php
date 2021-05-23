<?php   
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataIn.ch5_sampsheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany CG出货标签更新");//需处理
$fromWebPage=$funFrom."_add";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT 
S.OrderPO,M.OrderDate,M.ClientOrder,M.Id as Mid,M.OrderNumber,
S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.ShipType,S.DeliveryDate,O.PONo,O.ItemNo,O.Description,
P.cName,P.eCode,C.Forshort
FROM $DataIn.yw1_ordersheet S 
LEFT JOIN $DataIn.yw1_ordermain M on M.OrderNumber=S.OrderNumber 
LEFT JOIN $DataIn.cg_order O ON O.POrderId=S.POrderId
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
where S.Id=$Id ORDER BY S.OrderNumber DESC",$link_id);

if($upData = mysql_fetch_array($upResult)){
	$Forshort=$upData["Forshort"];
	$OrderPO=$upData["OrderPO"];
	$ItemNo=$upData["ItemNo"]==""?"&nbsp;":$upData["ItemNo"];
	$PONo=$upData["PONo"]==""?"&nbsp;":$upData["PONo"];
	$ClientOrder=$upData["ClientOrder"];
	$ProductId=$upData["ProductId"];
	$POrderId=$upData["POrderId"];
	$eCode=$upData["eCode"];
	$cName=$upData["cName"];
	$Description=$upData["Description"]==""?"&nbsp;":$upData["Description"];

	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ProductId,$ProductId";
//步骤5：//需处理
?>
<input type="hidden" name="POrderId" id="POrderId" value="<?php    echo $POrderId?>" />
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
       <table width="820" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right">隶属客户</td>
            <td>&nbsp;&nbsp;<?php    echo $Forshort?></td>
        </tr>
		<tr>
            <td align="right">订单PO</td>
            <td>&nbsp;&nbsp;<?php    echo $OrderPO?></td>
        </tr>
		<tr>
            <td align="right" scope="col">产品中文名</td>
            <td scope="col">&nbsp;&nbsp;<?php    echo $cName?></td>
		</tr>
		<tr>
            <td align="right" scope="col">Product Code</td>
            <td scope="col">&nbsp;&nbsp;<?php    echo $eCode?>
            </td>
		</tr>
		<tr>
            <td align="right" scope="col">Item No:</td>
            <td scope="col">&nbsp;&nbsp;<input name="ItemNo" type="text" id="ItemNo" value="<?php    echo $ItemNo?>" size="72" max="100">
            </td>
		</tr>
        <tr>
            <td align="right" scope="col">PO No:</td>
            <td scope="col">&nbsp;&nbsp;<input name="PONo" type="text" id="PONo" value="<?php    echo $PONo?>" size="72" max="100">
            </td>
		</tr>
       <tr>
            <td align="right" scope="col">Description:</td>
            <td scope="col">&nbsp;&nbsp;<textarea name="Description"  id="Description" value="<?php    echo $Description?>" cols="46" rows="4"></textarea>
            </td>
		</tr>
 
        </table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>