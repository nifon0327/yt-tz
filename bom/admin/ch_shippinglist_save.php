<?php
//defined('IN_COMMON') || include '../basic/common.php';

include "../model/modelhead.php";
//步骤2：
$Log_Item="出货资料";			//需处理
$fromWebPage=$funFrom."_wait";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$chooseDate=substr($rkDate,0,7);
$ALType="CompanyId=$CompanyId";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	if($checkid[$i]!=""){
		$TEMP=explode("^^",$checkid[$i]);
		$Id=$TEMP[0];$Type=$TEMP[1];
		if($Type==1){
			$Ids1=$Ids1==""?$Id:($Ids1.",".$Id);
			}
		else{
			$Ids2=$Ids2==""?$Id:($Ids2.",".$Id);
			}
		}
	}
$Ids1=$Ids1==""?0:$Ids1;
$Ids2=$Ids2==""?0:$Ids2;
//保存主单资料
$checkNumber=mysql_fetch_array(mysql_query("SELECT MAX(Number) AS Number FROM $DataIn.ch1_shipmain",$link_id));
$Number=$checkNumber["Number"]+1;
if ($ShipType) {
     	$ShipType='replen';
        } //新增补货标识
else{
	    $ShipType='';
        }
$Mid =0;
$FieldPO=explode("-",$OrderPO);
$POFloor=count($FieldPO)-1;
$POBuild=count($FieldPO)-2;
$BuildFloor = $FieldPO[$POBuild].'-'.$FieldPO[$POFloor];
$mainInSql="INSERT INTO $DataIn.ch1_shipmain (Id,CompanyId,ModelId,BankId,Number,InvoiceNO,InvoiceFile,Wise,Notes,Terms,PaymentTerm,PreSymbol,Date,Estate,Locks,Sign,Ship,ShipType,cwSign,Remark,Operator,CarNo) VALUES (NULL,'$CompanyId','$ModelId','$BankId','$Number','$InvoiceNO','1','$BuildFloor','$Notes','$Terms','$PaymentTerm','','$ShipDate','1','1','1','-1','$ShipType','1','','$Operator','$CarNo')";
$mainInAction=@mysql_query($mainInSql);
$Mid=mysql_insert_id();
if($Mid>0){
	  $Log.="出货主单($Mid)创建成功.<br>";
      //************************************************************************订单
   if($Ids1!=""){

     $CheckSplitResult=mysql_query("SELECT SP.Id,SP.Qty,S.ProductId,S.POrderId,S.Price
               FROM $DataIn.ch1_shipsplit SP  
              LEFT JOIN  $DataIn.yw1_ordersheet  S  ON S.POrderId=SP.POrderId  WHERE SP.Id IN ($Ids1)",$link_id);
     while($CheckSplitRow=mysql_fetch_array($CheckSplitResult)){
               $SPId=$CheckSplitRow["Id"];
               $POrderId=$CheckSplitRow["POrderId"];
               $ProductId=$CheckSplitRow["ProductId"];
               $Qty=$CheckSplitRow["Qty"];
               $Price=$CheckSplitRow["Price"];
               $sheetInSql="INSERT INTO $DataIn.ch1_shipsheet  SELECT NULL,'$Mid','$SPId','$POrderId','$ProductId',
               '$Qty','$Price','1','1','1','1','1','0','$Operator',NOW(),'$Operator',NOW(),CURDATE(),'$Operator'";
	           $sheetInAction=@mysql_query($sheetInSql);
               $ShipId=mysql_insert_id();
	            if($sheetInAction && $ShipId>0){
		                 $Log.="出货的订单加入出货明细表成功.<br>";
		               //更新状态
	 	                $pUpSql="UPDATE $DataIn.ch1_shipsplit  SET Estate='0',ShipId='$ShipId' WHERE Id='$SPId'";
		                $pUpResult=@mysql_query($pUpSql);
		          }
	           else{
		              $Log.="<div class='redB'>出货的订单加入出货明细表失败.</div><br>";
		              $OperationResult="N";
		       }
         }

		  $updateSql="UPDATE $DataIn.yw1_ordersheet S 
                   LEFT JOIN (  SELECT IFNULL(SUM(C.Qty),0) AS shipQty,C.POrderId 
                              FROM  $DataIn.ch1_shipsheet C 
                             WHERE C.POrderId  IN (SELECT  POrderId FROM $DataIn.ch1_shipsplit WHERE Id IN ($Ids1)) GROUP BY C.POrderId) A ON A.POrderId=S.POrderId
                   SET S.Estate=4  WHERE S.Qty=A.shipQty";
		  $upAction=@mysql_query($updateSql,$link_id);
		   if($upAction && mysql_affected_rows()>0){
			                 $Log.="更新订单已出货状态成功.<br>";
			              }
			 else{
				            $Log.="<div class='redB'>更新订单已出货状态失败.</div><br>";
			     }
            }
      //************************************************************************样品
   	if($Ids2!=""){
	   	     $IN_SampSql="INSERT INTO $DataIn.ch1_shipsheet  
	   	     SELECT NULL,'$Mid','0',SampId,'0',Qty,Price,'1','2','1','1','1','0','$Operator',NOW(),'$Operator',
	   	     NOW(),CURDATE(),'$Operator' FROM $DataIn.ch5_sampsheet WHERE Id IN ($Ids2)";
             $IN_SampResult=@mysql_query($IN_SampSql);
             if($IN_SampResult && mysql_affected_rows()>0){
		        	   	$Log.="随货项目($Ids2)加入出货明细表成功<br>";
	        	   	 $sUpSql="UPDATE $DataIn.ch5_sampsheet SET Estate='2' WHERE Id IN ($Ids2)";
	         	  	 $sUpResult=mysql_query($sUpSql);
	      	     	if($sUpResult && mysql_affected_rows()>0){
		        	   	$Log.="随货项目($Ids2)的状态更新成功.<br>";
		        	   	}
	          	 	else{
		        	   	$Log.="<div class='redB'>随货项目($Ids2)的将出状态更新失败.</div><br>";
		         	  	$OperationResult="N";
		        	   	}
                   }
              else{
		        	   	$Log.="<div class='redB'>随货项目($Ids2)加入出货明细表失败.$IN_SampSql</div><br>";
		         	  	$OperationResult="N";
                      }
          }
		$Id=$Mid;
		include "ch_shippinglistBlue_toinvoice.php";
		include "subprogram/order_outork.php";//研砼HK，研砼贸易的订单办出自动入库

//    include_once "../weixin/weixin_api.php";
//
//    $weixin = new weixin_api();
//
//    $touser = 'op_Tyw_8h2hzceNzjvmkDMICU60s'; //微信出货 open_id
//
//    $next_user = '刘文豪';//发送给的用户名字，与$touser相对应
//
//    $login_user = $_SESSION['Login_Name'];  //当前登录用户
//
//    $Log_Item = '待出货';  //当前操作
//
//    $login_time = date('Y-m-d H:i:s');//操作时间
//
//    $time = explode(' ', $login_time);
//
//    $time = $time[1];
//
//    $login_detail = $login_user.'于今日'.$time.'完成'.$Log_Item.'流程。现需要您完成下一步"当前出货"工作，请及时登录研砼治筑运营平台进行操作。';//登录详情
//
//    $remark = "\n 流程测试，如有疑问，请及时联系".$login_user."或ＩＴ部。";//备注
//
//    $res = $weixin->send_login_temp_msg($touser, $login_user, $next_user, $Log_Item, $login_time, $login_detail, $remark);
//
//    if ($res){
//        $Log.="<br>已通知 <span style='color: red'>$next_user</span> 进行下一步操作. <br>";
//    }


	}
else{
	$Log.="<div class='redB'>出货主单($Mid)创建失败. $mainInSql </div><br>";
	$OperationResult="N";
	}
   if($CompanyId==1074){
         include "ch_shippinglist_toxml(strax).php"; // 新增的库存
         include "ch_shippinglist_toxml(strax)_stock.php";//总的库存
    }
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion',\"$Log\",'$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>