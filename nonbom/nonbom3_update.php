<?php 
//非BOM配件主分类  EWEN 2013-02-17 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新非BOM配件供应商");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.nonbom3_retailermain WHERE Id='$Id'",$link_id));
$Company=$upData["Company"];
$Linkman=$upData["Linkman"];
$Tel=$upData["Tel"];
$Currency=$upData["Currency"];
$Remark=$upData["Remark"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
		<table width="760" border="0" align="center" cellspacing="5">
        	<tr>
            	<td width="150" height="40" align="right">供应商名称</td>
                <td><input name="Company" type="text" id="Company" style="width:380px;" value="<?php echo $Company?>" dataType="Limit" max="50" min="2" msg="必须在2-50个字之内"></td>
        </tr>
            <tr>
              <td height="40" align="right">联系人</td>
              <td><input name="Linkman" type="text" id="Linkman" style="width:380px;" value="<?php echo $Linkman?>" dataType="Limit" max="10" min="2" msg="必须在2-10个字之内"></td>
          </tr>
            <tr>
              <td height="40" align="right">联系电话</td>
              <td><input name="Tel" type="text" id="Tel" style="width:380px;" value="<?php echo $Tel?>" dataType="Limit" max="20" min="8" msg="必须在8-20个字之内"></td>
            </tr>
            <tr>
                <td height="40" align="right">结付货币</td>
                <td>
                	<?php 
               		include "../model/subselect/Currency.php";
                	?>
                </td>
            </tr>
            <tr>
              <td height="40" align="right" valign="top">备注</td>
              <td><textarea name="Remark" rows="5" id="Remark" style="width: 380px;" msg="未填写或格式不对"><?php echo $Remark?></textarea></td>
          </tr>
      	</table>
        </td></tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>