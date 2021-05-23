<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="新增外发供应商配件";		//需处理
$upDataSheet="$DataIn.stuffout";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 1:
		$Log_Funtion="新增外发供应商配件";
		if($_POST['ListId']){//如果指定了操作对象
			$Counts=count($_POST['ListId']);
			$Ids="";
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST[ListId][$i];
				$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
				}
			$TypeIdSTR="and StuffId IN ($Ids)";
			//将productqcimg表中以前上传的记录，删除。
			 $delSql="delete from $upDataSheet where 1 $TypeIdSTR";
			 $delResult=mysql_query($delSql);
                         
		     $inRecode="INSERT INTO $upDataSheet SELECT NULL,StuffId,'$Date','$Operator','1','0','0','$Operator','$DateTime',null,null FROM $DataIn.Stuffdata WHERE 1 $TypeIdSTR";
		    $inResult=@mysql_query($inRecode);
		    if($inResult){
			        $Log.="$Ids&nbsp;&nbsp;外发供应商配件新增成功! </br>";
		      	   }
		    else{
			       $Log.="<div class='redB'>&nbsp;&nbsp;外发供应商配件新增失败!</div></br>";
			       $OperationResult="N";
			     }
		    }
		break;
	}
include "../model/logpage.php";
?>
