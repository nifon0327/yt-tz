<?php   
//电信-zxq 2012-08-01
session_start();
$MyPDOEnabled = 1;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$Log_Item="备品入库审核";			//需处理
$Log_Funtion="状态更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$DateTemp = date("Ymd");
$Operator=$Login_P_Number;
$OperationResult="Y";

switch($ActionId){

	case 17://审核通过
	        $myResult=$myPDO->query("CALL proc_ck7_bprk_updatedestate('$Id',$Operator);");
			$myRow = $myResult->fetch(PDO::FETCH_ASSOC);
			$OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:$OperationResult;
		    $myResult=null;
		    $myRow=null; 
		    echo $OperationResult;
		break;
		
        case 15://审核退回
           $Sql = "UPDATE $DataIn.ck7_bprk SET Estate = 2,ReturnReason='$Remark'  
           WHERE Id =$Id AND Estate = 1";

		  $Result = $myPDO->exec($Sql);
		  if($Result){
              echo "Y";
           }else{
	           echo "N";
           }
       	break;
}
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
