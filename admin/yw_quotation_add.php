<?php   
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增报价记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
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
				echo"<option value='' selected>-Select-</option>";
				do{
					$CompanyId=$checkRow["CompanyId"];
					$Forshort=$checkRow["Forshort"];					
					echo "<option value='$CompanyId'>$Forshort</option>";
					}while( $checkRow = mysql_fetch_array($checkResult));
				}
			?>		 
		  </select></td>
    </tr>
    <tr>
    	<td height="19" valign="middle" align="right">Product Code ：</td>
	    <td valign="middle"><input name="ProductCode" type="text" id="ProductCode" size="83" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">Unit Price ：</td>
      <td valign="middle"><input name="Price" type="text" id="Price" size="83" dataType="Require"  msg="未填写"></td>
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
      <td valign="middle"><input name="Rate" type="text" id="Rate" size="83" DataType="Currency" msg="格式不对或未填写"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">Price term： </td>
      <td valign="middle"><input name="Priceterm" type="text" id="Priceterm" size="83" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">MOQ：</td>
      <td valign="middle"><input name="Moq" type="text" id="Moq" size="83" DataType="Number" msg="格式不对或未填写"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">Leadtime：</td>
      <td valign="middle"><input name="Leadtime" type="text" id="Leadtime" size="83" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内"></td>
    </tr>
    <tr>
      <td height="19" valign="middle"align="right">payment term： </td>
      <td valign="middle"><input name="Paymentterm" type="text" id="Paymentterm" size="83" dataType="LimitB" min="3" max="60"  msg="2-60个字节之内" title="必填项,2-60个字节内"></td>
    </tr>
    <tr>
      <td height="19" valign="middle" align="right">Date：</td>
      <td valign="middle"><input name="Date" type="text" id="Date" size="83" onfocus="WdatePicker()" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
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
				if($Number==$Login_P_Number){
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
      <td height="21" valign="middle" align="right">Image1(Main)：</td>
      <td valign="middle"><input name="Image1" type="file" id="Image1" size="71" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="11" Cel="1"></td>
    </tr>
    <tr>
      <td height="10" valign="middle" align="right">Images2：</td>
      <td valign="middle"><input name="Image2" type="file" id="Image2" size="71" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="12" Cel="1"></td>
    </tr>
    <tr>
      <td height="11" valign="middle" align="right">Image3：</td>
    <td valign="middle"><input name="Image3" type="file" id="Image3" size="71" DataType="Filter" Accept="jpg" Msg="文件格式不对" Row="13" Cel="1"></td>
	</tr>
    <tr>
      <td height="11" valign="top" align="right">Remark：</td>
      <td valign="middle"><textarea name="Remark" cols="54" rows="4" id="Remark" dataType="Require"  msg="未填写"></textarea></td>
    </tr>
    <tr>
      <td height="11" align="right">Approved By</td>
      <td valign="middle"><select name="ApprovedBy" id="ApprovedBy" style="width: 450px;">
        <option value="0" selected>默认</option>
        <option value="1">空白</option>
                  </select></td>
    </tr>
    <tr>
      <td height="11" align="right">PDF模板</td>
      <td valign="middle"><select name="Model" id="Model" style="width: 450px;">
        <option value="0" selected>英文</option>
        <option value="1">中文</option>
                  </select></td>
    </tr>
	</table>
</td></tr></table>
<?php   
//步骤5：
include "../model/subprogram/add_model_b.php";
?>