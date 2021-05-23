<?php   
//电信-zxq 2012-08-01
//MC、DP共享代码
include "../model/modelhead.php";
//$fromWebPage="Ch_shipoutclient_read";
//$nowWebPage="Ch_shipoutclient_updated";
$fromWebPage=$funFrom."_".$From;
$nowWebPage=$funFrom."_updated";
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
$j=1;
switch($ActionId){
	case 112://新增提货数据
	 $ArrProductId=explode("|",$AllProductId);
	 $ArrQty=explode("|",$AllQty);
	 $sLen=count($ArrProductId); 
	 /*
	 echo "$CompanyId <br>";
	 echo "$DeliveryNumber <br>";
	 echo "$ModelId <br>";
	 echo "$ForwaderId <br>";
	 echo "$AllProductId <br>";
	 echo "$AllQty <br>";	
	 */
	// echo "$AllProductId <br>";
	 $plen=count($ArrProductId)-1;
	 if($plen>0) {
		 $MainRecode="INSERT INTO $DataIn.ch1_deliverymain(Id, CompanyId, ModelId,ForwaderId,ForwaderRemark,DeliveryNumber,ShipType,Remark, DeliveryDate, Estate, Locks, Operator)
		 VALUES(NULL,'$CompanyId','$ModelId','$ForwaderId','$ForwaderRemark','$DeliveryNumber','$ShipType','$Remark','$DeliveryDate','2','0','$Operator')";
		 //echo "$MainRecode";
		 $MainAction=@mysql_query($MainRecode);
		 $Pid=mysql_insert_id();
		 //$Pid=99999;
		 if($Pid!=0 && $Pid!=""){
			  $Log.="提货主单生成成功<br>";
			 
			 
			 for($k=0;$k<$plen;$k++){
				$ProductId=$ArrProductId[$k];
				$TALLQty=$ArrQty[$k];
				$checkSql=mysql_query("SELECT  S.POrderId,S.Qty AS Qty 
									   FROM $DataIn.ch1_shipout O 
									   LEFT JOIN $DataIn.ch1_shipmain SM ON SM.Id=O.ShipId
									   LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=SM.Id
									   WHERE 1 AND S.ProductId ='$ProductId'
									   ORDER BY S.Id",$link_id);
				/*
				echo "SELECT  S.POrderId,S.Qty
									   FROM $DataIn.ch1_shipout O 
									   LEFT JOIN $DataIn.ch1_shipmain SM ON SM.Id=O.ShipId
									   LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=SM.Id
									   WHERE 1 AND S.ProductId ='$ProductId'
									   ORDER BY S.Id <br>";
				*/	   
				if($checkRow=mysql_fetch_array($checkSql)){
					do{	 
						$POrderId=$checkRow["POrderId"];
						$Qty=$checkRow["Qty"];
						$rkTemp=mysql_query(" SELECT SUM(DeliveryQty) AS DeliveryQty  FROM $DataIn.ch1_deliverysheet   WHERE POrderId='$POrderId' ",$link_id);
						//echo "SELECT SUM(DeliveryQty) AS DeliveryQty  FROM $DataIn.ch1_deliverysheet   WHERE POrderId='$POrderId' <br>";
						$DeliveryQty=mysql_result($rkTemp,0,"DeliveryQty");
						$DeliveryQty=$DeliveryQty==""?0:$DeliveryQty; //已提货数
                        $NoQty=$Qty-$DeliveryQty;
						//echo "$NoQty=$Qty-$DeliveryQty; <br> ";
						if($NoQty>0 && $TALLQty>0){  //未提数量
							if($TALLQty>=$NoQty){ //全部提完,最好有个提完标志，加上去后，就不用每次从头扫描
								  $TALLQty-=$NoQty;
								   if ($DataIn=='ac'){
								        $InSql="INSERT INTO $DataIn.ch1_deliverysheet 
										  SELECT NULL,'$Pid',S.Mid,'$POrderId','$NoQty',S.Price,S.Type,'1','0','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' 
										  FROM $DataIn.ch1_shipsheet S
										  INNER JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
										  INNER JOIN $DataIn.ch1_shipout O ON O.ShipId=M.Id  
										  WHERE S.POrderId='$POrderId'";
								   }
								   else{
										  $InSql="INSERT INTO $DataIn.ch1_deliverysheet 
										  SELECT NULL,'$Pid',S.Mid,'$POrderId','$NoQty',S.Price,S.Type,'1','0'
										  FROM $DataIn.ch1_shipsheet S
										  INNER JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
										  INNER JOIN $DataIn.ch1_shipout O ON O.ShipId=M.Id 
										  WHERE S.POrderId='$POrderId'";
								  }
								  $InRecode=mysql_query($InSql);
								  if($InRecode){
									  $Log.="流水号为 $POrderId 的订单提货 $NoQty 成功<br>";
									 }
								 else{
									  $Log.="<div class='redB'>流水号为 $POrderId 的订单提货 $NoQty 失败 $InSql</div><br>";
									  $OperationResult="Y";
									 }		 								
								
							}
							else{  //部分提
								 if ($DataIn=='ac'){
									  $InSql="INSERT INTO $DataIn.ch1_deliverysheet 
									  SELECT NULL,'$Pid',S.Mid,'$POrderId','$TALLQty',S.Price,S.Type,'1','0','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' 
									  FROM $DataIn.ch1_shipsheet S
									  INNER JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
									  INNER JOIN $DataIn.ch1_shipout O ON O.ShipId=M.Id  
									  WHERE S.POrderId='$POrderId'";
								}
								else{
									$InSql="INSERT INTO $DataIn.ch1_deliverysheet 
									  SELECT NULL,'$Pid',S.Mid,'$POrderId','$TALLQty',S.Price,S.Type,'1','0'
									  FROM $DataIn.ch1_shipsheet S
									  INNER JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
									  INNER JOIN $DataIn.ch1_shipout O ON O.ShipId=M.Id  
									  WHERE S.POrderId='$POrderId'";
								}
								  $InRecode=mysql_query($InSql);
								  if($InRecode){
									  $Log.="流水号为 $POrderId 的订单提货 $NoQty 成功<br>";
									 }
								 else{
									  $Log.="<div class='redB'>流水号为 $POrderId 的订单提货 $NoQty 失败 $InSql</div><br>";
									  $OperationResult="Y";
									 }							
								break;
							}
							
						} //if($NoQty>0){  //未提数量
					
						$j++;
					}while($checkRow=mysql_fetch_array($checkSql));
				} //if($checkRow=mysql_fetch_array($checkSql)){		
			 } //for($len=0;$len<$plen;$len++)
		 	$Id=$Pid;
			include "billtopdf/ch_shipout_tobill.php";	 
		 } //if($Pid!=0 && $Pid!=""){
	     else{
	       $Log.="<div class='redB'>提货主单生成失败!</div><br>";
		   $OperationResult="N";
	    }			 
	 } //if($plen>0) {
	 
	 /*
	 if (count($ArrQty)==$sLen && $sLen>0){
	     $MainRecode="INSERT INTO $DataIn.ch1_deliverymain(Id, CompanyId, ModelId,ForwaderId, DeliveryNumber, Remark, DeliveryDate, Estate, Locks, Operator)VALUES(NULL,'$CompanyId','$ModelId','$ForwaderId','$DeliveryNumber','$Remark','$DeliveryDate','2','0','$Operator')";
		 $MainAction=@mysql_query($MainRecode);
		 $Pid=mysql_insert_id();
		 if($Pid!=0 && $Pid!=""){
		   $Log.="提货主单生成成功<br>";
	       for ($i=0;$i<$sLen;$i++){
		      $InSql="INSERT INTO ch1_deliverysheet 
              SELECT NULL,'$Pid',S.Mid,'$ArrId[$i]','$ArrQty[$i]',S.Price,S.Type,'1','0'
			  FROM $DataIn.ch1_shipsheet S
			  LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
			  WHERE S.POrderId='$ArrId[$i]'";
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
	 */
    break;
}
//$ALType="DeliverySign=0";//返回未发货状态
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>