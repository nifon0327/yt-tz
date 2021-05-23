<?php   
include "../model/modelhead.php";
$fromWebPage=$funFrom."_add";
$nowWebPage=$funFrom."_outed";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="产品出货";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//保存主单资料
$checkNumber=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataIn.ch1_shipmain",$link_id));
$Number=$checkNumber["Number"]+1;
//$ShipType='';  //''(正常),debit(其它收款),credit(扣款),replen(补货)
if ($ShipType) {
	       $ShipType='replen';
          } //新增补货标识
else{
	     $ShipType='';
      }	
$mainInSql="INSERT INTO $DataIn.ch1_shipmain (Id,CompanyId,ModelId,BankId,Number,InvoiceNO,InvoiceFile,Wise,Notes,Terms,PaymentTerm,PreSymbol,Date,Estate,Locks,Sign,Ship,ShipType,cwSign,Remark,Operator) 
VALUES (NULL,'$CompanyId','$ModelId','$BankId','$Number','$InvoiceNO','1','$Wise','$Notes','$Terms','$PaymentTerm','','$ShipDate','1','1','1','-1','$ShipType','1','','$Operator')";
$mainInAction=@mysql_query($mainInSql);
$Mid=mysql_insert_id();
if($mainInAction){
	       $Log.="出货主单($Mid)创建成功.<br>";
           $OrderArray=explode("|", $OrderIds);
           $Lens=count($OrderArray);
           for($i=0;$i<$Lens;$i++){
	           if($OrderArray[$i]!=""){
		            $checkArray=explode("^^",$OrderArray[$i]);
		            $Id=$checkArray[0];$shipQty=$checkArray[1];$Type=$checkArray[2];
		            switch($Type){
                    case 1:
                     if ($DataIn=='ac'){
                         $sheetInSql="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,'$Mid',POrderId,ProductId,'$shipQty',Price,'1','1','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.yw1_ordersheet WHERE Id=$Id";
                     }
                     else{
	                     $sheetInSql="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,'$Mid',POrderId,ProductId,'$shipQty',Price,'1','1','1','1' FROM $DataIn.yw1_ordersheet WHERE Id=$Id";
                     }
                    
	                $sheetInAction=@mysql_query($sheetInSql,$link_id);
                    if($sheetInAction && mysql_affected_rows()>0){
		                            $Log.="出货的订单($Id)加入出货明细表成功.<br>";
                                     include "subprogram/Order_shipout.php";
	                               }
	                       else{
		                             $OperationResult="N";
		                             $Log.="<div class='redB'>出货的订单($Id)加入出货明细表失败.</div><br>";
	                              }
                     break;
                      case 2:
                       if ($DataIn=='ac'){
                         $sheetInSql="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,'$Mid',SampId,'0','$shipQty',Price,'1','2','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator' FROM $DataIn.ch5_sampsheet WHERE Id IN ($Id)";
                     }
                     else{
	                      $sheetInSql="INSERT INTO $DataIn.ch1_shipsheet SELECT NULL,'$Mid',SampId,'0','$shipQty',Price,'1','2','1','1' FROM $DataIn.ch5_sampsheet WHERE Id IN ($Id)";
	                  }
	                  $sheetInAction=@mysql_query($sheetInSql,$link_id);
                      if($sheetInAction && mysql_affected_rows()>0){
		                            $Log.="随货项目($Id)加入出货明细表成功.<br>";
	                                $sUpSql="UPDATE $DataIn.ch5_sampsheet SET Estate='2' WHERE Id IN ($Id)";
	                                $sUpResult=mysql_query($sUpSql);
	                                if($sUpResult && mysql_affected_rows()>0){
	                                         $Log.="随货项目($Id)的状态更新成功.<br>";
	                                       }
	                               else{
	                                           $Log.="<div class='redB'>随货项目($Id)的将出状态更新失败.</div><br>";
	                                           $OperationResult="N";
	                                         }
	                               }
	                       else{
		                             $OperationResult="N";
		                             $Log.="<div class='redB'>随货项目($Id)加入出货明细表失败.</div><br>";
	                              }
                       break;   
                        }
                  }
           }

	     $Id=$Mid;
	     //include "ch_shippinglist_toinvoice.php";
		 include "ch_shippinglistBlue_toinvoice.php";
	    }
else{
	     $Log.="<div class='redB'>出货主单($Mid)创建失败. $mainInSql </div><br>";
	     $OperationResult="N";
	    }

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
