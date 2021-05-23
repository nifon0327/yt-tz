<?php   
//电信-zxq 2012-08-01
/*
$DataPublic.staffmain
$DataIn.yw4_quotationsheet
$DataIn.trade_object
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany Renewal Quotation Sheet");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.yw4_quotationsheet WHERE Id='$Id'",$link_id));
$Number=$upData["Number"];
$ProductCode=$upData["ProductCode"];
$CompanyId=$upData["CompanyId"];
$Currency=$upData["Currency"];
$Price=$upData["Price"];
$Rate=$upData["Rate"];
$Moq=$upData["Moq"];
$Priceterm=$upData["Priceterm"];
$Paymentterm=$upData["Paymentterm"];
$Leadtime=$upData["Leadtime"];
$Remark=$upData["Remark"];
$Image1=$upData["Image1"];
$Image2=$upData["Image2"];
$Image3=$upData["Image3"];
$Sales=$upData["Sales"];
$Date=$upData["Date"];
$ApprovedBy=$upData["ApprovedBy"];
$TempBY="ApprovedBySTR".strval($ApprovedBy);
$$TempBY="selected";
$Model=$upData["Model"];
$TempModel="ModelSTR".strval($Model);
$$TempModel="selected";

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤5：//需处理
?>
<table border="0" width="<?php    echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table border="0" width="800" cellpadding="0" cellspacing="2" Id="NoteTable">
    <tr>
    	<td width="200" height="21" valign="middle" align="right">Client：
      </td>
	    <td valign="middle">
			<select name="thisCompanyId" id="thisCompanyId" style="width: 450px;" dataType="Require"  msg="未选择客户">
			<?php   
			$checkSql = "SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE cSign=$Login_cSign AND Estate='1' ORDER BY OrderBy DESC,Id";
			$checkResult = mysql_query($checkSql); 
			if( $checkRow = mysql_fetch_array($checkResult)){
				do{
					$thisCompanyId=$checkRow["CompanyId"];
					$Forshort=$checkRow["Forshort"];					
					if($thisCompanyId==$CompanyId){
						echo "<option value='$thisCompanyId' selected>$Forshort</option>";
						}
					else{
						echo "<option value='$thisCompanyId'>$Forshort</option>";
						}
					}while( $checkRow = mysql_fetch_array($checkResult));
				}
			?>		 
		  </select></td>
    </tr>
    <tr>
    	<td height="19" valign="middle" align="right">Product Code ：</td>
	    <td valign="middle"><input name="ProductCode" type="text" id="ProductCode" value="<?php    echo $ProductCode?>" size="83" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">Unit Price ：</td>
      <td valign="middle"><input name="Price" type="text" id="Price" size="83" value="<?php    echo $Price?>" dataType="Require"  msg="未填写"></td>
    </tr>
    <tr>
      <td height="21" valign="middle" align="right">Currency：</td>
      <td valign="middle"><select name="Currency" id="Currency" style="width: 450px;" dataType="Require"  msg="未选择">
      <?php   
		include "subprogram/currency.php";
	  ?>
      </select></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">Exchange Rate： </td>
      <td valign="middle"><input name="Rate" type="text" id="Rate" size="83" value="<?php    echo $Rate?>" DataType="Currency" msg="格式不对或未填写"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">Price term： </td>
      <td valign="middle"><input name="Priceterm" type="text" id="Priceterm" value="<?php    echo $Priceterm?>" size="83" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">MOQ：</td>
      <td valign="middle"><input name="Moq" type="text" id="Moq" value="<?php    echo $Moq?>" size="83" DataType="Number" msg="格式不对或未填写"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">Leadtime：</td>
      <td valign="middle"><input name="Leadtime" type="text" id="Leadtime" value="<?php    echo $Leadtime?>" size="83" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内"></td>
    </tr>
    <tr>
      <td height="19" valign="middle"align="right">payment term： </td>
      <td valign="middle"><input name="Paymentterm" type="text" id="Paymentterm" value="<?php    echo $Paymentterm?>" size="83" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">Date：</td>
      <td valign="middle"><input name="Date" type="text" id="Date" value="<?php    echo $Date?>" size="83" onfocus="WdatePicker()" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
    </tr>
    <tr>
      <td height="21" valign="middle" align="right">Sales：</td>
      <td valign="middle"><select name="Sales" id="Sales" style="width: 450px;" dataType="Require"  msg="未选择">
		<?php   
		//员工资料表
		$PD_Sql = "SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1 AND JobId<5 ORDER BY Number DESC";
		$PD_Result = mysql_query($PD_Sql); 
		if($PD_Myrow = mysql_fetch_array($PD_Result)){
			do{
				$Number=$PD_Myrow["Number"];
				$Name=$PD_Myrow["Name"];					
				if($Number==$Sales){
					echo "<option value='$Number' selected>$Name</option>";
					}
				else{
					echo "<option value='$Number'>$Name</option>";
					}
				} while ($PD_Myrow = mysql_fetch_array($PD_Result));
			}
		?>
	  </select></td>
    </tr>
    <tr>
      <td height="9" align="right" valign="middle">Image1(Main)：</td>
      <td valign="middle"><input name="Image1" type="file" id="Image1" size="71" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="11" Cel="1"></td>
    </tr>
	<?php   
	$Image2Row=12;
	if($Image1==1){
   		echo"<tr><td height='10'>&nbsp;</td><td valign='middle'><input name='oldImage1' type='checkbox' id='oldImage1' value='1'><LABEL for='oldImage1'>删除Image1</LABEL></td></tr>";
		$Image2Row=12+1;
		}
	?>
    <tr>
      <td height="10" valign="middle" align="right">Images2：</td>
      <td valign="middle"><input name="Image2" type="file" id="Image2" size="71" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="<?php    echo $Image2Row?>" Cel="1"></td>
    </tr>
	<?php   
	$Image3Row=$Image2Row+1;
	if($Image2==1){
   		echo"<tr><td height='10'>&nbsp;</td><td valign='middle'><input name='oldImage2' type='checkbox' id='oldImage2' value='1'><LABEL for='oldImage2'>删除Image2</LABEL></td></tr>";
		$Image3Row=Image3Row+1;
		}
	?>
    <tr>
      <td height="11" valign="middle" align="right">Image3：</td>
    <td valign="middle"><input name="Image3" type="file" id="Image3" size="71" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="<?php    echo $Image3Row?>" Cel="1"></td>
	</tr>
	<?php   
	if($Image3==1){
   		echo"<tr><td height='10'>&nbsp;</td><td valign='middle'><input name='oldImage3' type='checkbox' id='oldImage3' value='1'><LABEL for='oldImage3'>删除Image3</LABEL></td></tr>";
		}
	?>
    <tr>
      <td height="11" valign="top" align="right">Remark：</td>
      <td valign="middle"><textarea name="Remark" cols="54" rows="4" id="Remark" dataType="Require"  msg="未填写"><?php    echo $Remark?></textarea></td>
    </tr>
    <tr>
      <td height="11" valign="top" align="right">Approved By</td>
      <td valign="middle"><select name="ApprovedBy" id="ApprovedBy" style="width: 450px;">
        <option value="0" <?php    echo $ApprovedBySTR0?>>默认</option>
        <option value="1" <?php    echo $ApprovedBySTR1?>>空白</option>
      </select></td>
    </tr>
    <tr>
      <td height="11" valign="top" align="right">PDF模板</td>
      <td valign="middle"><select name="Model" id="Model" style="width: 450px;">
        <option value="0" <?php    echo $ModelSTR0?>>英文</option>
        <option value="1" <?php    echo $ModelSTR1?>>中文</option>
      </select></td>
    </tr>
	</table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>