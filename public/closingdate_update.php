<?php 
//电信-zxq 2012-08-01
//代码、数据库共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新关帐日期设置");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT Month,ClosingDate,Remark FROM $DataPublic.sys_closingdate WHERE Id='$Id'",$link_id));
$Month=$upData["Month"];
$ClosingDate=$upData["ClosingDate"];
$Remark=$upData["Remark"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
        <table width="600" border="0" align="center" cellspacing="5">
			<tr>
				<td align="right" scope="col" height="30">关帐月份</td>
				<td width="460" scope="col"><input name="Month" type="text" id="Month"  style="width:100px;"    format="ymd" DataType="Require" Msg="未填写"   value="<?php  echo $Month;?>" readonly></td>
			</tr> 
			<tr>
				<td align="right" scope="col" height="30">关帐日期</td>
				<td width="460" scope="col"><input name="ClosingDate" type="text" id="ClosingDate" style="width:100px;" dataType="Date" format="ymd" msg="日期未选择或格式不正确" onfocus="WdatePicker()"  value='<?php  echo $ClosingDate;?>' readonly></td>
			</tr>
			<tr>
              <td align="right" scope="col" height="30">备注信息</td>
              <td width="460" scope="col"><textarea name="Remark" cols="70" rows="2" id="Remark" ><?php  echo $Remark;?></textarea></td>
		  </tr>
	   </table>
</td></tr></table>

<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>