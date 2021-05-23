<?php   
include "../model/modelhead.php";
$fromWebPage=$funFrom."_deliverying";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="The Bill of   Delivery";		//需处理
$Log_Funtion="Updated";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){		
	case 141://Reback
          $CheckNumber=mysql_fetch_array(mysql_query("SELECT  * FROM $DataIn.ch1_deliverymain WHERE Id=$Id",$link_id));
          $DeliveryNumber=$CheckNumber["DeliveryNumber"];
	      $DelSql="DELETE M,S,P FROM $DataIn.ch1_deliverymain M 
		  LEFT JOIN $DataIn.ch1_deliverysheet S ON S.Mid=M.Id
		  LEFT JOIN $DataIn.ch1_deliverypacklist P ON P.Mid=M.Id
		  WHERE M.Id='$Id'";
		$DelResult=@mysql_query($DelSql);
		if($DelResult){
             $UpdateSql="UPDATE $DataIn.skech_deliverymain SET Estate=1 WHERE  DeliveryNumber=$DeliveryNumber";
			 $UpdateResult=@mysql_query($UpdateSql);
		     $Log.="Reback succeed<br>";
		     }
		else{
		    $Log.="<div class='redB'>Reback Failed</div><br>";
			$OperationResult="N";
		    }
	 break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
