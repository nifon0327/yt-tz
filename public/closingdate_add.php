<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增关帐日期");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="600" border="0" align="center" cellspacing="5">
			<tr>
				<td align="right" scope="col" height="30">关帐月份</td>
				<td width="460" scope="col"><input name="Month" type="text" id="Month"  style="width:100px;"    format="ymd" DataType="Require" Msg="未填写" onfocus="WdatePicker({dateFmt: 'yyyy-MM'})" readonly></td>
			</tr> 
			<tr>
				<td align="right" scope="col" height="30">关帐日期</td>
				<td width="460" scope="col"><input name="ClosingDate" type="text" id="ClosingDate" style="width:100px;" dataType="Date" format="ymd" msg="日期未选择或格式不正确" onfocus="WdatePicker()"  value='<?php  echo date("Y-m-d");?>' readonly></td>
			</tr>
			<tr>
              <td align="right" scope="col" height="30">备注信息</td>
              <td width="460" scope="col"><textarea name="Remark" cols="70" rows="2" id="Remark" ></textarea></td>
		  </tr>
	   </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>