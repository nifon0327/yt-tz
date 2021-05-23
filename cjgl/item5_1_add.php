<?php
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
include "../basic/config.inc";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$tableWidth=930;
$funFrom="item5_1";
$saveWebPage=$funFrom . "_ajax.php?ActionId=80";
//供应商:有采购且未收货完货
$SelectCode="<select name='CompanyId2' id='CompanyId2' onchange='deleteAllRow(this);' style='width: 150px;'>";
$GYS_Sql = "SELECT S.CompanyId,P.Forshort,P.Letter 
			FROM $DataIn.cg1_stocksheet S 
           	LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	        LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
			WHERE S.rkSign>0   AND S.blsign = 1 AND S.Mid>0 
            GROUP BY S.CompanyId ORDER BY P.Letter";
            //echo $GYS_Sql;// AND S.CompanyId NOT IN (".$APP_CONFIG['ASH_IN_SUPPLIER'].")
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
            <td width="120" align="right" height="30px">供应商</td>
            <input name="TempCompanyId" type="hidden" id="TempCompanyId" value="<?php    echo $CompanyId2?>">
            <td><?php    echo $SelectCode?></td>
             <td width="80" align="right">送货单号</td>
            <td width="300"><input  type="text" name="TempGysNumber" value="<?php echo date(YmdHis).mt_rand(100,999).substr(time(),7); ?>" id="TempGysNumber" size="20" onkeyup="this.value=this.value.replace(/\D/g,'')" >*只能输入数字</td>
             <td width="60"><input style='height:25px;' type="file" name="fileinput" size="15"></td>
           </tr>
           <tr>
            <td width="120" align="right" height="25px">送货单备注</td>
            <td  colspan="3"><input name="Remark" type="text" id="Remark" size="68" class="INPUT0100"></td>
             <td colspan="2" align="right">
              <input name="stuffQuery"   type="button" id="stuffQuery" value="加入送货配件" onClick="viewStuffdata()" >             </td>
        </tr>
   </table>
     <table border="0" width="<?php    echo $tableWidth?>px" cellpadding="0" cellspacing="0" align="center">
	       <tr bgcolor='#EEEEEE'>
		<td width="10" class="A0010" height="25" bgcolor='#CCC'>&nbsp;</td>
		<td class="A1111" width="40" align="center">操作</td>
		<td class="A1101" width="40" align="center">序号</td>
		<td class="A1101" width="50" align="center">配件ID</td>
		<td class="A1101" width="220" align="center">配件名称</td>
		<td class="A1101" width="70" align="center">订单需求数</td>
		<td class="A1101" width="70" align="center">采购总数</td>
		<td class="A1101" width="70" align="center">已送货总数</td>
		<td class="A1101" width="70" align="center">未送货总数</td>
		<td class="A1101" width="70" align="center">本次送货</td>
		<td class="A1101" width="70" align="center">未补货总数</td>
		<td class="A1101" width="70" align="center">本次补货</td>
		<td class="A1101" width="70" align="center">本次备品</td>
	</tr>
	<tr>
		<td width="10" class="A0010" height="432">&nbsp;</td>
		<td colspan="12" align="center" class="A0111">
		<div style="width:930;height:100%;overflow-x:hidden;overflow-y:scroll;background:#FFF;">
			<table width='930' cellpadding="0" cellspacing="0"  id="ListTable">
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