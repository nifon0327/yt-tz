<?php 
//电信-zxq 2012-08-01
//$DataIn.cwygjz/$DataPublic.staffmain 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工借支记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Z.BankId,Z.Mid,Z.PayDate,Z.Number,Z.Amount,Z.Payee,Z.Remark,M.Name 
FROM $DataIn.cwygjz Z,$DataPublic.staffmain M 
WHERE Z.Number=M.Number and Z.Id=$Id LIMIT 1",$link_id));
$BankId=$upData["BankId"];
$Mid=$upData["Mid"];
$Name=$upData["Name"];
$Number=$upData["Number"];
$PayDate=$upData["PayDate"];
$Amount=$upData["Amount"];
$Remark=$upData["Remark"];
$Payee=$upData["Payee"];
//检查是否已在工资表，是则锁定金额的更新

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth,Mid,$Mid";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" border="0" cellspacing="0" id="NoteTable">
		<tr>
          <td width="150" height="27" align="right" scope="col">借支员工：</td>
          <td scope="col"><?php  echo $Name?></td>
		</tr>
				<tr>
				  <td height="32" align="right" scope="col">借支金额：</td>
				  <td scope="col">
				<?php 
				if($Mid==0){
					echo"<input name='Amount' type='text' id='Amount' size='40' value='$Amount' dataType='Currency' Msg='未填写或格式不对'>";
					}
				else{
					echo $Amount;
					}
				?>
				</td>             
				</tr>
         <tr>
				  <td height="32" align="right" scope="col">结付银行：</td>
				  <td scope="col">
				<?php 
                include "../model/selectbank2.php";
				?>
                </td></tr>  
          <tr>
            <td height="27" align="right">借支日期：</td>
            <td >
			<?php 
			if($Mid==0){
				echo"<input name='PayDate' type='text' id='PayDate' value='$PayDate' size='40' maxlength='10' dataType='Date' format='ymd' Msg='未填写或格式不对' onFocus='WdatePicker()'>";
            	}
			else{
				echo $PayDate;
				}
				?>
			</td>
          </tr>
          <tr>
            <td height="37" align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注：</td>
            <td><textarea name="Remark" cols="50" rows="5" id="Remark" dataType="Require" Msg="未填写备注"><?php  echo $Remark?></textarea></td>
          </tr>
          <tr>
            <td height="27" align="right">借&nbsp;&nbsp;&nbsp;&nbsp;据：</td>
            <td><input name="Attached" type="file" id="Attached" size="66" dataType="Filter" msg="非jpg格式" accept="jpg" Row="1" Cel="1"></td>
          </tr>
		<?php  
		if($Payee==1){ 
        	echo"<tr><td height='27'>&nbsp;</td><td><input name='oldAttached' type='checkbox' id='oldAttached' value='1'><LABEL for='oldAttached'>删除已传借据</LABEL></td></tr>";
		  }
        ?>
		</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>