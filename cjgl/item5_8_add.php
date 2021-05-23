<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=820;
$funFrom="item5_8";
$saveWebPage=$funFrom . "_ajax.php?ActionId=1";

$SelectCode="<select name='CompanyId' id='CompanyId' onchange='document.getElementById(\"TempCompanyId\").value=this.value;' style='width: 150px;'>";
//供应商:有退换且没有补完货
$GYS_Sql ="SELECT M.CompanyId,P.Forshort,P.Letter 
	FROM $DataIn.ck2_thmain M
	LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 			
	WHERE 1 AND S.Estate = 0 GROUP BY M.CompanyId ORDER BY P.Letter";
$GYS_Result = mysql_query($GYS_Sql);
if($GYS_Myrow = mysql_fetch_array($GYS_Result)){
	$oldLetter="";
	do{
		$ProviderTemp=$GYS_Myrow["CompanyId"];
	  if ($ProviderTemp>0){
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
	     }
		}while ( $GYS_Myrow = mysql_fetch_array($GYS_Result));
	}
$SelectCode.="</select> 送货单号<input name='BillNumber' type='text' id='BillNumber' size='15'>";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $saveWebPage?>" method="post"  target="FormSubmit" name="saveForm" id="saveForm" onsubmit= "return CheckForm();">
 <table width="<?php    echo $tableWidth?>px" border="0" align="left" cellspacing="5">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
		<tr>
            <td width="64" align="right">供应商</td>
            <input name="TempCompanyId" type="hidden" id="TempCompanyId" value="<?php    echo $CompanyId?>">
            <td><?php    echo $SelectCode?> &nbsp;&nbsp;&nbsp;
            <input name="stuffQuery" type="button" id="stuffQuery" value="加入补仓配件" onClick="viewStuffdata()" >
            </td>
          </tr>
   </table>
     <table border="0" width="<?php    echo $tableWidth?>px" cellpadding="0" cellspacing="0" align="center">
	       <tr bgcolor='#EEEEEE'>
		<td width="10" class="A0010" height="25" bgcolor='#CCC'>&nbsp;</td>
		<td class="A1111" width="50" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="80" align="center">配件ID</td>
		<td class="A1101" width="276" align="center">配件名称</td>
		<td class="A1101" width="80" align="center">补仓数量</td>
		<td class="A1101" width="274" align="center">备注</td>
		<td width="1o" class="A0000" bgcolor='#CCC'>&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="432">&nbsp;</td>
		<td colspan="6" align="center" class="A0111">
		<div style="width:800;height:100%;overflow-x:hidden;overflow-y:scroll;background:#FFF;">
			<table width='800' cellpadding="0" cellspacing="0"  id="ListTable">
			<input name="TempValue" type="hidden" id="TempValue">
			</table>
		</div>
		</td>
        <td width="10" class="A0000" height="432">&nbsp;</td>
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