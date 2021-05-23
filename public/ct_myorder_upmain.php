<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$upDataMain="$DataPublic.ct_myordermain";
ChangeWtitle("$SubCompany 更新点餐主单资料");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_upmain";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$MainResult = mysql_query("SELECT R.Remark,R.Date  FROM $upDataMain R  WHERE R.Id='$Mid' LIMIT 1",$link_id);
if($MainRow = mysql_fetch_array($MainResult)) {
	$Remark=$MainRow["Remark"];
	$Date=$MainRow["Date"];
	}
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="ActionId,20,Mid,$Mid,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseDate,$chooseDate";

//步骤4：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
		<tr>
            <td class="A0010"  align="right" scope="col" width="200" height="30">点餐日期:</td>
            <td class="A0001"  scope="col"><?php  echo $Date?></td>
		</tr>
		<tr>
            <td class="A0010"  align="right" height="30">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;据</td>
            <td class="A0001" >
			<input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="3" Cel="1">
			</td>
		</tr>
		<tr>
            <td class="A0010"  align="right" valign="top" height="30">备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td class="A0001" ><textarea name="Remark" id="Remark" cols="56" rows="5" title="可选项"><?php  echo $Remark?></textarea></td>
          </tr>
</table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>