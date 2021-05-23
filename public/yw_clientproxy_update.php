<?php 
//电信-EWEN
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 客户授权书");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT CompanyId,Caption,TimeLimit,Attached FROM $DataIn.yw7_clientproxy WHERE Id=$Id ORDER BY Id LIMIT 1",$link_id));
$CompanyId=$upData["CompanyId"];
$TimeLimit=$upData["TimeLimit"];
$Caption=$upData["Caption"];
$Attached=$upData["Attached"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,oldAttached,$Attached";
//步骤5：//需处理
?>
<table width="<?php  echo $tableWidth?>" height="213" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
    <td width="100" height="35" align="right" class='A0010'>客户:</td>
    <td class='A0001'><select name="CompanyId" id="CompanyId" style="width:438px" dataType="Require" Msg="未选择">
        <?php 
	$checkSql=mysql_query("SELECT CompanyId,Forshort FROM $DataIn.trade_object WHERE Estate='1' AND ObjectSign IN (1,2) ORDER BY Id",$link_id);
	if($checkRow=mysql_fetch_array($checkSql)){
		do{
			$cTemp=$checkRow["CompanyId"];
			$nTemp=$checkRow["Forshort"];
			if($cTemp==$CompanyId){
				echo"<option value='$cTemp' selected>$nTemp</option>";
				}
			else{
				echo"<option value='$cTemp'>$nTemp</option>";
				}
			}while($checkRow=mysql_fetch_array($checkSql));
		}
	?>
    </select></td>
  </tr>
  <tr>
    <td height="35" align="right" class='A0010'>授权书名称:</td>
    <td class='A0001'><input name="Caption" type="text" id="Caption" value="<?php  echo $Caption?>" size="68" dataType="Require" Msg="未填写"></td>
  </tr>
  <tr>
    <td height="35" align="right" class='A0010'>授权截止:</td>
    <td class='A0001'><input name="TimeLimit" type="text" id="TimeLimit" value="<?php  echo $TimeLimit?>" size="68"  onfocus="WdatePicker()" readonly></td>
  </tr>
  <tr>
    <td height="41" align="right" class='A0010'>授权书附件: </td>
    <td class='A0001'><input name="Attached" type="file" id="Attached" size="68" dataType="Filter" msg="非法的文件格式" accept="pdf" Row="3" Cel="1"></td>
  </tr>
  <tr>
    <td height="52" align="right" class='A0010'>&nbsp;</td>
    <td class='A0001'>要求附件为PDF格式,多图片转PDF文件:先设好图片文件名(按顺序),然后同时选取，点鼠标右键，选&quot;在AdobeAcrobat 中合并&quot;</td>
  </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>