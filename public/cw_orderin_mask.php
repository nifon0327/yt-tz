<?php 
//电信-zxq 2012-08-01
//$DataIn.trade_object 二合一已更新
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$ModelCompanyId=" and CompanyId='$DeliveryValue'";
$checkC=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataIn.trade_object WHERE 1 $ModelCompanyId LIMIT 1",$link_id));
$Forshort=$checkC["Forshort"];
?>
	<table width="430" border="0" cellspacing="0"><input name="TempValue" type="hidden" id="TempValue">
		<tr>
		  <td colspan="2" align="center" valign="top">客户: <?php  echo $Forshort?> 的收款资料</td>
		</tr>
		<tr>
		  <td width="62" align="center">收款日期</td>
		  <td><input name="PayDate" type="text" id="PayDate" value="<?php  echo date("Y-m-d")?>" size="50" maxlength="10" onfocus="toTempValue(this.value)"></td>
		</tr>
		<tr>
		  <td align="center">手续费</td>
		  <td><input name="Handingfee" type="text" id="Handingfee" size="50" value="0"></td>
  		</tr>
		<tr>
		  <td align="center">TT备注</td>
		  <td><input name="Remark" type="text" id="Remark" size="50"></td>
  		</tr>
                <tr>
		  <td align="center">进帐凭证</td>
		  <td><input name="Attached" type="file" id="Attached"  style="width: 348px;" DataType="Filter" Accept="pdf" Msg="文件格式不对,请重选" Row="1" Cel="1"></br>注：限PDF格式</td>
  		</tr>
		<tr valign="bottom"><td height="27" colspan="2" align="right"><a href="javascript:ckeckForm()">确定</a> &nbsp;&nbsp; <a href="javascript:closeMaskDiv()">取消</a></td></tr>
</table>
