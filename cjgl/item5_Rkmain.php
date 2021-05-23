<?php   
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取入库资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$mySql="SELECT R.BillNumber,R.Remark,R.Date,R.Locks,P.Forshort,M.Name 
FROM $DataIn.ck1_rkmain R 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=R.CompanyId
LEFT JOIN $DataPublic.staffmain M ON M.Number=R.Operator
WHERE R.Id='$Mid' LIMIT 1";
$MainResult = mysql_query($mySql,$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$BillNumber=$MainRow["BillNumber"];
	$Remark=$MainRow["Remark"];
	$Forshort=$MainRow["Forshort"];
	$Name=$MainRow["Name"];
	$Locks=$MainRow["Locks"];
	$Date=$MainRow["Date"];
	}
$UpdateRkmain="UpdateRkmain($Mid);";
?>
  <table width="600" height="100" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
    <td width="200" height="50" class="A1111">供 应 商<input name="Id" type="hidden" id="Id" value="<?php    echo $Id?>"></td>
    <td width="100" class="A1101">操 作 人</td>
    <td width="150" class="A1101">送货日期</td>
    <td width="150" class="A1101">送货单号</td>
  </tr>
  <tr align="center">
    <td height="50" class="A0111"><?php    echo $Forshort?></td>
    <td class="A0101"><?php    echo $Name?></td>
    <td class="A0101"><input name="Date" type="text" id="Date" value="<?php    echo $Date?>" size="20" onfocus="WdatePicker()" title="必选项，结付日期" DataType="Date" format="ymd" Msg="日期不对或没选日期" readonly></td>
    <td class="A0101"><input name="BillNumber" type="text" id="BillNumber" value="<?php    echo $BillNumber?>" size="20"></td>
    </tr>
   <tr align="center">
    <td width="150" height="50" bgcolor="#d6efb5" class="A0111">入库备注</td>
  	<td colspan="3" bgcolor="#FFFFFF" class="A0101"><textarea name="Remark" id="Remark" cols="52" rows="5" title="可选项"><?php    echo $Remark?></textarea></td>
  </tr>
</table>
<table width="600" height="61" border="0" cellpadding="0" cellspacing="0" bgcolor="#d6efb5">
  <tr>
        <td width="400">&nbsp;</td>
        <td width="100" align="center"><input type="button" name="Submit" value="取消" onclick="closeMaskDiv()"></td>
        <td width="100" align="center"><input type="button" name="Submit" value="提交" onclick="<?php    echo $UpdateRkmain?>"></td>
      </tr>
</table>
