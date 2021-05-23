<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$upDataMain="$DataIn.cw15_gyskkmain";
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
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr><td class="A0010" align="right" scope="col" width="200" height="30">供 应 商</td>
            <td class="A0001"  scope="col"><?php  echo $Forshort?></td>
		</tr>
		<tr>
            <td class="A0010"  align="right" scope="col" height="30">扣款日期</td>
            <td class="A0001"  scope="col"><input name="Date" type="text" id="Date" value="<?php  echo $Date?>" size="72" onfocus="WdatePicker()" title="必选项，结付日期" DataType="Date" format="ymd" Msg="日期不对或没选日期" readonly></td>
		</tr>
		<tr>
            <td class="A0010"  align="right" height="30">扣款单号</td>
            <td class="A0001" >
			<input name="BillNumber" type="text" id="BillNumber" value="<?php  echo $BillNumber?>" size="72">
			</td>
		</tr>
		<tr>
            <td class="A0010"  align="right" height="30">凭证</td>
            <td class="A0001" >
			<input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="3" Cel="1"><span class="redB">.jpg格式</span>
			</td>
		</tr>
		<tr>
            <td class="A0010"  align="right" valign="top" height="30">扣款备注</td>
            <td class="A0001" ><textarea name="Remark" id="Remark" cols="56" rows="5" title="可选项"><?php  echo $Remark?></textarea></td>
          </tr>
</table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>