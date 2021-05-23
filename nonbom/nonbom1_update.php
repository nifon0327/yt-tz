<?php 
//非BOM配件主分类  EWEN 2013-02-17 OK
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新非BOM配件主分类");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT Name,Remark FROM $DataPublic.nonbom1_maintype WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
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
          		<td width="150" height="40" align="right" scope="col">分类名称</td>
          		<td scope="col"><input name="Name" type="text" id="Name" title="可输入2-30个字节(每1中文字占2个字节，每1英文字母占1个字节)" value="<?php  echo $Name?>" style="width: 380px;" maxlength="30" DataType="LimitB"  Max="30" Min="2" Msg="没有填写或字符不在2-16个字节内"></td>
        	</tr>
            <tr>
          		<td height="40" align="right" valign="top" scope="col">分类说明</td>
          		<td scope="col"><textarea name="Remark" rows="3" id="Remark" style="width: 380px;" msg="未填写或格式不对"><?php  echo $Remark?></textarea></td>
        	</tr>
      	</table>
        </td></tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>