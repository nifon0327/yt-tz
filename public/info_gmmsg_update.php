<?php 
//电信-ZX
//步骤1 $DataIn.info3_gmmsg 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新经理留言记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.info3_gmmsg WHERE Id='$Id'",$link_id));
$Remark=$upData["Remark"];
$Days=$upData["Days"];
$Date=$upData["Date"];
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="0">
	  <tr>
        <td align="right">留言日期</td>
        <td><input name="Date" type="text" id="Date" value="<?php  echo $Date?>" size="84" dataType="Date" format="ymd" msg="格式不对或未填写" onfocus="WdatePicker()" readonly></td>
	    </tr>
      <tr>
        <td width="101" align="right" valign="top">留言内容</td>
        <td>          <textarea name="Remark" cols="54" rows="8" id="Remark" dataType="Require"  msg="未填写"><?php  echo $Remark?></textarea></td>
      </tr>
      <tr>
        <td align="right">留言保存天数</td>
        <td><input name="Days" type="text" id="Days" value="<?php  echo $Days?>" size="84"></td>
      </tr>
    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>