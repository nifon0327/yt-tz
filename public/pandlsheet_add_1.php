<?php 
//电信---yang 20120801
//代码、数据库共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增损益表子项目");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" align="center" cellspacing="5">
		<tr>
			<td align="right" valign="middle" scope="col">主项目类型：</td>
		<td scope="col"><select name="Mid" id="Mid" style="width: 380px;" dataType="Require"  msg="未选择">
                  <?php 
				$checkSql=mysql_query("SELECT Id,ItemName FROM $DataPublic.sys8_pandlmain WHERE 1 GROUP BY SortId",$link_id);
				if($checkRow=mysql_fetch_array($checkSql)){
					echo"<option value='' selected>请选择</option>";
					do{
						$Mid=$checkRow["Id"];
						$BigName=$checkRow["ItemName"];
						echo"<option value='$Mid'>$BigName</option>";
						}while($checkRow=mysql_fetch_array($checkSql));
					}
				?>
             </select>
			</td>
		</tr>
		
		<tr>
		  <td align="right" valign="middle" scope="col">项目名称：</td>
		  <td scope="col"><input name="ItemName" type="text" id="ItemName" style="width: 380px;"DataType="Require" Msg="没有填写"></td>
		</tr>		
		<tr>
			<td align="right" valign="middle" scope="col">排&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;序：</td>
			<td scope="col"><input name="SortId" type="text" id="SortId" style="width: 380px;" DataType="Number" Msg="没有填写或格式不对"></td>
		</tr>
		<tr>
		  <td align="right" valign="middle" scope="col">参&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数：</td>
		  <td scope="col"><input name="Parameters" type="text" id="Parameters" style="width: 380px;" DataType="Require" Msg="没有填写"></td>
		</tr>
		<tr>
			<td align="right" valign="middle" scope="col">明&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;细：</td>
			<td scope="col"><select name="AjaxView" id="AjaxView" style="width: 380px;" dataType="Require"  msg="未选择" >
		    <option value="" selected>--请选择--</option>
		    <option value="1">显示 </option>
		    <option value="0">不显示 </option>
          </select></td>
		</tr>
		<tr>
		  <td align="right" valign="middle" scope="col">Ajax&nbsp;&nbsp;No：</td>
		  <td scope="col"><input name="AjaxNo" type="text" id="AjaxNo" style="width: 380px;" datatype="Require" msg="没有填写" /></td>
	    </tr>
		<tr>
			<td align="right" valign="middle" scope="col">行政项目：</td>
			<td scope="col"><input name="Sign" type="checkbox" id="Sign" value="1" /></td>
		</tr>

<tr>
		  <td height="40" align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注：</td>
		  <td scope="col"><textarea name="Remark" style="width: 380px;" rows="3" id="Contant"></textarea></td>
		</tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>