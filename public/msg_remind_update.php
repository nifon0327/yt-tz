<?php 
//步骤1 $DataPublic.msg2_overtime 二合一已更新电信---yang 20120801
//代码共享-EWEN 2012-09-05
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新特别提醒");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：
$upSql=mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.msg4_remind WHERE Id='$Id'",$link_id));
$cSign=$upSql["cSign"];
$Content=$upSql["Content"];
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php  echo $tableWidth?>" height="154" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
       <td align="right" class='A0010'>标识：</td>
	  <td class='A0001'><?php 
	  $SharingShow="Y";
      include "../model/subselect/cSign.php";
	  ?></td>
    </tr>
    <tr>
		<td align="right" valign="top" class='A0010'>提醒内容:</td>
	  <td class='A0001'><textarea name="Content" style="width:380px;" rows="8" id="Content" datatype="Require" msg="未填写"><?php  echo $Content?></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>