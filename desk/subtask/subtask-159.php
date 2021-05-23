<?php   
//电信-zxq 2012-08-01
/*
功能：统计用户的传真数量
独立已更新:EWEN 2009-12-18 17:05
*/
$fax_result = mysql_query("SELECT * FROM $DataPublic.faxdata WHERE Claimer='$Login_P_Number'",$link_id);
$Nos=0;
$Yess=0;
if ($fax_row = mysql_fetch_array($fax_result)) {
	do{
		$Sign=$fax_row["Sign"];
		if($Sign==1){
			$Nos=$Nos+1;}
		else{
			$Yess=$Yess+1;}
		}while ($fax_row = mysql_fetch_array($fax_result));
	}
/*	
$OutputInfo.= "&nbsp;&nbsp;$i"."、我的传真：未读<span class='yellowN'>".$Nos."</span>/已读<span class='yellowN'>".$Yess."</span>(<a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>详情</a>)<br>";
*/
$OutputInfo.= "&nbsp;&nbsp;$i"."、我的传真：未读<span class='yellowN'>"
 ."</span>(<a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>$Nos</a>)".
 "</span>/已读<span class='yellowN'>"
 ."</span>(<a href='$Extra' target='_blank' style='CURSOR: pointer;color:#FF6633'>$Yess</a>)<br>";
?>