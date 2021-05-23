<?php   
//电信-EWEN
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
//模板基本参数:模板$Login_WebStyle

ChangeWtitle("$SubCompany 订单锁定保存");
$Log_Funtion="更新";
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Log_Item="订单锁定";
$x=1;
$y=1;
$OperationResult="N";
switch ($Action){
	case "Lock":
        $Log_Funtion="未确定产品标记或取消标记";
		switch($UPType){
			case 1:
				$Remark="客户取消订单 ".$Remark."";
				break;				
			case 2:
				$Remark="产品未确定 ".$Remark."";
				break;
			
			default:
			break;
		}
		$Type=2;
           if($myLock==1){
					$DelSql="DELETE FROM $DataIn.yw2_orderexpress WHERE Type='$Type' AND POrderId=$POrderId";
					$DelResult=mysql_query($DelSql);
					if($DelResult){
					   //更新未下采单时间
					   $upcgSql="UPDATE $DataIn.cg1_stocksheet S 
					           LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
	                           LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
					           SET S.ywOrderDTime=NOW() 
					           WHERE S.POrderId=$POrderId AND T.mainType<2 AND S.Mid=0";
					     $upResult=mysql_query($upcgSql);     		   
						}
                  }
        else{
					  $UpdateRecode="Update  $DataIn.yw2_orderexpress SET  Remark='$LockRemark',Estate=1,ReturnReasons=''  WHERE   POrderId=$POrderId";
					  $UpdateResult=@mysql_query($UpdateRecode);
					}

	break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=mysql_query($IN_recode);	
?>