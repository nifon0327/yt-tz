<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新员工逾期签名资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理

$Id = $checkid[0];
$infos = explode('|', $Id);
$staffNumber = $infos[0];
$Month = $infos[1];

$mysql = "SELECT A.*, B.Remark FROM $DataPublic.staffmain A
         Left Join $DataPublic.wage_sign_overtime B On B.Number = A.Number AND B.Month = '$Month'
         WHERE A.Number='$staffNumber'  LIMIT 1";
$upData =mysql_fetch_array(mysql_query($mysql,$link_id));
$Name=$upData["Name"];
$Number=$upData["Number"];
$Reason = $upData["Remark"];
$PayMent = $upData['PayMent'];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤5：//需处理
?>
<input type='hidden' id='Number' name='Number' value='<?php echo $Number;?>'>
<input type='hidden' id='Month' name='Month' value='<?php echo $Month;?>'>
<input type='hidden' id='Name' name='Name' value='<?php echo $Name;?>'>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class='A0011'>
	<table width="700" border="0" align="center" cellspacing="5">
          <tr>
            <td width="112" height="16" scope="col">签名逾期信息</td>
            <td scope="col"> </td>
          </tr>
          <tr>
            <td align="right" scope="col">姓&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td scope="col"><?php  echo $Name;?></td>
          </tr>
          <tr>
            <td align="right" valign="top">扣款</td>
            <td><input type='text' name="PayMent" cols="47" rows="8" id="PayMent" value='<?php  echo $$PayMent?>'></td>
          </tr>
          <tr>
            <td align="right" valign="top">备注</td>
            <td><textarea name="Reason" cols="47" rows="8" id="Reason"><?php  echo $Reason?></textarea></td>
          </tr>
          <tr>
            <td valign="top"><div align="right"><input name='Id' id="Id" type="hidden" value="<?php echo $Id ?>"></div></td>
            <input name='ActionId' id="ActionId" type="hidden" value="3">
            <td>&nbsp;</td>
          </tr>
   </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>