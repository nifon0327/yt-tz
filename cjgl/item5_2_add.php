<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=860;
$funFrom="item5_2";
$saveWebPage=$funFrom . "_ajax.php?ActionId=1";
//供应商:有采购且未收货完货
$SelectCode="<select name='CompanyId2' id='CompanyId2' onchange='deleteAllRow(this);' style='width: 150px;'>";

$GYS_Sql = "
            SELECT S.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.cg1_stocksheet S 
           	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	        LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 		
			WHERE S.rkSign>0  AND S.blsign = 1  AND S.Mid>0  GROUP BY S.CompanyId ORDER BY P.Letter";
$GYS_Result = mysql_query($GYS_Sql);
while ( $GYS_Myrow = mysql_fetch_array($GYS_Result)){
		$ProviderTemp=$GYS_Myrow["CompanyId"];
		$CompanyId2=$CompanyId2==""?$ProviderTemp:$CompanyId2;
		$Forshort=$GYS_Myrow["Forshort"];
		$Letter=$GYS_Myrow["Letter"];
		$Forshort=$Letter.'-'.$Forshort;
		if ($ProviderTemp==$CompanyId2){
		    $SelectCode.="<option value='$ProviderTemp' selected>$Forshort</option>";
		}
		else{
			$SelectCode.="<option value='$ProviderTemp'>$Forshort</option>";
		}
 }

$SelectCode.="</select>";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $saveWebPage?>" method="post"  enctype="multipart/form-data" target="FormSubmit" name="saveForm" id="saveForm" onsubmit= "return CheckForm();">
 <table width="<?php    echo $tableWidth?>px" border="0" align="left" cellspacing="5">
 <!--显示表单内容 onsubmit="return Validator.Validate(this,3);"-->
		<tr >
            <td width="64" align="right">供应商</td>
            <input name="TempCompanyId" type="hidden" id="TempCompanyId" value="<?php    echo $CompanyId2?>">
             <input name="BuyerId" type="hidden" id="BuyerId" value="">
            <td><?php    echo $SelectCode?></td>
             <td width="64" align="right">送货单号</td>
            <td><input    name='BillNumber' type='text' id='BillNumber' size='15'> </td>
            <td><input  type="file" name="fileinput" size="15"></td>
            <td width="64" align="right">入库日期</td>
            <td><input name="rkDate" type="text" id="rkDate" value="<?php    echo date("Y-m-d")?>" size="10" maxlength="10"></td>
          </tr>
        <tr>
            <td width="64" align="right" >入库备注</td>
            <td colspan='4'><input name="Remark" type="text" id="Remark" size="70" class="INPUT0100"></td>
             <td colspan='2' align="center">
              <input name="stuffQuery"   type="button" id="stuffQuery" value="加入需求单" onClick="viewStuffdata()" >
             </td>
        </tr>
   </table>
     <table border="0" width="<?php    echo $tableWidth?>px" cellpadding="0" cellspacing="0" align="center">
	       <tr bgcolor='#EEEEEE'>
		<td width="10" class="A0010" height="25" bgcolor='#CCC'>&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="90" align="center">需求流水号</td>
		<td class="A1101" width="50" align="center">配件ID</td>
		<td class="A1101" width="300" align="center">配件名称</td>
		<td class="A1101" width="60" align="center">需求数量</td>
		<td class="A1101" width="60" align="center">增购数量</td>
		<td class="A1101" width="60" align="center">实购数量</td>
		<td class="A1101" width="60" align="center">未收数量</td>
		<td class="A1101" width="80" align="center">当前入库</td>
		<td width="10" class="A0000" bgcolor='#CCC'>&nbsp;</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="432">&nbsp;</td>
		<td colspan="10" align="center" class="A0111">
		<div style="width:860;height:100%;overflow-x:hidden;overflow-y:scroll;background:#FFF;">
			<table width='860' cellpadding="0" cellspacing="0"  id="ListTable">
			<input name="TempValue" type="hidden" id="TempValue"><input name='AddIds' type='hidden' id="AddIds">
			</table>
		</div>
		</td>
        <td width="10" class="A0000" height="432">&nbsp;</td>
	</tr>
</table>
 <table width="<?php    echo $tableWidth?>" border="0" align="center" cellspacing="5">
 <tr>
    <td>&nbsp;</td>
    <td align="center"><input type='submit'  id='submit' value='保存' /></td>
    <td align="center"><input class='ButtonH_25' type='button'   id='cancelBtn' value='取消' onclick='closeWinDialog()'/></td>
    <td>&nbsp;</td>
 </tr>
 </table>
</form>