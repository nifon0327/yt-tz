<?php 
//2013-10-14 ewen
include "../model/modelhead.php";
include "../model/livesearch/modellivesearch.php";//用于在文本框输入文字显示与之相关的内容
ChangeWtitle("$SubCompany 安全管理制度文档");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT A.Caption,A.Attached,B.Name FROM $DataPublic.aqsc02 A LEFT JOIN $DataPublic.aqsc01 B ON B.Id=A.TypeId WHERE A.Id='$Id' ORDER BY A.Id LIMIT 1",$link_id));
$Name=$upData["Name"];
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
    <td width="100" height="43" align="right" class='A0010'>文档分类:</td>
    <td class='A0001'><input name="doucmentName" type="text" id="doucmentName" title="必填项,2-100个字节的范围"  style="width: 380px;" maxlength="30" datatype="LimitB" max="100" min="2" msg="没有填写或超出字节的范围" onkeyup="showResult(this.value,'Name','aqsc01','1','Estate=1')" onblur="LoseFocus()" onchange="clearData()" autocomplete="off" value="<?php echo $Name;?>"></td>
  </tr>
    <tr>
		<td height="38" align="right" class='A0010'>文档标题:</td>
	  <td class='A0001'><input name="Caption" type="text" id="Caption" value="<?php echo $Caption;?>" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td height="43" align="right" class='A0010'>文档附件: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="60" dataType="Filter" Msg="非法的文件格式" Accept="mp4,pdf,ppt" Row="2" Cel="1"></td>
    </tr>
    <tr>
      <td height="52" align="right" class='A0010'>&nbsp;</td>
      <td class='A0001'>要求附件为mp4,pdf或ppt格式,多图片转PDF文件:先设好图片文件名(按顺序),然后同时选取，点鼠标右键，选&quot;在AdobeAcrobat 中合并&quot;</td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>