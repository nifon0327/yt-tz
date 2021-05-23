<?php 
//电信-EWEN
if($ChickIn==""){//正常:签到检查
	//情况1:为第一个签到记录
	$CHECKSIGN="<div align='center' class='greenB'>上班签到1</div>";
	$ChickIn=$CheckTime;
	$CheckNext= mysql_query("SELECT CheckTime,CheckType FROM $DatatIn.checkiotemp WHERE 1 and Number=$Number and CheckTime>'$CheckTime' order by CheckTime limit 0,1",$link_id);
	if($CheckNextRow = mysql_fetch_array($CheckNext)){//如果后面有签卡记录			
		if($CheckNextRow["CheckType"]=="I"){
			$CHECKSIGN="<div align='center' class='redB'>上班签到2</div>";
			$UnusualInfo="<div align='center' class='redB'>无效记录</div>";
			$UpdateInfo="<div class='redB'>删除或补下班记录</div>";
			}
		}
	else{
		$CHECKSIGN="<div align='center' class='redB'>上班签到3</div>";
		$UnusualInfo="<div align='center' class='redB'>无效记录</div>";
		$UpdateInfo="<div class='redB'>删除或补下班记录</div>";
		}
	}
else{//如果ChickIn已经存在，则当前记录可能是重复记录
	$CHECKSIGN="<div align='center' class='redB'>上班签到4</div>";
	$UnusualInfo="<div align='center' class='redB'>重复记录</div>";
	$UpdateInfo="<div class='redB'>删除或改下班签退</div>";
	}	
?>