<?php 
//代码、数据库共享-EWEN 2012-09-18 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 新增门禁设备");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";//需处理
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">    <tr>
    	<td width="150" height="35" align="right">门禁名称：
      </td>
	    <td><input name="PostValues[]" id="PostValues[]" type="text" style="width:380px" title="可输入1-150个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="50" Min="1" Msg="没有填写或字符超出50字节"></td>
    </tr>
    <tr>
      <td height="35" align="right">门禁地址：</td>
      <td><input name="PostValues[]" type="text" id="PostValues[]" style="width:380px" DataType="Require" Msg="没有填写描述"></td>
    </tr>
	<tr>
      <td height="35" align="right">控制器IP：</td>
      <td><input name="PostValues[]" type="text" id="PostValues[]" style="width:380px" DataType="LimitB" Max="15" Msg="超出15个字节的限制"></td>
    </tr>
	<tr>
	  <td height="35" align="right">控制开关：</td>
	  <td><select name="PostValues[]" id="PostValues[]" style="width:380px" DataType="Require" Msg="没有选择">
      <option value="">请选择</option>
	    <?php 
        $checkKGSql=mysql_query("SELECT Id,Name FROM $DataPublic.accessguard_order WHERE Estate='1' ORDER BY Id",$link_id);
		if($checkKGRow=mysql_fetch_array($checkKGSql)){
			do{
				echo"<option value='$checkKGRow[Id]'>$checkKGRow[Name]</option>";
				}while($checkKGRow=mysql_fetch_array($checkKGSql));
			}
		?>
	    </select></td>
  </tr>
</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>