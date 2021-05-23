<?php
$SaveOprationlog=1;
$ActionId = $_REQUEST['ActionId'];
if ($ActionId==17 || $ActionId==23 || $ActionId==72 || $ActionId==3 || $ActionId=="PIDate" || $ActionId=="AddStuffCombox"){
    $MyPDOEnabled=1;
	include "../model/modelhead.php";
	
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="产品订单";		//需处理
	$upDataSheet="$DataIn.yw1_ordersheet";	//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId";
	$SaveOprationlog=0;
	
	switch($ActionId){
		case 17://审核通过删除配件需求单 
		   switch($From){
		      case "m":
		        $Log_Funtion="删除配件需求单";	
			    $Lens=count($checkid);
				for($i=0;$i<$Lens;$i++){
					$Id=$checkid[$i];
					if($Id!=""){
		                $myResult=null;$myRow=null;
						$myResult=$myPDO->query("CALL proc_cg1_stocksheet_delete('','$Id',$Operator);");
			            $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
			            
			            $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:"Y";
			            
			            $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
			            $Log.="</br>";
				    }
				}
				$fromWebPage=$funFrom."_m";
		      
		      break;
		       
		      case "m1":
		        $Log_Funtion="删除配件需求单";	
			    $Lens=count($checkid);
				for($i=0;$i<$Lens;$i++){
					$Id=$checkid[$i];
					if($Id!=""){
		                $myResult=null;$myRow=null;
						$myResult=$myPDO->query("CALL proc_cg1_stocksheet_delete('','$Id',$Operator);");
			            $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
			            $OperationResult = $myRow['OperationResult']!="Y"?$myRow['OperationResult']:"Y";
			            $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
			            $Log.="</br>";
				    }
				}
				$fromWebPage=$funFrom."_m1";
		      
		       break;
		       case "m2": 
		         $Log_Funtion="订单数量和单价更新审核";	
		         $Lens=count($checkid);
				 for($i=0;$i<$Lens;$i++){
					$Id=$checkid[$i];
					if($Id!=""){
						 $checkIdSql = "SELECT U.Id,U.OldQty,U.NewQty,U.OldPrice,U.NewPrice,U.Remark,U.Operator,S.POrderId,S.OrderPO,S.ProductId,
						 S.Qty AS OrderQty,S.Price AS OrderPrice,P.cName,P.eCode
						 FROM $DataIn.yw1_orderupdate U 
						 LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId = U.POrderId
						 LEFT JOIN $DataIn.productdata P ON P.ProductId = S.ProductId
						 WHERE U.Id ='$Id' AND U.Estate=1";
			             $checkIdResult = $myPDO->query($checkIdSql);
			             $checkIdRow =$checkIdResult->fetch(PDO::FETCH_ASSOC);
			             $POrderId= $checkIdRow["POrderId"];
			             $OrderQty= $checkIdRow["OrderQty"];
			             $NewQty= $checkIdRow["NewQty"];
			             $OrderPrice= $checkIdRow["OrderPrice"];
			             $NewPrice= $checkIdRow["NewPrice"];
			             if ($NewQty>0){
							if ($NewQty!=$OrderQty){
								$myResult=$myPDO->query("CALL proc_yw1_ordersheet_updateqty('$POrderId','$NewQty',$Operator);");
						        $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
							    $OperationResult = $myRow['OperationResult'];    
							    $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
					            $myResult=null;
					            
							}
						}
						else{
							$Log.="<div class=redB>订单数量不能更新为零！<br></div>";
						}
						$checkIdResult =null;
						if($NewPrice!=$OrderPrice){
							$sheetSql = "UPDATE $DataIn.yw1_ordersheet SET Price='$NewPrice',Estate='1' WHERE POrderId='$POrderId'";
							$sheetResult = $myPDO->exec($sheetSql);
							if($sheetResult){
								$Log.="<p>&nbsp;&nbsp;&nbsp;&nbsp;POrderId为 $POrderId 的订单单价更新成功.<br>";
							}
				            $sheetResult = null;
						}
						if($OperationResult=="Y"){
							$checkEstateSql = "UPDATE $DataIn.yw1_orderupdate  SET Estate=0
				            WHERE Id ='$Id' ";
				            $checkEstateResult = $myPDO->exec($checkEstateSql);
							
						}
				    }
				}
		      
		        $fromWebPage=$funFrom."_m2";
		      break;
		      case "m3": //需求单异动增加 审核
		         $Log_Funtion="需求单异动增加审核";	
		         $Lens=count($checkid);
				 for($i=0;$i<$Lens;$i++){
					$Id=$checkid[$i];
					if($Id!=""){
						 $checkIdSql = "SELECT S.Level,S.POrderId,S.mStockId,S.mStuffId,S.StuffId,
						 S.uStuffId,S.Relation,A.StuffCname,S.UpTestStandard
						 FROM $DataIn.cg1_addstuff S
						 LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
						 WHERE S.Id ='$Id' AND S.Estate=1";
			             $checkIdResult = $myPDO->query($checkIdSql);
			             $checkIdRow =$checkIdResult->fetch(PDO::FETCH_ASSOC);
			             $Level= $checkIdRow["Level"];
			             $POrderId= $checkIdRow["POrderId"];
			             $mStockId= $checkIdRow["mStockId"];
			             $mStuffId= $checkIdRow["mStuffId"];
			             $_StuffId= $checkIdRow["StuffId"];
			             $_Relation= $checkIdRow["Relation"];
			             $StuffCname= $checkIdRow["StuffCname"];
			             $_Unite= $checkIdRow["uStuffId"];
			             $UpTestStandard = $checkIdRow["UpTestStandard"];
			             $checkIdResult = null;
			             if($Level==1 && $mStockId==''){
				               $myResult=$myPDO->query("CALL proc_cg1_stocksheet_add($POrderId,'$_StuffId','0','$_Relation','1',$Operator);");
						       $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
							   $OperationResult = $myRow['OperationResult']; 
							   $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>"; 
					           $myResult=null;
					           
					           if ($OperationResult=="Y" && strlen($_Unite)>0){
						            
						             $uniteResult=$myPDO->query("CALL proc_cg1_stuffunite_add($POrderId,'$_StuffId','$_Unite',$Operator);");
						             $uniteRow = $uniteResult->fetch(PDO::FETCH_ASSOC);
							         $uOperationResult = $uniteRow['OperationResult'];
							         $Log.=$uOperationResult=="Y"?"$_StuffId -配件的关联表保存成功!<br>":"<div class=redB>" .$_StuffId . " -配件的关联表保存失败!</div><br>";
							         $uOperationResult=null;
							         $uniteResult=null;
					            } 
					            if($OperationResult=="Y" && $UpTestStandard==1){
						               $tandardSql = "SELECT Id FROM  $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type=9";
						               $tandardResult = $myPDO->query($tandardSql);
			                           $tandardRow =$tandardResult->fetch(PDO::FETCH_ASSOC);
			                           $tandardId = $tandardRow["Id"];
			                           $tandardResult = null;
			                           $tandardRow = null;
			                           if($tandardId==""){
				                            $insertSql = "INSERT INTO $DataIn.yw2_orderteststandard(Id,POrderId,Type,Date,Operator, Estate,Locks)VALUES(NULL,'$POrderId','9','$Date','$Operator','1','0')";
				                            $insertResult = $myPDO->exec($insertSql);
						               }
					            } 
				             
			             }else{
				              
				                $myResult=$myPDO->query("CALL proc_cg1_stocksheet_add($mStockId,'$_StuffId','0','$_Relation','$Level',$Operator);");
						        $myRow = $myResult->fetch(PDO::FETCH_ASSOC);
							    $OperationResult = $myRow['OperationResult'];
							    $Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div><br>";  
					            $NewStockId=$myRow['NewStockId'];
					            $myResult=null;
					            if ($OperationResult=="Y"){
					               $ParentNode="";
					                $checkNodeResult = $myPDO->query("SELECT getStuffParentNode($mStockId) AS ParentNode");
					                if ($checkNodeResult){
						                $checkNodeRow =$checkNodeResult->fetch(PDO::FETCH_ASSOC);
						                $ParentNode = $checkNodeRow['ParentNode'];
					                }
					                $ParentNode=$ParentNode==""?$mStuffId:$ParentNode;
					                 $checkNodeResult=null;
					                  
					                $inSql="INSERT INTO cg1_semifinished(POrderId,mStockId,mStuffId,StockId,StuffId,
					                Relation,OrderQty,ParentNode,Date,Operator)SELECT $POrderId,$mStockId,$mStuffId,StockId,
					                StuffId,$_Relation,OrderQty,$ParentNode,CURDATE(),$Operator 
					                FROM cg1_stocksheet WHERE StockId=$NewStockId ";
					                $myPDO->exec($inSql);
						            if ($OperationResult=="Y" && strlen($_Unite)>0){
							            
							             $uniteResult=$myPDO->query("CALL proc_cg1_stuffunite_add($POrderId,'$_StuffId','$_Unite',$Operator);");
							             $uniteRow = $uniteResult->fetch(PDO::FETCH_ASSOC);
								         $uOperationResult = $uniteRow['OperationResult'];
								         $Log.=$uOperationResult=="Y"?"$_StuffId -配件的关联表保存成功!<br>":"<div class=redB>" .$_StuffId . " -配件的关联表保存失败!</div><br>";
								         $uniteResult=null;
						            }
					            } 
			             }
			             if($OperationResult=="Y"){
							$checkEstateSql = "UPDATE $DataIn.cg1_addstuff  SET Estate=0
				            WHERE Id ='$Id' ";
				            $checkEstateResult = $myPDO->exec($checkEstateSql);	
				            if($checkEstateResult){
					            $Log.="<div class='greenB'>配件ID为:$_StuffId 需求单异动增加审核成功!<br></div>";
				            }
						}
					}
				}
				
				//关联配件
				/*$checkUniteSql = "SELECT S.Level,S.POrderId,S.mStockId,S.StuffId,S.uStuffId,S.Relation,A.StuffCname
						 FROM $DataIn.cg1_addstuff S
						 LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId 
						 WHERE S.Id ='$Id' AND S.Estate=1";
	            $checkUniteResult = $myPDO->query($checkUniteSql);
	            $checkUniteRow =$checkUniteResult->fetch(PDO::FETCH_ASSOC);
				for ($i=0;$i<count($ExStuffId);$i++){
			  	     $_Unite=$Unites[$i];
			  	     $_oldUnite=$oldUnites[$i];
			  	     
			  	     if ($_oldUnite!=$_Unite){
			  	         $_StuffId=$ExStuffId[$i];
			  	         if ($_oldUnite!=""){
				  	        $delSql="DELETE FROM $DataIn.cg1_stuffunite WHERE POrderId=$POrderId AND StuffId=$_StuffId";
			  	            $myPDO->exec($delSql); 
			  	         }
			  	         
			  	         $UniteArray=explode(",", $_Unite);
			  	         $IN_recode="";
			  	         for ($n=0;$n<count($UniteArray);$n++){
			  	               $UniteId=$UniteArray[$n];
							   if ($UniteId>0){
							      $IN_recode.=$IN_recode==""?"":",";
								  $IN_recode.="(NULL,'$POrderId','$ProductId','$_StuffId','$UniteId',0,'$Date','$Operator')";     
							   }     
			  	         }
			  	         if ($IN_recode!="") {
			  	              $IN_recode="INSERT INTO $DataIn.cg1_stuffunite (Id,POrderId,ProductId,StuffId,uStuffId,Relation,Date,Operator) VALUES $IN_recode";
			  	              $myPDO->exec($IN_recode);
			  	         }
			  	         $Log.="已更新 $_StuffId 的关联配件<br>";
			  	     }
			  	  }*/
				
		       $fromWebPage=$funFrom."_m3";
		      break;

		    } 
	    break;
	    
	    
	    case 23:		//新加需求单,需要主管审核，审核走yw1_order_updated /m3
	      $updateTestStandard = $updateTestStandard==""?0:$updateTestStandard;
          for ($i=0;$i<count($StuffId);$i++){
               $_StuffId=$StuffId[$i];
			   $_Relation=$pandsQty[$i];
			   $_Unite=$Unite[$i]==""?0:$Unite[$i];
			   $DelSql="DELETE FROM $DataIn.cg1_addstuff WHERE POrderId=$POrderId AND StuffId=$_StuffId AND Estate>0 AND Level =1";
	  	       $myPDO->exec($DelSql); 
	  	       $In_Sql="INSERT INTO $DataIn.cg1_addstuff (Id,Level,POrderId,mStockId,mStuffId,StuffId,uStuffId,Relation,
	  	       UpTestStandard,Estate,Locks,Date,Operator,PLocks,creator,created,modifier,modified)
	  	       VALUES(NULL,'1','$POrderId',NULL,'0','$_StuffId','$_Unite','$_Relation','$updateTestStandard','1','0','$Date',
	  	       '$Operator','0','$Operator','$DateTime','$Operator','$DateTime')";
	  	       $In_Result = $myPDO->exec($In_Sql);
               $addId = $myPDO->lastInsertId();
               if($addId>0){
	               $Log.="<div class='greenB'>配件ID为:$_StuffId 需求单异动增加成功，请通知主管审核有效！<br></div>";
	               
               }else{
	               $Log.="<div class='redB'>配件ID为:$_StuffId 需求单异动添加失败！$In_Sql<br></div>";
	               $OperationResult = "N";
               }
          }
          
          for ($i=0;$i<count($ExStuffId);$i++){
			  	     $_Unite=$Unites[$i];
			  	     $_oldUnite=$oldUnites[$i];
			  	     
			  	     if ($_oldUnite!=$_Unite){
			  	         $_StuffId=$ExStuffId[$i];
			  	         if ($_oldUnite!=""){
				  	        $delSql="DELETE FROM $DataIn.cg1_stuffunite WHERE POrderId=$POrderId AND StuffId=$_StuffId";
			  	            $myPDO->exec($delSql); 
			  	         }
			  	         
			  	         $UniteArray=explode(",", $_Unite);
			  	         $IN_recode="";
			  	         for ($n=0;$n<count($UniteArray);$n++){
			  	               $UniteId=$UniteArray[$n];
							   if ($UniteId>0){
							      $IN_recode.=$IN_recode==""?"":",";
								  $IN_recode.="(NULL,'$POrderId','$ProductId','$_StuffId','$UniteId',0,'$Date','$Operator')";     
							   }     
			  	         }
			  	         if ($IN_recode!="") {
			  	              $IN_recode="INSERT INTO $DataIn.cg1_stuffunite (Id,POrderId,ProductId,StuffId,uStuffId,Relation,Date,Operator) VALUES $IN_recode";
			  	              $myPDO->exec($IN_recode);
			  	         }
			  	         $Log.="已更新 $_StuffId 的关联配件<br>";
			  	     }
			  	  }
          
          

	     break;
	   case 72: //生产工单设置
	      $_sPOrderId=$IdList;
	      $_sQty=$QtyList;
	      $_wsId=$WsList;
	      $_LockSignId = $LockSignList;
	      if ($POrderId!="" && $_sPOrderId!="" && $_sQty!=""){
	      
			    $checkIdSql = "SELECT Id FROM $DataIn.yw1_ordersplit WHERE POrderId='$POrderId' AND Estate>=1 LIMIT 1";
	            $checkIdResult = $myPDO->query($checkIdSql);
	            $checkIdRow =$checkIdResult->fetch(PDO::FETCH_ASSOC);
	            $checkId = $checkIdRow["Id"];
	            if($checkId>0){
		            $checkUpdateSql = "UPDATE $DataIn.yw1_ordersplit SET sPOrderId='$_sPOrderId',splitQty='$_sQty',wsId='$_wsId',LockSign='$_LockSignId',Estate=1,
		            modifier='$Operator',modified='$DateTime' WHERE Id ='$checkId'";
	            }else{
		            $checkUpdateSql = "INSERT INTO $DataIn.yw1_ordersplit(Id,POrderId,sPOrderId,splitQty,wsId,LockSign,Estate,Date,  
		            Operator,Locks,PLocks,creator,created,modifier,modified)VALUES(NULL,'$POrderId','$_sPOrderId','$_sQty','$_wsId',
		            '$_LockSignId','1','$Date','$Operator','0','0','$Operator','$DateTime','$Operator','$DateTime')";  
	            }
	            

	            $checkUpdateResult = $myPDO->exec($checkUpdateSql);
	            if($checkUpdateResult){
				   $Log.="<p>&nbsp;&nbsp;&nbsp;&nbsp;POrderId为 $POrderId 的订单拆分工单成功,需主管审核才有效.<br>";
				   //锁定工单
				   /*$_sPOrderIdArray = explode("|",$_sPOrderId);
				   $_sPOrderIdCount = count($_sPOrderIdArray);
				   for($k=0;$k<$_sPOrderIdCount;$k++){
					   $tempsPOrderId= $_sPOrderIdArray[$k];
					   if($tempsPOrderId>0){
					        $LockRemark = "工单拆分锁定，等待主管审核!";
						    $count_TempResult=$myPDO->query("SELECT count( * ) AS counts FROM $DataIn.yw1_sclock WHERE sPOrderId='$tempsPOrderId'");  
						    $count_TempRow =$count_TempResult->fetch(PDO::FETCH_ASSOC);
							$counts=$count_TempRow["counts"];
							$count_TempResult = null;
							if ($counts<1){ 
								$inRecode="INSERT INTO $DataIn.yw1_sclock (Id,sPOrderId,Estate,Locks,Remark,Date,Operator,creator,created,modifier,modified) VALUES (NULL,'$tempsPOrderId','1','0','$LockRemark','$Date','$Operator','$Operator','$DateTime','$Operator','$DateTime') ";
								$inResult=$myPDO->exec($inRecode);
							}
							else{
								$inRecode = "UPDATE $DataIn.yw1_sclock  SET Locks='0',Remark='$LockRemark',modifier='$Operator',modified='$DateTime'  WHERE sPOrderId='$tempsPOrderId'";
								$inResult = $myPDO->exec($inRecode);
							}
					   } 
				   }*/
				   
			   }

			    
	      } 
	      break;
	      
	    case 3://更新记录
	        $FilePath="../download/clientorder/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			//上传或删除文件
			if($ClientOrder!=""){	//有上传文件
				$FileType=substr("$ClientOrder_name", -4, 4);
				$OldFile=$ClientOrder;
				$PreFileName=$OrderNumber.$FileType;
				$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
				$FileValue=$uploadInfo==""?"":",ClientOrder='$PreFileName'";
				}
			else{			//没有上传文件
				if($delFile!=""){
				$DelFilePath=$FilePath.$delFile;
				if(file_exists($DelFilePath)){
					unlink($DelFilePath);
					$FileValue=",ClientOrder=''";
					}			
				}
			}
			//1主单信息更新
			$mainSql="UPDATE $DataIn.yw1_ordermain SET OrderPO='$OrderPO',OrderDate='$OrderDate' $FileValue WHERE Id='$Mid'";
		    $count = $myPDO->exec($mainSql);
		    if ($count>0){
		        $Log="<p>&nbsp;&nbsp;&nbsp;&nbsp;POrderId为 $POrderId 的主订单资料已经更新.<br>";
		    }
		    //2.数量,单价更新，需要审核处理 
            if($Qty!=$OldQty || $Price!=$OldPrice){
	            
	            $checkIdSql = "SELECT Id FROM $DataIn.yw1_orderupdate WHERE POrderId='$POrderId' AND Estate=1 LIMIT 1";
	            $checkIdResult = $myPDO->query($checkIdSql);
	            $checkIdRow =$checkIdResult->fetch(PDO::FETCH_ASSOC);
	            $checkId = $checkIdRow["Id"];
	            if($checkId>0){
		            $checkUpdateSql = "UPDATE $DataIn.yw1_orderupdate  
		            SET  OrderPO='$OrderPO',OldQty='$OldQty', NewQty='$Qty',OldPrice='$OldPrice',NewPrice='$Price'
		            WHERE Id ='$checkId'";
	            }else{
		            $checkUpdateSql = "INSERT INTO $DataIn.yw1_orderupdate(Id,POrderId,OrderPO,OldQty,NewQty,OldPrice,
		            NewPrice,Remark,Estate,Date,Operator,Locks,PLocks,creator,created,modifier,modified)
		            VALUES(NULL,'$POrderId','$OrderPO','$OldQty','$Qty','$OldPrice',
		            '$Price','$updateRemark','1','$Date','$Operator','0','0','$Operator','$DateTime','$Operator','$DateTime')";
		            
	            }
	            $checkUpdateResult = $myPDO->exec($checkUpdateSql);
	            if($checkUpdateResult){
				   $Log.="<p>&nbsp;&nbsp;&nbsp;&nbsp;POrderId为 $POrderId 的订单资料更新了数量和价格,需主管审核才有效.<br>";
			    }
	            $checkIdResult     = null;
	            $checkUpdateResult = null;
            }
            
			
			if($itf != ""){ //针对CEL的喷码参数保存
				$hasPrintParameterSql = "SELECT count(*) FROM $DataIn.printparameters 
				WHERE itf = '$itf' AND lotto = '$lotto' AND POrderId = '$POrderId'"; 	  
		        $myResult = $myPDO->query($hasPrintParameterSql);
                $myRow =$myResult->fetch(PDO::FETCH_ASSOC);
                $count = $myRow[0];
                $myResult=null;
				if($count== 0){
					$insertPrintParameters = "INSERT INTO $DataIn.printparameters(Id,POrderId,Lotto,itf) 
					Values (NULL,'$POrderId','$lotto', '$itf')";
					$myPDO->exec($insertPrintParameters);
				}
			}
	  break;	
			
	  case "PIDate":
	        $Log_Funtion="更新PI交期";
	        if($hasLeadtimeSign=="YES"){         //PI交期变动，变动交期要通知主管审核才有效
	           $ChangeSql = "REPLACE INTO  $DataIn.yw3_pileadtimechange(Id,POrderId,UpdateLeadtime,OldLeadtime,ReduceWeeks,OldReduceWeeks,Estate,Remark,Date,Operator) Values (NULL,'$POrderId','$PIDate','','$ReduceWeeks','','1','$updateWeekRemark','$Date','$Operator') ";
	           $count = $myPDO->exec($ChangeSql);            
	        }
	        else{
		        //设置订单采购交期
			  	if ($ReduceWeeks==='') $ReduceWeeks=-1; 
			  	$myResult=$myPDO->query("CALL proc_yw1_ordersheet_setdeliverydate('$POrderId','$PIDate',$ReduceWeeks,'1',$Operator);");
			  	$myRow = $myResult->fetch(PDO::FETCH_ASSOC);
		  		$OperationResult = $myRow['OperationResult'];
		  		$Log.=$OperationResult=="Y"?$myRow['OperationLog']:"<div class=redB>" .$myRow['OperationLog'] . "</div>";
				       
			    $myResult=null;
			    $myRow=null;
			    
			    $IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	            $myPDO->exec($IN_recode); 
	        }

	        break;
	   case "AddStuffCombox":
	       $Log_Funtion="设置子母配件关系";
	       $myResult=$myPDO->query("CALL proc_cg1_stuffcombox_insert('$StockId',$Operator);");
	       $SaveOprationlog=1;
	       $myResult=null;
	       break;
	}
	
}else{
	include "../model/modelhead.php";
	include "../model/stuffcombox_function.php";
	
	$fromWebPage=$funFrom."_read";
	$nowWebPage=$funFrom."_updated";
	$_SESSION["nowWebPage"]=$nowWebPage; 
	//步骤2：
	$Log_Item="产品订单";		//需处理
	$upDataSheet="$DataIn.yw1_ordersheet";	//需处理
	$Log_Funtion="更新";
	$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
	ChangeWtitle($TitleSTR);
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	$Operator=$Login_P_Number;
	$OperationResult="Y";
	$ALType="From=$From&Pagination=$Pagination&Page=$Page&CompanyId=$CompanyId";
	$SaveOprationlog=1;
	//步骤3：需处理，更新操作
	$x=1;
	switch($ActionId){
		case 7:
			$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
		case 8:
			$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
		case 21://如果原来为加急，再点时则取消加急
			$Log_Funtion="加急";
			$Lens=count($checkid);
			for($i=0;$i<$Lens;$i++){
				$Id=$checkid[$i];
				if ($Id!=""){
					$Check7Sql=mysql_query("SELECT E.Id FROM $DataIn.yw2_orderexpress E LEFT JOIN $upDataSheet S ON S.POrderId=E.POrderId WHERE S.Id='$Id' AND E.Type='7'",$link_id);
					if($Check7Row=mysql_fetch_array($Check7Sql)){//取消加急
						$DelSql="DELETE FROM $DataIn.yw2_orderexpress WHERE Type='7' AND POrderId=(SELECT POrderId FROM $upDataSheet WHERE Id='$Id')";
						$DelResult=mysql_query($DelSql);
						if($DelResult){
							$Log.="&nbsp;&nbsp;订单明细ID为 $Id 的订单取消加急A状态.</br>";
							}
						else{
							$Log.="<div class='redB'>&nbsp;&nbsp;订单明细ID为 $Id 的订单取消加急A状态失败. $DelSql </div></br>";
							$OperationResult="N";
							}
						}
					else{//加急
					$sql = "UPDATE  $DataIn.yw2_orderexpress SET Type=7 WHERE POrderId=(SELECT POrderId FROM $upDataSheet WHERE Id='$Id') AND Type>6";
						$result = mysql_query($sql);
						if ($result  && mysql_affected_rows()>0){
							$Log.="&nbsp;&nbsp;订单明细ID为 $Id 的订单状态更新为加急A.</br>";
							}
						else{
							 $inRecode="INSERT INTO $DataIn.yw2_orderexpress SELECT NULL,POrderId,'7','','','1','$Date','$Operator','$DateTime','0','0','$Operator',NOW(),'$Operator',NOW()  
							 FROM $upDataSheet WHERE Id='$Id'";
							
							$inResult=@mysql_query($inRecode);
							if($inResult){
								$Log.="&nbsp;&nbsp;订单明细ID为 $Id 的订单状态设为加急A.</br>";
								}
							else{
								$Log.="<div class='redB'>&nbsp;&nbsp;订单明细ID为 $Id 的订单加急A状态失败. $inRecode </div></br>";
								$OperationResult="N";
								}
							}
						}
					}
				}
			break;
		
		case 74://如果原来为更改标准图的，再点时则取消 
			$Log_Funtion="如果原来为更改标准图";
			$Lens=count($checkid);
			for($i=0;$i<$Lens;$i++){
				$Id=$checkid[$i];
				if ($Id!=""){
					$Check7Sql=mysql_query("SELECT E.Id FROM $DataIn.yw2_orderteststandard E LEFT JOIN $upDataSheet S ON S.POrderId=E.POrderId WHERE S.Id='$Id' AND E.Type='9'",$link_id);
					if($Check7Row=mysql_fetch_array($Check7Sql)){//取消更改标准图
						$DelSql="DELETE FROM $DataIn.yw2_orderteststandard WHERE Type='9' AND POrderId=(SELECT POrderId FROM $upDataSheet WHERE Id='$Id')";
						$DelResult=mysql_query($DelSql);
						if($DelResult){
							$Log.="&nbsp;&nbsp;订单明细ID为 $Id 的订单取消更改标准图状态.</br>";
							}
						else{
							$Log.="<div class='redB'>&nbsp;&nbsp;订单明细ID为 $Id 的订单取消更改标准图状态失败. $DelSql </div></br>";
							$OperationResult="N";
							}
						}
					else{//更改标准图
						$inRecode="INSERT INTO $DataIn.yw2_orderteststandard SELECT NULL,POrderId,'9','$Date','$Operator','1','0','0','$Operator',NOW(),'$Operator',NOW() FROM $upDataSheet WHERE Id='$Id'";
						
						$inResult=@mysql_query($inRecode);
						if($inResult){
							$Log.="&nbsp;&nbsp;订单明细ID为 $Id 的订单状态设为更改标准图.</br>";
							}
						else{
							$Log.="<div class='redB'>&nbsp;&nbsp;订单明细ID为 $Id 的订单更改标准图状态失败. $inRecode </div></br>";
							$OperationResult="N";
							}
						}
					}
				}
			break;	
		

	
		case 60:
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
			
			$Type=2;//未确认标记
				if ($Id!=""){
					$Check7Sql=mysql_query("SELECT E.Id FROM $DataIn.yw2_orderexpress E LEFT JOIN $upDataSheet S ON S.POrderId=E.POrderId WHERE S.Id='$Id' AND E.Type='$Type'",$link_id);
					if($Check7Row=mysql_fetch_array($Check7Sql)){
			
					     $inRecode="INSERT INTO $DataIn.yw2_orderexpress_log  SELECT NULL,POrderId,Type,Remark,Date,Operator,'1','0','0','$Operator',NOW(),'$Operator',NOW()  FROM $DataIn.yw2_orderexpress WHERE  POrderId=(SELECT POrderId FROM $upDataSheet WHERE Id='$Id') AND Type='2'";
					    
	                    $inAction=@mysql_query($inRecode);
	                    
						$DelSql="DELETE FROM $DataIn.yw2_orderexpress WHERE Type='$Type' AND POrderId=(SELECT POrderId FROM $upDataSheet WHERE Id='$Id')";
						$DelResult=mysql_query($DelSql);
						if($DelResult){
						   //更新未下采单时间
						   $upcgSql="UPDATE $DataIn.cg1_stocksheet S 
						           LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId
		                           LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
						           SET S.ywOrderDTime=NOW() 
						           WHERE S.POrderId=(SELECT POrderId FROM $upDataSheet WHERE Id='$Id') AND T.mainType<2 AND S.Mid=0";
						    $upResult=mysql_query($upcgSql);     
						      
							$Log.="&nbsp;&nbsp;订单明细ID为 $Id 的订单取消未确认标记.</br>";
							}
						else{
							$Log.="<div class='redB'>&nbsp;&nbsp;订单明细ID为 $Id 的订单取消未确认标记.失败. $DelSql </div></br>";
							$OperationResult="N";
							}
						}
					else{
						$inRecode="INSERT INTO $DataIn.yw2_orderexpress SELECT NULL,POrderId,'$Type','$Remark','','1','$Date','$Operator','$DateTime','0','0','$Operator',NOW(),'$Operator',NOW() FROM $upDataSheet WHERE Id='$Id'";
						
						$inResult=@mysql_query($inRecode);
						if($inResult){
							$Log.="&nbsp;&nbsp;订单明细ID为 $Id 的订单标记为未确认.</br>";
							}
						else{
							$Log.="<div class='redB'>&nbsp;&nbsp;订单明细ID为 $Id 的订单标记未确认失败. $inRecode </div></br>";
							$OperationResult="N";
							}
						}
					}
			break;

		case 15://退回删除配件需求单
		 switch($From){
		      case "m":
				$Log_Funtion="退回配件需求单删除申请";
				$upDataSheet="$DataIn.cg1_stocksheet";
				$SetStr="Estate=0,modifier='$Operator',modified=NOW()";				
				include "../model/subprogram/updated_model_3d.php";
				$fromWebPage=$funFrom."_m";
			  break;
			  
			  case "m3":
	            $Log_Funtion = "采购类配件需求单增加退回";
				$Lens=count($checkid);
				for($i=0;$i<$Lens;$i++){
					$Id=$checkid[$i];
					if ($Id!=""){
							 
						 $DelSql = "DELETE FROM $DataIn.cg1_addstuff WHERE Id = $Id AND Estate>0  ";
						 $DelResult = mysql_query($DelSql);
						 if ($DelResult && mysql_affected_rows()>0){
							$Log="&nbsp;&nbsp;ID在( $Id )的采购类配件需求单增加退回 成功.<br>";
							}
						else{
							$OperationResult="N";
							$Log="<div class='redB'>ID在( $Id )的采购类配件需求单增加退回 失败.</div><br>";
							}
					}
				}
				$fromWebPage=$funFrom."_m3";
			  
			  break; 
			}
		break;
	

		case "delStuff"://要测试数量是否退回库存
			$Log_Funtion="标记删除需求单";//如果有领料，先删除领料退料记录
			$SetStr="Estate=4,StockRemark='$StockRemark',modifier='$Operator',modified=NOW()";
			$sql = "UPDATE $DataIn.cg1_stocksheet SET $SetStr WHERE StockId='$StockId'";
			$result = mysql_query($sql);
			if($result){
			       $insertSql= "INSERT INTO $DataIn.cg1_stocksheet_log(StockId,Opcode,Estate,Locks,Date,Operator,PLocks,creator,created) SELECT StockId,'4',Estate,Locks,'$DateTime','$Operator',0,'$Operator','$DateTime' FROM $DataIn.cg1_stocksheet WHERE StockId='$StockId' ";
		           $insertResult= mysql_query($insertSql);
		               
			      $Log="需求单 $StockId 标记删除成功.</br>";
				}
			else{
				$Log="需求单 $StockId 标记删除失败! $sql</br>";
				$OperationResult="N";
				}
			break;
		case "PackRemark":
			$Log_Funtion="更新包装说明";
			$sql = "UPDATE $upDataSheet SET PackRemark='$tempPackRemark' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
			$result = mysql_query($sql);
			if ($result){
				$Log="订单流水号为 $POrderId 的包装方式更新成功. $sql <br>";
				$insertSql = "INSERT INTO $DataIn.yw2_orderremark(Id,POrderId,Type,Remark,Date,Operator) Values (NULL,'$POrderId','2','$tempPackRemark','$DateTime','$Operator')";
			  $result = mysql_query($insertSql);
				}
			else{
				$Log="<div class=redB>订单流水号为 $POrderId 的包装方式更新失败. $sql </div><br>";
				$OperationResult="N";
				}
			break;
	
		case "OrderPO":
			$Log_Funtion="更新PO";
			$sql = "UPDATE $upDataSheet SET OrderPO='$tempOrderPO' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
			$result = mysql_query($sql);
			if ($result){
				$Log="订单流水号为 $POrderId PO更新成功. $sql <br>";
				}
			else{
				$Log="<div class=redB>订单流水号为 $POrderId 的PO更新失败. $sql </div><br>";
				$OperationResult="N";
				}
				
			break;
			
		case "sgRemark":
			
			$Log_Funtion="更新生管备注";
			$sql = "UPDATE $upDataSheet SET sgRemark='$sgRemark' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
			$result = mysql_query($sql);
			if ($result){
				$Log="订单流水号为 $POrderId 的生管备注更新成功.<br>";
				}
			else{
				$Log="<div class=redB>订单流水号为 $POrderId 的生管备注更新失败.</div><br>";
				$OperationResult="N";
				}
			break;
		case "cgRemark":
			$Log_Funtion="更新采购备注";
			$sql = "UPDATE $upDataSheet SET cgRemark='$cgRemark' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
			$result = mysql_query($sql);
			if ($result){
				$Log="订单流水号为 $POrderId 的采购备注更新成功.<br>";
				}
			else{
				$Log="<div class=redB>订单流水号为 $POrderId 的采购备注更新失败.</div><br>";
				$OperationResult="N";
				}
			break;	
	
		case "dcRemark":
			$Log_Funtion="更新待出备注";
			$sql = "UPDATE $upDataSheet SET dcRemark='$dcRemark' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
			$result = mysql_query($sql);
			if ($result){
				$Log="订单流水号为 $POrderId 的待出备注更新成功.<br>";
				}
			else{
				$Log="<div class=redB>订单流水号为 $POrderId 的待出备注更新失败.</div><br>";
				$OperationResult="N";
				}
			break;			
	  case "enRemark":
			$Log_Funtion="更新英文备注";
			  $insertSql = "INSERT INTO $DataIn.yw2_orderremark(Id,POrderId,Type,Remark,Date,Operator) Values (NULL,'$POrderId','1','$enRemark','$DateTime','$Operator')";
			  $result = mysql_query($insertSql);
			  if ($result){
				$Log="订单流水号为 $POrderId 的英文备注更新成功.<br>";
				}
			else{
				$Log="<div class=redB>订单流水号为 $POrderId 的英文备注更新失败. $insertSql</div><br>";
				$OperationResult="N";
				}
			break;
			
	  case "Price":
	      $Log_Funtion="更新价格";
	      $sql = "UPDATE $upDataSheet SET Price='$Price' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
	      
	      echo $sql;
	      
	      $result = mysql_query($sql);
	      if ($result){
	          $Log="订单流水号为 $POrderId 的订单价格更新成功.<br>";
	          
	          //根据设计的体积 更新产品的价格
	          $sql = "select p.MainWeight, p.ProductId from 
                            $DataIn.productdata p 
                            LEFT JOIN $DataIn.yw1_ordersheet y on p.ProductId = y.ProductId
                            where y.POrderId = '$POrderId' LIMIT 1";
              $result1 = mysql_query($sql);
              
              if ($result1 && $row=mysql_fetch_row($result1)) {
                  $MainWeight = $row["MainWeight"];
                  $ProductId = $row["ProductId"];
                  $Price = $Price * $MainWeight;
                  
                  $sql = "UPDATE $DataIn.productdata SET Price='$Price' WHERE ProductId='$ProductId' ORDER BY Id DESC LIMIT 1";
                  $result = mysql_query($sql);
              }

	      }
	      else{
	          $Log="<div class=redB>订单流水号为 $POrderId 的价格更新失败.</div><br>";
	          $OperationResult="N";
	      }
	      break;
			
		case "ShipType":	
			$sql = "UPDATE $upDataSheet SET ShipType='$tempShipType' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
			$result = mysql_query($sql);
			if ($result){
				$Log="订单流水号为 $POrderId 的出货方式更新成功.<br>";
				}
			else{
				$Log="<div class=redB>订单流水号为 $POrderId 的出货方式更新失败.</div><br>";
				$OperationResult="N";
				}
			break;
		case "DeliveryDate":
			$Log_Funtion="更新订单交货期";
			$sql = "UPDATE $upDataSheet SET DeliveryDate='$DeliveryDate' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
			$result = mysql_query($sql);
			if ($result){
				$Log="订单流水号为 $POrderId 的订单交货期更新成功.<br>";
				}
			else{
				$Log="<div class=redB>订单流水号为 $POrderId 的订单交货期更新失败.</div><br>";
				$OperationResult="N";
				}
			break;
		case "PIDate":
		   $Log_Funtion="更新PI交期";
	     if($hasLeadtimeSign=="YES"){         //PI交期变动，变动交期要通知主管审核才有效
	                    $ChangeLeadtimeResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.yw3_pileadtimechange WHERE POrderId=$POrderId",$link_id));
	                    $ChangeId=$ChangeLeadtimeResult["Id"];
	                   if($ChangeId>0){
			                $ChangeSql = "UPDATE $DataIn.yw3_pileadtimechange SET UpdateLeadtime='$PIDate',OldLeadtime='',ReduceWeeks='$ReduceWeeks', OldReduceWeeks='',Estate=1,Remark='$updateWeekRemark' ,Operator='$Operator' WHERE POrderId='$POrderId' ";
	                        $ChangeResult=@mysql_query($ChangeSql);
	                     }
	                  else{
			                  $ChangeSql = "INSERT INTO  $DataIn.yw3_pileadtimechange (Id,POrderId,UpdateLeadtime,OldLeadtime,ReduceWeeks,OldReduceWeeks,
	                          Estate,Remark,Date,Operator) Values (NULL,'$POrderId','$PIDate','','$ReduceWeeks','','1','$updateWeekRemark','$Date','$Operator') ";
	                         $ChangeResult=@mysql_query($ChangeSql);
	                        }
	            }
	   else{
		 		  if ($PIoId=="N"){
			 		    $sql="Replace into $DataIn.yw3_pileadtime(Id,POrderId,Leadtime,Date,Operator) Values (NULL,'$POrderId','$PIDate','$Date','$Operator')";
		 		  }
		 		  else{
					      $sql = "UPDATE $DataIn.yw3_pisheet SET Leadtime='$PIDate' WHERE oId='$PIoId' ";
					}
	
					$result = mysql_query($sql);
					if ($result &&  mysql_affected_rows()>0){
						$Log="订单流水号为 $POrderId 的PI交期更新成功.<br>";
			
			 		   //设置订单采购交期
			  		  if (strlen($ReduceWeeks)==0) $ReduceWeeks=-1;
		     		   $updateCGSql="";
		      		  $CheckResult=mysql_query("SELECT ReduceWeeks FROM $DataIn.yw2_cgdeliverydate WHERE POrderId='$POrderId' LIMIT 1",$link_id);
						if($CheckRow=mysql_fetch_array($CheckResult)){
				    		   $oldReduceWeeks=$CheckRow["ReduceWeeks"];
				    		   if ($oldReduceWeeks!=$ReduceWeeks){
					    		       $updateCGSql = "UPDATE $DataIn.yw2_cgdeliverydate SET ReduceWeeks='$ReduceWeeks',Date='$DateTime',Operator='$Operator' WHERE POrderId='$POrderId' ";
							   }
						}
						else{
				     		    $updateCGSql = "INSERT INTO $DataIn.yw2_cgdeliverydate(Id,POrderId,ReduceWeeks,Date,Operator) Values (NULL,'$POrderId','$ReduceWeeks','$DateTime','$Operator')";
						 }
						 if ($updateCGSql!=""){
							     $updateResult = mysql_query($updateCGSql);
								 if ($updateResult && mysql_affected_rows()>0){
									     $Log.="&nbsp;&nbsp;订单流水号$POrderId 的采购交期设置成功.</br>";
								 }
								 else{
									    $Log.="&nbsp;&nbsp;<div class='redB'>订单流水号$POrderId 的采购交期设置失败.</div>$updateSql</br>";
								 }
						  }
			  
						//保存原已下采购单的交货日期	
						     $insertSql="INSERT INTO $DataIn.cg1_deliverydate SELECT NULL,StockId,DeliveryDate,'1','0','$Date','$Operator','0','$Operator',NOW(),'$Operator',NOW() FROM $DataIn.cg1_stocksheet WHERE POrderId='$POrderId' AND Mid>0 AND DeliveryDate>'2012-01-01'";
						 $insertResult= mysql_query($insertSql);
				 
						 //按设置的交货周期更新交货日期
		    		   $Leadtime=explode("*",$PIDate);
		     		   $PIDate=$Leadtime[0];
						$CheckSql=mysql_query("SELECT G.Id,D.TypeId,G.CompanyId,G.StockId,YEARWEEK('$PIDate',1)  AS PIWeek,YEARWEEK(CURDATE(),1) AS CurWeek   
				   		         FROM $DataIn.yw1_ordersheet S
									LEFT JOIN  $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId 
									LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId  
									LEFT JOIN  $DataIn.stuffproperty  OP  ON OP.StuffId=G.StuffId AND OP.Property=2 
									WHERE S.POrderId='$POrderId' AND (G.Mid>0 OR  OP.Property=2)",$link_id);
						while($CheckRow = mysql_fetch_array($CheckSql)){
						     $sId=$CheckRow["Id"];
						     $TypeId=$CheckRow["TypeId"];
				       
						     $PIWeek=$CheckRow["PIWeek"];
				  		     $CurWeek=$CheckRow["CurWeek"];
				       
				  		     $jhDays= $ReduceWeeks*7;
				  		     $DeliveryDate=$ReduceWeeks==0?$PIDate:date("Y-m-d",strtotime("$PIDate  $jhDays  day"));
				
				  		     $DeliveryDateSql = "UPDATE $DataIn.cg1_stocksheet SET DeliveryDate='$DeliveryDate' WHERE Id='$sId' AND Estate='0'";
						     $DeliveryDateResult = mysql_query($DeliveryDateSql);
				       
						}
					}
					else{
						$Log.="<div class=redB>订单流水号为 $POrderId 的PI交期更新失败.</div>$sql<br>";
						$OperationResult="N";
						}
	        }
			break;
		case "ChangeStuff":
			$Log_Funtion="更新配件";//更新配件明细，更新收货明细，更新领料明细
			$sql = "UPDATE $DataIn.cg1_stocksheet G 
			LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=G.StockId
			LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId=G.StockId
			SET G.StuffId='$StuffId',G.Price='$Price',R.StuffId='$StuffId',L.StuffId='$StuffId' WHERE G.StockId='$StockId'";
			$result = mysql_query($sql);
			if ($result){
				$Log="流水号为 $StockId 的配件更新成功.<br>";
				}
			else{
				$Log="<div class=redB>流水号为 $StockId 的配件更新失败.</div><br>";
				$OperationResult="N";
				}
			break;
			
		case 131: //生产类配件置换 需审核
		    
		    $checkStuffRow = mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.yw1_stuffchange WHERE StockId = $StockId AND Estate>0 ", $link_id));
		    $ChangeId = $checkStuffRow["Id"];
		    if($ChangeId>0){
			    $Log="<div class=redB>流水号为 $StockId 的配件置换失败，有未审核的置换记录.</div><br>";
				$OperationResult="N";
			    
		    }else{
		       
			    $InsertStuffSql = "INSERT INTO $DataIn.yw1_stuffchange(Id,POrderId,StockId,OldStuffId,NewStuffId,NewRelation,Remark,
			    Date,Operator, Estate,Locks,PLocks,creator,created,modifier,modified) VALUES(NULL,'$POrderId','$StockId','$OldStuffId',
			    '$ChangeStuffId','$NewRelation','$Remark','$Date','$Operator','1','0','0','$Operator','$DateTime','$Operator','$DateTime')";
			    $InsertStuffResult = mysql_query($InsertStuffSql);
				if ($InsertStuffResult){
					$Log="流水号为 $StockId 的配件 $OldStuffId 置换为 $ChangeStuffId  成功，请通知主管审核<br>";
					}
				else{
					$Log="<div class=redB>流水号为 $StockId 的配件 $OldStuffId 置换为 $ChangeStuffId 失败.$InsertStuffSql</div><br>";
					$OperationResult="N";
				 }
		    }
		
		  break;

        case "liningNo": //更新台车
            $Log_Funtion="更新台车";
            $sql = "UPDATE $upDataSheet SET RealLining='$liningNo' WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1";
            $result = mysql_query($sql);
            if ($result){
                $Log="订单流水号为 $POrderId 台车更新成功. $sql <br>";
            }
            else{
                $Log="<div class=redB>订单流水号为 $POrderId 的台车更新失败. $sql </div><br>";
                $OperationResult="N";
            }

            break;

		  
		default://更新订单资料OK
			$FilePath="../download/clientorder/";
			if(!file_exists($FilePath)){
				makedir($FilePath);
				}
			//上传或删除文件
			if($ClientOrder!=""){	//有上传文件
				$FileType=substr("$ClientOrder_name", -4, 4);
				$OldFile=$ClientOrder;
				$PreFileName=$OrderNumber.$FileType;
				$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
				$FileValue=$uploadInfo==""?"":",ClientOrder='$PreFileName'";
				}
			else{					//没有上传文件
				if($delFile!=""){//已选取删除原文件
				$DelFilePath=$FilePath.$delFile;
				if(file_exists($DelFilePath)){
					unlink($DelFilePath);
					$FileValue=",ClientOrder=''";
					}			
				}
			}
			//1主单信息更新
			$mainSql = "UPDATE $DataIn.yw1_ordermain SET OrderPO='$OrderPO',OrderDate='$OrderDate' $FileValue WHERE Id='$Mid'";
			$mainResult = mysql_query($mainSql);
			if ($mainResult){
				$Log="<p>Id为 $Mid 的主订单资料已经更新.<br>";
				}
			//2.数量处理
			if ($Qty>0){
			//	include "subprogram/updated_model_order.php";
			}
	
			//针对CEL的喷码参数保存
			if($itf != ""){
				$hasPrintParameterSql = "Select * From $DataIn.printparameters Where itf = '$itf' and lotto = '$lotto' and POrderId = '$POrderId'";
				$hasPrintParameterResult = mysql_query($hasPrintParameterSql);
				if(mysql_num_rows($hasPrintParameterResult) == 0)
				{
					$insertPrintParameters = "Insert into $DataIn.printparameters (Id, POrderId, Lotto, itf) Values (NULL, '$POrderId', '$lotto', '$itf')";
					mysql_query($insertPrintParameters);
				}
	
			}
	
			break;		
		}
}
if ($SaveOprationlog==1){
	$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
	$IN_res=@mysql_query($IN_recode);
}
if ($ActionId=="AddStuffCombox"){
    echo $OperationResult;
}else{
    include "../model/logpage.php";	
}

?>