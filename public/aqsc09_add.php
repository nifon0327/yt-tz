<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增员工安全生产知识考核");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理

?>
<table width="<?php  echo $tableWidth?>" height="250" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id="NoteTable">
  <tr>
		<td width="100"  align="right" valign="top" class='A0010'>员工姓名：</td>
    <td valign="top" class='A0001'><input name="Name" type="text" id="Name" value="" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
 <tr>
		<td  align="right" valign="top" class='A0010'>考核日期：</td>
    <td valign="top" class='A0001'><input name="ExamDate" type="text" id="ExamDate" style="width:380px;" maxlength="10" dataType="Require" Msg="未填写" value="<?php echo date("Y-m-d")?>" onfocus="WdatePicker()" readonly></td>
    </tr>
    <tr>
		<td  align="right" valign="top" class='A0010'>考核内容：</td>
	  <td valign="top" class='A0001'>
      <select name="ExamContent" id="ExamContent" style="width:380px" dataType="Require" msg='未选择'>
            <option value='' selected>请选择</option>
            <?php
			$checkResult = mysql_query("SELECT A.Id,A.Caption FROM $DataPublic.aqsc06 A WHERE A.Estate=1 ORDER BY A.Id",$link_id);
			if($checkRow = mysql_fetch_array($checkResult)){
				$i=1;
				do{
					echo"<option value='$checkRow[Id]'>$i $checkRow[Caption]</option>";
					$i++;
					}while($checkRow = mysql_fetch_array($checkResult));
				}
            ?>
            </select>
      </td>
    </tr>
    <tr>
		<td  align="right" valign="top" class='A0010'>考核成绩：</td>
	  <td valign="top" class='A0001'><input name="Results" type="text" id="Results" value="" style="width:380px;" maxlength="60" dataType="Require" Msg="未填写"></td>
    </tr>
    <tr>
      <td  align="right" valign="top" class='A0010'>文档附件：</td>
    <td valign="top" class='A0001'><input name="Attached" type="file" id="Attached" size="60" dataType="Filter" Msg="非法的文件格式" Accept="pdf" Row="2" Cel="1"></td>
    </tr>
    <tr>
      <td  align="right" valign="top" class='A0010'>审核人：</td>
      <td valign="top" class='A0001'><input name="Checker" type="text" id="Checker" value="" style="width:380px;" maxlength="60" datatype="Require" msg="未填写" /></td>
    </tr>
    <tr>
      <td align="right" valign="top" class='A0010'>审核意见：</td>
      <td valign="top" class='A0001'><textarea name="Opinion" id="Opinion" style="width:380px;" datatype="Require" msg="未填写"></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>