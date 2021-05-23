<?php 
//2013-09-25 ewen
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增安全生产培训教程");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
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
            <td><textarea name="Caption" style="width:380px" rows="2" id="Caption"></textarea></td>
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