<?php 
//代码 branchdata by zx 2012-08-13
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增忘签记录");//需处理
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
        <table width="700" border="0" align="center" cellspacing="5">
          <tr>
            <td width="100" height="21" align="right">签到类型</td>
            <td width="398">
				<select name="CheckType" id="CheckType" style="width:215px">
              	<option value="I" selected>上班签到</option>
              	<option value="O">下班签退</option>
              	<option value="K">跨日签退</option>
            	</select>
			</td>
            <td width="226" rowspan="5">
			<p>注：<br>
&nbsp;&nbsp;&nbsp;&nbsp;1. 忘签必须是前三天至今天之内的记录，考勤记录要求隔天处理完毕，星期五的考勤记录最迟在下星期一内处理完毕，超时不允许处理。</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;2.如因停电其它因素，需补签整个部门或全部人的签到记录时，不需指定员工，选取部门即可加入该部门所有需要考勤的员工的签到记录</p>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;3.指定员工优先处理,即同时指定部门和员工时,只处理指定员工的签到记录,而不是加入该部门全部需要考勤的员工的签到记录</p></td>
          </tr>
          <tr>
            <td height="21" align="right">签到日期</td>
            <td height="21"><input name="CheckDate" type="text" id="CheckDate" value="<?php  echo date("Y-m-d")?>" size="36" maxlength="10" onfocus="WdatePicker()" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="24" align="right">签到时间</td>
            <td height="24"><input name="CheckTime" type="text" id="CheckTime" value="08:00" size="36" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
          <tr>
            <td height="28" align="right">指定部门</td>
            <td height="28">
		  	<select name="BranchId" id="BranchId" style="width:215px" onChange="ClearList('ListId')">
		 	<option value="" selected>全部</option>
			<?php 
			$outResult=mysql_query("SELECT Id,Name FROM $DataPublic.branchdata 
								    WHERE Estate=1  AND (cSign=$Login_cSign OR cSign=0 )  ORDER BY Id",$link_id);
			if($outRow = mysql_fetch_array($outResult)) {
				do{
					$outId=$outRow["Id"];
					$outName=$outRow["Name"];
					echo "<option value='$outId'>$outName</option>";
					}while ($outRow = mysql_fetch_array($outResult));
				}
			?>
          </select></td>
          </tr>
          <tr>
            <td height="41" align="right" valign="top"><p>指定员工</p>
            </td>
          <td height="41">
			<select name="ListId[]" size="13" multiple id="ListId" style="width: 215px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,3)" dataType="autoList" readonly></select>
          </td>
          </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>