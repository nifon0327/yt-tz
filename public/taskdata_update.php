<?php 
//电信-joseph
//代码、数据库共享-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新特殊功能资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$upResult = mysql_query("SELECT * FROM $DataPublic.tasklistdata WHERE Id=$Id LIMIT 1",$link_id);
if($upRow = mysql_fetch_array($upResult)){
	$Title=$upRow["Title"];
	$Description=$upRow["Description"];
	$Extra=$upRow["Extra"];
	$TypeId=$upRow["TypeId"];
	$InCol=$upRow["InCol"];
	$Oby=$upRow["Oby"];
	$cSign=$upRow["cSign"];
	$TempInCol="InColSTR".strval($InCol);$$TempInCol="selected";
	}
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,ItemId,$ItemId,funFrom,$funFrom,From,$From,Estate,$Estate,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2" class='A0011'>&nbsp;</td>
	</tr>
   <!-- <tr>
      <td height="35" class='A0010' align="right">使用标识：</td>
      <td class='A0001'><?php 
	 	$SharingShow="Y";
      	include "../model/subselect/cSign.php";
	  ?></td>
  </tr>-->
	<tr>
      <td height="35" class='A0010' align="right">功能类别：</td>
      <td class='A0001'><?php 
      include "../model/subselect/taskType.php";?></td>
  </tr>
	<tr>
      <td height="35" class='A0010' align="right">所 在 列：</td>
      <td class='A0001'><select name="InCol" id="InCol" style="width:380px">
          <option value="1" <?php  echo $InColSTR1?>>第一列</option>
          <option value="2" <?php  echo $InColSTR2?>>第二列</option>
          <option value="3" <?php  echo $InColSTR3?>>第三列</option>
      </select></td>
  </tr>
    <tr>
    	<td width="150" height="35" valign="middle" class='A0010'><div align="right">功能名称：</div></td>
	    <td valign="middle" class='A0001'><input name="Title" type="text" id="Title" value="<?php  echo $Title?>" style="width:380px" title="可输入1-150个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="150" Min="1" Msg="没有填写或字符超出150字节"></td>
    </tr>
    <tr>
      <td height="35" valign="middle" class='A0010'><div align="right">描&nbsp;&nbsp;&nbsp;&nbsp;述：</div></td>
      <td valign="middle" class='A0001'><input name="Description" type="text" id="Description" style="width:380px" value="<?php  echo $Description?>" DataType="Require" Msg="没有填写描述"></td>
    </tr>
    <tr>
      <td height="35" valign="middle" class='A0010'><div align="right">特别参数：</div></td>
      <td valign="middle" class='A0001'><input name="Extra" type="text" id="Extra" style="width:380px" value="<?php  echo $Extra?>" DataType="Require"  Msg="没有填写描述"></td>
    </tr>
	<tr>
      <td height="35" valign="middle" class='A0010'><div align="right">排序号码：</div></td>
      <td valign="middle" class='A0001'><input name="Oby" type="text" id="Oby" style="width:380px" value="<?php  echo $Oby?>" DataType="Integer" Msg="格式不对"></td>
    </tr>
</table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>