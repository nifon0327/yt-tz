<?php
	
	include "../model/modelhead.php";
	//步骤2：
	$Log_Item="上班日期对调记录";			//需处理
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_save";
	$_SESSION["nowWebPage"]=$nowWebPage;
	$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
	//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
	$Log_Funtion="保存";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Operator=$Login_P_Number;
	$OperationResult="Y";

	$NumberSTR="";
	$InFo="全部需要考勤的";
	if($_POST["ListId"])
	{//如果指定
		$Counts=count($_POST["ListId"]);
		$Ids="";
		for($i=0;$i<$Counts;$i++)
		{
			$thisId=$_POST["ListId"][$i];
			$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
		$NumberSTR="AND Number IN ($Ids)";
		$InFo="员工ID在($Ids)的";
	}
	else
	{
		if($JobId!="")
		{
			$NumberSTR="AND JobId='$JobId'";
			$InFo="职位ID为 $JobId 且需要考勤的";
		}
		else{
		if($BranchId!=""){
			$NumberSTR="AND BranchId='$BranchId'";
			$InFo="部门ID为 $BranchId 且需要考勤的";
			}
		}		
	}
	$thisMonth=substr($GDate,0,7);
    $inRecode="INSERT INTO $DataIn.kq_ddsheet Select NULL, $ddId, Number, 1, $Operator,0,0,$Operator,NOW(),$Operator,NOW(),NOW()r From $DataPublic.staffmain
    		   Where 1 $NumberSTR";
    echo $inRecode;
	$inResult=@mysql_query($inRecode);
	if($inResult){
		$Log.="&nbsp;&nbsp;".$InFo."员工".$DayTemp."的".$TitleSTR."成功! </br>";
	}
	else{
		$Log.="<div class='redB'>&nbsp;&nbsp;".$InFo."员工".$DayTemp."的".$TitleSTR."失败!</div></br>";
	$OperationResult="N";
	}
	//操作日志
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
	include "../model/logpage.php";
	
?>