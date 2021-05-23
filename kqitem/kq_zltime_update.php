<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新直落资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Z.Id,Z.Date,Z.Stime,Z.Etime,Z.Hours,M.Number,M.Name FROM $DataPublic.kqzltime Z,$DataPublic.staffmain M 
WHERE Z.Number=M.Number and Z.Id=$Id LIMIT 1",$link_id));
$Name=$upData["Name"];
$Number=$upData["Number"];
$Date=$upData["Date"];
$Stime=$upData["Stime"];
$Etime=$upData["Etime"];
$Hours=$upData["Hours"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="5">
          <tr>
            <td height="35" align="right">员工姓名</td>
            <td height="35"><?php  echo $Name?></td>
          </tr>
          <tr>
            <td height="31" align="right">直落日期</td>
            <td height="31"><input name="Date" type="text" id="Date" value="<?php  echo $Date?>" size="40" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly> </td>
          </tr>
          <tr>
            <td height="42" align="right">直落起始时间</td>
            <td width="510"><input name="Stime" type="text" id="Stime" value="<?php  echo $Stime?>" size="40" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
          <tr>
            <td height="39" align="right" >直落结束时间</td>
            <td><input name="Etime" type="text" id="Etime" value="<?php  echo $Etime?>" size="40" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
		</table>
	</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>