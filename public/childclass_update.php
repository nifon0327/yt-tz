<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新年级资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.childclass WHERE Id='$Id' LIMIT 1",$link_id));
$Name=$upData["Name"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="750" height="140" border="0" align="center" cellspacing="5">
        <tr>
          <td height="40" valign="middle" scope="col" align="right">年&nbsp;&nbsp;&nbsp;&nbsp;级</td>
          <td valign="middle" scope="col"><input name="Name" type="text" id="Name" value="<?php  echo $Name?>" style="width:380px" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出2-30个字节的范围" title="必填项,2-30个字节的范围">
          </td>
              </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>