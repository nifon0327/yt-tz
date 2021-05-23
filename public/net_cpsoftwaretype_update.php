<?php 
//电信-ZX  2012-08-01
//步骤1 $DataPublic.dimissiontype 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新软件分类资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.net_softwaretype WHERE Id='$Id'",$link_id));
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
            <td scope="col" width="150" height="60" align="right">分类名称</td>
            <td scope="col">
              <input name="Name" type="text" id="Name" size="60" maxlength="50" value="<?php  echo $Name?>" title="可输入1-50个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="50" Min="1" Msg="没有填写或字符超出20字节"> 
			</td>
		</tr>
	</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>