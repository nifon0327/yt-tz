<?php 
//电信-EWEN
//代码、数据共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新立项分类");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT Name,Remark FROM $DataIn.projectset_type WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
$Remark=$upData["Remark"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="750" border="0" align="center" cellspacing="5">
		<tr>
            <td scope="col" width="150" height="60" align="right">分类名称</td>
            <td scope="col">
              <input name="Name" type="text" id="Name" style="width:380px;  maxlength="40" value="<?php  echo $Name?>" title="可输入1-40个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="40" Min="1" Msg="没有填写或字符超出40字节"> 
			</td>
		</tr>
		<tr>
		  <td height="20"  align="right" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
		  <td scope="col"><textarea name="Remark" cols="50" rows="3" id="Remark" ><?php  echo $Remark?></textarea></td>
		</tr>
		
	</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>