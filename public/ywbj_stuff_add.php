<?php 
//电信-zxq 2012-08-01
//步骤1
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增报介配件资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="750" border="0" cellspacing="5" id="NoteTable">
		<tr>
            <td width="119" height="42" align="right" scope="col">配件名称</td>
            <td width="612" scope="col"><input name="Name" type="text" id="Name" size="89" dataType="LimitB" min="3" max="50"  msg="必须在2-50个字节之内" title="必填项,2-50个字节内"></td>
		</tr>
        <tr>
            <td height="42" align="right">参考价格</td>
            <td><input name="Price" type="text" id="Price" size="89" dataType="Currency" msg="错误的价格"></td>
        </tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>