<?php   
/*电信---yang 20120801
$upDataSheet
$DataPublic.dimissiondata
$DataPublic.staffmain
$DataIn.online
$DataIn.usertable
$DataIn.upopedom
二合一已更新
*/
//默认
$updateSQL = "UPDATE $upDataSheet SET $SetStr WHERE $upDataSheet.Id='$Id' $OtherWhere";
$updateResult = mysql_query($updateSQL);
if ($updateResult && mysql_affected_rows()>0){

	//ipad用
	$LogIpad = "更新成功";
	$err = "no";
	
	//push
	if(($Log_Item == "加班通知") || ($Log_Item == "电子公告"))
	{
		$rootPath = $_SERVER['DOCUMENT_ROOT'];
		$perPath = "$rootPath/ipdAPI/pushCer/Attendance_ck.pem";
		include "$rootPath/ipdAPI/push_kq.php";
	}
	
	$Log=$Log."&nbsp;&nbsp;ID号为 $Id 的 $ItemName $Log_Item 资料 $Log_Funtion 成功!<br>";
	if($Log_Funtion=="离职"){
		//$inRrecode="INSERT INTO $DataPublic.dimissiondata SELECT NULL,Number,'$theDate','1','$Reason','$LeavedType','0','$Date','$Operator' FROM $DataPublic.staffmain WHERE Id=$Id";
		if ($DataIn=='ac'){
		     $inRrecode="INSERT INTO $DataPublic.dimissiondata SELECT NULL,Number,'$theDate','$LeavedType','$Reason','$LeavedType','0','$Date','$Operator','1','0','$Operator',NOW(),'$Operator',NOW() FROM $DataPublic.staffmain WHERE Id=$Id";
		}
		else{
			 $inRrecode="INSERT INTO $DataPublic.dimissiondata SELECT NULL,Number,'$theDate','$LeavedType','$Reason','$LeavedType','0','$Date','$Operator' FROM $DataPublic.staffmain WHERE Id=$Id";
		}
		$res=@mysql_query($inRrecode);
		if($res){
			$Log.="&nbsp;&nbsp;离职资料存档成功.<br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;离职资料存档失败.</div><br>";
			$OperationResult="N";
			}
		//删除登录相关的资料:在线、用户表、权限表、特殊权限表以及外联的在线、用户表、权限、特殊权限表;其它部门调动、职位调动、考勤调动、默认奖金数据不清除，但不显示
		$delResult = mysql_query("DELETE D,O,U 
		FROM  $DataIn.usertable D 
		LEFT JOIN $DataIn.online O ON O.uId=D.Id 
		LEFT JOIN $DataIn.upopedom U ON D.Id=U.UserId 
		WHERE D.Number IN (SELECT Number FROM $DataPublic.staffmain WHERE Id='$Id')",$link_id);
		if($delResult){
			$Log.="&nbsp;&nbsp;相关系统帐号资料处理成功.<br>";
			//删除外联公司资料???????????
			}
		else{
			$Log.="&nbsp;&nbsp;相关系统帐号资料处理失败.<br>";
			$OperationResult="N";
			}
	    	//皮套账号
			$delptResult = mysql_query("DELETE D,O,U 
			FROM  $DataOut.usertable D 
			LEFT JOIN $DataOut.online O ON O.uId=D.Id 
			LEFT JOIN $DataOut.upopedom U ON D.Id=U.UserId 
			WHERE D.Number IN (SELECT Number FROM $DataPublic.staffmain WHERE Id='$Id')",$link_id);
			if($delptResult){
				$Log.="&nbsp;&nbsp;皮套系统帐号资料处理成功.<br>";
				}
			else{
				$Log.="&nbsp;&nbsp;皮套系统帐号资料处理失败.<br>";
				}
		}
	}
else{
	$Log=$Log."<div class=redB>&nbsp;&nbsp;ID号为 $Id 的 $ItemName $Log_Item 资料 $Log_Funtion 失败! $updateSQL </div><br>";
	$OperationResult="N";
	
	//ipad用
	$LogIpad = "更新失败";
	$err = "yes";
	
	}
?>