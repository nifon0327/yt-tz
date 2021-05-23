<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"];
//步骤2：
$Log_Item="劳务工资料";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;$y=0;
$StaffPhotoPath="../download/lw_staffPhoto/";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$sql = "SELECT M.Number,M.Name,S.Photo,S.IdcardPhoto,S.HealthPhoto 
		FROM $DataPublic.lw_staffmain M
		LEFT JOIN $DataPublic.lw_staffsheet S ON M.Number=S.Number
		WHERE M.Id='$Id' ";
		$myrow = mysql_fetch_array(mysql_query($sql)); 		
		$Name=$myrow["Name"];
		$Number=$myrow["Number"];
		$Photo=$myrow["Photo"]==1?"P".$Number.".jpg":"";
		$IdcardPhoto=$myrow["IdcardPhoto"]==1?"C".$Number.".jpg":"";
		$HealthPhoto=$myrow["HealthPhoto"]==1?"H".$Number.".jpg":"";
		$Del = "DELETE $DataPublic.lw_staffmain,$DataPublic.lw_staffsheet
		FROM $DataPublic.lw_staffmain
		LEFT JOIN $DataPublic.lw_staffsheet ON $DataPublic.lw_staffsheet.Number=$DataPublic.lw_staffmain.Number 
		WHERE $DataPublic.lw_staffmain.Id='$Id'"; 
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