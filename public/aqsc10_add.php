<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增安全生产负责人培训记录");//需处理
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
      <td width="100" height="30" align="right" valign="top" class='A0010'>培训日期 </td>
    <td valign="top" class='A0001'><input name="TrainDate" type="text" id="TrainDate" value="" style="width:380px;" maxlength="60" dataType="Date" Msg="未填写" onfocus="WdatePicker()" readonly></td>
  </tr>
  <tr>
    <td height="30" align="right" valign="top" class='A0010'>培训地点</td>
	  <td valign="top" class='A0001'><input name="Address" type="text" id="Address" value="" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>培训内容 </td>
    <td valign="top" class='A0001'><input name="TrainContent" type="text" id="TrainContent" value="" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>受训人员</td>
    <td valign="top" class='A0001'><input name="Name" type="text" id="Name" value="" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>受训人职位 </td>
    <td valign="top" class='A0001'><input name="Job" type="text" id="Job" style="width:380px;" value="" datatype="Require" msg="未填写" /></td>
  </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>附件: </td>
    <td valign="top" class='A0001'><input name="Attached" type="file" id="Attached" size="60" dataType="Filter" Msg="非法的文件格式" Accept="pdf" Row="5" Cel="1"></td>
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