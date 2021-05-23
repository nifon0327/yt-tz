<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新忘签记录");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT C.CheckTime,C.CheckType,M.Name 
FROM $DataIn.checkinout C,$DataPublic.staffmain M 
WHERE 1 AND M.Number=C.Number AND C.Id=$Id order by C.CheckTime DESC,M.Number",$link_id));
$CheckTime=$upData["CheckTime"];
$CheckType=$upData["CheckType"];
$Temp="CheckTypeSTR".strval($CheckType);$$Temp="selected";
$KrSign=$upData["KrSign"];
$Name=$upData["Name"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Month,$Month";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
        <table width="600" border="0" align="center" cellspacing="5">

          <tr>
            <td width="106" rowspan="6"></td>
            <td width="475" height="17"><input name="Id" type="hidden" id="Id" value="<?php  echo $Id?>"></td>
          </tr>
          <tr>
            <td width="475" height="21">员工姓名：<?php  echo $Name?></td>
          </tr>
          <tr>
            <td height="21">类&nbsp;&nbsp;&nbsp;&nbsp;型：
              <select name="CheckType" id="CheckType" style="width:215px ">
			  <option value='I' <?php  echo $CheckTypeSTRI?>>上班签到</option>
			  <option value='O' <?php  echo $CheckTypeSTRO?>>下班签退</option>
			  <option value='K' <?php  echo $CheckTypeSTRK?>>跨日签退</option>";
              </select></td>
          </tr>
          <tr>
            <td height="24">日&nbsp;&nbsp;&nbsp;&nbsp;期：
              <input name="CheckDate" type="text" id="CheckDate" value="<?php  echo substr($CheckTime,0,10)?>" size="37" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="28">时&nbsp;&nbsp;&nbsp;&nbsp;间：
              <input name="CheckTime" type="text" id="CheckTime" value="<?php  echo substr($CheckTime,11,5)?>" size="37" maxlength="5" dataType="Time" Msg="未填写或格式不对">
</td>
          </tr>
          <tr>
            <td height="41">&nbsp;</td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>