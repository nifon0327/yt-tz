<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新加班时间设置");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT otDate,workday, weekday, holiday From $DataIn.kqovertime Where Id = '$Id'",$link_id));
$workday = $upData["workday"];
$weekday = $upData["weekday"];
$holiday = $upData["holiday"];
$otDate = $upData["otDate"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="5">
          <tr>
            <td height="35" align="right">日期</td>
            <td height="35"><?php  echo $otDate?></td><input id="Id" type="hidden" value="<?php echo $Id?>">
          </tr>
          <tr>
            <td height="42" align="right">工作日</td>
            <td width="510"><input name="workdayTime" type="text" id="workdayTime" value="<?php  echo $workday?>" size="40" maxlength="5" </td>
          </tr>
          <tr>
            <td height="39" align="right" >双休日</td>
            <td><input name="weekdayTime" type="text" id="weekdayTime" value="<?php  echo $weekday?>" size="40" maxlength="5" ></td>
          </tr>
          <tr>
            <td height="39" align="right" >节假日</td>
            <td><input name="holidayTime" type="text" id="holidayTime" value="<?php  echo $holiday?>" size="40" maxlength="5" ></td>
          </tr>
          <tr>
            <td height="39" align="right" >更新类型</td>
            <td><select id="updateType" name="updateType">
              <option value='0'>全部</option>
              <option value='1'>包装</option>
              <option value='2'>皮套</option>
            </select></tb>
          </tr>
		</table>
	</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>