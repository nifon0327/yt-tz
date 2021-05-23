<?php 
//电信-EWEN
include "../model/modelhead.php";
//步骤2：
$Log_Item="固定薪考勤统计";			//需处理

switch($ActionId){
	case 2:  //新增固定薪考勤
		$fromWebPage="kq_office_read";
		break;
	default:
		$fromWebPage="kq_office_count";
		break;
}
$nowWebPage="kq_office_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&CheckMonth=$CheckMonth&Number=$Number&CountType=1";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");

switch($ActionId){
	case 2:  //新增固定薪考勤
		$mainRow=mysql_fetch_array(mysql_query("SELECT M.BranchId,M.JobId
		FROM $DataPublic.staffmain M
		WHERE M.Number='$Number' LIMIT 1",$link_id));
		$BranchId=$mainRow["BranchId"];
		$JobId = $mainRow["JobId"];
		
		//插入上班时间
     	$In_Sql3="INSERT INTO $DataIn.kq_office(Id, BranchId, JobId, Number, CheckTime, CheckType, dFrom, Estate, Locks, ZlSign, KrSign, Operator) VALUES(NULL,'$BranchId','$JobId','$Number','$StartDate $StartTime:00','I','0','0','1','0','0','0')";
		//echo $In_Sql3 .'<br>';
     	$In_Result3=@mysql_query($In_Sql3);		
		//插入下班时间
        $In_Sql4="INSERT INTO $DataIn.kq_office(Id, BranchId, JobId, Number, CheckTime, CheckType, dFrom, Estate, Locks, ZlSign, KrSign, Operator) VALUES(NULL,'$BranchId','$JobId','$Number','$StartDate $EndTime:00','O','0','0','1','0','0','0')";
        //echo $In_Sql4 .'<br>';
		$In_Result4=@mysql_query($In_Sql4);
		$Log.="员工".$Number."考勤".$StartDate."加入"."成功!<br>";
		
		
		break;
	
	
	default:
		echo "default:";
		
		//保存月统计结果
		$inRecode1="INSERT INTO $DataIn.kq_office_data 
		SELECT NULL,Number,'$Dhours','$Whours','$Ghours','$InLates','$OutEarlys','$SJhours',
		'$BJhours','$YXJhours','$WXJhours','$QQhours','$YBs','$WXhours','$KGhours','$dkhours','$CheckMonth','1','$Operator','1',1,0,'$Operator',NOW(),'$Operator',NOW(),null
		FROM $DataPublic.staffmain WHERE Number='$Number' AND Number NOT IN (SELECT Number FROM $DataIn.kqdata WHERE Month='$CheckMonth' and Number='$Number')";
		$inAction1=@mysql_query($inRecode1);
		if ($inAction1){ 
			$Log.="员工".$Number.$chooseMonth."的".$TitleSTR."成功!<br>";
			} 
		else{
			$Log.="<div class=redB>员工".$Number.$chooseMonth."的".$TitleSTR."失败! $inRecode1 </div><br>";
			$OperationResult="N";
			}
			
	break;
	
}

	
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>