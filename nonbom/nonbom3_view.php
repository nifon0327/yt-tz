<?php 
/*
代码、数据库合并后共享-ZXQ 2012-08-08
*/
//include "../basic/chksession.php" ;
include "../basic/parameter.inc";
function anmaOut($RuleStr,$EncryptStr,$Type){
	$SinkOrder="xacdefghijklmbnopqrstuvwyz";
	$RuleLen = strlen($RuleStr);					//渗透码长度，隔1取1
	for($i=1;$i<$RuleLen;$i++){				
		$inChar=substr($RuleStr,$i,1);				//取出渗透码字符
		$inNum=strpos($SinkOrder,$inChar);			//将 渗透码字母 转为数字
		$oldStr.=substr($EncryptStr,$inNum,1);		//从加密码中读取原文字符
		$i++;
		}
	return $oldStr;
	}
$dArray=explode("|",$d);//ID
$RuleStr2=$dArray[0];
$EncryptStr2=$dArray[1];
$CompanyId=anmaOut($RuleStr2,$EncryptStr2,"d");
$checkResult=mysql_fetch_array(mysql_query("SELECT A.Forshort,A.Date,
										   B.Tel,B.Remark,B.Area,B.Company,B.Fax,B.Website,B.Address,B.Bank,
										   C.Name AS Currency,D.Name AS Operator 
										   FROM $DataPublic.nonbom3_retailermain A 
										   LEFT JOIN $DataPublic.nonbom3_retailersheet B ON B.CompanyId=A.CompanyId
										   LEFT JOIN $DataPublic.currencydata C ON C.Id=A.Currency
										   LEFT JOIN $DataPublic.staffmain D ON D.Number=A.Operator
										   WHERE A.CompanyId ='$CompanyId' LIMIT 1",$link_id));
$Operator=$checkResult["Operator"];
?>
<html>
<head>
<?php 
include "../model/characterset.php";
?>
<link rel="stylesheet" href="../images/BullentidCss.css">
<title>公司详细资料</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
}
.style1 {color: #0033FF}
-->
</style></head>
<body>
<?php 
$linkman_result = mysql_query("SELECT * FROM $DataPublic.nonbom3_retailerlink  where CompanyId=$CompanyId order by  Defaults",$link_id);
?>
<table width="522" height="534" border="0" align="center" cellspacing="0">
  <tr>
    <td width="16" height="68" background="../images/M_viewaddress_r1_c1.gif">&nbsp;</td>
    <td background="../images/M_viewaddress_r1_c2.gif"></td>
    <td width="16" background="../images/M_viewaddress_r1_c3.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="443" background="../images/M_viewaddress_r2_c1.gif"></td>
    <td width="484" valign="top" background="../images/addresslist_back.gif">
		<div align="left">
		国家地区：<?php  echo $checkResult["Area"]?><br>
        客户名称：<?php  echo $checkResult["Company"]?><br>
        客户简称：<?php  echo $checkResult["Forshort"]?><br>
        结付货币：<?php  echo $checkResult["Currency"]?><br>
        电话号码：<?php  echo $checkResult["Tel"]?><br>
        传真号码：<?php  echo $checkResult["Fax"]?><br>
        网&nbsp;&nbsp;&nbsp;&nbsp;址：<?php  echo $checkResult["Website"]?><br>
        通信地址：<?php  echo $checkResult["Address"]?><br>
        银行帐户：<?php  echo $checkResult["Bank"]?><br>
        备&nbsp;&nbsp;&nbsp;&nbsp;注：<?php  echo $checkResult["Remark"]?><br>
        <br>
        <?php 
		$i=1;

      if ($linkman_result) {
		if ($linkman_myrow = mysql_fetch_array($linkman_result)) {
			do{
				$Defaults=$linkman_myrow["Defaults"]=="0"?"(默认)":"";
				
				$Name=$linkman_myrow["Name"]==""?"&nbsp":$linkman_myrow["Name"];
				$Nickname=$linkman_myrow["Nickname"]==""?$Name:$linkman_myrow["Nickname"];
				$Mobile=$linkman_myrow["Mobile"]==""?"&nbsp":$linkman_myrow["Mobile"];
				$MSN=$linkman_myrow["MSN"]==""?"&nbsp":$linkman_myrow["MSN"];
				$SKYPE=$linkman_myrow["SKYPE"]==""?"&nbsp":$linkman_myrow["SKYPE"];
				$Email=$linkman_myrow["Email"]==""?"&nbsp":$linkman_myrow["Email"];
				$Headship=$linkman_myrow["Headship"]==""?"&nbsp":$linkman_myrow["Headship"];
				$Nickname=$linkman_myrow["Email"]==""?$Nickname:"<a href='mailto:$linkman_myrow[Email]'>$Nickname</a>";
				echo "联系人$i"."：$Name$Defaults<br>";
				echo"职 &nbsp;&nbsp;&nbsp;务：$Headship<br>
				昵&nbsp;&nbsp;&nbsp;&nbsp;称：$Nickname<br>
				移动电话：$Mobile<br>				
				邮件地址：$Email<br>
				&nbsp;&nbsp;&nbsp;SKYPE：$SKYPE<br>&nbsp;
				&nbsp;&nbsp;&nbsp;MSN：$MSN<br><br>";
				$i++;
				}while ($linkman_myrow = mysql_fetch_array($linkman_result));
			}
	     }
        ?>
		资料上传日期：<?php  echo $checkResult["Date"]?>&nbsp;&nbsp;&nbsp;&nbsp;操作：<?php  echo $Operator?><br>
    </div></td>
    <td background="../images/M_viewaddress_r2_c3.gif"></td>
  </tr>
  <tr>
    <td height="12" background="../images/M_viewaddress_r3_c1.gif"></td>
    <td background="../images/M_viewaddress_r3_c2.gif"></td>
    <td background="../images/M_viewaddress_r3_c3.gif"></td>
  </tr>
</table>
</body>
</html>
