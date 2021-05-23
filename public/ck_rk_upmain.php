<?php 
//电信-zxq 2012-08-01
/*
$DataIn.ck1_rkmain
$DataIn.trade_object
$DataPublic.staffmain
二合一已更新
*/
include "../model/modelhead.php";
$upDataMain="$DataIn.ck1_rkmain";
ChangeWtitle("$SubCompany 更新入库主单资料");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：

$MainResult = mysql_query("SELECT R.BillNumber,R.Remark,R.Date,R.Locks,P.Forshort 
FROM $upDataMain R 
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=R.CompanyId
WHERE R.Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$BillNumber=$MainRow["BillNumber"];
	$Remark=$MainRow["Remark"];
	$Forshort=$MainRow["Forshort"];
	$Locks=$MainRow["Locks"];
	$Date=$MainRow["Date"];
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,20,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate,CompanyId,$CompanyId";

//步骤4：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right" scope="col">供 应 商</td>
            <td scope="col"><?php  echo $Forshort?></td>
		</tr>
		<tr>
            <td align="right" scope="col">送货日期</td>
            <td scope="col"><input name="Date" type="text" id="Date" value="<?php  echo $Date?>" size="72" onfocus="WdatePicker()" title="必选项，结付日期" DataType="Date" format="ymd" Msg="日期不对或没选日期" readonly></td>
		</tr>
		<tr>
            <td align="right">送货单号</td>
            <td>
			<input name="BillNumber" type="text" id="BillNumber" value="<?php  echo $BillNumber?>" size="72">
			</td>
		</tr>
		<tr>
            <td align="right" valign="top">入库备注</td>
            <td ><textarea name="Remark" id="Remark" cols="56" rows="5" title="可选项"><?php  echo $Remark?></textarea></td>
          </tr>
</table>
	</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>