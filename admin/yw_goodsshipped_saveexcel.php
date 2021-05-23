<?php   
//电信-zxq 2012-08-01

include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 出货统计信息输出");//需处理
//$fromWebPage=$funFrom."_".$From;		
$nowWebPage ="yw_goodsshipped_saveexcel";	
$toWebPage  =$funFrom."saveexceled";	
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤4：
$tableWidth=870;$tableMenuS=550;$spaceSide=15;
$CheckFormURL="thisPage";
$SaveSTR="NO";$isBack="N";
$CustomFun="<span onClick='CheckForm(1)' $onClickCSS>生成出货Excel</span>&nbsp; <span onClick='CheckForm(2)' $onClickCSS>生成退款Excel</span>&nbsp;";//自定义功能
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From";
//步骤5：//需处理
 ?>
<table width="<?php    echo $tableWidth?>" height="154" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="150" height="25" align="right" class='A0010'>客&nbsp;&nbsp;&nbsp;&nbsp;户: </td>
    <td class='A0001'><select name="CompanyId" id="CompanyId" style="width:250px">
       <?php   
        $cSql = mysql_query("SELECT M.CompanyId,C.Forshort 
					FROM $DataIn.ch1_shipmain M 
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId GROUP BY M.CompanyId ORDER BY C.Estate DESC,C.OrderBy DESC",$link_id);
					echo "<option value='' selected>全部</option>";
					if($cRow = mysql_fetch_array($cSql)){
						do{
							$CompanyId=$cRow["CompanyId"];
							$Forshort=$cRow["Forshort"];					
							echo "<option value='$CompanyId'>$Forshort</option>";
							}while ($cRow = mysql_fetch_array($cSql));
						}			 
			 ?>      
    </select></td>
  </tr>
  <tr>
    <td height="25" align="right" class='A0010' >出货日期: </td>
    <td class='A0001'><INPUT  type="text"  name='sDate'  id="sDate" style="width:110px" onfocus="WdatePicker()" readonly> &nbsp;&nbsp;至&nbsp;&nbsp;
	                                <INPUT  type="text"  name='eDate'  id="eDate" style="width:110px" onfocus="WdatePicker()" readonly>
    </td>
  </tr>
    <tr>
		<td align="right"   height="25" class='A0010'>InvoiceNO:</td>
	  <td class='A0001'><INPUT type="text" name='InvoiceNO'  id="InvoiceNO"  style="width:250px"></td>
    </tr>
</table>
<?php   
include "../model/subprogram/add_model_b.php";
?>
<script LANGUAGE='JavaScript'>
function CheckForm(sign){
	var CompanyIdSTR=document.getElementById("CompanyId").value;
	var sDateSTR=document.getElementById("sDate").value;
	var eDateSTR=document.getElementById("eDate").value;
	var InvoiceNOSTR=document.getElementById("InvoiceNO").value;
	if (CompanyIdSTR=="" && InvoiceNOSTR==""){
		alert("请选择公司名称或填写InvoiceNO");return;
	}
	if (sDateSTR==""){
		alert("请输入开始出货日期");return;
	}
	if (sign==1){
		 document.form1.action="yw_goodsshipped_saveexceled.php?CompanyId="+CompanyIdSTR+"&sDate="+sDateSTR+"&eDate="+eDateSTR+"&InvoiceNO="+InvoiceNOSTR;
	}else{
		document.form1.action="yw_goodsshipped_saveexceled2.php?CompanyId="+CompanyIdSTR+"&sDate="+sDateSTR+"&eDate="+eDateSTR+"&InvoiceNO="+InvoiceNOSTR;
	}
	
	//alert("yw_goodsshipped_saveexceled.php?CompanyId="+CompanyIdSTR+"&sDate="+sDateSTR+"&eDate="+eDateSTR+"&InvoiceNO="+InvoiceNOSTR);
	document.form1.submit();
}
</script>