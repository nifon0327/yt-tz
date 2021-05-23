<?php 
//电信-joseph
//代码、数据库共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新其他收入");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT hzID,Name as flName FROM $DataPublic.cw4_otherintype WHERE Id='$Id'",$link_id));
$hzID=$upData["hzID"];
$flName=$upData["flName"];
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
            <td scope="col" width="150" height="60" align="right">收入名称</td>
            <td scope="col"><input name="flName" type="text" id="flName" title="可输入2-16个字节(每1中文字占2个字节，每1英文字母占1个字节)" value="<?php  echo $flName?>" style="width: 380px;" maxlength="16" DataType="LimitB"  Max="16" Min="2" Msg="没有填写或字符不在2-16个字节内"></td>
		</tr>
		<tr>
            <td scope="col" width="150" height="60" align="right">对应行政收入</td>
            <td><?php 
            	include "../model/subselect/HzType.php";
            	?></td>
		</tr>

	</table>
</td></tr></table>
<?php 
//步骤6：表尾
include "../model/subprogram/add_model_b.php";
?>