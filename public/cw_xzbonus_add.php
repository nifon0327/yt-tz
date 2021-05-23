<?php 
include "../model/modelhead.php";
//步骤2：//需处理
ChangeWtitle("$SubCompany 新增其它奖金费用");
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理;
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
		  <td height="29" align="right" scope="col" width="150px">请款月份</td>
		  <td scope="col"><input name="theMonth" type="text" id="theMonth"  value="<?php  echo date("Y-m");?>" size="49" maxlength="10"></td>
	    </tr>
		<tr>
		  	<td height="24" align="right" scope="col">货&nbsp;&nbsp;&nbsp;&nbsp;币</td>
		  	<td scope="col">
			<select name="Currency" id="Currency" style="width:380px" dataType="Require"  msg="未选择货币">
			<option value="" selected>请选择</option>
		  	<?php 
		   	$cResult = mysql_query("SELECT Name,Id FROM $DataPublic.currencydata WHERE Estate=1 order by Id",$link_id);
		    if($cRow = mysql_fetch_array($cResult)){
				do{
					echo"<option value='$cRow[Id]'>$cRow[Name]</option>";
					}while ($cRow = mysql_fetch_array($cResult));
				}
          	?>
		  	</select></td>
	    </tr>


		<tr>
		  <td height="30" align="right" scope="col">指定人员</td>
		  <td scope="col">	<select name="ListId[]" size="8" id="ListId" multiple style="width: 380px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,4)" dataType="PreTerm" Msg="没有指定员工" readonly>
        </select></td>
	    </tr>

		<tr>
		  <td height="30" align="right" scope="col">金&nbsp;&nbsp;&nbsp;&nbsp;额</td>
		  <td scope="col"><input name="Amount" type="text" id="Amount" size="49" dataType="Double" Msg="未填写或格式不对"></td>
	    </tr>

		<tr>
		  <td height="13" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Content" cols="51" rows="3" id="Content" dataType="Require" Msg="未填写说明"></textarea></td>
		</tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">单 &nbsp;&nbsp;&nbsp;据</td>
		  <td scope="col"><input name="Attached" type="file" id="Attached" size="52" DataType="Filter" Accept="jpg" Msg="文件格式不对,请重选" Row="5" Cel="1"></td>
	    </tr>

        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>
