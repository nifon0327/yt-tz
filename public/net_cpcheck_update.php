<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.net_cpdata
$DataPublic.net_cpcheckdiary
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新维护日志");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT hdId,Opinion,Remark FROM $DataPublic.net_cpcheckdiary WHERE Id='$Id'",$link_id));
$hdId=$upData["hdId"];
$Opinion=$upData["Opinion"];
$Remark=$upData["Remark"];
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
    	  <td width="100" height="26" align="right">电脑名称</td>
    	  <td>
		<select name="hdId" id="hdId" style="width:500px" dataType="Require"  msg="未选择">
		<?php 
		$CheckSql = mysql_query("SELECT Id,CpName FROM $DataPublic.net_cpdata ORDER BY cpName",$link_id);
		if($CheckRow = mysql_fetch_array($CheckSql)){
			do {
				$cpId=$CheckRow["Id"];
				$CpName=$CheckRow["CpName"];
				if($cpId==$hdId){
					echo"<option value='$cpId' selected>$CpName</option>";
					}
				else{
					echo"<option value='$cpId'>$CpName</option>";
					}
				}while($CheckRow = mysql_fetch_array($CheckSql));
			}
		?>
       </select></td>
    </tr>
    <tr>
      <td align="right" valign="top">维护内容</td>
      <td valign="top"><textarea name="Remark" cols="60" rows="10" id="Remark" dataType="Remark"  msg="未填写"><?php  echo $Remark?></textarea></td>
    </tr>
    <tr>
      <td align="right" valign="top">维护评述</td>
      <td valign="top"><textarea name="Opinion" cols="60" rows="6" id="Opinion" dataType="Require"  msg="未填写"><?php  echo $Opinion?></textarea></td>
    </tr>
</table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>