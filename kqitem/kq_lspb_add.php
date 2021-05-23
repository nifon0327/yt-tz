<?php 
//代码 jobdata by zx 2012-08-13
//代码 branchdata by zx 2012-08-13
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增临时排班资料");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'><table width="800" border="0" align="center" cellspacing="0">
  <tr>
    <td colspan="2" align="center">临时排班的员工</td>
    <td colspan="2" align="center">临时班次上下班时间设定</td>
  </tr>
  <tr>
    <td width="76" align="right">指定部门 </td>
    <td width="193">
	<select name="BranchId" id="BranchId" style="width:180px">
	<option value="">全部</option>
	<?php 
	$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
						    WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
	if($outRow = mysql_fetch_array($outResult)) {
		do{
			$outId=$outRow["Id"];
			$outName=$outRow["Name"];
			echo "<option value='$outId'>$outName</option>";
			}while ($outRow = mysql_fetch_array($outResult));
		}
	?>
    </select></td>
    <td width="87" align="right">起始日期</td>
    <td width="436">
      <input name="StartDate" type="text" id="StartDate" size="40" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly></td>
  </tr>
  <tr>
    <td align="right">指定职位 </td>
    <td><select name="JobId" id="JobId" style="width:180px">
	<option value="">全部</option>
	<?php 
	$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.jobdata 
						     WHERE Estate=1 AND (cSign=$Login_cSign OR cSign=0 ) order by Id",$link_id);
	if($outRow = mysql_fetch_array($outResult)) {
		do{
			$outId=$outRow["Id"];
			$outName=$outRow["Name"];
			echo "<option value='$outId'>$outName</option>";
			}while ($outRow = mysql_fetch_array($outResult));
		}
	?>
    </select></td>
    <td align="right">结束日期</td>
    <td>
      <input name="EndDate" type="text" id="EndDate" size="40" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly></td>
  </tr>
  <tr>
    <td rowspan="7" align="right" valign="top">指定员工</td>
    <td rowspan="7">
	<select name="ListId[]" size="18" multiple id="ListId" style="width: 180px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,3)" dataType="autoList" readonly></select></td>
    <td align="right">时 间 段</td>
    <td>
      <select name="TimeType" id="TimeType" style="width:235px" dataType="Require" msg="未选择时间段">
	  	<option value="" selected>请选择</option>
        <option value="0">夜班</option>
        <option value="1">日班</option>
      </select></td>
  </tr>
  <tr>
    <td align="right">签到时间</td>
    <td>
      <input name="STime" type="text" id="STime" size="40" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
  </tr>
  <tr>
    <td align="right">签退时间</td>
    <td>
      <input name="ETime" type="text" id="ETime" size="40" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
  </tr>
  <tr>
    <td align="right">迟到设定</td>
    <td>
      <input name="InLate" type="text" id="InLate" value="0" size="40" maxlength="2" dataType="Number" Msg="未填写或格式不对"></td>
  </tr>
  <tr>
    <td align="right">早退设定</td>
    <td>
      <input name="OutEarly" type="text" id="OutEarly" value="0" size="40" maxlength="2" dataType="Number" Msg="未填写或格式不对"></td>
  </tr>
  <tr>
    <td align="right">中途休息</td>
    <td>
      <input name="RestTime" type="text" id="RestTime" size="40" maxlength="3" dataType="Number" Msg="未填写或格式不对"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><p>说明： <br>
&nbsp;&nbsp;&nbsp;&nbsp;1、签退跨日的情况选夜班(有夜宵补助),其他选日班;<br>
&nbsp;&nbsp;&nbsp;&nbsp;2、指定员工最优先,指定职位次之.即指定员工，则忽略职位和部门,但可以在指定的部门和职位查找相应员工</p></td>
  </tr>
</table>
        </td>
</tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>