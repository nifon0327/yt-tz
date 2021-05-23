<?php 
//电信-joseph
//步骤1
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="考核试题";		//需处理
$upDataSheet="$DataPublic.aqsc05";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 5:
		$Log_Funtion="可用";	$SetStr="Estate=1,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 6:
		$Log_Funtion="禁用";	$SetStr="Estate=0,Locks=0";		include "../model/subprogram/updated_model_3d.php";		break;
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 139:
		$fromWebPage=$funFrom."_online";
		$ALType="ActionId=$ActionId&funFrom=$funFrom";
		//写入考核记录
		$inRecode=$DataIn !== 'ac' ? "INSERT INTO $DataPublic.aqsc09 SELECT NULL,'$DateTime','0','0','',Number,'0','','','$DateTime','1','0','$Operator' FROM $DataPublic.staffmain WHERE Name='$Name' LIMIT 1" : 
		                             "INSERT INTO $DataPublic.aqsc09 SELECT NULL,'$DateTime','0','0','',Number,'0','','','$DateTime','1','0','$Operator',0,'$Operator',NOW(),'$Operator',NOW() FROM $DataPublic.staffmain WHERE Name='$Name' LIMIT 1";
		$inAction=@mysql_query($inRecode);
		$ExamId=mysql_insert_id();
		if($ExamId>0){//主记录保存成功
			$Log.="考核记录保存dn 功.<br>";
			//写入电子试题答卷
			for($i=1;$i<21;$i++){//单选题20题
				$KT1="radio".strval($i); 
				$theValue=$$KT1;
				$Aqsc05_Id=$KtIdA[$i];
				$inRecode=$DataIn !== 'ac' ? "INSERT INTO $DataPublic.aqsc09_sheet SELECT NULL,'$ExamId',Id,TypeId,'$theValue',Answer,'0' FROM $DataPublic.aqsc05 WHERE Id='$Aqsc05_Id' LIMIT 1" : 
				                             "INSERT INTO $DataPublic.aqsc09_sheet SELECT NULL,'$ExamId',Id,TypeId,'$theValue',Answer,'0', 1,0,0,null,NOW(),null,NOW(),NOW(),null FROM $DataPublic.aqsc05 WHERE Id='$Aqsc05_Id' LIMIT 1";;
				$inAction=@mysql_query($inRecode);
				if($inAction){
					$Log.="单选题".$i."的记录加入成功.<br>";
					}
				else{
					$Log.="<div class='redB'>单选题".$i."的记录加入失败. $inRecode</div><br>";
					}
				}
			//多选题10题
			for($i=1;$i<11;$i++){//单选题20题
				$KT2="checkbox".strval($i); 
				$KT2Array=$_POST[$KT2];
				$theValue="";
				foreach($KT2Array as $Label=>$Value){
					$theValue.=$Value;
					}
				$Aqsc05_Id=$KtIdB[$i];
				//写入复选题答题记录
				$inRecode=$DataIn !== 'ac' ? "INSERT INTO $DataPublic.aqsc09_sheet SELECT NULL,'$ExamId',Id,TypeId,'$theValue',Answer,'0' FROM $DataPublic.aqsc05 WHERE Id='$Aqsc05_Id' LIMIT 1" : 
				                             "INSERT INTO $DataPublic.aqsc09_sheet SELECT NULL,'$ExamId',Id,TypeId,'$theValue',Answer,'0', 1,0,0,null,NOW(),null,NOW(),NOW(),null FROM $DataPublic.aqsc05 WHERE Id='$Aqsc05_Id' LIMIT 1";;
				$inAction=@mysql_query($inRecode);
				if($inAction){
					$Log.="多选题".$i."的记录加入成功.<br>";
					}
				else{
					$Log.="<div class='redB'>多选题".$i."的记录加入失败. $inRecode<div><br>";
					}
				}
			//更新得分记录
			$upSql = "UPDATE $DataPublic.aqsc09_sheet SET Grade=(CASE WHEN TypeId=1 THEN '2.5' ELSE '5' END) WHERE ExamId='$ExamId' AND theAnswer=DefaultAnswer";//当答案一致时，更新得分
			$upResult = mysql_query($upSql);
			if($upResult){
				$Log.="考核试题评分成功.<br>";
				}
			else{
				$Log.="<div class='redB'>考核试题评分失败.$upSql </div><br>";
				}
			//更新主记录总得分
			$upSql = "UPDATE $DataPublic.aqsc09 SET Results=(SELECT SUM(Grade) AS Results FROM $DataPublic.aqsc09_sheet WHERE ExamId='$ExamId') WHERE Id='$ExamId'";
			$upResult = mysql_query($upSql);
			if($upResult){
				$Log.="考核试题总分更新成功.<br>";
				}
			else{
				$Log.="<div class='redB'>考核试题总分更新失败.$upSql </div><br>";
				}
			}
		else{
			$Log="<div class='redB'>考核记录保存失败! $inRecode</div><br>";
			}
		break;
	default:
		$Remark=FormatSTR($Remark);
		$Name=FormatSTR($Name);
		$SetStr="TypeId='$TypeId',TestQuestions='$TestQuestions',Answer='$Answer',Date='$DateTime',Locks='0',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>