<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="产品QC标准图";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 94:
		$Log_Funtion="类图产品剔除";
		$Date=date("Y-m-d");
		if($_POST['ListId']){//如果指定了操作对象
			$Counts=count($_POST['ListId']);
			$Ids="";
			for($i=0;$i<$Counts;$i++){
				$thisId=$_POST[ListId][$i];
				$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
				}
			  $TypeIdSTR="and ProductId IN ($Ids)";
			  $delSql="delete from qcstandardless where QcId='$Id'";
			  $delResult=mysql_query($delSql);
		      $inRecode="INSERT INTO $DataIn.qcstandardless SELECT NULL,ProductId,'$Id','$Date','$Operator',1,0,0,'$Operator',NOW(),'$Operator',NOW() FROM $DataIn.productdata WHERE 1 $TypeIdSTR";
		      $inResult=@mysql_query($inRecode);
		      if($inResult){
			                  $Log.="$Ids&nbsp;&nbsp;类图产品剔除成功! </br>";
		      	          }
		        else{
			                 $Log.="<div class='redB'>&nbsp;&nbsp;类图产品剔除失败!$inRecode</div></br>";
			                 $OperationResult="N";
			           }
		     }
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
