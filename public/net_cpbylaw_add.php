<?php 
//电信-ZX  2012-08-01
//$DataPublic.net_lawtype 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增电脑管理规定");//需处理
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
            <td width="150" height="40" align="right" scope="col">分类</td>
            <td scope="col">
              <select name="Type" id="Type" style="width:500px" dataType="Require"  msg="未选择">
			  <option value="">请选择</option>
			  <?php 
			  $CheckTypeSql=mysql_query("SELECT Id,Name FROM $DataPublic.net_lawtype WHERE Estate=1 ORDER BY Id",$link_id);
			  if($CheckTypeRow=mysql_fetch_array($CheckTypeSql)){
			  	do{
					$Id=$CheckTypeRow["Id"];
					$Name=$CheckTypeRow["Name"];
					echo"<option value='$Id'>$Name</option>";
					}while($CheckTypeRow=mysql_fetch_array($CheckTypeSql));
				}
			  ?>
              </select></td>
		</tr>
		<tr>
		  <td height="40" align="right" valign="top" scope="col">管理规定</td>
		  <td scope="col"><textarea name="Remark" cols="60" rows="6" id="Remark" dataType="Require" Msg="未填写"></textarea></td>
	    </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>