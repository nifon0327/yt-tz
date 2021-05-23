<?php 
//电信-ZX  2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
//步骤2：
ChangeWtitle("$SubCompany 更新联系人资料");//需处理
$fromWebPage=$funFrom."_read";		
$nowWebPage =$funFrom."_update";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤3：//需处理
switch($Type){
	case 2:$CompanyType="客&nbsp;&nbsp;&nbsp;&nbsp;户";$CompanyData="$DataIn.trade_object";$SearchRows="AND  (cSign=$Login_cSign OR cSign=0)";break;
	case 3:$CompanyType="供 应 商";$CompanyData="$DataIn.trade_object";$SearchRows="AND (cSign=$Login_cSign OR cSign=0)";break;
	case 4:$CompanyType="Forward";$CompanyData="$DataPublic.freightdata";break;
	case 5:$CompanyType="快递公司";$CompanyData="$DataPublic.freightdata";break;
	}
$result = mysql_query("SELECT CompanyId AS cId,Forshort AS cName FROM $CompanyData WHERE Estate='1' $SearchRows ORDER BY Id",$link_id);

$upData =mysql_fetch_array(mysql_query("SELECT CompanyId,Name,Defaults,Headship,Nickname,Sex,Mobile,Tel,Email,SKYPE,MSN,Remark FROM $DataIn.linkmandata WHERE Id='$Id' order by Id LIMIT 1",$link_id));
$CompanyId=$upData["CompanyId"];
$Name=$upData["Name"];
$DefaultsSTR=$upData["Defaults"]==1?"checked":"";
$Headship=$upData["Headship"];
$Nickname=$upData["Nickname"];
$Sex=$upData["Sex"];
$Mobile=$upData["Mobile"];
$Tel=$upData["Tel"];
$Email=$upData["Email"];
$SKYPE=$upData["SKYPE"];
$MSN=$upData["MSN"];
$Remark=$upData["Remark"];
$TempSex="SexSTR".strval($Sex);
$$TempSex="selected";
//步骤4：
$tableWidth=850;$tableMenuS=500;
include "../model/subprogram/add_model_t.php";
$Parameter="Id,$Id,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page,Type,$Type,ComeFrom,$ComeFrom";
//步骤5：//需处理
?>
<table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"><tr><td class="A0011">
      <table width="700" border="0" align="center" cellspacing="5">
      <tr><td align='right'><?php  echo $CompanyType?></td><td colspan='3'>
		<select name="CompanyId" id="CompanyId" style="width: 380px;">
		<?php  
		if($myrow = mysql_fetch_array($result)){
			do{
				if($myrow["cId"]==$CompanyId){
					echo"<option value='$myrow[cId]' selected>$myrow[Letter] $myrow[cName]</option>";
					}
				else{
					echo"<option value='$myrow[cId]'>$myrow[Letter] $myrow[cName]</option>";
					}
				} while ($myrow = mysql_fetch_array($result));
			}
		?>
		</select> </td></tr>
        <tr>
          <td width="90" align="right">联 系 人</td>
          <td><input name="Linkman" type="text" id="Linkman" value="<?php  echo $Name?>" style="width: 150px;" dataType="Limit" max="20" min="2" msg="必须在2-20个字之内"></td>
          <td align="right">性&nbsp;&nbsp;&nbsp;&nbsp;别</td>
          <td>
            <select name="Sex" id="Sex" style="width:150px">
              <option value="0" <?php  echo $SexSTR0?>>女</option>
			  <option value="1" <?php  echo $SexSTR1?>>男</option>
            </select>
		</td>
        </tr>
        <tr>
          <td align="right">职&nbsp;&nbsp;&nbsp;&nbsp;务</td>
          <td width="159"><input name="Headship" type="text" id="Headship" style="width: 150px;" value="<?php  echo $Headship?>" maxlength="20"></td>
          <td width="57" align="right">昵&nbsp;&nbsp;&nbsp;&nbsp;称</td>
          <td width="361"><input name="Nickname" type="text" id="Nickname" style="width: 150px;" value="<?php  echo $Nickname?>" maxlength="20"></td>
        </tr>
        <tr>
          <td align="right">移动电话</td>
          <td><input name="Mobile" type="text" id="Mobile" style="width: 150px;" value="<?php  echo $Mobile?>"></td>
          <td align="right">固定电话</td>
          <td><input name="Tel" type="text" id="Tel" style="width: 150px;" value="<?php  echo $Tel?>"></td>
        </tr>
        <tr>
          <td align="right">MSN</td>
          <td colspan="3"><input name="MSN" type="text" id="MSN" style="width: 380px;" value="<?php  echo $MSN?>" require="false" dataType="Email" msg="MSN格式不正确"></td>
        </tr>
        <tr>
          <td align="right">SKYPE</td>
          <td colspan="3"><input name="SKYPE" type="text" id="SKYPE" style="width: 380px;" value="<?php  echo $SKYPE?>"></td>
        </tr>
        <tr>
          <td align="right">邮件地址</td>
          <td colspan="3"><input name="Email" type="text" id="Email" style="width: 380px;" value="<?php  echo $Email?>" require="false" dataType="Email" msg="信箱格式不正确"></td>
        </tr>
        <tr>
          <td valign="top" align="right">说&nbsp;&nbsp;&nbsp;&nbsp;明</td>
          <td colspan="3"><textarea name="Remark" style="width: 380px;" rows="4" id="Remark"><?php  echo $Remark?></textarea></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan="3"><input name="Defaults" type="checkbox" id="Defaults" value="1" <?php  echo $DefaultsSTR?>><LABEL for="Defaults">非默认联系人</LABEL></input></td>
        </tr>
      </table>
</td></tr></table>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>