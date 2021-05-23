<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 劳务员工离职");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_leave";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"];
//步骤3：//需处理
$mainRow=mysql_fetch_array(mysql_query("SELECT M.Id,M.Number,M.Name
FROM $DataPublic.lw_staffmain M WHERE M.Id='$Id' LIMIT 1",$link_id));
$Number=$mainRow["Number"];
$Name=$mainRow["Name"];

//步骤4：
$tableWidth=750;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number,ActionId,32";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="450" border="0" align="center" cellspacing="5" id="NoteTable">

          <tr>
            <td align="right" width="180" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名:</td>
            <td scope="col">&nbsp;<?php  echo $Name?></td>
          </tr>
		  <tr>
            <td align="right" width="180" scope="col">Number &nbsp;:</td>
            <td scope="col">&nbsp;<?php  echo $Number?></td>
          </tr>
		  <tr>
            <td align="right" width="180" scope="col">离职类型:</td>
            <td scope="col"><select id="LeavedType" name="LeavedType" dataType="Require" Msg="未选择">
			 <option value="" selected>--请选择--</option>
			 <?php 
						$dResult=mysql_query("SELECT Id,Name FROM $DataPublic.dimissiontype WHERE Estate=1 order by Id",$link_id);
						if($dRow = mysql_fetch_array($dResult)) {
							do{
								$dId=$dRow["Id"];
								$dName=$dRow["Name"];
								echo "<option value='$dId'>$dName</option>";
								}while ($dRow = mysql_fetch_array($dResult));
							}
						?>
			</select></td>
          </tr>
		<tr>
		  <td height="29" align="right" scope="col">登记日期</td>
		  <td scope="col"><input name="theDate" type="text" id="theDate" onfocus="WdatePicker()" value="<?php  echo date("Y-m-d");?>" size="40" maxlength="10" dataType="Date" format="ymd" Msg="未选日期或格式不对" readonly></td>
	    </tr>
		<tr>
		  <td height="13" align="right" valign="top" scope="col">离职原因</td>
		  <td scope="col"><textarea name="Reason" cols="40" rows="3" id="Reason" dataType="Require" Msg="未填写原因"></textarea></td>
		</tr>
   </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>