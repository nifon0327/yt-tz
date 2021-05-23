<?php 
//代码共享、数据库共享-EWEN 2012-11-02
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增行政审核分类");//需处理
$nowWebPage ="CGLabel_19";	
$toWebPage  ="CGLabel_19_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
		<table width="760" border="0" align="center" cellspacing="5">
			<tr>
            	<td width="150" height="40" align="right" scope="col">分类名称</td>
            	<td scope="col"><input name="Name" type="text" id="Name" style="width:380px" maxlength="20" title="可输入2-20个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="20" Min="2" Msg="不合要求"></td>
			</tr>
			<tr>
			  <td height="40" align="right" scope="col">审核人姓名</td>
			  <td scope="col"><input name="StaffName" type="text" id="StaffName" style="width:380px" maxlength="8" title="可输入4-8个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="8" Min="4" Msg="不合要求">/></td>
		  </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>