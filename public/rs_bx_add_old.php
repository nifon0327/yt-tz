<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增请假记录");//需处理
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
	<table width="600" border="0" align="center" cellspacing="5">
		<tr>
		<td height="18" align="right" valign="top" scope="col">员工:</td>
		<td valign="middle" scope="col">
		 	<select name="ListId[]" size="10" id="ListId" multiple style="width: 430px;" onclick="SearchRecord('staff','<?php  echo $funFrom?>',2,122)" dataType="PreTerm" Msg="没有指定员工" readonly>
		 	</select>
		</td>
		</tr>			          
          <tr>
            <td height="9" align="right">起始日期：</td>
            <td width="520"><input name="StartDate" type="text" id="StartDate" value="<?php  echo date("Y-m-d")?>" size="38" maxlength="10" onfocus="WdatePicker()" dataType="Date" formqt="ymd" Msg="未填或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="13" align="right">时间：</td>
            <td><input name="StartTime" type="text" id="StartTime" value="08:00" size="38" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
          <tr>
            <td height="9" align="right">结束日期：</td>
            <td><input name="EndDate" type="text" id="EndDate" value="<?php  echo date("Y-m-d")?>" size="38" maxlength="10" onfocus="WdatePicker()" dataType="Date" formqt="ymd" Msg="未填或格式不对" readonly></td>
          </tr>
          <tr>
            <td height="13" align="right">时间：</td>
            <td><input name="EndTime" type="text" id="EndTime" value="17:00" size="38" maxlength="5" dataType="Time" Msg="未填写或格式不对"></td>
          </tr>
           <tr>
            <td height="13" align="right">计算方式：</td>
            <td><select id="CalculateType" name="CalculateType">
	            <option value="0" selected>按小时计算</option>
	            <option value="1">按天计算</option>
            </select></td>
          </tr>
          <tr>
            <td height="13" align="right">备注：</td>
            <td><input name="note" type="text" id="note" size="38"note></td>
          </tr>
          
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>