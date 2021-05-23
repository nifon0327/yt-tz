<?php 
//电信-ZX  2012-08-01
/*
$DataPublic.net_softwarelist
$DataPublic.net_softwaretype
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新软件资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.net_softwarelist WHERE Id='$Id'",$link_id));
$Name=$upData["Name"];
$Sign=$upData["Sign"];
$SignTemp="SignStr".strval($Sign);
$$SignTemp="selected";
$Type=$upData["Type"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";

?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="760" border="0" align="center" cellspacing="5">
			  <tr>
				<td width="150" align="right" scope="col">软件/驱动名称</td>
				<td valign="middle" scope="col"><input name="Name" type="text" id="Name" size="53" value="<?php  echo $Name?>" maxlength="50" dataType="Require"  msg="未填写"></td>
			  </tr>
			  <tr>
			    <td align="right" scope="col">许可状态</td>
			    <td valign="middle" scope="col"><select name="Sign" id="Sign" style="width: 300px;">
   	      			<option value="1" <?php  echo $SignStr1?>>可用</option>
   	      			<option value="2" <?php  echo $SignStr2?>>需批准</option>
                    </select></td>
		      </tr>
			  <tr>
			    <td align="right" scope="col">软件类型</td>
			    <td valign="middle" scope="col"><select name="Type" id="Type" style="width: 300px;" dataType="Require"  msg="未选择">
				<?php 
				$typeResult = mysql_query("SELECT Id,Name FROM $DataPublic.net_softwaretype WHERE Estate=1 ORDER BY Id",$link_id);
				if($typeRow = mysql_fetch_array($typeResult)){
					$i=1;
					do{
						$Id=$typeRow["Id"];
						$Name=$i<10?$i." - ".$typeRow["Name"]:$i."- ".$typeRow["Name"];
						if($Type==$Id){
							echo"<option value='$Id' selected>$Name</option>";
							}
						else{
							echo"<option value='$Id'>$Name</option>";
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