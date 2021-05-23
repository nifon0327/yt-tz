<?php 
/*
$DataPublic.ot1_service
$DataPublic.staffmain
二合一已更新
电信-joseph
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新报修项目");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT B.Remark1,B.Remark2,B.Remark3,B.Date,B.Servicer,B.Estate,M.Name AS Operator FROM $DataPublic.ot1_service B,$DataPublic.staffmain M WHERE B.Id='$Id' AND M.Number=B.Operator LIMIT 1",$link_id));
$Remark1=$upData["Remark1"];
$Remark2=$upData["Remark2"];
$Remark3=$upData["Remark3"];

$Date=$upData["Date"];
$Estate=$upData["Estate"];
$EstateTemp="EstateSTR".strval($Estate);
$$EstateTemp="selected";
$Operator=$upData["Operator"];

$Servicer=$upData["Servicer"];


//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
		<tr>
            <td width="150" height="30" align="right" scope="col">报修日期</td>
            <td scope="col"><?php  echo $Date?></td>
		</tr>
		<tr>
		  <td height="30" align="right" scope="col">申 请 人</td>
		  <td scope="col"><?php  echo $Operator?></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">处 理 人</td>
		  <td scope="col"><select name="Servicer" id="Servicer" style="width:485px" dataType="Require"  msg="未选择">
				<?php 
				$CheckSql1 = mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE JobId=2 OR JobId=9 ORDER BY JobId",$link_id);
				if($CheckRow1 = mysql_fetch_array($CheckSql1)){
					do{
						$Number=$CheckRow1["Number"];
						$Name=$CheckRow1["Name"];
						if($Number==$Servicer){
							echo"<option value='$Number' selected>$Name</option>";
							}
						else{
							echo"<option value='$Number'>$Name</option>";
							}
						}while ($CheckRow1 = mysql_fetch_array($CheckSql1));
					}
				?>
				</select></td>
	    </tr>
		<tr>
		  <td height="43" align="right" valign="top" scope="col">报修项目</td>
		  <td height="43" scope="col"><textarea name="Remark1" cols="58" rows="6" id="Remark1" dataType="Require" msg="未填写"><?php  echo $Remark1?></textarea></td>
	    </tr>
	<?php 
	if($Servicer==$Login_P_Number){
	?>
		<tr>
		  <td height="43" align="right" valign="top" scope="col"><input name="Sign" type="hidden" id="Sign" value="1">
	      报修状况</td>
		  <td height="43" scope="col"><textarea name="Remark2" cols="58" rows="6" id="Remark2"><?php  echo $Remark2?></textarea></td>
	    </tr>
		<tr>
		  <td height="43" align="right" valign="top" scope="col">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
		  <td height="43" scope="col"><textarea name="Remark3" cols="58" rows="6" id="Remark3"><?php  echo $Remark3?></textarea></td>
	    </tr>
		<tr>
		  <td height="30" align="right" scope="col">处理状态</td>
		  <td height="43" scope="col"><select name="Estate" id="Estate" style="width:485px">
			<option value='1' <?php  echo $EstateSTR1?>>未处理</option>
			<option value='2' <?php  echo $EstateSTR2?>>处理中</option>
			<option value='0' <?php  echo $EstateSTR0?>>已处理</option>
          </select></td>
	    </tr>
	<?php  }?>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>