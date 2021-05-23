<?php 
//步骤1 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 供应商税款发票上传");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upfile";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT TaxNo FROM $DataIn.cw14_mdtaxmain WHERE Id='$Id' LIMIT 1",$link_id);
if($upData = mysql_fetch_array($upResult)){
	$TaxNo=$upData["TaxNo"];
	//$Amount="免抵退税发票号：" . $upData["Amount"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
$SelectCode="免抵退税发票号: $TaxNo";
include "../model/subprogram/add_model_t.php";
//$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,$ActionId";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,$ActionId";
//步骤5：//需处理
?>
<input name="TaxNo" id="TaxNo"  type="hidden" value="<?php  echo $TaxNo ?>">
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
   <table width="650" border="0" align="center" cellspacing="0" id="NoteTable">
	 <tr>
          <td height="24" align="right" scope="col">扫描附件</td>
          <td scope="col"><input name="Attached" type="file" id="Attached" size="20" title="jpg格式" Row="0" Cel="2"></td>
	    </tr>

		<tr>
		  <td height="13" align="right" valign="top" scope="col">结付凭证</td>
		  <td scope="col"><input name="proof" type="file" id="proof" size="20" title="jpg格式" Row="1" Cel="2">  </td>
	    </tr>
</table>
</td></tr></table>
<?php 
include "../model/subprogram/add_model_b.php";
?>
