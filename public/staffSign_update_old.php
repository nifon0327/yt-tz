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

$upData =mysql_fetch_array(mysql_query("
SELECT A.*, B.Name FROM $DataPublic.wage_sign_overtime A
Left Join $DataPublic.staffmain B On B.Number = A.Number
WHERE A.Id='$Id' LIMIT 1",$link_id));

$Name=$upData["Name"];
$Number=$upData["Number"];
$Reason = $upData["Remark"];
$payment = $upData["PayMent"];

//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Number,$Number";
//步骤5：//需处理
?>
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
            <td align="right">扣款金额</td>
            <td><input name="pay" type="text" id="pay" value="<?php  echo $payment?>" size="40" maxlength="10"></td>
          </tr>
          <tr>
            <td align="right" valign="top">修改原因</td>
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