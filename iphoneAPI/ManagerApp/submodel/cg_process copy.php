<?php
//采购订单状态显示  传入参数:StockId,StuffId

//1.检查开发状态
$ProcessArray=array();
$L_xdTime="";
if ($ComboxSign==1){
	 include "cg_process_combox.php";
}
else{
			if ($StockId>0 && $StuffId>0){
			          $cgmainResult=mysql_query("SELECT Id FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId'  AND Mid>0 LIMIT 1",$link_id);
			          if(mysql_num_rows($cgmainResult)<=0){
							$developResult=mysql_query("SELECT D.GroupId,D.Estate,D.finishdate,TIMESTAMPDIFF(DAY,D.Date,IF(D.Estate=0,D.finishdate,CURDATE())) AS Days,IF(YEARWEEK(Targetdate,1)<YEARWEEK(IF(D.Estate=0,D.finishdate,CURDATE()),1),1,0) AS Over FROM $DataIn.stuffdevelop  D WHERE D.StuffId='$StuffId'",$link_id);
							if($developRow = mysql_fetch_array($developResult)){
							       switch($developRow["GroupId"]){
								       case 102:  $L_Value="图";  break;
								       case 502:  $L_Value="B";       break;
								       case 503:  $L_Value="C";       break;
								         default:  $L_Value="A";       break;
							       }
							       $L_Color=$developRow["Estate"]==0?2:1;
							       $L_Badge=$developRow["Days"];
							       $L_Over=$developRow["Over"];
							       $ProcessArray[]=array("Title"=>"开","Color"=>"$L_Color","Value"=>"$L_Value","Badge"=>"$L_Badge","Over"=>"$L_Over");
							       $L_xdTime=$developRow["Estate"]==0?date("m/d H:i",strtotime($developRow["finishdate"])):$L_xdTime;
							}
					 }
						//2.检查锁定状态
						$L_Locks=1;$L_unLockDate="";
						$lockResult=mysql_query("SELECT TIMESTAMPDIFF(DAY,IF(L.LockDate='0000-00-00 ',M.OrderDate,L.LockDate),IF(L.Locks=1,L.Date,CURDATE())) AS Days,L.Locks,L.LockDate,L.Date  
						FROM $DataIn.cg1_lockstock L
						LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=LEFT(L.StockId,12)   
						LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
						WHERE L.StockId='$StockId'",$link_id);
						if($lockRow = mysql_fetch_array($lockResult)){
						       $L_Badge=$lockRow["Days"];
						       $L_Locks=$lockRow["Locks"];
						       $L_unLockDate=$L_Locks==1?$lockRow["LockDate"]:"";
						       $L_Color=$L_Locks==1?2:1;
						       $L_Over=$L_Badge>3?1:0;
						       $ProcessArray[]=array("Title"=>"锁","Color"=>"$L_Color","Value"=>"","Badge"=>"$L_Badge","Over"=>"$L_Over");
						       //$L_xdTime=$L_Locks==1?date("m/d H:i",strtotime($lockRow["Date"])):$L_xdTime;
						}
						
						//3.采购时间
						$L_pos=count($ProcessArray);
						$cgsheetResult=mysql_query("SELECT S.Mid,(S.AddQty+S.FactualQty) AS Qty,YEARWEEK(S.DeliveryDate,1) AS Weeks,YEARWEEK(CURDATE(),1) AS cWeeks,
						TIMESTAMPDIFF(DAY,IF(S.Mid>0,M.created,S.ywOrderDTime),CURDATE()) AS Days,
						IF(S.Mid=0,TIMESTAMPDIFF(HOUR,S.ywOrderDTime,CURDATE()),0) AS Hours,IF(S.Mid>0,M.created,S.ywOrderDTime) AS xdDate  FROM $DataIn.cg1_stocksheet S
						LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid
						WHERE S.StockId='$StockId' ",$link_id);
						if($cgsheetRow = mysql_fetch_array($cgsheetResult)){
						     $Mid=$cgsheetRow["Mid"];
						     $L_xdDate=$cgsheetRow["xdDate"];
						     $L_dWeeks=$cgsheetRow["Weeks"];
						     $L_cWeeks=$cgsheetRow["cWeeks"];
						     $L_Badge=$cgsheetRow["Days"];
						     if ($Mid==0){ //未下采购单
						          $L_Over=$cgsheetRow["Hours"]>4 && $L_Locks==1?1:0;
							      $ProcessArray[]=array("Title"=>"采","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"$L_Over");
							      $ProcessArray[]=array("Title"=>"单","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
								  $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
								  $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
								  $ProcessArray[]=array("Title"=>"入","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
						     }
						     else{ //已下采购单
						          $L_Qty=$cgsheetRow["Qty"]; //采购数量
							     //检查送货单
						         $gyssheetResult=mysql_query("  SELECT SUM(S.Qty) AS shQty,MAX(M.Date) AS shDate,SUM(IF(S.Estate<>1,S.Qty,0)) shQty2,SUM(IF(S.Estate=0,S.Qty,0)) shQty3 FROM $DataIn.gys_shsheet S 
						LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
						WHERE S.StockId='$StockId' ",$link_id);
						         if($gyssheetRow = mysql_fetch_array($gyssheetResult)){
						                 $L_shQty=$gyssheetRow["shQty"];
						                 if ($L_shQty>0){
							                 $L_Color=$L_Qty==$L_shQty?2:1;
						                     $ProcessArray[]=array("Title"=>"单","Color"=>"$L_Color","Value"=>"$L_shQty","Badge"=>"","Over"=>"");
						                     
						                      //检查送货到达确认
						                      $L_shQty2=$gyssheetRow["shQty2"];
						                      if ($L_shQty2>0){
							                         $L_Color=$L_Qty==$L_shQty2?2:1;
							                         $ProcessArray[]=array("Title"=>"到","Color"=>"$L_Color","Value"=>"$L_shQty2","Badge"=>"","Over"=>"");
						                      }
						                      else{
							                         $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
						                      }
						                      
						                      //检查品检记录
						                      $L_shQty3=$gyssheetRow["shQty3"];
						                      if ($L_shQty3>0){
							                         $L_Color=$L_Qty==$L_shQty3?2:1;
							                         $ProcessArray[]=array("Title"=>"检","Color"=>"$L_Color","Value"=>"$L_shQty3","Badge"=>"","Over"=>"");
						                      }
						                       else{
							                         $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
						                      }
						               }
						                else{
								            $ProcessArray[]=array("Title"=>"单","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
								            $ProcessArray[]=array("Title"=>"到","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
								            $ProcessArray[]=array("Title"=>"检","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
						                }
						         }
						           
						         $L_Over=$L_cWeeks>$L_dWeeks?1:0; 
						         $L_Color=$L_Locks==1?1:0;
						         //检查入库记录
						         $rksheetResult=mysql_query("SELECT SUM(S.Qty) AS rkQty,MAX(M.Date) AS rkDate,YEARWEEK(MAX(M.Date),1) AS rkWeeks 
						                                                                FROM $DataIn.ck1_rksheet S 
																						LEFT JOIN $DataIn.ck1_rkmain M ON M.Id=S.Mid
																						WHERE S.StockId='$StockId' ",$link_id);
								 if($rksheetRow = mysql_fetch_array($rksheetResult)){
								          $L_rkQty=$rksheetRow["rkQty"];
								          if ($L_rkQty>0){
								                 $L_Color=$L_Qty==$L_rkQty?2:$L_Color;
									             $ProcessArray[]=array("Title"=>"入","Color"=>"$L_Color","Value"=>"$L_rkQty","Badge"=>"","Over"=>"");
									              $L_Badge=$L_Qty==$L_rkQty?geDifferDateTimeNum($L_xdDate,$rksheetRow["rkDate"],2):$L_Badge;
									              if ($L_Qty==$L_rkQty){
										              $L_Over=$rksheetRow["rkWeeks"]>$L_dWeeks?1:0;
									              }
								          }
								          else{
									            $ProcessArray[]=array("Title"=>"入","Color"=>"0","Value"=>"","Badge"=>"","Over"=>"");
								          }
								 }
							 	 $cgdateArray=array(); 
							 	 
								 $cgdateArray[]=array("Title"=>"采","Color"=>"$L_Color","Value"=>"","Badge"=>"$L_Badge","Over"=>"$L_Over");
								  array_splice($ProcessArray,$L_pos,0,$cgdateArray);
								  						
						     }
						}
			}
}

?>