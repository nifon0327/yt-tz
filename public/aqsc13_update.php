<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 安全生产投入计划更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Caption,Attached FROM $DataPublic.aqsc13 WHERE Id='$Id' ORDER BY Id LIMIT 1",$link_id));
$Caption=$upData["Caption"];
$Attached=$upData["Attached"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,oldAttached,$Attached";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="176" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="100" height="43" align="right" class='A0010'>计划名称:</td>
	  <td class='A0001'><input name="Caption" type="text" id="Caption" value="<?php echo $Caption;?>" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="43" align="right" class='A0010'>计划文档: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="60" dataType="Filter" Msg="非法的文件格式" Accept="pdf" Row="1" Cel="1"></td>
    </tr>
    <tr>
      <td height="52" align="right" class='A0010'>&nbsp;</td>
      <td class='A0001'>要求附件为pdf格式,多图片转PDF文件:先设好图片文件名(按顺序),然后同时选取，点鼠标右键，选&quot;在AdobeAcrobat 中合并&quot;</td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>