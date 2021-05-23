<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增货币资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="600" border="0" align="center" cellspacing="5">
			<tr>
				<td align="right" scope="col">货币说明</td>
				<td width="460" scope="col"><input name="Name" type="text" id="Name" style="width:380px;" maxlength="6" title="必选项,在5个汉字内." dataType="LimitB" min="1" max="12" msg="没有填写或超出许可范围"></td>
			</tr> 
			<tr>
				<td align="right" scope="col">货币符号</td>
				<td scope="col"><input name="Symbol" type="text" id="Symbol" style="width:380px;" maxlength="4" title="必选项,在3个英文字符内." dataType="LimitB" min="1" max="4" msg="没有填写或超出许可范围"></td>
			</tr>
			<tr>
              <td align="right" scope="col">简写符号</td>
              <td scope="col"><input name="PreChar" type="text" id="PreChar" style="width:380px;" maxlength="3"></td>
		  </tr>
			<tr>
				<td align="right" scope="col">汇率</td>
				<td scope="col"><input name="Rate" type="text" id="Rate" style="width:380px;"  title="必选项,至多可保留8位小数." dataType="Currency" msg="没有填写或不合要求"></td>
		  </tr>
	   </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>