<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增货币资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Name,Symbol,Rate,PreChar FROM $DataPublic.currencydata WHERE Id='$Id'",$link_id));
$PreChar=$upData["PreChar"];
$Name=$upData["Name"];
$Symbol=$upData["Symbol"];
$Rate=$upData["Rate"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="544" border="0" align="center" cellspacing="5">
		<tr>
			<td align="right" scope="col">货币说明</td>
			<td width="460" scope="col"><input name="Name" type="text" id="Name" style="width:380px;" maxlength="6" value="<?php  echo $Name?>" title="必选项,在5个汉字内." dataType="LimitB" min="1" max="12" msg="没有填写或超出许可范围"></td>
		</tr>
		<tr>
          <td align="right" scope="col">货币符号</td>
          <td scope="col"><input name="Symbol" type="text" id="Symbol" style="width:380px;" maxlength="4" value="<?php  echo $Symbol?>" title="必选项,在3个英文字符内." dataType="LimitB" min="1" max="4" msg="没有填写或超出许可范围"></td>
	    </tr>
		<tr>
			<td align="right" scope="col">简写符号</td>
			<td scope="col"><input name="PreChar" type="text" id="PreChar" style="width:380px;" maxlength="3" value="<?php  echo $PreChar?>" title="必选项,在3个英文字符内." dataType="LimitB" min="1" max="3" msg="没有填写或超出许可范围"></td>
		</tr>
		<tr>
			<td align="right" scope="col">汇率</td>
			<td scope="col"><input name="Rate" type="text" id="Rate" style="width:380px;" value="<?php  echo $Rate?>" title="必选项,至多可保留8位小数." dataType="Double" msg="没有填写或不合要求"></td>
		</tr>
	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>