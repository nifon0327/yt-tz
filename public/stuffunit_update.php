<?php 
//电信---yang 20120801
//代码、数据库共享-EWEN 2012－08－14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新配件单位");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.stuffunit WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
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
            <td scope="col" width="150" height="60" align="right">配件单位名称</td>
            <td scope="col">
              <input name="Name" type="text" id="Name" style="width:380px;" maxlength="10" value="<?php  echo $Name?>" title="可输入1-10个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="10" Min="1" Msg="没有填写或字符超出10字节"> 
			</td>
		</tr>
	</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>