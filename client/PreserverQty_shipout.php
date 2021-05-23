<?php
include "../model/modelhead.php";
$fromWebPage="PreserverQty_read";
$nowWebPage="PreserverQty_shipout";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="Bill Of Delivery";		//需处理
$upDataSheet="delivery_sheet";	//需处理
$Log_Funtion="生成";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
$CheckmainResult=mysql_query("SELECT * FROM $DataIn.skech_deliverymain WHERE Id=$Id",$link_id);
if($CheckmainRow=mysql_fetch_array($CheckmainResult)){
           $CompanyId=$CheckmainRow["CompanyId"];
          $ModelId=$CheckmainRow["ModelId"];
          $ForwaderId=$CheckmainRow["ForwaderId"];
          $DeliveryNumber=$CheckmainRow["DeliveryNumber"];
          $DeliveryDate=$CheckmainRow["DeliveryDate"];
          if($ModelId==0){
                    $EndPlace=$CheckmainRow["EndPlace"];
                    $Adress=$CheckmainRow["Adress"];
                    if($EndPlace!="" && $Adress!=""){
                         $Title="出Skech".$EndPlace;
                         $inRecode="INSERT INTO $DataIn.ch8_shipmodel (Id,CompanyId,Title,InvoiceModel,LabelModel,StartPlace,EndPlace,SoldFrom,FromAddress,
                         FromFaxNo,SoldTo,Address,FaxNo,PISign,Date,Estate,Locks,Operator) VALUES (NULL,'$CompanyId','$Title','1','1','Ash Cloud Co.,Ltd. Shenzhen','$EndPlace',
                         '','','','','$Address','','0','$Date','1','0','10039')";
                         $inAction=@mysql_query($inRecode);
                         $ModelId=mysql_insert_id();
                     }
             }
          if($ModelId>0){
                     $MainRecode="INSERT INTO $DataIn.ch1_deliverymain(Id, CompanyId, ModelId,ForwaderId, DeliveryNumber, Remark, DeliveryDate, Estate, Locks, Operator)VALUES(NULL,'$CompanyId','$ModelId','$ForwaderId','$DeliveryNumber','$Remark','$DeliveryDate','2','0','$Operator')";
		             $MainAction=@mysql_query($MainRecode);
		              $Pid=mysql_insert_id();
                     if($Pid>0){
                                if ($DataIn=='ac'){
                                        $addRecodes="INSERT INTO $DataIn.ch1_deliverysheet SELECT NULL,'$Pid',ShipId,POrderId,Qty,Price,'1',Estate,Locks,'0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator'   
                                  FROM $DataIn.skech_deliverysheet WHERE Mid=$Id";   
                                  }
                                  else{
	                                   $addRecodes="INSERT INTO $DataIn.ch1_deliverysheet SELECT NULL,'$Pid',ShipId,POrderId,Qty,Price,'1',Estate,Locks  
                                  FROM $DataIn.skech_deliverysheet WHERE Mid=$Id";  
                                  }
								    $addAction=@mysql_query($addRecodes);
                                    if($addAction){
                                          $UpdateSql="UPDATE $DataIn.skech_deliverymain SET Estate=0 WHERE Id=$Id";
								          $UpdateResult=@mysql_query($UpdateSql);
                                          $Log.="<span class='greenB'>the Bill Of Delivery IS Sucess<br></span>";
                                     }
                               	    $Id=$Pid;
		                            include "../admin/billtopdf/ch_shipout_tobill.php";
                         }
                  }
       else{
              $Log.="<span class='redB'>the Bill Of Delivery IS Fail<br></span>";
              $OperationResult="N";
           }
}
else{
   $Log.="<span class='redB'>the Bill Of Delivery IS Fail<br></span>";
   $OperationResult="N";
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>