<?php 
//电信-zxq 2012-08-01
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
/*	
$fArray=explode("|",$c);
$RuleStr1=$fArray[0];
$EncryptStr1=$fArray[1];
$Company=anmaOut($RuleStr1,$EncryptStr1,"f");
*/
$dArray=explode("|",$d);//ID
$RuleStr2=$dArray[0];
$EncryptStr2=$dArray[1];
$Id=anmaOut($RuleStr2,$EncryptStr2,"d");

//$Id=$Mid;
$CompanyRow = mysql_fetch_array(mysql_query("SELECT Company,Name,Tel,Fax,Address,Remark   FROM $DataPublic.company_assets WHERE Mid=$Id ",$link_id));

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
		公司名称：<?php  echo $CompanyRow["Company"]?><br>
        联&nbsp;系&nbsp;人：<?php  echo $CompanyRow["Name"]?><br>
        电话号码：<?php  echo $CompanyRow["Tel"]?><br>
        传真号码：<?php  echo $CompanyRow["Fax"]?><br>
        通信地址：<?php  echo $CompanyRow["Address"]?><br>
        备&nbsp;&nbsp;&nbsp;&nbsp;注：<?php  echo $CompanyRow["Remark"]?><br>
        <br>
  
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
