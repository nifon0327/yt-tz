<?php 
/*电信---yang 20120801
$DataIn.development
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新开发项目");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"] = $nowWebPage;

//步骤3：//需处理
$upResult = mysql_query("SELECT * FROM $DataIn.development WHERE Id=$Id",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	$CompanyId=$upRow["CompanyId"];
	$ItemId=$upRow["ItemId"];
	$ItemName=$upRow["ItemName"];
	$Content=$upRow["Content"];
	$Attached=$upRow["Attached"];
	$StartDate=$upRow["StartDate"];
    $Qty=$upRow["Qty"];
	$Plan=$upRow["Plan"];
	}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ItemId,$ItemId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
   	  <td width="160" height="26" align="right" class="A0010" scope="col">项目编号：</td>
    	<td scope="col" class="A0001"><?php  echo $ItemId?></td>
	</tr>
	<tr>
	  <td width="160" height="26" align="right" class="A0010" scope="col">登记日期：</td>
	  <td class="A0001" scope="col"><?php  echo $StartDate?></td>
   </tr>
   <tr>
       <td height="26" align="right" class="A0010" scope="col">项目名称：</td>
	   <td class="A0001" scope="col"><?php  echo $ItemName?></td>
	</tr>
	<tr>
		<td scope="col" class="A0010" height="32" align="right">数&nbsp;&nbsp;&nbsp;&nbsp;量:</td>
    	<td class="A0001" scope="col"><input name="Qty" type="text" id="Qty" size="48"  value="<?php  echo $Qty?>" dataType="Currency" value="0" msg="错误的数量"/>
		</td>
	</tr>
	<tr>
    	<td width="160" scope="col" class="A0010" height="32" align="right">样品交期：</td>
    	<td class="A0001" scope="col">
		<input name="EndDate" type="text" id="EndDate" size="48" onfocus="WdatePicker()" maxlength="10" value=<?php  echo date("Y-m-d")?> DataType="Date" format="ymd" Msg="日期不对或没选日期" readonly>
		</td>
	</tr>
	<tr>
		<td height="26" align="right" class="A0010" scope="col">产品效果图：</td>
		<td class="A0001" scope="col"><input name="upFile" type="file" id="upFile" size="48" datatype="Filter" accept="jpg,pdf" msg="文件格式不对,请重选" row="3">限JPG,PDF格式</td>
	</tr>
	<?php 
	if($Attached!=0){
		echo"<tr><td height='26' align='right' class='A0010' scope='col'>&nbsp;</td><td class='A0001' scope='col'><input name='oldFile' type='checkbox' id='oldFile' value='$Attached'><LABEL for='oldFile'>删除已传图片</LABEL> $Attached</td></tr>";
		}
	?>
	<tr>
		<td height="26" align="right" class="A0010" scope="col">AI图档：</td>
		<td class="A0001" scope="col"><input name="Gfile" type="file" id="Gfile" size="48" datatype="Filter" accept="ai,pdf" msg="文件格式不对,请重选" row="3">限ai,pdf格式</td>
	</tr>
	<tr>
		<td height="81" align="right" valign="top" class="A0010" scope="col">开发进度：</td>
		<td valign="top" class="A0001" scope="col"><textarea name="Plan" cols="48" rows="6" id="Plan" DataType="Require" GandB="1" Msg="没有填写"><?php  echo $Plan?></textarea> </td>
	</tr>
</table>		
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>