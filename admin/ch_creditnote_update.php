<?php   
//电信-zxq 2012-08-01
/*
$DataIn.trade_object
$DataIn.ch6_creditnote
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 扣款资料更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤3：//需处理
$upResult = mysql_query("SELECT * FROM $DataIn.ch6_creditnote WHERE Id='$Id' LIMIT 1",$link_id); 
if($upRow = mysql_fetch_array($upResult)){
	$Estate=$upRow["Estate"];
	$PO=$upRow["PO"];
	$Company=$upRow["Company"];
	$Description=$upRow["Description"];
	$Qty=$upRow["Qty"];	
	$Price=$upRow["Price"];
	$CompanyId=$upRow["CompanyId"];
	}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,Estate,$Estate";
//步骤5：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="6">
			<tr>
				<td width="150" align="right" scope="col">客户:</td>
				<td scope="col">
				<select name="theCompanyId" id="theCompanyId" size="1" style="width:300pt;" dataType="Require" msg="未填写">
				<option value="" selected>请选择</option>
				<?php   
				$ClientResult = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE 1 AND ObjectSign IN (1,2) AND Estate=1",$link_id);
				if($ClientRow = mysql_fetch_array($ClientResult)){
					do{
						if($CompanyId ==$ClientRow["CompanyId"] ){
							echo"<option value='$ClientRow[CompanyId]' selected>$ClientRow[Forshort]</option>";
						}else{
							echo"<option value='$ClientRow[CompanyId]'>$ClientRow[Forshort]</option>";
						}
					} while ($ClientRow = mysql_fetch_array($ClientResult));
					}
				?></select></td>
			</tr>
			<tr>
				<td scope="col" align="right">PO:</td>
				<td scope="col"><input name="PO" type="text" id="PO" size="61" value="<?php    echo $PO?>"></td>
			</tr>
			<tr>
				<td scope="col" align="right">注释:</td>
				<td scope="col"><input name="Description" type="text" id="Description" size="61" value="<?php    echo $Description?>" dataType="Require" msg="未填写"></td>
			</tr>
			<tr>
				<td align="right">数量:</td>
			  <td><input name="Qty" type="text" id="Qty" size="61" dataType="Number" value="<?php    echo $Qty?>" msg="未填写或格式不对"></td>
			</tr>
			<tr>
				<td align="right">单价:</td>
				<td><input name="Price" type="text" id="Price" size="61" dataType="Currency" value="<?php    echo $Price?>" msg="未填写或格式不对"></td>
			</tr>
	</table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>