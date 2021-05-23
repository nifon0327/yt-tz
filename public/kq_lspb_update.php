<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新临时排班资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT L.Number,L.InTime,L.OutTime,L.InLate,L.OutEarly,L.TimeType,L.RestTime,M.Name 
FROM $DataIn.kqlspbb L,$DataPublic.staffmain M WHERE L.Number=M.Number and L.Id=$Id LIMIT 1",$link_id));
$Name=$upData["Name"];
$Number=$upData["Number"];
$InTime=date("Y-m-d",strtotime($upData["InTime"]));
$STime=date("H:i",strtotime($upData["InTime"]));
$ETime=date("H:i",strtotime($upData["OutTime"]));
$InLate=$upData["InLate"];
$OutEarly=$upData["OutEarly"];
$TimeType="TimeTypeSTR".strval($upData["TimeType"]); 
$$TimeType="selected";
$RestTime=$upData["RestTime"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,chooseMonth,$chooseMonth";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="750" border="0" align="center" cellspacing="0">
      <tr>
        <td align="right">员工姓名&nbsp;</td>
        <td><?php  echo $Name?></td>
      </tr>
      <tr>
        <td width="149" align="right">临班日期&nbsp;</td>
        <td width="597"><input name="StartDate" type="text" id="StartDate" size="40" maxlength="10" value="<?php  echo $InTime?>" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly></td>
      </tr>
      <tr>
        <td align="right">时 间 段&nbsp;</td>
        <td><select name="TimeType" id="TimeType" style="width:230px" dataType="Require" msg="未选择时间段">
            <option value="0" <?php  echo $TimeTypeSTR0?>>夜班</option>
            <option value="1" <?php  echo $TimeTypeSTR1?>>日班</option>
          </select> </td>
      </tr>
      <tr>
        <td align="right">签到时间&nbsp;</td>
        <td><input name="STime" type="text" id="STime" size="40" maxlength="5" value="<?php  echo $STime?>" dataType="Time" Msg="未填写或格式不对"></td>
      </tr>
      <tr>
        <td align="right">签退时间&nbsp;</td>
        <td><input name="ETime" type="text" id="ETime" size="40" maxlength="5" value="<?php  echo $ETime?>" dataType="Time" Msg="未填写或格式不对"></td>
      </tr>
      <tr>
        <td align="right">迟到设定&nbsp;</td>
        <td><input name="InLate" type="text" id="InLate" size="40" maxlength="2" value="<?php  echo $InLate?>" dataType="Number" Msg="未填写或格式不对"></td>
      </tr>
      <tr>
        <td align="right">早退设定&nbsp;</td>
        <td><input name="OutEarly" type="text" id="OutEarly" size="40" maxlength="2" value="<?php  echo $OutEarly?>" dataType="Number" Msg="未填写或格式不对"></td>
      </tr>
      <tr>
        <td align="right">中途休息&nbsp;</td>
        <td><input name="RestTime" type="text" id="RestTime" size="40" maxlength="3" value="<?php  echo $RestTime?>" dataType="Number" Msg="未填写或格式不对"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><p>说明： <br>
&nbsp;&nbsp;&nbsp;&nbsp;签退跨日的情况选夜班(有夜宵补助),其他选日班</p></td>
      </tr>
    </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>