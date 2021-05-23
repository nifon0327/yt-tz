<?php 
//代码、数据共享-EWEN 2012-09-18
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新门禁指令");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT DoorName,DoorAdd,IP,OrderId FROM $DataPublic.accessguard_door WHERE Id='$Id'",$link_id));
$DoorName=$upData["DoorName"];
$DoorAdd=$upData["DoorAdd"];
$IP=$upData["IP"];
$OrderId=$upData["OrderId"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../Admin/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="650" border="0" align="center" cellspacing="5">    <tr>
    	<td width="150" height="35" align="right">门禁名称：
      </td>
	    <td><input name="PostValues[]" id="PostValues[]" type="text" style="width:380px" value="<?php  echo $DoorName?>" title="可输入1-50个字节(每1中文字占2个字节，每1英文字母占1个字节)" DataType="LimitB"  Max="50" Min="1" Msg="没有填写或字符超出50字节"></td>
    </tr>
    <tr>
      <td height="35" align="right">门禁地址：</td>
      <td><input name="PostValues[]" type="text" id="PostValues[]" style="width:380px" value="<?php  echo $DoorAdd?>" DataType="Require" Msg="没有填写描述"></td>
    </tr>
	<tr>
      <td height="35" align="right">控制器IP：</td>
      <td><input name="PostValues[]" type="text" id="PostValues[]" style="width:380px" value="<?php  echo $IP?>" DataType="LimitB" Max="15" Msg="没有填写或超出15个字节的限制"></td>
    </tr>
	<tr>
	  <td height="35" align="right">控制开关：</td>
	  <td><select name="PostValues[]" id="PostValues[]" style="width:380px" DataType="Require" Msg="没有选择">
	    <?php 
        $checkKGSql=mysql_query("SELECT Id,Name FROM $DataPublic.accessguard_order WHERE Estate='1' ORDER BY Id",$link_id);
		if($checkKGRow=mysql_fetch_array($checkKGSql)){
			do{
				if($checkKGRow["Id"]==$OrderId){
					echo"<option value='$checkKGRow[Id]' selected>$checkKGRow[Name]</option>";
					}
				else{
					echo"<option value='$checkKGRow[Id]'>$checkKGRow[Name]</option>";
					}
				}while($checkKGRow=mysql_fetch_array($checkKGSql));
			}
		?>
	    </select></td>
  </tr>
</table>
</td></tr></table>
<?php 
//步骤5：
include "../Admin/subprogram/add_model_b.php";
?>