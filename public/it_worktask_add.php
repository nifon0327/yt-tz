<?php 
//电信-ZX  2012-08-01
//MC、DP共用代码
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增任务");//需处理
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
            <td width="150" height="40" align="right" scope="col">发布日期</td>
            <td scope="col"><input name="TaskDate" type="text" id="TaskDate" size="90" maxlength="10" value="<?php  echo date("Y-m-d")?>" DataType="Date"  Msg="日期不对" onfocus="WdatePicker()" readonly></td>
		</tr>
		<tr>
		  <td height="40" align="right" scope="col">发 布 人</td>
		  <td scope="col">
			  	<?php 
				$CheckSql1 = mysql_query("SELECT Name FROM $DataPublic.staffmain WHERE Number='$Login_P_Number' LIMIT 1",$link_id);
				if ($CheckRow1 = mysql_fetch_array($CheckSql1)){
					$Sponsor=$CheckRow1["Name"];
					}
				?>
		    <input name="Sponsor" type="text" id="Sponsor" size="90" maxlength="10" value="<?php  echo $Sponsor?>" dataType="LimitB" min="2" max="10"  msg="必须在2-10个字节之内" title="必填项,2-10个字节内"></td>
	    </tr>
		
		<tr>
          <td height="40" align="right" scope="col">任务类型</td>
          <td height="40" scope="col"><select name="TaskType" id="TaskType" style="width:485px; ">
             <?php 
			 $checkTypeSql=mysql_query("SELECT Id,TypeName FROM $DataPublic.it_worktype WHERE 1 ORDER BY Id",$link_id);
			if($checkTypeRow=mysql_fetch_array($checkTypeSql)){
				do{
					$TypeId=$checkTypeRow["Id"];
					$TypeName=$checkTypeRow["TypeName"];
					if($TaskType==$TypeId){	
						echo"<option value='$TypeId' selected>$TypeName</option>";
						}
					else{
						echo"<option value='$TypeId'>$TypeName</option>";
						}
					}while($checkTypeRow=mysql_fetch_array($checkTypeSql));
				}
			 ?>
          </select></td>
	    </tr>
		<tr>
		  <td height="43" align="right" valign="top" scope="col">任务内容</td>
		  <td height="43" scope="col"><textarea name="TaskContent" cols="58" rows="6" id="TaskContent" dataType="Require" msg="未填写"></textarea></td>
	    </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>