<?php
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取入库资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$mySql="SELECT R.BillNumber,R.Date,R.Locks,P.Forshort
FROM $DataIn.ck2_thmain  R 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=R.CompanyId
WHERE R.Id='$Mid' LIMIT 1";
$MainResult = mysql_query($mySql,$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$BillNumber=$MainRow["BillNumber"];
	$Forshort=$MainRow["Forshort"];
	$Locks=$MainRow["Locks"];
	$Date=$MainRow["Date"];
	}
$saveWebPage= "item5_7_ajax.php?Id=$Mid&ActionId=5";
?>
<iframe name="FormSubmit" id="FormSubmit" width="1" height="1" style="display:none;"></iframe>
<form action="<?php    echo $saveWebPage?>" method="post"  target="FormSubmit" name="saveForm" id="saveForm"  enctype="multipart/form-data" >
  <table width="600" height="100" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
    <td width="200" height="50" class="A1111">供 应 商<input name="Id" type="hidden" id="Id" value="<?php    echo $Mid?>"></td>
    <td width="150" class="A1101">退货日期</td>
    <td width="150" class="A1101">退货单号</td>
  </tr>
  <tr align="center">
    <td height="50" class="A0111"><?php    echo $Forshort?></td>
    <td class="A0101"><input name="thDate" type="text" id="thDate" value="<?php    echo $Date?>" size="20" onfocus="WdatePicker()" title="必选项，结付日期" DataType="Date" format="ymd" Msg="日期不对或没选日期" readonly></td>
    <td class="A0101"><input name="BillNumber" type="text" id="BillNumber" readonly  value="<?php    echo $BillNumber?>"  size="20" ></td>
    </tr>
   <tr align="center">
    <td width="150" height="50" bgcolor="#d6efb5" class="A0111">退货图片</td>
  	<td colspan="2" bgcolor="#FFFFFF" class="A0101"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" ></td>
  </tr>
</table>
<table width="600" height="61" border="0" cellpadding="0" cellspacing="0" bgcolor="#d6efb5">
  <tr>
        <td width="400">&nbsp;</td>
        <td width="100" align="center"><input type="button" name="cancelBtn" value="取消" onclick="closeMaskDiv()"></td>
        <td width="100" align="center"><input type='submit' id='submit' value='保存' /></td>
      </tr>
</table>
 </form>
