<?php 
//电信-ZX
//步骤1 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增经理留言记录");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="0">
	  <tr>
        <td align="right">留言日期</td>
        <td><input name="Date" type="text" id="Date" value="<?php  echo date("Y-m-d")?>" size="84" dataType="Date" format="ymd" msg="格式不对或未填写" onfocus="WdatePicker()" readonly></td>
	    </tr>
      <tr>
        <td width="101" align="right" valign="top">留言内容</td>
        <td>          <textarea name="Remark" cols="54" rows="8" id="Remark" dataType="Require"  msg="未填写"></textarea></td>
      </tr>
      <tr>
        <td align="right">留言保存天数</td>
        <td><input name="Days" type="text" id="Days" value="1" size="84" dataType="Number"  msg="格式不对"></td>
      </tr>
    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>