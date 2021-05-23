<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增用户页面功能");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";//需处理
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="750" border="0" align="center" cellspacing="5"> 
				<tr>
            <td width="98" align="right" scope="col">功能名称</td>
            <td width="633" scope="col"><input name="ModuleName" type="text" id="ModuleName" style="width:380px;" title="可输入1-25个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="25" Min="1" Msg="没有填写或字符超出25字节">
            </td>
				</tr>
				<tr>
				  <td align="right" scope="col">连接参数</td>
		          <td scope="col"><input name="Parameter" type="text" id="Parameter" style="width:380px;" title="可输入不超过100个字节的参数(每1英文字母占1个字节)" Max="100" Msg="字符超出100字节"> 
	              </td>
		  </tr>
				<tr>
				  <td align="right" scope="col">位&nbsp;&nbsp;&nbsp;&nbsp;置</td>
				  <td scope="col"><input name="Oby" type="text" id="Oby" style="width:380px;" title="可输入不超过100个字节的参数(每1英文字母占1个字节)" Max="100" Msg="字符超出100字节"></td>
		  </tr>
		<tr>
		  <td align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
		  <td scope="col">
		  <textarea name="Remark" style="width:380px;" id="Remark" title="可输入不超过100个字节的参数(每1英文字母占1个字节)" max="100" msg="字符超出100个字节"><?php  echo $upRow["Remark"]?></textarea></td>
		  </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>