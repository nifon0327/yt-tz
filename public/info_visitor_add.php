<?
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增来访登记记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?=$tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" height="95" border="0" align="center" cellspacing="5">
	    <tr>
	      <td width="113" align="right">来访日期：</td>
            <td><input name="ComeDate" type="text" id="ComeDate"  style="width:380px"  maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="未填写或来访日期不正确" readonly>
          </td>
        </tr>
       	<tr>
			<td height="40" align="right" valign="middle" scope="col">来访分类：</td>
			<td scope="col">
              <select name="TypeId" id="TypeId" style="width:380px" dataType="Require" msg="未选择">
			  	<option value="" selected>请选择</option>
                <?php 
					 $type_Result = mysql_query("SELECT C.Id,C.Name AS TypeName FROM $DataPublic.come_type C WHERE C.Estate=1",$link_id);
						if($typeRow = mysql_fetch_array($type_Result)) {
							do{			
								$TypeId=$typeRow["Id"];
								$TypeName=$typeRow["TypeName"];
								echo"<option value='$TypeId'>$TypeName</option>";				
								}while($typeRow = mysql_fetch_array($type_Result));
							}
					  ?>
              </select>
			</td>
		</tr> 
		<tr>
		  <td height="40" align="right" valign="middle" scope="col">来访单位：</td>
		  <td scope="col"><input name="Name" type="text" id="Name"  style="width:380px"  DataType="Require" Msg="没有填写"></td>
		</tr>
		<tr>
		  <td height="40" align="right" valign="middle" scope="col">来访人数：</td>
		  <td scope="col"><input name="Person" type="text" id="Person"  style="width:380px"  DataType="Number" Msg="没有填写"></td>
		</tr>	
		<tr>
		  <td height="40" align="right" valign="middle" scope="col">来访说明：</td>
		  <td scope="col"><textarea name="Remark" cols="50" rows="6" id="Remark"></textarea></td>
		</tr>
	</table>
</td></tr></table>
<?
//步骤5：
include "../model/subprogram/add_model_b.php";
?>