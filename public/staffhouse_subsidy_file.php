<?php   
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 上传凭证");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_file";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT   S.Id,S.Remark,M.Name,B.Name AS Branch,J.Name AS Job,S.Number
FROM  $DataIn.cw21_housefeesheet   S 
LEFT JOIN $DataIn.staffmain M ON M.Number=S.Number
LEFT JOIN $DataIn.branchdata B ON B.Id=S.BranchId
LEFT JOIN $DataIn.jobdata J ON J.Id=S.JobId
WHERE S.Id='$Mid'",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$Name=$MainRow["Name"];
    $Remark=$MainRow["Remark"];
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,127,Mid,$Mid,Id,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

//步骤4：//需处理
?>
<table border="0" width="<?php echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5" id="NoteTable">
		<tr>
            <td  height="25"  align="right" scope="col">姓名:</td>
            <td scope="col"><?php    echo $Name?></td>
		</tr>               
		<tr>
		  <td align="right"  height="25">凭证上传:</td>
            <td scope="col"><input name="Attached" type="file" id="Attached" size="60" DataType="Filter" Accept="pdf,jpg" Msg="格式不对,请重选" Row="1" Cel="2"></td>
    	</tr>

		<tr>
		  <td height="25" align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
		  <td scope="col"><textarea name="Remark" cols="40" rows="3" id="Remark" ><?php echo $Remark?></textarea></td>
		</tr>
</table>
	</td></tr></table>
<?php   
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>