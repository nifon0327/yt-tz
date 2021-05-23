<?php 
/*
代码、数据库合并后共享-ZXQ 2012-08-08
$DataPublic.staffmain
$DataPublic.staffsheet
$DataPublic.paybase 
二合一已更新，注意跨库多表删除不能使用别名
*/
//步骤1：初始化参数、页面基本信息及CSS、javascrip函数
include "../model/modelhead.php";
include "public_appconfig.php";

$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"];
//步骤2：
$Log_Item="员工资料";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		//可以删除员工的条件：不能在订单、采购表、社保、行政费用等其它表中出现；原则上，只对刚入职的员工可以做删除操作，其它的只能使用离职
		$sql = "SELECT M.Number,M.Name,S.Photo,S.IdcardPhoto,S.HealthPhoto 
		FROM $DataPublic.staffmain M,$DataPublic.staffsheet S WHERE M.Id='$Id' and M.Number=S.Number";
		$myrow = mysql_fetch_array(mysql_query($sql)); 		
		$Name=$myrow["Name"];
		$Number=$myrow["Number"];
		$Photo=$myrow["Photo"]==1?"P".$Number.".jpg":"";
		$IdcardPhoto=$myrow["IdcardPhoto"]==1?"C".$Number.".jpg":"";
		$HealthPhoto=$myrow["HealthPhoto"]==1?"H".$Number.".jpg":"";
		$Del = "DELETE $DataPublic.staffmain,$DataPublic.staffsheet,$DataPublic.paybase FROM $DataPublic.staffmain
		LEFT JOIN $DataPublic.staffsheet ON $DataPublic.staffsheet.Number=$DataPublic.staffmain.Number 
		LEFT JOIN $DataPublic.paybase ON $DataPublic.paybase.Number=$DataPublic.staffmain.Number 
		WHERE $DataPublic.staffmain.Id='$Id'"; 
		$result = mysql_query($Del);
		if ($result){
			$Log="$x-1 员工 $Name 的资料已经删除!<br>";
			if($Photo!=""){
				$Photo=$StaffPhotoPath . $Photo;
				unlink($Photo);
				$Log.="&nbsp;&nbsp;$x-2 相关的员工相片已经删除!<br>";
				}
			if ($IdcardPhoto!=""){
				$IdcardPhoto=$StaffPhotoPath . $IdcardPhoto;
				unlink($IdcardPhoto);
				$Log.="&nbsp;&nbsp;$x-3 相关的员工身份证扫描档已经删除!<br>";
				}
			if ($HealthPhoto!=""){
				$HealthPhoto=$StaffPhotoPath . $HealthPhoto;
				unlink($HealthPhoto);
				$Log.="&nbsp;&nbsp;$x-4 相关的试用期健康证扫描档已经删除!<br>";
				}
			$y++;
			}
		else{
			$Log.="<div class='redB'>$x - 员工 $Name 的".$Log_Item."删除失败. $Del </div><br>";
			$OperationResult="N";
			}//end if ($result)
		$x++;
		}//end if ($Id!="")
	}//end for($i=1;$i<$IdCount;$i++)	
//操作日志
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Pagination=$Pagination&Page=$Page";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>