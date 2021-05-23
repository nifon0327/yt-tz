<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增联系名单");//需处理
$nowWebPage =$funFrom."_add";	
$toWebPage  =$funFrom."_save";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";//需处理
//步骤3：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
//步骤4：需处理
?>
<script>
function ChooseRemark(the){
	document.form1.Remark.value=the.value;
	}
</script>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
    	<td colspan="2" class='A0011'>&nbsp;</td>
	</tr>
	<tr>
      <td width="150" height="35" align="right" class='A0010'>客&nbsp;&nbsp;&nbsp;&nbsp;户：</td>
      <td class='A0001'><select name="CompanyId" id="CompanyId" style="width:380px" dataType="Require" msg="未选择">
        <option value="" selected>请选择</option>
       <?php 
	   $CheckClientSql=mysql_query("SELECT A.CompanyId,A.Forshort FROM $DataIn.trade_object A WHERE A.cSign='$Login_cSign' AND A.Estate='1' ORDER BY A.OrderBy DESC,A.CompanyId",$link_id);
	   $i=1;
	   if($CheckClientRow=mysql_fetch_array($CheckClientSql)){
	   		do{
				$CompanyId=$CheckClientRow["CompanyId"];
				$Forshort=$CheckClientRow["Forshort"];
				echo"<option value='$CompanyId'>$i $Forshort</option>";
				$i++;
				}while($CheckClientRow=mysql_fetch_array($CheckClientSql));
			}
	   ?>
      </select></td>
  </tr>
	<tr>
      <td height="35" class='A0010' align="right">联 络 人：</td>
      <td class='A0001'><select name="Number" id="Number" style="width:380px" dataType="Require" msg="未选择">
	   <option value="" selected>请选择</option>
       <?php 
	   $CheckStaffSql=mysql_query("SELECT A.Number,A.Name,B.Name AS Job 
								  FROM $DataPublic.staffmain A
								  LEFT JOIN $DataPublic.jobdata B ON B.Id=A.JobId
								  WHERE A.Estate=1 AND A.JobId>2 AND A.JobId<9 AND A.cSign='$Login_cSign' ORDER BY A.BranchId,A.JobId,A.Number",$link_id);
	   if($CheckStaffRow=mysql_fetch_array($CheckStaffSql)){
	   		do{
				$Number=$CheckStaffRow["Number"];
				$Name=$CheckStaffRow["Job"]."-".$CheckStaffRow["Name"];
				echo"<option value='$Number'>$Name</option>";
				}while($CheckStaffRow=mysql_fetch_array($CheckStaffSql));
			}
		?>
      </select></td>
	</tr>
    <tr>
      <td height="35" align="right" valign="top" class='A0010'>描&nbsp;&nbsp;&nbsp;&nbsp;述：</td>
      <td  class='A0001'><textarea name="Remark" style="width:380px" rows="4" id="Remark" datatype="Require" msg="没有填写描述"></textarea></td>
    </tr>
	<?php 
	//预选描述
	$CheckRemarkSql=mysql_query("SELECT Remark FROM $DataIn.sys_clientstaffs GROUP BY Remark ORDER BY Remark",$link_id);
	if($CheckRemarkRow=mysql_fetch_array($CheckRemarkSql)){
		echo"<tr>
      		<td height='35' class='A0010' align='right' valign='top'>预选描述：</td>
      		<td class='A0001'>";
		$i=1;
		do{
			$Remark=$CheckRemarkRow["Remark"];
			echo"<input type='radio' name='PreRemark' id='PreRemark$i' value='$Remark' onclick='ChooseRemark(this)'>$Remark<br>";
			$i++;
			}while($CheckRemarkRow=mysql_fetch_array($CheckRemarkSql));
		echo"</td></tr>";
		}
	?>
</table>	
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>