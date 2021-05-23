<?php 
//$DataPublic.staffmain 二合一已更新
//电信-joseph
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增报修项目");//需处理
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
      <table width="760" border="0" align="center" cellspacing="5">
		<tr>
            <td width="150" height="40" align="right" scope="col">报修日期</td>
            <td scope="col"><input name="Date" type="text" id="Date" size="90" maxlength="10" value="<?php  echo date("Y-m-d")?>" DataType="Date"  Msg="日期不对" onfocus="WdatePicker()" readonly></td>
		</tr>
		<tr>
		  <td height="40" align="right" scope="col">申 请 人</td>
		  <td scope="col"><select name="Number" id="Number" style="width:485px" dataType="Require"  msg="未选择">
			  	<?php 
				$CheckSql1 = mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE Number='$Login_P_Number' LIMIT 1",$link_id);
				if ($CheckRow1 = mysql_fetch_array($CheckSql1)){
					$Number=$CheckRow1["Number"];
					$Name=$CheckRow1["Name"];
					echo"<option value='$Number'>$Name</option>";
					}
				?>
				</select>  </td>
	    </tr>
		<tr>
		  <td height="40" align="right" scope="col">处 理 人</td>
		  <td scope="col"><select name="Servicer" id="Servicer" style="width:485px" dataType="Require"  msg="未选择">
				<option value="" selected>请选择</option>
				<?php 
				$CheckSql1 = mysql_query("SELECT Number,Name FROM $DataPublic.staffmain WHERE JobId=2 OR JobId=9",$link_id);
				if($CheckRow1 = mysql_fetch_array($CheckSql1)){
					do{
						$Number=$CheckRow1["Number"];
						$Name=$CheckRow1["Name"];
						echo"<option value='$Number'>$Name</option>";
						}while ($CheckRow1 = mysql_fetch_array($CheckSql1));
					}
				?>
				</select>  </td>
	    </tr>
		<tr>
		  <td height="43" align="right" valign="top" scope="col">报修项目</td>
		  <td height="43" scope="col"><textarea name="Remark1" cols="58" rows="6" id="Remark1" dataType="Require" msg="未填写"></textarea></td>
	    </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>