<?php 
//MC、DP共享$DataIn.电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新维护提醒信息");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData = mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.sys_updateinfo WHERE Id='$Id'",$link_id));
$cRemark=$upData["cRemark"];
$eRemark=$upData["eRemark"];
$Estate=$upData["Estate"];
$TempEstateSTR="EstateSTR".strval($Estate); 
$$TempEstateSTR="selected";	
	
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
          <td width="150" height="40" align="right" scope="col">中文提醒信息</td>
          <td scope="col"><textarea name="cRemark" id="cRemark" style="width:500px;"><?php  echo $cRemark?></textarea></td>
        </tr>
        <tr>
          <td height="40" align="right" scope="col">英文提醒信息</td>
          <td scope="col"><textarea name="eRemark" id="eRemark" style="width:500px;"><?php  echo $Id?></textarea></td>
        </tr>
        <tr>
          <td height="40" align="right" scope="col">可用状态</td>
          <td scope="col"><select name="Estate" id="Estate"  style="width:500px;">
            <option value="1" <?php  echo $EstateSTR1?>>可用</option>
            <option value="0" <?php  echo $EstateSTR0?>>不可用</option>
          </select></td>
        </tr>
      </table></td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>