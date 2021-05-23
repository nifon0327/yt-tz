<?php 
//电信---yang 20120801
//步骤1  $DataIn.trade_object  二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增研发项目");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"] = $nowWebPage;
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" id='NoteTable' cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td width="160" scope="col" class="A0010" height="32" align="right">登记日期: </td>
    	<td class="A0001" scope="col">
		<input name="djDate" type="text" id="djDate" style="width:120px" onfocus="WdatePicker()" maxlength="10"  DataType="Date" format="ymd" Msg="日期不对或没选日期"  value='<?php echo date('Y-m-d');?>' readonly>
		</td>
	</tr>
	<tr>
		<td scope="col" class="A0010" height="32" align="right">项目类型: </td>
    	<td class="A0001" scope="col">
		<select name="TypeId" id="TypeId" size="1" style="width: 402px;">
	    	<option value='' selected>请选择</option>
      	<?php  
		$result = mysql_query("SELECT Id,Name FROM $DataIn.projectset_Type WHERE Estate=1 ORDER BY Id",$link_id);
		if($myrow = mysql_fetch_array($result)){
			do{
				echo"<option value='$myrow[Id]'>$myrow[Name]</option>";
				} while ($myrow = mysql_fetch_array($result));
			}
		?>
    	</select>
		</td>
	</tr>
	<tr>
		<td scope="col" class="A0010" height="32" align="right">项目名称: </td>
		<td scope="col" class="A0001">
		<input name="ItemName" type="text" id="ItemName" style="width: 402px;" title="可输入1-50个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="50" Min="1" Msg="没有填写或字符超出50字节">
		</td>
	</tr>
    
     <tr>
        <td scope="col" class="A0010" height="32" align="right">责任人: </td>
        <td class="A0001" scope="col">
            <select name="Principal" id="Principal" style="width:402px"  dataType="Require"  msg="未选择">
            <option value='' selected>请选择</option>
		    <?php  
			    $StaffSql = mysql_query("SELECT M.Number,M.Name
						FROM $DataPublic.staffmain M 
						WHERE  1  AND M.Estate='1'  AND M.kqSign>1 ORDER BY M.Number",$link_id);
                  	while ($StaffRow = mysql_fetch_array($StaffSql)){
                  	    $theNumber=$StaffRow["Number"];
						$StaffName=$StaffRow["Name"];
						
						echo "<option value='$theNumber'>$StaffName</option>";
	                }
		    ?>
        </select>
          </td>
    </tr>
  
    <tr>
	  <td scope="col" class="A0010" height="32" align="right">参与人: </td>
	  <td class="A0001" scope="col"><p>
	    <select name="ListId[]" size="5" id="ListId" multiple style="width: 200px;" datatype="autoList"   onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,7)" readonly>
        </select>
	  </p>
	    </td>
	</tr>
	
	 <tr>
		<td scope="col" class="A0010" height="32" align="right">开始日期: </td>
		<td class="A0001" scope="col">
		 <input name="StartDate" type="text" id="StartDate"  style="width:120px" onclick="WdatePicker()" src='../model/DatePicker/skin/datePicker.gif'  align='absmiddle'  dataType="Date" format="ymd" msg="日期不正确" readonly>
	  </td>
	</tr>
	 <tr>
		<td scope="col" class="A0010" height="32" align="right">预计完成日期: </td>
		<td class="A0001" scope="col">
		 <input name="EstimatedDate" type="text" id="EstimatedDate"  style="width:120px" onclick="WdatePicker()" src='../model/DatePicker/skin/datePicker.gif'  align='absmiddle'  dataType="Date" format="ymd" msg="日期不正确" readonly>
	  </td>
	</tr>
	
     <tr>
		<td scope="col" class="A0010" height="32" align="right">项目描述: </td>
		<td class="A0001" scope="col">
		<textarea name="Description" cols="55" rows="6" id="Description" DataType="Require" Msg="没有填写"></textarea>
	  </td>
	</tr>
     <tr>
		<td scope="col" class="A0010" height="32" align="right">费用预算: </td>
		<td class="A0001" scope="col">
		 <input name="Amount" type="text" id="Amount"  style="width:120px" dataType="Number" msg="金额不正确">
	  </td>
	</tr>
	<tr>
		<td scope="col" class="A0010" height="32" align="right">档案上传: </td>
		<td class="A0001" scope="col"><input name="Attached" type="file" id="Attached" size="48" datatype="Filter" accept="pdf" msg="文件格式不对,请重选"  Row="9" Cel="1">限PDF格式</td>
	</tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>