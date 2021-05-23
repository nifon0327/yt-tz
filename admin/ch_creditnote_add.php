<?php   
//电信-zxq 2012-08-01
//步骤1 $DataIn.trade_object 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 添加扣款资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId,Estate,$Estate";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
        <table width="800" border="0" align="center" cellspacing="6">
			<tr>
				<td width="100" align="right" scope="col">客户:</td>
				<td scope="col">
				<select name="theCompanyId" id="theCompanyId" size="1" style="width:300pt;" dataType="Require" msg="未填写">
				<option value="" selected>请选择</option>
				<?php   
				$ClientResult = mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE 1 AND Estate=1 AND ObjectSign IN (1,2) ORDER BY Id",$link_id);
				if($ClientRow = mysql_fetch_array($ClientResult)){
					do{
						echo"<option value='$ClientRow[CompanyId]'>$ClientRow[Forshort]</option>";
						} while ($ClientRow = mysql_fetch_array($ClientResult));
					}
				?></select></td>
			</tr>
			<tr>
				<td scope="col" align="right">PO:</td>
				<td scope="col"><input name="PO" type="text" id="PO" size="73"></td>
			</tr>
			<tr>
				<td scope="col" align="right">注释:</td>
				<td scope="col"><input name="Description" type="text" id="Description" size="73" dataType="Require"  msg="未填写"></td>
			</tr>
			<tr>
				<td align="right">数量:</td>
			  <td><input name="Qty" type="text" id="Qty" size="73" dataType="Number"  msg="未填写或格式不对"></td>
			</tr>
			<tr>
				<td align="right">单价:</td>
				<td><input name="Price" type="text" id="Price" size="73" dataType="Currency"  msg="未填写或格式不对"></td>
			</tr>
	</table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>