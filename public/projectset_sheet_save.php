<?php 
//步骤1： $DataIn.development 二合一已更新电信---yang 20120801
include "../model/modelhead.php";
//步骤2：
$Log_Item="研发发项目";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"] = $nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

$YearTemp=substr($StartDate,0,4);
$YearStr=$YearTemp."%";
	//项目编号:以年分隔yyyy****
//锁定表
//$LockSql=" LOCK TABLES $DataIn.development WRITE";$LockRes=@mysql_query($LockSql);
	$maxTemp=mysql_query("SELECT MAX(ItemId) AS MaxId FROM $DataIn.projectset_sheet WHERE ItemId LIKE '$YearStr'",$link_id);
	$ItemId=mysql_result($maxTemp,0,"MaxId");
	if($ItemId==0){
		$ItemId=$YearTemp."0001";
		}
	else{
		$ItemId=$ItemId+1;
		}
	//写入记录
	$FilePath="../download/projectset";
	if(!file_exists($FilePath)){
			makedir($FilePath);
			}
	if($Attached!=""){
		$OldFile=$Attached;
		$FileType=substr("$Attached_name", -4, 4);
		$PreFileName=$ItemId.$FileType;
		$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
	}
	
    $Attached=$uploadInfo==""?0:$uploadInfo;
	$ItemName=FormatSTR($ItemName);
	$Description=FormatSTR($Description);
	
	$Participant="";
	if($_POST['ListId']){//如果指定了操作对象
		$Counts=count($_POST['ListId']);
		for($i=0;$i<$Counts;$i++){
			$thisId=$_POST[ListId][$i];
			$Participant.=$Participant==""?$thisId:",".$thisId;
		}
	}
	
	$inRecode="INSERT INTO $DataIn.projectset_sheet(Id,ItemId,ItemName,TypeId,Principal,Participant,Description,Attached,Amount, StartDate,EstimatedDate, Estate, Date, Operator,creator,created)VALUES(NULL,'$ItemId','$ItemName',
	'$TypeId','$Principal','$Participant','$Description','$Attached','$Amount','$StartDate','$EstimatedDate','2','$djDate','$Operator','$Operator','$DateTime')";
	echo $inRecode;
	$inAction=@mysql_query($inRecode);
	if ($inAction){ 
		$Log="$TitleSTR 成功!<br>";
		} 
	else{
		$Log=$Log."<div class=redB>$TitleSTR 失败!</div><br>";
		$OperationResult="N";
		} 
//解锁表
//$unLockSql="UNLOCK TABLES";$unLockRes=@mysql_query($unLockSql);
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
