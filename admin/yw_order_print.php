<?php   
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 未出订单列印");//需处理
$Parameter="CompanyId,$CompanyId";
//步骤3：
$tableWidth=850;$tableMenuS=500;
$CheckFormURL=="thisPage";
$CustomFun="<span onClick='submitFrom()' $onClickCSS>确定</span>&nbsp;";
$SaveSTR="NO";
$isBack="N";
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<input name="TypeId" type="hidden" id="TypeId" value="<?php    echo $TypeId?>">
<input name="SearchRows" type="hidden" id="SearchRows" value="<?php    echo $SearchRows?>">
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2" class='A0011'>&nbsp;</td>
	</tr>
	<tr>
      <td width="300" height="30" align="right" class='A0010'>&nbsp;</td>
      <td class='A0001'><input name="PrintType" type="radio" value="1" id="PrintType1" checked><LABEL for="PrintType1">车间列印(不带金额)></LABEL></td>
    </tr>
    <tr>
    	<td height="30" class='A0010' align="right">&nbsp;</td>
	    <td class='A0001'><input type="radio" name="PrintType" id="PrintType2" value="2"><LABEL for="PrintType2">列印(带金额)</LABEL></td>
    </tr>
</table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
<script>
function submitFrom(){
	for(var i=0;i<document.form1.PrintType.length;i++)   
	if(document.form1.PrintType[i].checked)
		var PrintType=document.form1.PrintType[i].value;  
	document.form1.action="yw_order_print"+PrintType+".php";
	document.form1.submit();
	}
</script>