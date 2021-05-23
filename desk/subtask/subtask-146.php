<?php   
//电信-zxq 2012-08-01
/*
功能：系统未处理的新传单
独立已更新:EWEN 2009-12-18 17:04
*/
$FaxRresult = mysql_fetch_array(mysql_query("SELECT count(*) FROM $DataPublic.faxdata WHERE Sign=1 AND Claimer='' AND ClaimDate='0000-00-00 00:00:00'",$link_id));
$Nos=$FaxRresult[0];
/*
$OutputInfo.= "&nbsp;&nbsp;$i"."、未处理的传真：<span class='yellowN'>".$Nos."</span> 份(<a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>详情</a>)<br>";
*/
$OutputInfo.= "&nbsp;&nbsp;$i"."、未处理的传真：<span class='yellowN'>"
."<a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>$Nos</a>".
"</span> 份 <br>";

?>