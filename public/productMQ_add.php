<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增产品材质资料");//需处理
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
      <table width="760" border="0" align="left" cellspacing="5">
		<tr>
            <td width="120" height="40" align="right" scope="col">材质名称</td>
            <td scope="col" ><input name="Name" type="text" id="Name" style="width:380px;" maxlength="100" title="可输入2-100个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="100" Min="2" Msg="没有填写或字符不在2-100个字节内"></td>
		</tr>

        <tr>
            <td width="120" height="40" align="right" valign="top" scope="col">备注</td>
            <td scope="col" ><textarea name="Remark" rows="4" id="Remark" style="width:380px;" tdatatype="Require" msg="没有填写"></textarea></td>
		</tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>

