<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 安全生产负责人培训记录更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.TrainDate,A.Address,A.TrainContent,A.Job,A.Attached,B.Name FROM $DataPublic.aqsc10 A LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number WHERE A.Id='$Id' ORDER BY A.Id LIMIT 1",$link_id));
$Attached=$upData["Attached"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,oldAttached,$Attached";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="176" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
 <tr>
      <td width="100" height="30" align="right" valign="top" class='A0010'>培训日期: </td>
    <td valign="top" class='A0001'><input name="TrainDate" type="text" id="TrainDate" value="<?php echo $upData["TrainDate"];?>"  style="width:380px;" maxlength="60" dataType="Date" Msg="未填写" onfocus="WdatePicker()" readonly></td>
  </tr>
 <tr>
    <td height="30" align="right" valign="top" class='A0010'>培训地点</td>
	  <td valign="top" class='A0001'><input name="Address" type="text" id="Address" value="<?php echo $upData["Address"];?>" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>培训内容 </td>
    <td valign="top" class='A0001'><input name="TrainContent" type="text" id="TrainContent" value="<?php echo $upData["TrainContent"];?>" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>受训人员</td>
    <td valign="top" class='A0001'><input name="Name" type="text" id="Name" value="<?php echo $upData["Name"];?>" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="30" align="right" valign="top" class='A0010'>受训人职位 </td>
    <td valign="top" class='A0001'><input name="Job" type="text" id="Job" style="width:380px;" value="<?php echo $upData["Job"];?>" datatype="Require" msg="未填写" /></td>
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