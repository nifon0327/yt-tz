<?php 
//步骤1 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 发送短消息");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" height="74" border="0" cellspacing="5">
	  <tr>
		<td width="119" height="18" align="right" valign="top" scope="col">接收消息的员工</td>
		<td valign="middle" scope="col"><textarea name="User0" cols="60" rows="2" id="User0" onclick="SearchRecord('user','<?php  echo $funFrom?>',2,3)" dataType="Require" Msg="未选择员工"></textarea></td>
	</tr>	
	<tr>
	  <td height="18" align="right" valign="top" scope="col">抄送</td>
	  <td valign="middle" scope="col"><textarea name="User1" cols="60" rows="2" id="User1" onclick="SearchRecord('user','<?php  echo $funFrom?>',2,3)"></textarea></td>
  </tr>
	<tr>
	  <td height="18" align="right" valign="top" scope="col">短消息内容</td>
	  <td valign="middle" scope="col"><textarea name="Note" cols="60" rows="6" id="Note" dataType="Require" Msg="未填写内容"></textarea></td>
	  </tr>
</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>