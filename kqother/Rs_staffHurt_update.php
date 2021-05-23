<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工工伤报销费用");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$MyRow = mysql_fetch_array(mysql_query("SELECT S.Id,S.Month,S.Amount,S.Estate,M.Name,S.Remark,S.HurtDate,S.AllAmout,S.SSecurityAmout,S.PSecurityAmout,S.Amount
FROM $DataIn.cw18_workhurtsheet S 
LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Number 
WHERE S.Id=$Id",$link_id));
$Id=$MyRow["Id"];
$Month=$MyRow["Month"];
$AllAmout=$MyRow["AllAmout"];
$SSecurityAmout=$MyRow["SSecurityAmout"];
$PSecurityAmout=$MyRow["PSecurityAmout"];
$Amount=$MyRow["Amount"];
$Name=$MyRow["Name"];
$Estate=$MyRow["Estate"];
$Remark=$MyRow["Remark"];
$HurtDate=$MyRow["HurtDate"];

if($Estate==0){	
	$SaveSTR="NO";
	}

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,chooseMonth,$chooseMonth,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="600" height="120" border="0" align="center" cellspacing="5">
         <td align="right">工伤日期</td>
         <td><input name="HurtDate" type="text" id="HurtDate" value="<?php echo $HurtDate?>" style="width:230px" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>
         </td>
         </tr>
		<tr>
			<td width="146" height="18" align="right" valign="middle" scope="col">员工姓名</td>
			<td width="435" valign="middle" scope="col"><?php  echo $Name?></td>
		</tr>
		<tr>
		  <td height="18" align="right" valign="middle" scope="col">总金额</td>
		  <td valign="middle" scope="col"><input name="AllAmout" type="text" id="AllAmout" value="<?php  echo $AllAmout?>" dataType="Currency" msg="金额格式不对">
	      </td>
	    </tr>
 
		<tr>
		  <td height="18" align="right" valign="middle" scope="col">社保金额(公）</td>
		  <td valign="middle" scope="col"><input name="SSecurityAmout" type="text" id="SSecurityAmout" value="<?php  echo $SSecurityAmout?>" dataType="Currency" msg="金额格式不对">
	      </td>
	    </tr>
  
  
		<tr>
		  <td height="18" align="right" valign="middle" scope="col">社保金额(私)</td>
		  <td valign="middle" scope="col"><input name="PSecurityAmout" type="text" id="PSecurityAmout" value="<?php  echo $PSecurityAmout?>" dataType="Currency" msg="金额格式不对">
	      </td>
	    </tr>       

		<tr>
		  <td height="18" align="right" valign="middle" scope="col">实报金额</td>
		  <td valign="middle" scope="col"><input name="Amount" type="text" id="Amount" value="<?php  echo $Amount?>" dataType="Currency" msg="金额格式不对">
	      </td>
	    </tr>
        
		<tr>
		  <td height="13" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Remark" cols="30" rows="4" id="Remark" dataType="Require" Msg="未填写说明"><?php echo $Remark ?></textarea></td>
		</tr>
        <tr>
		  <td align="right" >工伤凭证:</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg,pdf" Msg="文件格式不对,请重选" Row="6" Cel="1"></td>
	    </tr>   
		<?php 
		if($Estate!=1){
			echo"<tr><td height='18' colspan='2' align='center' scope='col' ><div class='redB'>(资料非未处理状态，需审核或结付退回方可更新)</div></td></tr>";
			}
		?>
         <tr>
		  <td align="right" >费用凭证:</td>
		  <td scope="col"><input name="HostpitalInvoice" type="file" id="HostpitalInvoice" size="52" DataType="Filter" Accept="jpg,pdf" Msg="文件格式不对,请重选" Row="7" Cel="1"></td>
	    </tr>   
		<?php 
		if($Estate!=1){
			echo"<tr><td height='18' colspan='2' align='center' scope='col' ><div class='redB'>(资料非未处理状态，需审核或结付退回方可更新)</div></td></tr>";
			}
		?>
        
         <tr>
		  <td align="right" >社保凭证:</td>
		  <td scope="col"><input name="SSecurityInvoice" type="file" id="SSecurityInvoice" size="52" DataType="Filter" Accept="jpg,pdf" Msg="文件格式不对,请重选" Row="8" Cel="1"></td>
	    </tr>   
		<?php 
		if($Estate!=1){
			echo"<tr><td height='18' colspan='2' align='center' scope='col' ><div class='redB'>(资料非未处理状态，需审核或结付退回方可更新)</div></td></tr>";
			}
		?>              
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>