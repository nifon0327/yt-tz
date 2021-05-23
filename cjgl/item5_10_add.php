<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=820;
$funFrom="item5_10";
$saveWebPage=$funFrom . "_ajax.php?ActionId=1";

$SelectCode="<select name='CompanyId' id='CompanyId' onchange='document.getElementById(\"TempCompanyId\").value=this.value;' style='width: 150px;'>";
//供应商:有采购且有收货的方可退料
$GYS_Sql = "SELECT S.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.cg1_stocksheet S 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 			
			WHERE S.rkSign!=1 AND S.Mid>0 GROUP BY S.CompanyId ORDER BY P.Letter";
$GYS_Result = mysql_query($GYS_Sql);
if($GYS_Myrow = mysql_fetch_array($GYS_Result)){
	$oldLetter="";
	do{
		$ProviderTemp=$GYS_Myrow["CompanyId"];
		$CompanyId=$CompanyId==""?$ProviderTemp:$CompanyId;
		$Forshort=$GYS_Myrow["Forshort"];
		$Letter=$GYS_Myrow["Letter"];
		if($oldLetter==$Letter){
			$Forshort='&nbsp;&nbsp;&nbsp;&nbsp;'.$Forshort;
			}
		else{
			$Forshort=$Letter.'-'.$Forshort;
			$oldLetter=$Letter;
			}
		if($ProviderTemp==$CompanyId){
			$SelectCode.="<option value='$ProviderTemp' selected>$Forshort</option>";
			}
		else{
			$SelectCode.="<option value='$ProviderTemp'>$Forshort</option>";
			}
		}while ( $GYS_Myrow = mysql_fetch_array($GYS_Result));
	}
    $SelectCode.="</select>";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $saveWebPage?>" method="post"  target="FormSubmit" name="saveForm" id="saveForm" onsubmit= "return CheckForm();">
 <table width="<?php    echo $tableWidth?>px" border="0" align="left" cellspacing="5">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
		<tr>
            <td width="80" align="right">置换配件名称</td>
            <td  width="350" align="left"><input name="StuffCname" type="text" id="StuffCname" size="42"  datatype="Require"  msg="未选择入库配件"  oninput="CnameChanged()" onchange="CnameChanged()"> <input name="StuffId" type="hidden" id="StuffId"></td>
            <td align="left">
            <input name="stuffQuery" type="button" id="stuffQuery" value="加入配件领料记录" onClick="viewStuffdata()" disabled>
            </td>
          </tr>
         <tr>
            <td width="80" align="right">置换备料原因</td>
            <td  colspan="2" width="500" align="left"><textarea name="Remark" cols="80" rows="2" id="Remark" dataType="Require"  msg="未输入置换配件原因<"></textarea></td>
            </td>
          </tr>
   </table>
     <table border="0" width="<?php    echo $tableWidth?>px" cellpadding="0" cellspacing="0" align="center">
	       <tr bgcolor='#EEEEEE'>
		<td width="10" class="A0010" height="25" bgcolor='#CCC'>&nbsp;</td>
		<td class="A1111" width="50" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
        <input type='hidden' id='llId' name='llId'/>
		<td class="A1101" width="100" align="center">需求流水号</td>
        <td class="A1101" width="60" align="center">订单PO</td>
		<td class="A1101" width="210" align="center">配件名称</td>
        <td class="A1101" width="60" align="center">需备料数</td>
		<td class="A1101" width="60" align="center">原领料数</td>
		<td class="A1101" width="150" align="center">置换操作</td>
        <select name='operator' id='operator' style='display:none;'></select>
        <input type='hidden' name='thQTY' id='thQTY' />
        <td class="A1101" width="" align="center">置换后数量</td>
        <input type='hidden' name='changeQty' id='changeQty' />
		<td width="10" class="A0000" bgcolor='#CCC'>&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="432">&nbsp;</td>
		<td colspan="9" align="center" class="A0111">
		<div style="width:800;height:100%;overflow-x:hidden;overflow-y:scroll;background:#FFF;">
			<table width='800' cellpadding="0" cellspacing="0"  id="ListTable">
			<input name="TempValue" type="hidden" id="TempValue">
			</table>
		</div>
		</td>
        <td width="10" class="A0000">&nbsp;</td>
	</tr>
</table>

 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><input type='submit' id='submit' value='保存' /></td>
    <td align="center"><input class='ButtonH_25' type='button'  id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
</form>