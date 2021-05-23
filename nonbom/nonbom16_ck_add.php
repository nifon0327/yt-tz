<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增非BOM仓储位置");//需处理
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
	<table width="750" height="95" border="0" align="center" cellspacing="5">
		<tr>
			<td width="137" height="40" valign="middle" scope="col" align="right">位&nbsp;&nbsp;&nbsp;&nbsp;置</td>
			<td valign="middle" scope="col"><input name="Name" type="text" id="Name" style="width:380px;" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出2-30个字节的范围" title="必填项,2-30个字节的范围">
			</td>
		</tr>

		<tr>
			<td width="137" height="40" valign="middle" scope="col" align="right">分&nbsp;&nbsp;&nbsp;类</td>
			<td valign="middle" scope="col"><select name="TypeId"  id="TypeId" style="width:380px;" dataType="Require"  msg="未选择">
              <option value="" selected>--请选择--</option>
              <option value="0" >共享</option>
              <option value="1" >仓库</option>
              <option value="2" >使用地点</option>
            </select>
			</td>
		</tr>


		<tr>
			<td width="137" height="40" valign="middle" scope="col" align="right">负&nbsp;责&nbsp;人</td>
			<td valign="middle" scope="col"><select name="Number"  id="Number" style="width:380px;" dataType="Require"  msg="未选择">
              <option value="" selected>--请选择--</option>
              <?php 
			 $Result1 = mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE Estate=1 AND (BranchId=7) order by Id",$link_id);
			 if($myRow1 = mysql_fetch_array($Result1)){
				do{
					echo" <option value='$myRow1[Number]'>$myRow1[Name]</option>";
					}while($myRow1 = mysql_fetch_array($Result1));
				}
			 ?>
            </select>
			</td>
		</tr>
		<tr>
		  <td height="40" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td valign="middle" scope="col"><textarea name="Remark" style="width:380px;" rows="6" id="Remark"></textarea></td>
	    </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>