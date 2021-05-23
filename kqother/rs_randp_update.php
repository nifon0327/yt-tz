<?php 
//电信-ZX  2012-08-01
//$DataIn.staffrandp/$DataPublic.staffmain  二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新奖惩记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT R.Number,R.Type,R.Content,R.Amount,R.Date,M.Name 
FROM $DataIn.staffrandp R,$DataPublic.staffmain M 
WHERE R.Number=M.Number and R.Id=$Id LIMIT 1",$link_id));
$Number=$upData["Number"];
$Name=$upData["Name"];
if($upData["Type"]==1){
	$TypeSTR1="selected";
	}
else{
	$TypeSTR0="selected";
	}
$Content=$upData["Content"];
$Amount=$upData["Amount"];		
$Date=$upData["Date"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
//步骤5：//需处理

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
    	<td width="150" height="36" align="right" valign="middle" class='A0010'><p>员工姓名：<br> 
      </td>
	    <td valign="middle" class='A0001'><?php  echo $Name?>
		</td>
    </tr>
    <tr>
    	<td height="26" align="right" valign="middle" class='A0010'>日&nbsp;&nbsp;&nbsp;&nbsp;期：</td>
	    <td valign="middle" class='A0001'><input name="Date" type="text" id="Date" size="77" maxlength="10" value="<?php  echo $Date?>" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly></td>
    </tr>
    <tr>
      <td height="29" align="right" valign="middle" class='A0010'>金&nbsp;&nbsp;&nbsp;&nbsp;额：</td>
      <td valign="middle" class='A0001'><input name="Amount" type="text" id="Amount" size="77" maxlength="6" value="<?php  echo $Amount?>" dataType="Currency" Msg="未填写或格式不对"></td>
    </tr>
    <tr>
      <td height="31" align="right" valign="middle" class='A0010'>奖惩类别：</td>
      <td valign="middle" class='A0001'><select name="Type" id="Type" style="width:420px" dataType="Require"  msg="未选择类别">
          <option value="1" <?php  echo $TypeSTR1?>>奖励</option>
          <option value="-1" <?php  echo $TypeSTR0?>>惩罚</option>
      </select></td>
    </tr>
    <tr>
      <td height="47" valign="top" class='A0010' align="right">奖惩内容：</td>
      <td valign="top" class='A0001'><textarea name="Content" cols="50" rows="6" id="Content" dataType="Require" Msg="未填写"><?php  echo $Content?></textarea></td>
    </tr>
</table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>