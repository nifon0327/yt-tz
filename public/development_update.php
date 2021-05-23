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
	$Plan=$upRow["Plan"];
	$Attached=$upRow["Attached"];
	$Qty=$upRow["Qty"];
	$StartDate=$upRow["StartDate"];
	$EndDate=$upRow["EndDate"]=="0000-00-00"?"":$upRow["EndDate"];
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
	  <td width="160" height="26" align="right" class="A0010" scope="col">起始日期：</td>
	  <td class="A0001" scope="col"><?php  echo $StartDate?></td>
  </tr>

	<tr>
		<td width="160" height="26" align="right" class="A0010" scope="col">客&nbsp;&nbsp;&nbsp;&nbsp;户：</td>
    	<td scope="col" class="A0001">
		<select name="CompanyId" id="CompanyId" size="1" style="width: 402px;">
		<?php  
		$result = mysql_query("SELECT * FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 ORDER BY Id",$link_id);
		if($myrow = mysql_fetch_array($result)){
			do{	
				if($myrow[CompanyId]==$CompanyId){
					echo"<option value='$myrow[CompanyId]' selected>$myrow[Forshort]</option>";
					}
				else{
					echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
					}
				} while ($myrow = mysql_fetch_array($result));
			}
		?>
        </select>
		</td>
	</tr>
	<tr>
    	<td height="26" align="right" class="A0010" scope="col">项目名称：</td>
		<td class="A0001" scope="col"><input name="ItemName" type="text" id="ItemName" size="74" value="<?php  echo $ItemName?>" title="可输入1-50个字节(每1中文字占2个字节，第1英文字母占1个字节)" DataType="LimitB"  Max="50" Min="1" Msg="没有填写或字符超出50字节"> </td>
	</tr>
	<tr>
		<td scope="col" class="A0010" height="32" align="right">数&nbsp;&nbsp;&nbsp;&nbsp;量:</td>
    	<td class="A0001" scope="col"><input name="Qty" type="text" id="Qty" size="74"  value="<?php  echo $Qty?>" dataType="Currency" value="0" msg="错误的数量"/>
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
		<td class="A0001" scope="col"><input name="Gfile" type="file" id="Gfile" size="48" datatype="Filter" accept="ai" msg="文件格式不对,请重选" row="3">限ai格式</td>
	</tr>
	<tr>
		<td height="81" align="right" valign="top" class="A0010" scope="col">项目内容：</td>
		<td valign="top" class="A0001" scope="col"><textarea name="Content" cols="48" rows="6" id="Contant" DataType="Require" GandB="1" Msg="没有填写"><?php  echo $Content?></textarea> </td>
	</tr>
</table>		
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>