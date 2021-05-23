<?php 
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 劳务公司资料更新");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upResult = mysql_query("SELECT * FROM lw_company  WHERE Id='$Id' LIMIT 1",$link_id); 
if($upRow = mysql_fetch_array($upResult)){
	$CompanyId=$upRow["CompanyId"];
	$Forshort=$upRow["Forshort"];
	$LinkMan=$upRow["LinkMan"];
	$Company=$upRow["Company"];
	$Tel=$upRow["Tel"];
	$Address=$upRow["Address"];
	$Bank=$upRow["Bank"];
	$Remark=$upRow["Remark"];
}
			
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Orderby,$Orderby,Pagination,$Pagination,Page,$Page,CompanyId,$CompanyId";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
     <table width="700" border="0" align="center" cellspacing="5">
  <tr>
    <td width="150"  align="right" >公司名称</td>
    <td colspan="3"><input name="Company" type="text" id="Company" value="<?php  echo $Company?>" style="width:380px;" dataType="LimitB" max="50" min="2" msg="必须在2-50个字节之内"></td>
  </tr>
  <tr>
    <td align="right">公司简称</td>
    <td colspan="3"><input name="Forshort" type="text" id="Forshort" value="<?php  echo $Forshort?>" style="width:380px;" dataType="LimitB" max="20" min="2" msg="必须在2-20个字节之内"></td>
  </tr>
         
  <tr>
    <td align="right">公司电话</td>
    <td colspan="3"><input name="Tel" type="text" id="Tel" value="<?php  echo $Tel?>" style="width:380px;"></td>
  </tr>
  <tr>
    <td align="right">联系人</td>
    <td colspan="3"><input name="LinkMan" type="text" id="LinkMan" value="<?php  echo $LinkMan?>" style="width:380px;"></td>
  </tr>
  <tr>
    <td align="right">地址</td>
    <td ><textarea name="Address" type="text" id="Address"  style="width:380px;" ataType="Limit" max="50" msg="必须在50个字之内"><?php  echo $Address?></textarea></td>
  </tr>
  <tr>
    <td align="right" valign="top">银行帐户</td>
    <td ><textarea name="Bank" style="width:380px;" id="Bank"><?php  echo $Bank?></textarea></td>
  </tr>
  <tr>
    <td align="right" valign="top">备&nbsp;&nbsp;&nbsp;&nbsp;注</td>
    <td ><textarea name="Remark" style="width:380px;" id="Remark"><?php  echo $Remark?></textarea></td>
  </tr>
  </table>
</td></tr></table>
<?php 
include "../model/subprogram/add_model_b.php";
?>