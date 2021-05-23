<?php 
/*电信---yang 20120801
$DataIn.development
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新研发项目");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"] = $nowWebPage;
//步骤3：//需处理
$upResult = mysql_query("SELECT * FROM $DataIn.projectset_sheet WHERE Id=$Id",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	
		$ItemId = $upRow["ItemId"];
		$ItemName = $upRow["ItemName"];
		$TypeId = $upRow["TypeId"];
		$Principal = $upRow["Principal"];
		$Participant = $upRow["Participant"];
		$Attached = $upRow["Attached"];
		
		$StartDate = $upRow["StartDate"];
		$EstimatedDate = $upRow["EstimatedDate"];
		$Description = $upRow["Description"];
		$Amount = $upRow["Amount"];
		$Remark = $upRow["Remark"];
		$Estate = $upRow["Estate"];
}

$optionSTR = '';
if ($Participant!=''){
	$checkSheet=mysql_query("SELECT Number,Name  FROM $DataPublic.staffmain  WHERE Number IN ($Participant) ",$link_id); 
	                  
	    while($checkRow = mysql_fetch_array($checkSheet)){
	        $iNumber  = $checkRow['Number'];
	        $iName    = $checkRow['Name'];
	        $optionSTR.="<option value='$iNumber'>$iNumber $iName</option>";
	    }      
}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,ItemId,$ItemId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">

	<tr>
		<td scope="col" class="A0010" height="32" align="right">项目类型: </td>
    	<td class="A0001" scope="col">
		<select name="TypeId" id="TypeId" size="1" style="width: 402px;">
      	<?php  
		$result = mysql_query("SELECT Id,Name FROM $DataIn.projectset_Type WHERE Estate=1 ORDER BY Id",$link_id);
		if($myrow = mysql_fetch_array($result)){
			do{
			    if ($TypeId==$myrow[Id]){
				    echo"<option value='$myrow[Id]' selected>$myrow[Name]</option>";
			    }else{
				    echo"<option value='$myrow[Id]'>$myrow[Name]</option>";
			    }
				
			  } while ($myrow = mysql_fetch_array($result));
			}
		?>
    	</select>
		</td>
	</tr>
	<tr>
		<td scope="col" class="A0010" height="32" align="right">项目名称: </td>
		<td scope="col" class="A0001">
		<input name="ItemName" type="text" id="ItemName" style="width: 402px;" title="可输入1-50个字节(每1中文字占2个字节，每1英文字母占1个字节)" value='<?php echo $ItemName;?>' DataType="LimitB"  Max="50" Min="1" Msg="没有填写或字符超出50字节">
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
						if($Principal==$theNumber){
							echo "<option value='$theNumber' selected>$StaffName</option>";
							}
						else{
							echo "<option value='$theNumber'>$StaffName</option>";
							}
	                }
		    ?>
        </select>
          </td>
    </tr>
  
    <tr>
	  <td scope="col" class="A0010" height="32" align="right">参与人: <br><br>
			<input type='button' name='resetList' value='重选' onclick="removeLists()"/>
	  </td>
	  <td class="A0001" scope="col"><p>
	    <select name="ListId[]" size="5" id="ListId" multiple style="width: 200px;" datatype="autoList"   onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,7)" readonly>
	      <?php echo $optionSTR;?>
        </select>
	  </p>
	    </td>
	</tr>
	
	 <tr>
		<td scope="col" class="A0010" height="32" align="right">开始日期: </td>
		<td class="A0001" scope="col">
		 <input name="StartDate" type="text" id="StartDate"  style="width:120px" onclick="WdatePicker()" src='../model/DatePicker/skin/datePicker.gif' value='<?php echo $StartDate;?>'  align='absmiddle'  dataType="Date" format="ymd" msg="日期不正确" readonly>
	  </td>
	</tr>
	 <tr>
		<td scope="col" class="A0010" height="32" align="right">预计完成日期: </td>
		<td class="A0001" scope="col">
		 <input name="EstimatedDate" type="text" id="EstimatedDate"  style="width:120px" onclick="WdatePicker()" src='../model/DatePicker/skin/datePicker.gif'  align='absmiddle' value='<?php echo $EstimatedDate;?>'  dataType="Date" format="ymd" msg="日期不正确" readonly>
	  </td>
	</tr>
	
     <tr>
		<td scope="col" class="A0010" height="32" align="right">项目描述: </td>
		<td class="A0001" scope="col">
		<textarea name="Description" cols="55" rows="6" id="Description" DataType="Require" Msg="没有填写"><?php echo $Description;?></textarea>
	  </td>
	</tr>
     <tr>
		<td scope="col" class="A0010" height="32" align="right">费用预算: </td>
		<td class="A0001" scope="col">
		 <input name="Amount" type="text" id="Amount" value='<?php echo $Amount;?>' style="width:120px" dataType="Number" msg="金额不正确">
	  </td>
	</tr>
	<tr>
        <td scope="col" class="A0010" height="32" align="right">变更状态: </td>
        <td class="A0001" scope="col">
            <select name="Estate" id="Estate" style="width:120px"  dataType="Require"  msg="未选择">
		    <?php  
		       $TempEstateSTR="EstateSTR".strval($Estate); 
	           $$TempEstateSTR="selected";	
	           switch($Estate){
	              case 1:
	                echo"<option value='1' $EstateSTR1>开发中</option>
						 <option value='3' $EstateSTR3>未验收</option>";
	               break;
	             case 2:
	                  echo"<option value='2' $EstateSTR2>未审批</option>";
	               break;
	             
	           }
			?>
        </select>
          </td>
    </tr>
   <?php 
	if($Attached!=''){
		$d=anmaIn("download/projectset/",$SinkOrder,$motherSTR);
		$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="<a href=\"openorload.php?d=$d&f=$f&Type=&Action=6\" target=\"download\"  style='CURSOR: pointer;color:#FF6633'>已上传</a>";
	}
	?>
	<tr>
		<td scope="col" class="A0010" height="32" align="right">档案上传: </td>
		<td class="A0001" scope="col"><input name="Attached" type="file" id="Attached" size="48" datatype="Filter" accept="pdf" msg="文件格式不对,请重选"  Row="9" Cel="1">限PDF格式  <?php  echo $Attached?> </td>
	</tr>
	<tr>
		<td height="81" align="right" valign="top" class="A0010" scope="col">备注：</td>
		<td valign="top" class="A0001" scope="col"><textarea name="Remark" cols="48" rows="6" id="Remark" DataType="Require" GandB="1" Msg="没有填写"><?php  echo $Remark?></textarea> </td>
	</tr>
</table>		
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>

<script>
function removeLists(){
	var el=document.getElementById("ListId");
	var length=el.options.length;
	for (var i=length-1;i>=0;i--){
		el.options[i].remove();
    }
    listChanged(el);
}
</script>