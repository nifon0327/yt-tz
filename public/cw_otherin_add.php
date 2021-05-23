<?php 
//电信-zxq 2012-08-01
//$DataPublic.cw4_otherintype/$DataPublic.currencydata 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 其它收入");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$Date=date("Y-m-d");
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
 <table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	 <table width="800" border="0" align="center" cellspacing="5">
		<tr>
		   <td width="150" align="right" scope="col">收款日期</td>
		   <td scope="col"><input name="getDate" type="text" id="getDate" value="<?php echo $Date?>" size="58" dataType="Date" format="ymd" msg="格式不对或未填写" onfocus="WdatePicker()" readonly></td>
		 </tr>
		<tr>
            <td width="150" align="right" scope="col">款项来源</td>
            <td scope="col"><select name="TypeId" id="TypeId" size="1" style="width: 420px;" dataType="Require"  msg="未选择">
                <option value="" selected>请选择</option> 
              	<?php 
				$TypeResult = mysql_query("SELECT Id,Name FROM $DataPublic.cw4_otherintype WHERE Estate=1 order by Id",$link_id);
				if($TypeRow = mysql_fetch_array($TypeResult)){
					do{
						$Id=$TypeRow["Id"];
						$Name=$TypeRow["Name"];
						echo"<option value='$Id'>$Name</option>";
						}while($TypeRow = mysql_fetch_array($TypeResult));
					}
				?>
          </select></td>
		</tr>
        <tr>
            <td align="right">货&nbsp;&nbsp;&nbsp;&nbsp;币</td>
            <td><select name="Currency" id="Currency" style="width: 420px;" dataType="Require"  msg="未选择">
             	<option value="" selected>请选择</option>
              	<?php 
				$Currency_Result = mysql_query("SELECT Id,Name FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
				if($Currency_Row = mysql_fetch_array($Currency_Result)){
					do{
						$Id=$Currency_Row["Id"];
						$Name=$Currency_Row["Name"];
						echo"<option value='$Id'>$Name</option>";
						}while ($Currency_Row = mysql_fetch_array($Currency_Result));
					}
				?>
              	</select></td>
        </tr>
          <tr>
            <td align="right">金&nbsp;&nbsp;&nbsp;&nbsp;额</td>
            <td><input name="Amount" type="text" id="Amount" size="58" dataType="Currency" msg="未填写或格式不对"></td>
          </tr>
                    <!--     <tr>
				  <td height="32" align="right" scope="col">结付银行：</td>
				  <td scope="col">
				<?php 
                include "../model/selectbank1.php";
				?>
                </td></tr>-->
          <tr>
            <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" cols="50" rows="5" id="Remark" dataType="Require"  msg="未填写"></textarea></td>
          </tr>
          <tr>
            <td height="13" align="right" valign="top" scope="col">凭&nbsp;&nbsp;&nbsp;证</td>
            <td scope="col"><input name="Attached" type="file" id="Attached" size="52" datatype="Filter" accept="jpg" msg="文件格式不对,请重选" row="5" cel="1" /></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>