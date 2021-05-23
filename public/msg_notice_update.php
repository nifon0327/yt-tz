<?php 
//步骤1 $DataPublic.msg3_notice 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新加班通知");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$upSql=mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.msg3_notice WHERE Id='$Id'",$link_id));
$Date=$upSql["Date"];
$Content=$upSql["Content"];
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php  echo $tableWidth?>" height="154" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="120" height="34" align="right" class='A0010'>日&nbsp;&nbsp;&nbsp;&nbsp;期: </td>
    <td class='A0001'><input name="Date" type="text" id="Date" onfocus="WdatePicker()" value="<?php  echo $Date?>" size="105" maxlength="10" readonly  dataType="Date" format="ymd" msg="格式不对或未选择"></td>
  </tr>
    <tr>
		<td align="right" valign="top" class='A0010'>通知内容:</td>
	  <td class='A0001'><textarea name="Content" cols="67" rows="8" id="Content" datatype="Require" msg="未填写"><?php  echo $Content?></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>