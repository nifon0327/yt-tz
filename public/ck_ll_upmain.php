<?php 
//电信-zxq 2012-08-01
/*
$DataPublic.staffmain
$DataIn.ck5_llmain
*/
include "../model/modelhead.php";
$upDataMain="$DataIn.ck5_llmain";
ChangeWtitle("$SubCompany 更新领料主单资料");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT L.Remark,L.Date,L.Locks,M.Name 
FROM $upDataMain L 
LEFT JOIN $DataPublic.staffmain M ON M.Number=L.Materieler
WHERE L.Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$Remark=$MainRow["Remark"];
	$Name=$MainRow["Name"];
	$Locks=$MainRow["Locks"];
	$Date=$MainRow["Date"];
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,20,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate";

//步骤4：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td align="right" scope="col">领&nbsp;人&nbsp;料</td>
            <td scope="col"><?php  echo $Name?></td>
		</tr>
		<tr>
            <td align="right" scope="col">领料日期</td>
            <td scope="col"><input name="Date" type="text" id="Date" value="<?php  echo $Date?>" size="87" onfocus="WdatePicker()" title="必选项，结付日期" DataType="Date" format="ymd" Msg="日期不对或没选日期" readonly></td>
		</tr>
		<tr>
            <td align="right" valign="top">领料备注</td>
            <td ><textarea name="Remark" id="Remark" cols="56" rows="5" title="可选项"><?php  echo $Remark?></textarea></td>
          </tr>
</table>
	</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>