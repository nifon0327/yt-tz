<?php 
//门禁进出检验：卡号，密码
/*
流程
数据来源判断
是卡号还是密码，或者卡号＋密码

如果是卡号，检查此卡号是否存在门禁帐号，如果无，不处理，如果有，检查是否需要密码，如果需要，则判断密码是否正确，如果正确，则判断此卡号在此门的权限，否则不处理，如果不需要密码，直接判断此帐号在此门的权限

如果是密码，检查此密码是否已经存在门禁帐号，如果无，不处理，如果有，则判断此帐号在此门的权限

如果连续三次密码输入错误，则此读卡器停止使用3分钟
*/
$checkIN="001";
$checkINPsw=MD5($checkIN);
$checkSql=mysql_query("SELECT A.Number,A.chkType,A.Password,B.IdNum  FROM $DataPublic.accessguard_user A LEFT JOIN $DataPublic.staffmain B ON B.Number=A.Number WHERE A.Password='$checkINPsw' OR B.IdNum='$checkIN'  LIMIT 1",$link_id)
if($checkRow=mysql_fetch_array($checkSql)){//如果有帐号：
	$Number=$checkRpw["Number"];
	$chkType=$checkRpw["chkType"];
	
	}
?>