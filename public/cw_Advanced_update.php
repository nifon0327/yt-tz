<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：//需处理
ChangeWtitle("$SubCompany 更新预结付取款记录");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("
						SELECT A.Id,A.BankId,A.Amount,A.Remark,A.Date,A.Estate,A.Locks,A.Operator,A.Currency,D.Name AS Teller
 	FROM $DataIn.cw_advanced A 
	LEFT JOIN $DataPublic.my2_bankinfo B ON B.Id=A.BankId
	LEFT JOIN $DataPublic.staffmain D ON D.Number=A.Teller
	Where A.Id=$Id",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	$Teller=$upRow["Teller"];
	$Date=$upRow["Date"];
	$Amount=$upRow["Amount"];
	$Currency=$upRow["Currency"];
	$Remark=$upRow["Remark"];
	$BankId=$upRow["BankId"];
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
	?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
            <td width="100" height="25" align="right" scope="col">取款银行</td>
            <td scope="col"><?php 
                include "../model/selectbank2.php";
				?></td></tr>
          <tr>
            <td width="100" height="25" align="right" scope="col">取款人</td>
            <td scope="col"><?php echo $Teller;?></td></tr>      
                
		<tr>
		  <td height="29" align="right" scope="col">取款日期</td>
		  <td scope="col"><input name="theDate" type="text" id="theDate" onfocus="WdatePicker()" value="<?php  echo $Date;;?>" style="width:380px" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">取款金额</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" style="width:380px" value="<?php echo $Amount;?>" dataType="Double" Msg="未填写或格式不对"></td>
	    </tr>
		<tr>
		  	<td height="24" align="right" scope="col">货币</td>
		  	<td scope="col">
			<select name="Currency" id="Currency" style="width:380px" dataType="Require"  msg="未选择货币">
			<option value="" selected>请选择</option>
		  	<?php 
		   	$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
				do{
					if($cRow["Id"]==$Currency){
						echo"<option value='$cRow[Id]' selected>$cRow[Name]</option>";
						}
					else{
						echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
						}
					}while ($cRow = mysql_fetch_array($cResult));
				}
          	?>
		  	</select></td>
	    </tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">取款备注</td>
		  <td scope="col"><textarea name="Remark" style="width:380px" rows="3" id="Remark" dataType="Require" Msg="未填写说明"><?php echo $Remark;?></textarea></td>
		</tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
