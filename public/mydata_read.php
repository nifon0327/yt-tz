<?php 
/*电信---yang 20120801
$DataPublic.staffmain
$DataPublic.staffsheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$funFrom="mydata";
ChangeWtitle("$SubCompany 更新登录资料");//需处理
$nowWebPage =$funFrom."_read";	
$toWebPage  =$funFrom."_updated";	
$_SESSION["nowWebPage"]=$nowWebPage; 
$Parameter="fromWebPage,$fromWebPage,funFrom,$funFrom,From,$From,Pagination,$Pagination,Page,$Page";
//步骤3：
$tableWidth=850;$tableMenuS=500;
$isBack="N";
?>
<script>
function UpItem(Action){
	if(Action==1){
		for(var i=4;i<8;i++){
			if(form1.elements[i].disabled==true)
				form1.elements[i].disabled=false;
			else
				form1.elements[i].disabled=true;
			}
		}
	else{
		for(var i=9;i<15;i++){
			if(form1.elements[i].disabled==true)
				form1.elements[i].disabled=false;
			else
				form1.elements[i].disabled=true;
			}
		}
	}
</script>
<div><table border="0" width="<?php  echo $tableWidth?>" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td width="300" height="30" align="right" valign="bottom" class=""><LABEL for="Action1">登录帐号更新</LABEL></td>
    <td valign="bottom"><input name="Action" type="checkbox" id="Action1" value="1" onclick="UpItem(1)"></td>
    <td class="">&nbsp;</td>
  </tr>
          <tr>
		  	<td height="26" align="right" class="">新 帐 号：</td>
            <td scope="col"><input name="uName" type="text" id="uName" size="38" maxlength="15" require="false" dataType="Username" msg="ID名不符合规定" disabled></td>
			<td width="10" class="">&nbsp;</td>
          </tr>
          <tr>
		  	<td height="26" align="right" class="">原 密 码： </td>
            <td scope="col"><input name="oldPassword" type="password" id="oldPassword" size="40" maxlength="32" dataType="Require" Msg="未填写原密码" disabled></td>
			<td width="10" class="">&nbsp;</td>
          </tr>
          <tr>
		  	<td height="26" align="right" class="">新 密 码：</td>
            <td scope="col"><input name="newPassword" type="password" id="newPassword" size="40" maxlength="32" dataType="SafeString"   msg="密码不符合安全规则" disabled></td>
			<td width="10" class="">&nbsp;</td>
          </tr>
          <tr>
		  	<td height="26" align="right" class="">密码确认：</td>
            <td scope="col"><input name="RePassword" type="password" id="RePassword" size="40" maxlength="32" dataType="Repeat" to="newPassword" msg="两次输入的密码不一致" disabled></td>
			<td width="10" class="">&nbsp;</td>
          </tr>
			<?php 
			$checkMydate=mysql_fetch_array(mysql_query("SELECT M.Name,M.Nickname,S.Dh,S.Mobile,M.ExtNo,M.Mail 
			FROM $DataPublic.staffmain M LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number WHERE M.Number='$Login_P_Number' LIMIT 1",$link_id));
			$Name=$checkMydate["Name"];
			$Nickname=$checkMydate["Nickname"];
			$Dh=$checkMydate["Dh"];
			$ExtNo=$checkMydate["ExtNo"];
			$Mobile=$checkMydate["Mobile"];
			$Mail=$checkMydate["Mail"];
			?>
          <tr>
            <td height="30" align="right" valign="bottom" class=""><LABEL for="Action2">人事资料更新</LABEL></td>
            <td valign="bottom"><input name="Action" type="checkbox" id="Action2" value="2" datatype="Group" min="1" max="1"  msg="必须选择一更新项目" onclick="UpItem(2)"></td>
            <td class="">&nbsp;</td>
          </tr>
          <tr>
            <td height="26" align="right" class="">我的姓名：</td>
            <td scope="col"><input name="Name" type="text" id="Name" size="38" maxlength="4" value="<?php  echo $Name?>" dataType="Chinese" msg="未填或非中文" disabled></td>
            <td class="">&nbsp;</td>
          </tr>
          <tr>
            <td height="26" align="right" class="">英文名称：</td>
            <td scope="col"><input name="Nickname" type="text" id="Nickname" size="38" maxlength="20" value="<?php  echo $Nickname?>" disabled title="寄国外快递等功能时必须使用"></td>
            <td class="">&nbsp;</td>
          </tr>
          <tr>
            <td height="26" align="right" class="">手机短号：</td>
            <td scope="col"><input name="Dh" type="text" id="Dh" size="38" maxlength="10" value="<?php  echo $Dh?>" disabled></td>
            <td class="">&nbsp;</td>
          </tr>
          <tr>
            <td height="26" align="right" class="">手机号码：</td>
            <td scope="col"><input name="Mobile" type="text" id="Mobile" size="38" maxlength="11" value="<?php  echo $Mobile?>" disabled></td>
            <td class="">&nbsp;</td>
          </tr>
          <tr>
            <td height="26" align="right" class="">邮件地址：</td>
            <td scope="col"><input name="Mail" type="text" id="Mail" size="38" maxlength="40" value="<?php  echo $Mail?>" disabled></td>
            <td class="">&nbsp;</td>
          </tr>
          <tr>
            <td height="26" align="right" class="">分机号码：</td>
            <td scope="col"><input name="ExtNo" type="text" id="ExtNo" size="38" maxlength="40" value="<?php  echo $ExtNo?>" disabled></td>
            <td class="">&nbsp;</td>
          </tr>
</table></div>
<?php 
//步骤5：
include "../model/subprogram/add_model_b.php";
?>