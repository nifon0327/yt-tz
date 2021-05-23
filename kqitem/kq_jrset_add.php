<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增假日");//需处理
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
	<table width="604" height="205" border="0" align="center" cellspacing="5">
		<tr>
            <td height="34" align="right" scope="col">假日名称</td>
            <td scope="col"><input name="Name" type="text" id="Name" size="53" maxlength="16" dataType="LimitB" min="3" max="16"  msg="假日名称须在3-16个字节之内"></td>
		</tr>
		<tr>
            <td height="29" align="right" scope="col">起始日期</td>
            <td scope="col">
              <input name="StartDate" type="text" id="StartDate" onfocus="WdatePicker()" size="53" maxlength="10" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly></td>               
		</tr>
		<tr>
			<td height="26" align="right" scope="col">终止日期</td>
			<td scope="col"><input name="EndDate" type="text" id="EndDate" onfocus="WdatePicker()" size="53" maxlength="10" dataType="Date" format="ymd" Msg="未填写或格式不对" readonly></td>
		</tr>
		<tr>
            <td height="27" align="right">假日类型</td>
            <td width="483">
			<select name="Type" id="Type" style="width:300px" dataType="Require"  msg="未选择假日类型">
			  <option value="" selected>请选择</option>
			  <option value="0">无薪假期</option>
			  <option value="1">有薪假期</option>
			  <option value="2">法定假期</option>
            </select></td>
        </tr>
		<tr>
		  <td height="27" align="right">加班倍率</td>
		  <td><select name="jbTimes" id="jbTimes" style="width:300px" dataType="Require"  msg="未选择加班倍率">
		    <option value="" selected>请选择</option>
		    <option value="1">1倍</option>
		    <option value="2">2倍</option>
		    <option value="3">3倍</option>
	      </select></td>
	    </tr>
		<tr>
		  <td height="27" align="right">是否带薪</td>
		  <td><select name="Sign" id="Sign" style="width:300px" dataType="Require"  msg="未选择是否带薪">
		    <option value="" selected>请选择</option>
		    <option value="1">带薪</option>
		    <option value="0">不带薪</option>
	      </select></td>
	    </tr>
        </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>