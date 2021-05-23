<?php 
//电信-ZX  2012-08-01
//$DataPublic.net_softwaretype 二合一已更新
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 新增软件资料");//需处理
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
				<td width="150" align="right" scope="col">软件/驱动名称</td>
				<td valign="middle" scope="col"><input name="Name" type="text" id="Name" size="53" maxlength="50" dataType="Require"  msg="未填写"></td>
			  </tr>
			  <tr>
			    <td align="right" scope="col">许可状态</td>
			    <td valign="middle" scope="col"><select name="Sign" id="Sign" style="width: 300px;">
   	      			<option value="1" selected>可用</option>
   	      			<option value="2">需批准</option>
                    </select></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">软件类型</td>
			    <td valign="middle" scope="col"><select name="Type" id="Type" style="width: 300px;" dataType="Require"  msg="未选择">
				<option value="" selected>请选择</option>
				<?php 
				$typeResult = mysql_query("SELECT Id,Name FROM $DataPublic.net_softwaretype WHERE Estate=1 ORDER BY Id",$link_id);
				if($typeRow = mysql_fetch_array($typeResult)){
					$i=1;
					do{
						$Id=$typeRow["Id"];
						$Name=$typeRow["Name"];
						if($i<10){
							echo"<option value='$Id'>$i - $Name</option>";
							}
						else{
							echo"<option value='$Id'>$i- $Name</option>";
							}
						$i++;
						}while ($typeRow = mysql_fetch_array($typeResult));
					}
				 ?>
				 </select></td>
		      </tr>
	  </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>