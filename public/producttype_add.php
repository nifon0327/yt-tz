<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增产品分类");//需处理
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
	<table width="750" height="185" border="0" align="center" cellspacing="5">
		<tr>
			<td width="137" height="40" valign="middle" scope="col" align="right">新增的产品分类名称</td>
			<td valign="middle" scope="col"><input name="TypeName" type="text" id="TypeName" title="必填项,2-30个字节的范围" style="width: 380px;" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出2-30个字节的范围">
			</td>
		</tr>
		<tr>
		  <td height="40" align="right" valign="middle" scope="col">主分类</td>
		  <td valign="middle" scope="col"><select name="mainType" id="mainType" style="width: 380px;" dataType="Require"  msg="未选择主分类">
            <option value="" selected>请选择</option>
            <?php 
			$result = mysql_query("SELECT Id,Name FROM $DataIn.productmaintype order by Id",$link_id);
			if($myrow = mysql_fetch_array($result)){
				do{
					$Id=$myrow["Id"];
					$Name=$myrow["Name"];
					echo "<option value='$Id'>$Name</option>";
					}while ($myrow = mysql_fetch_array($result));
				} 
			?>
             </select></td>
	    </tr>
		<tr>
		  <td height="40" align="right" valign="middle" scope="col">生产商</td>
		  <td valign="middle" scope="col"><select name="scType" id="scType" style="width: 380px;" dataType="Require" msg="未选择生产分类">
		  <option value="" selected="selected">请选择</option>
		  <option value="1">景创</option>
		  <option value="2">鼠宝</option>
          <option value="3">皮套</option>
		  </select>
		  </td>
		</tr>

		 <tr>
            <td align="right" valign="top">命名规则</td>
            <td><textarea name="NameRule" style="width:380px" rows="4" id="NameRule"></textarea></td>
        </tr>
		<tr>
		  <td height="40" align="right" valign="middle" scope="col">排序号码</td>
		  <td valign="middle" scope="col"><input name="SortId" type="text" id="SortId" title="必填项" style="width: 380px;"  datatype="Number" msg="没有填写或格式不对" /></td>
	    </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>