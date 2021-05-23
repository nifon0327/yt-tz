<?php 
//步骤1 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 非BOM预付订金列表凭证上传");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upfile";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理

//步骤4：
$tableWidth=850;$tableMenuS=550;$spaceSide=15;
//$SelectCode="($Forshort) $Amount";
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,CompanyId,$CompanyId,ActionId,$ActionId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<tr><td class="A0011">
   <table width="650" border="0" align="center" cellspacing="0" id="NoteTable">
   <!--
	 <tr>
          <td height="24" align="right" scope="col">合 同 号</td>
          <td scope="col"><input name="InvoiceNUM" type="text" id="InvoiceNUM" style="width:420px" maxlength="20" value="" dataType="Require" Msg="未填写合同号"></td>
	    </tr>
	<tr>
		  <td height="29" align="right" scope="col">合同号日期</td>
		  <td scope="col"><input name="Getdate" type="text" id="Getdate" onfocus="WdatePicker()" value="" style="width:420px" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly>          </td>
	    </tr>
	 -->
		<tr>
		  <td height="13" align="right" valign="top" scope="col">上传凭证(限jpg格式)</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" style="width:420px" DataType="Filter" Accept="jpg,JPG" Msg="文件格式不对,请重选" Row="0" Cel="1"></td>
	    </tr>
</table>
</td></tr></table>
<?php 
include "../model/subprogram/add_model_b.php";
?>
