<?php 
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新年限资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
$upData =mysql_fetch_array(mysql_query("SELECT * FROM $DataPublic.nonbom6_nx WHERE Id='$Id' LIMIT 1",$link_id));
$Frequency=$upData["Frequency"];
$days=$upData["days"];
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr><td class="A0011">
      <table width="750" height="140" border="0" align="center" cellspacing="5">
        <tr>
          <td height="40" valign="middle" scope="col" align="right">年&nbsp;&nbsp;&nbsp;&nbsp;限</td>
          <td valign="middle" scope="col"><input name="Frequency" type="text" id="Frequency" value="<?php  echo $Frequency?>" style="width:380px" maxlength="30" datatype="LimitB" max="30" min="2" msg="没有填写或超出2-30个字节的范围" title="必填项,2-30个字节的范围">
          </td>
        </tr>
		<tr>
			<td  height="40" valign="middle" scope="col" align="right">天&nbsp;&nbsp;&nbsp;&nbsp;数</td>
			<td valign="middle" scope="col"><input name="days" type="text" id="days" value="<?php echo $days?>" style="width:380px;"  dataType="Number"  msg="天数不正确">
			</td>
		</tr>
              </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>