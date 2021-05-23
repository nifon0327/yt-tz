<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 安全生产软件投入更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.aqsc15 WHERE Id='$Id' ORDER BY Id LIMIT 1",$link_id));
$Attached=$upData["Attached"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,oldAttached,$Attached";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="176" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
 <tr>
      <td width="100" height="30" align="right" valign="top" class='A0010'>费用日期: </td>
    <td valign="top" class='A0001'><input name="UseDate" type="text" id="UseDate" value="<?php echo $upData["UseDate"];?>"  style="width:380px;" maxlength="60" dataType="Date" Msg="未填写" onfocus="WdatePicker()" readonly></td>
  </tr>
 <tr>
    <td height="30" align="right" valign="top" class='A0010'>费用名称:</td>
    <td valign="top" class='A0001'><input name="Caption" type="text" id="Caption" value="<?php echo $upData["Caption"];?>" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>费用金额: </td>
    <td valign="top" class='A0001'><input name="Amount" type="text" id="Amount" value="<?php echo $upData["Amount"];?>"  style="width:380px;" maxlength="60" dataType="Currency" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>经手人: </td>
    <td valign="top" class='A0001'><input name="Handler" type="text" id="Handler" value="<?php echo $upData["Handler"];?>"  style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>检验人: </td>
    <td valign="top" class='A0001'><input name="Checker" type="text" id="Checker" value="<?php echo $upData["Checker"];?>"  style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>赁证: </td>
    <td valign="top" class='A0001'><input name="Attached" type="file" id="Attached" size="60" dataType="Filter" Msg="非法的文件格式" Accept="pdf" Row="5" Cel="1"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>备注: </td>
    <td valign="top" class='A0001'><textarea name="Remark" id="Remark" style="width:380px;" datatype="Require" msg="未填写"><?php echo $upData["Remark"];?></textarea></td>
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