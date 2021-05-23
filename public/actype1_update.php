<?php 
//电信-EWEN
//更新到public by zx 2012-08-03
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新资产费用分类");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Name,Remark FROM $DataPublic.actype WHERE Id='$Id'",$link_id));
$TypeId=$upData["TypeId"];
$Name=$upData["Name"];
$Amount=$upData["Amount"];
$Remark=$upData["Remark"];
$AuditType=$upData["AuditType"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../Admin/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">

		  	<td height="13" scope="col" align="right">分类名称</td>
		 	<td width="460" scope="col"><input name="Name" type="text" id="Name" value="<?php  echo $Name?>" style="width:380px;" maxlength="20" title="必选项,在20个汉字内." dataType="LimitB" min="1" max="40" msg="没有填写或超出许可范围"></td>
		</tr> 
            <td valign="top" align="right">备 &nbsp;&nbsp;&nbsp;注</td>
            <td><textarea name="Remark" style="width:380px;" rows="3" id="Remark"><?php  echo $Remark?></textarea></td>
        </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../Admin/subprogram/add_model_b.php";
?>