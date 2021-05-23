<?php 
//步骤1  $DataIn.trade_object  二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增测试项目");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td width="160" scope="col" class="A0010" height="32" align="right">登记日期：</td>
    	<td class="A0001" scope="col">
		<input name="Date" type="text" id="Date" size="94" onfocus="WdatePicker()" maxlength="10" value=<?php  echo date("Y-m-d")?> DataType="Date" format="ymd" Msg="日期不对或没选日期" readonly>
		</td>
	</tr>
	<tr>
		<td scope="col" class="A0010" height="32" align="right">客&nbsp;&nbsp;&nbsp;&nbsp;户：</td>
    	<td class="A0001" scope="col">
		<select name="CompanyId" id="CompanyId" size="1" style="width: 502px;">
      	<?php  
		$result = mysql_query("SELECT * FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate=1 ORDER BY Id",$link_id);
		if($myrow = mysql_fetch_array($result)){
			do{
				echo"<option value='$myrow[CompanyId]'>$myrow[Forshort]</option>";
				} while ($myrow = mysql_fetch_array($result));
			}
		?>
    	</select>
		</td>
	</tr>
	<tr>
		<td scope="col" class="A0010" height="41"><div align="right">项目名称：</div></td>
		<td scope="col" class="A0001">
		<input name="ItemName" type="text" id="ItemName" size="94" title="可输入1-50个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="50" Min="1" Msg="没有填写或字符超出50字节">
		</td>
	</tr>
	<tr>
		<td valign="top" class="A0010" scope="col"><div align="right">项目内容：</div></td>
		<td class="A0001" scope="col">
		<textarea name="Content" cols="60" rows="6" id="Contant" DataType="Require" Msg="没有填写"></textarea>
	  </td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>