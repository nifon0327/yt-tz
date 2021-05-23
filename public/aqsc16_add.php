<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增应急预案");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table width="<?php  echo $tableWidth?>"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
	<tr>
		<td width="100" height="30" align="right" valign="top" class='A0010'>预案分类</td>
        <td valign="top" class='A0001'>
    	<?
        include "../model/subselect/aqsc16type.php";
		?>
        </td>
	</tr>
  <tr>
    <td height="30" align="right" valign="top" class='A0010'>预案名称</td>
	  <td valign="top" class='A0001'><input name="Caption" type="text" id="Caption" value="" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>审核人</td>
    <td valign="top" class='A0001'><input name="Checker" type="text" id="Checker" value="" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>预案附件</td>
    <td valign="top" class='A0001'><input name="Attached" type="file" id="Attached" size="60" dataType="Filter" Msg="非法的文件格式" Accept="pdf" Row="3" Cel="1"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>&nbsp;</td>
      <td valign="top" class='A0001'>要求附件为pdf格式,多图片转PDF文件:先设好图片文件名(按顺序),然后同时选取，点鼠标右键，选&quot;在AdobeAcrobat 中合并&quot;</td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>