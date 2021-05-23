<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$upDataMain="$DataIn.ck2_thmain";
ChangeWtitle("$SubCompany 更新退换主单资料");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT T.BillNumber,T.Date,T.Locks,P.Forshort
FROM $upDataMain T
LEFT JOIN $DataIn.trade_object P ON P.CompanyId=T.CompanyId
WHERE T.Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$BillNumber=$MainRow["BillNumber"];
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
        <table width="100%" border="0" align="center" cellspacing="5" id="NoteTable">
			<tr>
				<td width="100" align="right" scope="col">供 应 商</td>
				<td scope="col"><?php  echo $Forshort?></td>
			</tr>
			<tr>
				<td align="right" scope="col">退换单号</td><td scope="col"><?php  echo $BillNumber?>
				  <input name="BillNumber" type="hidden" id="BillNumber" value="<?php  echo $BillNumber?>"></td>
			</tr>
			<tr>
				<td align="right">退换日期</td><td><input name="Date" type="text" id="Date" value="<?php  echo $Date?>" size="40" onfocus="WdatePicker()" title="必选项" datatype="Date" format="ymd" msg="格式不对" readonly></td>
			</tr>
			<tr>
				<td align="right" valign="top">附&nbsp;&nbsp;&nbsp;&nbsp;件</td><td ><input name="Attached" type="file" id="Attached" size="75" title="可选项,JPG格式" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="3" Cel="1"></td>
			</tr>
			<?php 
			if($BillNumber!=0){
				echo"<tr><td>&nbsp;</td><td><input name='oldAttached' type='checkbox' id='oldAttached' value='1'><LABEL for='oldAttached'>删除已传附件</LABEL></td></tr>";
				}
			?>
		</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>