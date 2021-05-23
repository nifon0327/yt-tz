<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 安全生产培训教程更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.aqsc04 WHERE Id='$Id' ORDER BY Id LIMIT 1",$link_id));
$TypeId=$upData["TypeId"];
$Caption=$upData["Caption"];
$Attached=$upData["Attached"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,oldAttached,$Attached";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5"  id="NoteTable">
		<tr>
            <td width="100"  align="right" scope="col">教程类型</td>
            <td scope="col">
            <?
            include "../model/subselect/aqsc04type.php";
			?>
            </td>
          </tr>
          <tr>         
            <td align="right" valign="top">教程主题</td>
            <td><textarea name="Caption" style="width:380px" rows="2" id="Caption"><?php echo $Caption;?></textarea></td>
          </tr>
          <tr>
            <td align="right" valign="top">教程附件</td>
            <td><input name="Attached" type="file" id="Attached" style="width:380px" dataType="Filter" Msg="非法的文件格式" Accept="mp4,ppt,pdf" Row="2" Cel="1"></td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>