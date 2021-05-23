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
$fArray=explode("|",$c);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Company=anmaOut($RuleStr1,$EncryptStr1,"f");
//echo "Company:$Company <br>";

$dArray=explode("|",$d);//ID
$RuleStr2=$dArray[0];
$EncryptStr2=$dArray[1];
$Id=anmaOut($RuleStr2,$EncryptStr2,"d");

//echo "Id:$Id";

if ($Company=="freightdata" || $Company=="dealerdata"){
	if($Company=="freightdata") { 
	    //echo "SELECT * FROM $DataPublic.$Company WHERE Id ='$Id' LIMIT 1";
		$CompanyResult=mysql_query("SELECT * FROM $DataIn.$Company WHERE Id ='$Id' LIMIT 1",$link_id);
	}
	else  $CompanyResult=mysql_query("SELECT * FROM $DataIn.$Company WHERE Id ='$Id' LIMIT 1",$link_id);
}
else{
	$CompanyResult=mysql_query("SELECT * FROM  $Company WHERE Id ='$Id' LIMIT 1",$link_id);
}

if ($CompanyResult){
	$CompanyRow = mysql_fetch_array($CompanyResult);
    $Operator=$CompanyRow["Operator"];
    $CompanyId=$CompanyRow["CompanyId"];
}
//echo "SELECT * FROM $DataIn.$Company WHERE Id ='$Id' LIMIT 1";
$CompanyInfo=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.companyinfo WHERE CompanyId ='$CompanyId' AND Type=8",$link_id));
$pRow = mysql_fetch_array(mysql_query("SELECT Name FROM $DataIn.staffmain WHERE Number='$Operator' Limit 1",$link_id));
$ProviderInfo=mysql_fetch_array(mysql_query("SELECT * FROM $DataIn.providersheet WHERE CompanyId ='$CompanyId' Limit 1",$link_id));
$Operator=$pRow["Name"];
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
$linkman_result = mysql_query("SELECT * FROM $DataIn.linkmandata  where CompanyId=$CompanyId AND Type = 8 order by  Defaults",$link_id);

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
		<div align="left" style='line-height:23px;vertical-align: middle;'>
		国家地区：<?php  echo $CompanyInfo["Area"]?><br>
        客户名称：<?php  echo $CompanyInfo["Company"]?><br>
        客户简称：<?php  echo $CompanyRow["Forshort"]?><br>
        电话号码：<?php  echo $CompanyInfo["Tel"]?><br>
        传真号码：<?php  echo $CompanyInfo["Fax"]?><br>
        网&nbsp;&nbsp;&nbsp;&nbsp;址：<?php  echo $CompanyInfo["Website"]?><br>
        通信地址：<?php  echo $CompanyInfo["Address"]?><br>
        快递帐户：<?php  echo $CompanyInfo["ExpNum"]?><br>
        银行名称：<?php  echo $CompanyInfo["Bank"]?><br>
        开&nbsp;户&nbsp;&nbsp;名：<?php  echo $CompanyInfo["BankUID"]?><br>
        银行帐号：<?php  echo $CompanyInfo["BankAccounts"]?><br>
        发票税点：<?php  echo $ProviderInfo["InvoiceTax"]?>%<br>
        备&nbsp;&nbsp;&nbsp;&nbsp;注：<?php  echo $CompanyInfo["Remark"]?><br>
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
		资料上传日期：<?php  echo $CompanyRow[Date]?>&nbsp;&nbsp;&nbsp;&nbsp;操作：<?php  echo $Operator?><br>
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
