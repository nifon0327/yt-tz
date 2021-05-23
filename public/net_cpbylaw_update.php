<?php 
//电信-ZX  2012-08-01
//步骤1 $DataPublic.net_cpbylaw 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新电脑管理规定");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT Type,Remark FROM $DataPublic.net_cpbylaw WHERE Id='$Id'",$link_id));
$Remark=$upData["Remark"];
$Type=$upData["Type"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
		<tr>
            <td width="150" height="40" align="right" scope="col">分类</td>
            <td scope="col">
              <select name="Type" id="Type" style="width:500px" dataType="Require"  msg="未选择">
			  <option value="">请选择</option>
			  <?php 
			  $CheckTypeSql=mysql_query("SELECT Id,Name FROM $DataPublic.net_lawtype WHERE Estate=1 ORDER BY Id",$link_id);
			  if($CheckTypeRow=mysql_fetch_array($CheckTypeSql)){
			  	do{
					$TypeId=$CheckTypeRow["Id"];
					$Name=$CheckTypeRow["Name"];
					if($TypeId==$Type){
						echo"<option value='$TypeId' selected>$Name</option>";
						}
					else{
						echo"<option value='$TypeId'>$Name</option>";
						}
					}while($CheckTypeRow=mysql_fetch_array($CheckTypeSql));
				}
			  ?>
              </select></td>
		</tr>
		<tr>
		  <td height="40" align="right" valign="top" scope="col">管理规定</td>
		  <td scope="col"><textarea name="Remark" cols="60" rows="6" id="Remark" dataType="Require" Msg="未填写"><?php  echo $Remark?></textarea></td>
	    </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>