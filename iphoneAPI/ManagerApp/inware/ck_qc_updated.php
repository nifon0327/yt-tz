<?php 
 $Log_Item="品检记录";
 $curDate=date("Y-m-d");
 $DateTime=date("Y-m-d H:i:s");
 $OperationResult="N";
 $Operator=$LoginNumber;
 include "ck_function.php";
 
 switch($ActionId){
	 	 case "current_tv": {
		 $stuffid=$info[0];
		 $gys_id=$info[1];
		  $Log_Funtion="连接电视";
		 $lineQuanChou=$info[2];
		 $lineQuanChou = "3B-".($lineQuanChou==""?1:$lineQuanChou);
		 // -1 全检   0 抽检
		  $upSql = "UPDATE $DataIn.qc_currentcheck SET gys_id='$gys_id',stuffId='$stuffid' 
		  ,datetime='$DateTime'
		  WHERE line_app='$lineQuanChou'";
			   $upResult = mysql_query($upSql,$link_id);
			   $infoSTR = $upSql;
				if ($upResult){
					$OperationResult = 'Y';
					}
		 
	 }
	 break;
	 case  "Capacity": {
		  $Log_Funtion="装箱数量保存";
		 $FrameCapacityNew = $info[0]; $CapacityStuff = $info[1];
		 $success = updateStuffFrameCapacity($DataIn,$CapacityStuff,$FrameCapacityNew);
		 if ($success) {
			  $Log=$Log_Item .$Log_Funtion . "成功! StuffId:$CapacityStuff<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
		 } else {
			  $Log="<div class=redB>$Log_Item $Log_Funtion 失败!   StuffId:$CapacityStuff</div><br>";
                        $infoSTR=$Log_Funtion ."失败";
		 }
		 
	 }
	 	break;
          case "Remark":
               $LineId=$info[0];$Sid=$info[1]; $Remark=$info[2];
               $Log_Funtion="备注保存";
               $inSql = "INSERT INTO $DataIn.qc_remark (Id, Sid, Remark, Date, Operator) VALUES (NULL, '$Sid', '$Remark', '$DateTime', '$Operator')";
			   $inResult = mysql_query($inSql,$link_id);
              if ($inResult){
                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
                        $OperationResult="Y";
                        $infoSTR=$Log_Funtion ."成功";
                } 
                else{
                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inSql </div><br>";
                        $infoSTR=$Log_Funtion ."失败";
                 }
            break;  
         case "Register":
           $LineId=$info[0];  $Sid=$info[1]; $Qty=$info[2];
            $Log_Funtion="登记保存";
               $FrameCapacityNew = $info[3]; $CapacityStuff = $info[4];
		   updateStuffFrameCapacity($DataIn,$CapacityStuff,$FrameCapacityNew);
           $QtyResult=mysql_fetch_array( mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS djQty,S.Qty FROM $DataIn.gys_shsheet S  LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id WHERE S.Id='$Sid' ",$link_id));
           $djQty=$QtyResult["djQty"]+$Qty;
           $shQty=$QtyResult["Qty"];
            
           if ($Qty > 0 && $djQty<=$shQty){
		            $inSql="INSERT INTO $DataIn.qc_cjtj (Id, Sid, StockId, StuffId, Qty, LineId,Remark, Date, Estate, Locks, Operator) SELECT  NULL,'$Sid', StockId, StuffId, '$Qty','$LineId', '', '$DateTime', '1', '0', '$Operator' FROM $DataIn.gys_shsheet WHERE Id='$Sid' ";
		
		            $inResult = mysql_query($inSql,$link_id);
		            if ($inResult && mysql_affected_rows()>0){
		                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
		                        $OperationResult="Y";
		                        $infoSTR=$Log_Funtion ."成功";
		                        
		                        //更新状态
		                        $upResult=mysql_query("UPDATE $DataIn.qc_mission M 
													LEFT JOIN $DataIn.gys_shsheet S ON S.Id=M.Sid 
													SET M.Estate=0 
													WHERE M.Sid='$Sid' AND S.Qty=(SELECT SUM(Qty) AS Qty FROM $DataIn.qc_cjtj WHERE Sid='$Sid')",$link_id);
		              } 
		            else{
		                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inSql </div><br>";
		                        $infoSTR=$Log_Funtion ."失败";
		                 }
            }
            else{
	              $Log="<div class=redB>$Log_Item $Log_Funtion 失败! 登记数量>送货数量</div><br>";
		          $infoSTR=$Log_Funtion ."失败";
            }
           break;
         case "SpareRegister"://备品登记
           $LineId=$info[0];  $Sid=$info[1]; $Qty=$info[2];
		   //1|411339|1|0|152812
		   $FrameCapacityNew = $info[3]; $CapacityStuff = $info[4];
		   updateStuffFrameCapacity($DataIn,$CapacityStuff,$FrameCapacityNew);
            $Log_Funtion="备品登记保存";
             
              //备品登记后直接入库
             $inRecode="INSERT INTO $DataIn.ck7_bprk (Id,StuffId,Qty,Remark,Date,Estate,Locks,Operator) VALUES(NULL,'$CapacityStuff','$Qty','品检备品登记转入','$curDate','0','0','$Operator')";
			$inAction=@mysql_query($inRecode);
			if ($inAction && mysql_affected_rows()>0){ 
			        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
                    $OperationResult="Y";
                    $infoSTR=$Log_Funtion ."成功";
                  /*  
			 	 $UpSql = "UPDATE  $DataIn.ck9_stocksheet  SET tStockQty=tStockQty+$Qty,oStockQty=oStockQty+$Qty WHERE StuffId='$CapacityStuff' ";
				$UpResult = mysql_query($UpSql);
				if($UpResult){
					$Log.="配件 $CapacityStuff 的在库和可用库存更新成功(增加 $Qty)!<br>";
					}
				else{
					$Log.="配件 $CapacityStuff 的在库和可用库存更新失败!<br>";
					$OperationResult="N";
					}
				*/
			} 
			else{
				$Log="<div class=redB>$Log_Funtion 入库保存失败!</div><br>";
				$OperationResult="N";
		  } 

             /* 
            $checkBillSql=mysql_query("SELECT M.BillNumber,S.StuffId FROM $DataIn.gys_shsheet S 
						LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid 
						WHERE S.Id='$Sid'  LIMIT 1",$link_id);
		   if($checkBillRow=mysql_fetch_array($checkBillSql)){
			     $BillNumber=$checkBillRow["BillNumber"];
			     $StuffId=$checkBillRow["StuffId"];
			     
			     $CheckResult=mysql_query("SELECT S.Id FROM $DataIn.gys_shsheet S 
						LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid 
						WHERE M.BillNumber='$BillNumber' AND S.StockId='-2' and S.StuffId='$StuffId'  ORDER BY S.StockId LIMIT 1",$link_id);//AND S.StockId='-2' ??? 
			     if($checkRow=mysql_fetch_array($CheckResult)){
			          $bp_Id=$checkRow["Id"];
			          
			          $inSql="INSERT INTO $DataIn.qc_cjtj (Id, Sid, StockId, StuffId, Qty, LineId,Remark, Date, Estate, Locks, Operator) SELECT  NULL,'$bp_Id', StockId, StuffId, '$Qty','$LineId', '', '$DateTime', '1', '0', '$Operator' FROM $DataIn.gys_shsheet WHERE Id='$bp_Id' ";
			          $inResult = mysql_query($inSql,$link_id);
			          if ($inResult && mysql_affected_rows()>0){
			                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
			                        $OperationResult="Y";
			                        $infoSTR=$Log_Funtion ."成功";

		              }
			     }
		   }
		   
		   if ($OperationResult=="N"){
			   $Log="<div class=redB>$Log_Item $Log_Funtion 失败!</div> $inSql <br>";
               $infoSTR=$Log_Funtion ."失败";
		   }
           else{
               $rkSign = shRk($bp_Id, $DataIn, $link_id, $Operator); 
			   if ($rkSign=="Y"){
                    $Log.="备品入库成功!<br>";
                }
                else{
                    $Log.="<div class=redB>备品入库失败! </div><br>";
               }
               
           }
          */
           
           break;
           
       case "Confirm"://入库确认
           $upId=$info[0]; $Estate=$info[1];
           $Log_Funtion="入库确认";
           
           $upSql = "UPDATE $DataIn.qc_mission SET rkSign=0  WHERE  Id='$upId' ";
		   $upResult = mysql_query($upSql,$link_id);
			if ($upResult){
                    $Log=$Log_Item .$Log_Funtion . "($upId)成功!<br>";
                    $OperationResult="Y";
                    $infoSTR=$Log_Funtion ."成功";
                    $CheckResult=mysql_query("SELECT S.Mid,S.StuffId FROM $DataIn.qc_mission M
						LEFT JOIN $DataIn.gys_shsheet S ON S.Id=M.Sid
						WHERE M.Id='$upId'  LIMIT 1",$link_id);
			      if($checkRow=mysql_fetch_array($CheckResult)){
			             $Mid=$checkRow["Mid"];
			             $StuffId=$checkRow["StuffId"];
			             
			             $CountResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts FROM  $DataIn.gys_shsheet S WHERE S.Mid='$Mid' AND S.StuffId='$StuffId' AND StockId>0 AND Estate>0 ",$link_id));
			             $Counts=$CountResult["Counts"];
			             if ($Counts==0){
			                   //更新备品送货单状态
				                $upSql2="UPDATE $DataIn.gys_shsheet SET Estate=0 WHERE Mid='$Mid' and StuffId='$StuffId' and stockid='-2' and Estate>0";
				                $upResult2 = mysql_query($upSql2,$link_id);
			             }
			      }
            } 
            else{
                    $Log="<div class=redB>$Log_Item $Log_Funtion ($upId)失败! $inRecode </div><br>";
                    $infoSTR=$Log_Funtion ."失败";
             }
          break;
          
         case "confirms": //入库确认 
         {
	          $allUpIds = $info[0];
	          $allUpIds = explode(",", $allUpIds);
	            $infoSTR = "";$Log_Funtion="入库确认";
	          foreach ($allUpIds as $upId) {
		          
	           
	         
	           $upSql = "UPDATE $DataIn.qc_mission SET rkSign=0  WHERE  Id='$upId' ";
			   $upResult = mysql_query($upSql,$link_id);
				if ($upResult){
	                    $Log.=$Log_Item .$Log_Funtion . "($upId)成功!<br>";
	                    $OperationResult="Y";
	                    $infoSTR=$Log_Funtion ."成功";
	                    $CheckResult=mysql_query("SELECT S.Mid,S.StuffId FROM $DataIn.qc_mission M
							LEFT JOIN $DataIn.gys_shsheet S ON S.Id=M.Sid
							WHERE M.Id='$upId'  LIMIT 1",$link_id);
				      if($checkRow=mysql_fetch_array($CheckResult)){
				             $Mid=$checkRow["Mid"];
				             $StuffId=$checkRow["StuffId"];
				             
				             $CountResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Counts FROM  $DataIn.gys_shsheet S WHERE S.Mid='$Mid' AND S.StuffId='$StuffId' AND StockId>0 AND Estate>0 ",$link_id));
				             $Counts=$CountResult["Counts"];
				             if ($Counts==0){
				                   //更新备品送货单状态
					                $upSql2="UPDATE $DataIn.gys_shsheet SET Estate=0 WHERE Mid='$Mid' and StuffId='$StuffId' and stockid='-2' and Estate>0";
					                $upResult2 = mysql_query($upSql2,$link_id);
				             }
				      }
	            } 
	            else{$OperationResult="N";
	                    $Log.="<div class=redB>$Log_Item $Log_Funtion ($upId)失败! $inRecode </div><br>";
	                    $infoSTR=$Log_Funtion ."失败";
	             }

		          
	          }
         }
                   break;
          
         case "SAVE"://新增记录
                 //if ($LoginNumber==10868) break;
                $Log_Funtion="保存";
                $Id=$info[0];$CheckQty=$info[1];$AQL=$info[2];$Accepts=$info[3];$CauseId=$info[4];$Qty=$info[5]; $FileSign=$info[6]; $Reason=$info[7];
                //$Accepts=1;//不退回 
                $upFileCounts=$_POST["FileCount"];
                
                $TotalQty=0;
                $CauseId=explode("^", $CauseId);
                $badQty=explode("^", $Qty);
                $FileSign=explode("^", $FileSign);
                
                for($m=0;$m<count($badQty);$m++){
	                if ($badQty[$m]>0) $TotalQty+=$badQty[$m];
                }
                
                $Estate=$TotalQty==0?0:1;
                
                if ($Accepts==1){
                   if($DataIn=="ac"){
	                 $inRecode="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty','$TotalQty','$AQL','','$Estate','0','$DateTime','$LoginNumber' ,'0','$Operator','$DateTime','$Operator','$DateTime'
                  FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                      }else{
	                 $inRecode="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty','$TotalQty','$AQL','','$Estate','0','$DateTime','$LoginNumber' FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                       }
                }
                else{//抽检不合格，设为全部送货数量不合格并退回
                  if($DataIn=="ac"){
	                   $inRecode="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty',Qty,'$AQL','抽检不合格','$Estate','0','$DateTime','$LoginNumber' ,'0','$Operator','$DateTime','$Operator','$DateTime' 
                        FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                 }else{
	                 $inRecode="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty',Qty,'$AQL','抽检不合格','$Estate','0','$DateTime','$LoginNumber' FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                      }
                }
                
	             $inAction=@mysql_query($inRecode);
	             $Mid=mysql_insert_id();
	          // if ($LoginNumber!=10868){
	                if ($inAction){ 
	                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
	                        $OperationResult="Y";
	                        $infoSTR=$Log_Funtion ."数据成功";
	                        
	                         $FileType=".jpg";$k=0;
	                         for($i=0;$i<count($badQty);$i++){
	                           if ($badQty[$i]>0){
	                             $ReasonString=$CauseId[$i]=="-1"?$Reason:"";
                                 //生成明细表
                                $insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '$CauseId[$i]', '$badQty[$i]', '$ReasonString','0')";
                                $insheetAction=@mysql_query($insheetSql,$link_id);
                                if (!$insheetAction){
                                         $qcError=1;break;
                                 }
                                 else{
                                       $Sid=mysql_insert_id();
                                       //上传不良图片
                                        if ($FileSign[$i]==1){
                                           $fileName="Q".$Sid.$FileType;
                                           $path = "../../download/qcbadpicture/".$fileName;
                                           $upFileName='upFile' . $k;
                                            $upFile=$_POST[$upFileName];
                                            
                                           if(move_uploaded_file($_FILES[$upFileName]['tmp_name'],$path)){
                                                //更新刚才的记录
                                                 $sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='1' WHERE Id='$Sid'";
                                                $result = mysql_query($sql);
                                                 $infoSTR.= "上传文件成功！";		
                                             }
                                             else{
                                                 $infoSTR.= "上传文件失败！";	
                                            }
                                            $k++;
                                       }
                                  }
                             } 
	                     }
	                } 
	                else{
	                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
	                        $infoSTR=$Log_Funtion ."数据失败";
	                        }
	    
	        //入库操作
	        if ($OperationResult=="Y"){
		          if ($Accepts==1){//允收
			           $rkSign = shRk($Id, $DataIn, $link_id, $Operator); 
			           if ($rkSign=="Y"){
			                $Log.="送货单Id($Id)配件入库成功!<br>";
			                //入库成功
				            if(!isCustomerSupplier($Id, $DataIn, $link_id))
				        	{
					        	$autoSign=autoPayment($Id, $DataIn, $link_id,"");
					        	$Log.=$autoSign=="Y"?"自动请款成功!<br>":"";
					        }
			           }
			           else{
				                 $Log.="送货单Id($Id)配件入库失败!<br>";
			           }
		          }
		          else{
		               //抽检拒收，不入库操作;结束送货单，不再退回原来状态
		                $upSH="UPDATE  $DataIn.gys_shsheet S  SET S.Estate=0,S.Locks=0 WHERE  S.Id='$Id'";
		               $upSHAction=mysql_query($upSH);
		          }
	        }   
           break;
           
            case "SAVE_MULTI"://新增记录
                 //if ($LoginNumber==10868) break;
                $Log_Funtion="保存";
                $Id=$info[0];$CheckQty=$info[1];$AQL=$info[2];$Accepts=$info[3];$CauseId=$info[4];$Qty=$info[5]; $FileSign=$info[6]; $Reason=$info[7];  
                
                $OtherIdStr = $info[8];
                $OtherIds = explode(",", $OtherIdStr);
                
                //$Accepts=1;//不退回 
                $upFileCounts=$_POST["FileCount"];
                
                $TotalQty=0;
                $CauseId=explode("^", $CauseId);
                $badQty=explode("^", $Qty);
                $FileSign=explode("^", $FileSign);
                
                for($m=0;$m<count($badQty);$m++){
	                if ($badQty[$m]>0) $TotalQty+=$badQty[$m];
                }
                
                $Estate=$TotalQty==0?0:1;
                
                if ($Accepts==1){
                   if($DataIn=="ac"){
	                 $inRecode="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty','$TotalQty','$AQL','','$Estate','0','$DateTime','$LoginNumber' ,'0','$Operator','$DateTime','$Operator','$DateTime'
                  FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                      }else{
	                 $inRecode="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty','$TotalQty','$AQL','','$Estate','0','$DateTime','$LoginNumber' FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                       }
                }
                else{//抽检不合格，设为全部送货数量不合格并退回
                  if($DataIn=="ac"){
	                   $inRecode="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty',Qty,'$AQL','抽检不合格','$Estate','0','$DateTime','$LoginNumber' ,'0','$Operator','$DateTime','$Operator','$DateTime' 
                        FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                 }else{
	                 $inRecode="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty',Qty,'$AQL','抽检不合格','$Estate','0','$DateTime','$LoginNumber' FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                      }
                }
                
	             $inAction=@mysql_query($inRecode);
	             $Mid=mysql_insert_id();
	          // if ($LoginNumber!=10868){
	                if ($inAction){ 
	                        $Log=$Log_Item .$Log_Funtion . "成功!<br>";
	                        $OperationResult="Y";
	                        $infoSTR=$Log_Funtion ."数据成功";
	                        
	                         $FileType=".jpg";$k=0;
	                         for($i=0;$i<count($badQty);$i++){
	                           if ($badQty[$i]>0){
	                             $ReasonString=$CauseId[$i]=="-1"?$Reason:"";
                                 //生成明细表
                                $insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$Mid', '$CauseId[$i]', '$badQty[$i]', '$ReasonString','0')";
                                $insheetAction=@mysql_query($insheetSql,$link_id);
                                if (!$insheetAction){
                                         $qcError=1;break;
                                 }
                                 else{
                                       $Sid=mysql_insert_id();
                                       //上传不良图片
                                        if ($FileSign[$i]==1){
                                           $fileName="Q".$Sid.$FileType;
                                           $path = "../../download/qcbadpicture/".$fileName;
                                           $upFileName='upFile' . $k;
                                            $upFile=$_POST[$upFileName];
                                            
                                           if(move_uploaded_file($_FILES[$upFileName]['tmp_name'],$path)){
                                                //更新刚才的记录
                                                 $sql = "UPDATE $DataIn.qc_badrecordsheet SET Picture='1' WHERE Id='$Sid'";
                                                $result = mysql_query($sql);
                                                 $infoSTR.= "上传文件成功！";		
                                             }
                                             else{
                                                 $infoSTR.= "上传文件失败！";	
                                            }
                                            $k++;
                                       }
                                  }
                             } 
	                     }
	                } 
	                else{
	                        $Log="<div class=redB>$Log_Item $Log_Funtion 失败! $inRecode </div><br>";
	                        $infoSTR=$Log_Funtion ."数据失败";
	                        }
	    
	        //入库操作
	        if ($OperationResult=="Y"){
		          if ($Accepts==1){//允收
			           $rkSign = shRk($Id, $DataIn, $link_id, $Operator); 
			           if ($rkSign=="Y"){
			                $Log.="送货单Id($Id)配件入库成功!<br>";
			                //入库成功
				            if(!isCustomerSupplier($Id, $DataIn, $link_id))
				        	{
					        	$autoSign=autoPayment($Id, $DataIn, $link_id,"");
					        	$Log.=$autoSign=="Y"?"自动请款成功!<br>":"";
					        }
					        
					          //update otherids
		          foreach ($OtherIds as $IdandCheckQty) {
			          $IdandCheckQty = explode("-", $IdandCheckQty);
			          $Id = $IdandCheckQty[0];
			          $CheckQty = $IdandCheckQty[1];
			          $TotalQty =0;
			          $Estate = 0;
			          $Accepts=1;$CauseId='';$Qty=0; $FileSign=''; $Reason='系统审核通过';  
			          //通过
					   if($DataIn=="ac"){
	                 $inRecode="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty','$TotalQty','$AQL','','$Estate','0','$DateTime','$LoginNumber' ,'0','$Operator','$DateTime','$Operator','$DateTime'
                  FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                      }else{
	                 $inRecode="INSERT INTO $DataIn.qc_badrecord SELECT NULL,Mid,StockId,StuffId,Qty,'$CheckQty','$TotalQty','$AQL','','$Estate','0','$DateTime','$LoginNumber' FROM $DataIn.gys_shsheet WHERE Id='$Id'";
                       }
                        $inAction=@mysql_query($inRecode);
	             $Mid=mysql_insert_id();
	             
	             //入库
	           $rkSign=  shRk($Id, $DataIn, $link_id, $Operator); 
	          // if ($LoginNumber!=10868){
	           if ($rkSign=="Y"){
			                $Log.="系统:送货单Id($Id)配件入库成功!<br>";
			                //入库成功
				            if(!isCustomerSupplier($Id, $DataIn, $link_id))
				        	{
					        	$autoSign=autoPayment($Id, $DataIn, $link_id,"");
					        	$Log.=$autoSign=="Y"?"自动请款成功!<br>":"";
					        }
	                

			          
		          } else {
			          $Log.="系统:送货单Id($Id)配件入库失败!<br>";
		          }

					        
			  }
			           }
			           else{
				                 $Log.="送货单Id($Id)配件入库失败!<br>";
			           }
		          }
		          else{
		               //抽检拒收，不入库操作;结束送货单，不再退回原来状态
		                $upSH="UPDATE  $DataIn.gys_shsheet S  SET S.Estate=0,S.Locks=0 WHERE  S.Id='$Id'";
		               $upSHAction=mysql_query($upSH);
		          }
		          
		        
	        }   
           break;

         case "Cancel":
           $Log_Funtion="取消品检";
           $Id=$info[0];
           $delSql="DELETE FROM $DataIn.qc_cjtj WHERE Sid='$Id' ";
           $delResult=mysql_query($delSql,$link_id); 
           if ($delResult){
	            $delSql2="DELETE FROM $DataIn.qc_mission WHERE Sid='$Id' ";
	            
	            
	            
                $delResult2=mysql_query($delSql2,$link_id); 
                
                if ($Floor == 6) {
	                mysql_query("UPDATE  $DataIn.gys_shsheet  set  Estate=1 where Id='$Id'",$link_id);
                }
                
                $OperationResult=="Y";
                $Log.="送货单Id($Id)$Log_Funtion 成功!<br>";
                $infoSTR=$Log_Funtion ."数据成功";
           }
           else{
	           $Log.="送货单Id($Id)$Log_Funtion 失败!<br>";
	           $infoSTR=$Log_Funtion ."数据失败";
           }
           break;
           
          case "CancelBack":
           $Log_Funtion="取消品检";
           $Id=$info[0];
           $Log = "";
           $delSql="DELETE FROM $DataIn.qc_cjtj WHERE Sid='$Id' ";
           $delResult=mysql_query($delSql,$link_id); 
           if ($delResult){
	            $delSql2="DELETE FROM $DataIn.qc_mission WHERE Sid='$Id' ";
                $delResult2=mysql_query($delSql2,$link_id); 
                //$OperationResult=="Y";
                $Log.="送货单Id($Id)$Log_Funtion 成功!<br>";
                $infoSTR=$Log_Funtion ."数据成功";
                
                 $upId=$info[0];
               $Remark=$info[1];
               $Log_Funtion="退回";
               $upResult=shBack($upId,$Remark,$DataIn, $link_id,$Operator);
                if ($upResult){
                        $Log.=$upId . "-" . $Log_Item .$Log_Funtion . "成功!<br>";
                        $OperationResult="Y";
                        $infoSTR.=$Log_Funtion ."成功";
                 }
                 else{
	                 $Log.="<div class=redB>$Log_Item $Log_Funtion失败! $upSql </div><br>";
                     $infoSTR.=$Log_Funtion ."失败";
                 }

           }
           else{
	           $Log.="送货单Id($Id)$Log_Funtion 失败!<br>";
	           $infoSTR=$Log_Funtion ."数据失败";
           }
           break;
  }
  
  
  function updateStuffFrameCapacity($DataIn,$StuffId,$Capacity) {
	  $resultBool = false;
	  if ($StuffId>0 && $Capacity>0) {
	  $checkSql = mysql_fetch_array(mysql_query(" select FrameCapacity from $DataIn.stuffdata where StuffId=$StuffId"));
	  $oldCapacity = $checkSql["FrameCapacity"];
	  if ($oldCapacity != $Capacity) {
		 $resultBool = @mysql_query(" update $DataIn.stuffdata set FrameCapacity=$Capacity where StuffId=$StuffId");
	  }
	  }
	  return $resultBool;
  }
  

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);

$jsonArray = array("ActionId"=>"$ActionId","Result"=>"$OperationResult","Info"=>"$infoSTR");
?>