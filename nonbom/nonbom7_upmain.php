<?php 
//ewen 2013-03-04 OK
include "../model/modelhead.php";
$upDataMain="$DataIn.nonbom7_inmain";
ChangeWtitle("$SubCompany 更新非bom配件入库主单资料");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT A.BillNumber,A.Bill,A.Remark,A.Date,A.Locks,B.Forshort,C.Name 
FROM $upDataMain A 
LEFT JOIN $DataPublic.nonbom3_retailermain B ON B.CompanyId=A.CompanyId
LEFT JOIN $DataPublic.staffmain C ON C.Number=A.BuyerId
WHERE A.Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$BillNumber=$MainRow["BillNumber"];
	$Bill=$MainRow["Bill"];
	$Remark=$MainRow["Remark"];
	$Forshort=$MainRow["Forshort"];
	$Name=$MainRow["Name"];
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
            <td align="right" scope="col">采&nbsp;&nbsp;&nbsp;&nbsp;购</td>
            <td scope="col"><?php  echo $Name?></td>
		</tr>
		<tr>
            <td align="right" scope="col">入库日期</td>
            <td scope="col"><input name="Date" type="text" id="Date" value="<?php  echo $Date?>" size="72" onfocus="WdatePicker()" title="必选项，结付日期" DataType="Date" format="ymd" Msg="日期不对或没选日期" readonly></td>
		</tr>

		<tr>
            <td align="right">入库凭证</td>
            <td><input name="Bill" type="file" id="Bill" size="70" title="可选项,JPG格式" DataType="FilterB" Accept="pdf,jpg,png" Msg="文件格式不对,请重选" ></td>
		</tr>
            <tr>
                <td align="right" scope="col">入库单号</td>
                <td scope="col"><input name="BillNumber" type="text" id="BillNumber" value="<?php  echo $BillNumber?>" size="72" title="可选项，入库单号" DataType="LimitB" require="false"  Msg="必须在2-25个字节之内" min="2" max="25"></td>
            </tr>

        <?php 
			if($Bill!=0){
				echo"<tr><td>&nbsp;</td><td><input name='oldBill' type='checkbox' id='oldBill' value='1'><LABEL for='oldPayee'>删除已传凭证</LABEL></td></tr>";
				}
			?>
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