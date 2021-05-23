<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新其它收入记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.cw4_otherin WHERE Id='$Id' ORDER BY Id",$link_id));
$BankId=$upData["BankId"];
$TypeId=$upData["TypeId"];
$Amount=$upData["Amount"];
$Currency=$upData["Currency"];
$Payee=$upData["Payee"];
$Remark=$upData["Remark"];
$Date=$upData["Date"];
$Bill=$upRow["Bill"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理

?>
 <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	 <table width="800" border="0" align="center" cellspacing="5">
		 <tr>
		   <td width="150" align="right" scope="col">收款日期</td>
		   <td scope="col"><input name="getDate" type="text" id="getDate" size="58" value="<?php  echo $Date?>" dataType="Date" format="ymd" msg="格式不对或未填写" onfocus="WdatePicker()" readonly></td>
		 </tr>
		<tr>
            <td align="right" scope="col">款项来源</td>
            <td scope="col"><select name="TypeId" id="TypeId" size="1" style="width: 420px;" dataType="Require"  msg="未选择">
              	<?php 
				$TypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.cw4_otherintype WHERE Estate=1 order by Id",$link_id);
				if($TypeRow = mysql_fetch_array($TypeResult)){
					do{
						$Id=$TypeRow["Id"];
						$Name=$TypeRow["Name"];
						if($Id==$TypeId){
							echo"<option value='$Id' selected>$Name</option>";
							}
						else{
							echo"<option value='$Id'>$Name</option>";
							}
						}while($TypeRow = mysql_fetch_array($TypeResult));
					}
				?>
          </select></td>
		</tr>
        <tr>
            <td align="right">货&nbsp;&nbsp;&nbsp;&nbsp;币</td>
            <td><select name="Currency" id="Currency" style="width: 420px;" dataType="Require"  msg="未选择">
              	<?php 
				$Currency_Result = mysql_query("SELECT Id,Name FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
				if($Currency_Row = mysql_fetch_array($Currency_Result)){
					do{
						$Id=$Currency_Row["Id"];
						$Name=$Currency_Row["Name"];
						if($Id==$Currency){
							echo"<option value='$Id' selected>$Name</option>";
							}
						else{
							echo"<option value='$Id'>$Name</option>";
							}
						}while ($Currency_Row = mysql_fetch_array($Currency_Result));
					}
				?>
              	</select></td>
        </tr>
          <tr>
            <td align="right">金&nbsp;&nbsp;&nbsp;&nbsp;额</td>
            <td><input name="Amount" type="text" id="Amount" size="58" value="<?php  echo $Amount?>" dataType="Currency" msg="未填写或格式不对"></td>
          </tr>
           <!--<tr>
				  <td height="32" align="right" scope="col">结付银行：</td>
				  <td scope="col">
				<?php 
                //include "../model/selectbank2.php";
				 include "../model/selectbank1.php";
				?>
                </td></tr>-->

          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="50" rows="5" id="Remark" dataType="Require"  msg="未填写"><?php  echo $Remark?></textarea></td>
          </tr>
          <tr>
            <td height="13" valign="top" scope="col"><div align="right">单 &nbsp;&nbsp;&nbsp;据</div></td>
            <td scope="col"><input name="Attached" type="file" id="Attached" size="65" datatype="Filter" accept="jpg" msg="文件格式不对,请重选" row="6" cel="1" /></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>