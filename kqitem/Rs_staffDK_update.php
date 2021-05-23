<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工抵扣工时");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$MyRow = mysql_fetch_array(mysql_query("SELECT 
	S.Id,S.Number,S.Locks,S.Date,S.Operator,S.Estate,P.Name,S.Remark,P.ComeIn,S.dkDate,S.dkHour,S.RemainHour
	 FROM $DataPublic.staff_dkdate S
	LEFT JOIN $DataPublic.staffmain P ON S.Number=P.Number 
WHERE S.Id=$Id",$link_id));
$Id=$MyRow["Id"];
$Name=$MyRow["Name"];
$Estate=$MyRow["Estate"];
$Remark=$MyRow["Remark"];
$dkDate=$MyRow["dkDate"];
$dkHour=$MyRow["dkHour"];
$RemainHour=$MyRow["RemainHour"];


//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,chooseMonth,$chooseMonth,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
	<table width="600" height="120" border="0" align="center" cellspacing="5">
  		<tr>
			<td width="146" height="18" align="right" valign="middle" scope="col">员工姓名</td>
			<td width="435" valign="middle" scope="col"><?php  echo $Name?></td>
		</tr>
          
       <tr>
         <td align="right">抵扣日期</td>
         <td><input name="dkDate" type="text" id="dkDate" value="<?php echo $dkDate ?>" style="width:230px" onfocus="WdatePicker()" dataType="Date" format="ymd" msg="日期不正确" readonly>
         </td>
         </tr>



		<tr>
		  <td height="18" align="right" valign="middle" scope="col">抵扣时长:</td>
		  <td valign="middle" scope="col"><input name="dkHour" type="text" id="dkHour" value="<?php  echo $dkHour ?>" dataType="Double" msg="未填写或格式不对">
	      </td>
	    </tr>
		
        <tr>
		  <td height="18" align="right" valign="middle" scope="col">未抵扣时长:</td>
		  <td valign="middle" scope="col"><input name="RemainHour" type="text" id="RemainHour" value="<?php  echo $RemainHour ?>" dataType="Double" msg="未填写或格式不对">
	      </td>
	    </tr>
                
        
		<tr>
		  <td height="13" align="right" valign="top" scope="col">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
		  <td scope="col"><textarea name="Remark" cols="30" rows="4" id="Remark" dataType="Require" Msg="未填写说明"><?php echo $Remark ?></textarea></td>
		</tr>

	</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>