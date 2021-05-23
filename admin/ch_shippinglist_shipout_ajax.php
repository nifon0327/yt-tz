<?php   
//电信-zxq 2012-08-01
//MC、DP共享代码
include "../model/modelhead.php";
$fromWebPage="ch_shippinglist_read";
$nowWebPage="ch_shippinglist_shipout_ajax";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="提货单";		//需处理
$upDataSheet="delivery_sheet";	//需处理
$Log_Funtion="生成";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
switch($ActionId){
	case 31://新增提货数据
	 $ArrId=explode("|",$Id);
	 $ArrQty=explode("|",$Qty);
	 $sLen=count($ArrId); 	  
	 if (count($ArrQty)==$sLen && $sLen>0){
	     $MainRecode="INSERT INTO $DataIn.ch1_deliverymain(Id, CompanyId, ModelId,ForwaderId, DeliveryNumber, Remark, DeliveryDate, Estate, Locks, Operator)VALUES(NULL,'$CompanyId','$ModelId','$ForwaderId','$DeliveryNumber','$Remark','$DeliveryDate','2','0','$Operator')";
		 $MainAction=@mysql_query($MainRecode);
		 $Pid=mysql_insert_id();
		 if($Pid!=0 && $Pid!=""){
		   $Log.="提货主单生成成功<br>";
	       for ($i=0;$i<$sLen;$i++){
	         if ($DataIn=='ac'){
					   $InSql="INSERT INTO $DataIn.ch1_deliverysheet 
		              SELECT NULL,'$Pid',S.Mid,'$ArrId[$i]','$ArrQty[$i]',S.Price,S.Type,'1','0','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' 
					  FROM $DataIn.ch1_shipsheet S
					  LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
					  WHERE S.POrderId='$ArrId[$i]'";
				}
				else{
				      $InSql="INSERT INTO ch1_deliverysheet 
		              SELECT NULL,'$Pid',S.Mid,'$ArrId[$i]','$ArrQty[$i]',S.Price,S.Type,'1','0'
					  FROM $DataIn.ch1_shipsheet S
					  LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
					  WHERE S.POrderId='$ArrId[$i]'";
			  }
		      $InRecode=mysql_query($InSql);
		      if($InRecode){
		          $Log.="流水号为 $ArrId[$i] 的订单提货 $ArrQty[$i]成功<br>";
			     }
		     else{
		          $Log.="<div class='redB'>流水号为 $ArrId[$i] 的订单提货 $ArrQty[$i]失败 $InSql</div><br>";
			      $OperationResult="Y";
		         }		 
		      }//end for($i=0;$i<$sLen;$i++)
			  
			  $Id=$Pid;
			  include "billtopdf/ch_shipout_tobill.php";
		   }
	   else{
	       $Log.="<div class='redB'>提货主单生成失败!</div><br>";
		   $OperationResult="N";
	       }
	 }
    break;
}
$ALType="DeliverySign=0";//返回未发货状态
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>