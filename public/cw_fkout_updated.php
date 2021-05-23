<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw1_fkoutsheet
$DataIn.cw1_fkoutmain
$DataIn.cw2_fkdjsheet
二合一已更新
*/
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="供应商货款";		//需处理
$Log_Funtion="更新";
$upDataSheet="$DataIn.cw1_fkoutsheet";	//需处理
$upDataMain="$DataIn.cw1_fkoutmain";	//需处理
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
$FileDir="cwfk";
switch ($ActionId){
	case 15://???
		$Log_Funtion="退回修改";//未结付退回
		if($Estate==3){					//未结付退回
			$delRecode="DELETE FROM $upDataSheet WHERE Id IN ($Ids)";
			$delAction = mysql_query($delRecode);
			if($delAction && mysql_affected_rows()>0){
				$Log="请款ID在($Ids)的需求单请款退回成功.";
				//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw1_fkoutsheet");
				$ALType="From=$From&CompanyId=$CompanyId";
				}
			else{
				$Log="<div class='redB'>请款ID在($Ids)的需求单请款退回失败.</div>";
				$OperationResult="N";
				}
			}
		else{//已结付退回:
			/////////////////////////////////
			$Lens=count($checkid);
			for($i=0;$i<$Lens;$i++){
				$Id=$checkid[$i];
				if($Id!=""){
					$checkRow=mysql_fetch_array(mysql_query("SELECT S.Mid,S.Amount FROM $upDataSheet S WHERE S.Id=$Id LIMIT 1",$link_id));
					$Mid=$checkRow["Mid"];
					$Amount=$checkRow["Amount"];
					$DelSqls="DELETE FROM $upDataSheet WHERE Id='$Id'";
					$DelResults = mysql_query($DelSqls);
					if($DelResults && mysql_affected_rows()>0){
						$Log.="$x 3-1:请款需求单 $Id 已退回,请款记录删除成功.<br>";
						$checkMain=mysql_query("SELECT Id FROM $upDataSheet WHERE Mid=$Mid",$link_id);
						if($checkMainRow=mysql_fetch_array($checkMain)){//还有记录，更新
							$UpSql="UPDATE $upDataMain SET PayAmount=PayAmount-'$Amount' WHERE Id='$Mid'";
							$UpResult = mysql_query($UpSql);
							if($UpResult && mysql_affected_rows()>0){
								$Log.="&nbsp;&nbsp;3-2结付金额已随之更新.<br>";
								}
							else{
								$Log.="<div class='redB'>&nbsp;&nbsp;3-2结付金额更新失败.</div><br>";
								$OperationResult="N";
								}
							}
						else{//删除主单记录：删除前需退回订单及金额	
							/////////
							$DelSql="DELETE FROM $upDataMain WHERE Id='$Mid'";
							$DelResult = mysql_query($DelSql);
							if($DelResult && mysql_affected_rows()>0){
								$Log.="&nbsp;3-2:主结付单没有结付明细,删除成功.<br>";
								//删除结付图档
								$FilePathC="../download/$FileDir/C".$Mid.".jpg";
								if(file_exists($FilePathC)){
									unlink($FilePathC);
									}
								$FilePathP="../download/$FileDir/P".$Mid.".jpg";
								if(file_exists($FilePathP)){
									unlink($FilePathP);
									}
								$FilePathR="../download/$FileDir/R".$Mid.".jpg";
								if(file_exists($FilePathR)){
									unlink($FilePathR);
									}
	
								$djSql = "UPDATE $DataIn.cw2_fkdjsheet SET Estate=0,Did='0',Locks=0 WHERE Did='$Mid'";
								$djResult = mysql_query($djSql);
								if($djResult && mysql_affected_rows()>0){
									$Log.="&nbsp;3-3:结付单用到的订金退回成功.<br>";
									}
								else{
									$Log.="<div class='redB'>&nbsp;3-3:结付单没有用到订金或用到的订金退回失败. $djSql </div><br>";
									$OperationResult="N";
									}
                                  	//采购扣款退回
								  $kkSql = "UPDATE $DataIn.cw15_gyskksheet SET Kid='0' WHERE Kid='$Mid'";
							      $kkResult = mysql_query($kkSql);
							       if($kkResult && mysql_affected_rows()>0){
								   $Log.="&nbsp;3-4:结付单用到的采购单扣款退回成功.<br>";
								        }
							        else{
								      $Log.="<div class='redB'>&nbsp;3-4:结付单没有用采购单扣款或用到的采购单扣款退回失败. $kkSql </div><br>";
								      $OperationResult="N";
								      }

								//*****************************货款返利取消结付
								$ReturnSql = "UPDATE $DataIn.cw2_hksheet SET Did='0' WHERE Did='$Mid'";
							     $ReturnResult = mysql_query($ReturnSql);
							      if($ReturnResult && mysql_affected_rows()>0){
								       $Log.="&nbsp;3-5:结付单用到的供应商货款返利退回成功.<br>";
								      }
							    else{
							    	    $Log.="<div class='redB'>&nbsp;3-5:结付单没有用供应商货款返利或用到的供应商货款返利退回失败. $ReturnSql </div><br>";
									    $OperationResult="N";
								     }
								}
							else{
								$Log.="<div class='redB'>&nbsp;3-2:主单删除失败！ $DelSql </div><br>";
								$OperationResult="N";
								}
							/////////
							}
						}
					else{
						$Log="<div class='redB'>$x 3-1:请款的需求单 $Id 退回失败.</div>";
						$OperationResult="N";
						}
					$x++;
					}//end if($Id!="")
				}//end for
			////////////////////////////////
			}
		break;
	case 16://
		$Log_Funtion="取消结付";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$checkRow=mysql_fetch_array(mysql_query("SELECT S.Mid FROM $upDataSheet S WHERE S.Id=$Id LIMIT 1",$link_id));
				$Mid=$checkRow["Mid"];
				$UpSql="UPDATE $upDataSheet S LEFT JOIN $upDataMain M ON S.Mid=M.Id SET S.Mid='0',S.Estate='3',M.PayAmount=M.PayAmount-S.Amount WHERE S.Id='$Id'";
				$UpResult = mysql_query($UpSql);
				if($UpResult && mysql_affected_rows()>0){
					$Log.="$x 3-1:请款需求单 $Id 取消结付成功.<br>";
					$checkMain=mysql_query("SELECT Id FROM $upDataSheet WHERE Mid=$Mid",$link_id);
					if($checkMainRow=mysql_fetch_array($checkMain)){//还有记录，更新
						$Log.="&nbsp;&nbsp;3-2结付金额已随之更新.<br>";
						}
					else{//删除主单记录：删除前需退回订单及金额	
						/////////
						$DelSql="DELETE FROM $upDataMain WHERE Id='$Mid'";
						$DelResult = mysql_query($DelSql);
						if($DelResult && mysql_affected_rows()>0){
							$Log.="&nbsp;3-2:主结付单删除成功.<br>";
							//删除结付图档
							$FilePathC="../download/$FileDir/C".$Mid.".jpg";
							if(file_exists($FilePathC)){
								unlink($FilePathC);
								}
							$FilePathP="../download/$FileDir/P".$Mid.".jpg";
							if(file_exists($FilePathP)){
								unlink($FilePathP);
								}
							$FilePathR="../download/$FileDir/R".$Mid.".jpg";
							if(file_exists($FilePathR)){
								unlink($FilePathR);
								}
                            //定金退回
							$djSql = "UPDATE $DataIn.cw2_fkdjsheet SET Estate=0,Did='0',Locks=0 WHERE Did='$Mid'";
							$djResult = mysql_query($djSql);
							if($djResult && mysql_affected_rows()>0){
								$Log.="&nbsp;3-3:结付单用到的订金退回成功.<br>";
								}
							else{
								$Log.="<div class='redB'>&nbsp;3-3:结付单没有用到订金或用到的订金退回失败. $djSql </div><br>";
								$OperationResult="N";
								}
								//*****************************采购扣款退回
								$kkSql = "UPDATE $DataIn.cw15_gyskksheet SET Kid='0' WHERE Kid='$Mid'";
							   $kkResult = mysql_query($kkSql);
							    if($kkResult && mysql_affected_rows()>0){
								 		$Log.="&nbsp;3-4:结付单用到的采购单扣款退回成功.<br>";
								     }
							   else{
										$Log.="<div class='redB'>&nbsp;3-4:结付单没有用采购单扣款或用到的采购单扣款退回失败. $kkSql </div><br>";
										$OperationResult="N";
							    	}
								//*****************************货款返利取消结付
								$ReturnSql = "UPDATE $DataIn.cw2_hksheet SET Did='0' WHERE Did='$Mid'";
							   $ReturnResult = mysql_query($ReturnSql);
							    if($ReturnResult && mysql_affected_rows()>0){
								       $Log.="&nbsp;3-5:结付单用到的供应商货款返利退回成功.<br>";
								      }
							   else{
							    	    $Log.="<div class='redB'>&nbsp;3-5:结付单没有用供应商货款返利或用到的供应商货款返利退回失败. $ReturnSql </div><br>";
									    $OperationResult="N";
								     }
								
							}
						else{
							$Log.="<div class='redB'>&nbsp;3-2:主单删除失败！ $DelSql </div><br>";
							$OperationResult="N";
							}
						/////////
						}
					}
				else{
					$Log="<div class='redB'>$x 3-1:请款的需求单 $Id 取消结付失败.</div>";
					$OperationResult="N";
					}
				$x++;
				}//end if($Id!="")
			}//end for
		break;
	case 18://结付
		$Log_Funtion="结付";
		$Date=date("Y-m-d");
		//锁定表
		//$sql=" LOCK TABLES $upDataMain WRITE,$upDataSheet WRITE,$DataIn.cw2_fkdjsheet WRITE";$res=@mysql_query($sql);
		
		$IN_recode="INSERT INTO $upDataMain (Id,BankId,CompanyId,PayDate,PayAmount,djAmount,Payee,Receipt,Checksheet,Remark,Date,Locks,Operator) 
		VALUES (NULL,'$BankId','$CompanyId','$Date','0','0','0','0','0','','$Date','0','$Operator')";
		$inRes=@mysql_query($IN_recode);
		$Mid=mysql_insert_id();		
		if($inRes){
			$Log="4-1 结付主单记录成功入库！明细帐目处理如下:<br>";
			//需求单处理
			$sql = "UPDATE $upDataSheet SET Estate=0,Mid=$Mid,Locks=0 WHERE Id IN ($Ids)";
			$result = mysql_query($sql);
			if($result){
				$Log.="&nbsp;&nbsp;4-2 Id在为($Ids)的 $Log_Item 结付成功。<br>";
				}
			else{
				$Log.="<div class='redB'>&nbsp;&nbsp;4-2 Id在为($Ids)的 $Log_Item 结付失败！$sql</div><br>";
				$OperationResult="N";
				}
				
			//******************************订金处理
			$djCount=count($checkdj);
			for($i=0;$i<$djCount;$i++){
				$djId=$checkdj[$i];
				if ($djId!=""){
					$djIds=$djIds==""?$djId:($djIds.",".$djId);
					}
				}
			if($djIds!=""){
				$djSql = "UPDATE $DataIn.cw2_fkdjsheet SET Estate=0,Did='$Mid',Locks=0 WHERE Id IN ($djIds)";
				$djResult = mysql_query($djSql);
				if($djResult && mysql_affected_rows()>0){
					$Log.="&nbsp;&nbsp;4-3 Id在为($djIds)的订金抵付成功。<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;4-3 Id在为($djIds)的订金抵付失败！$djSql</div><br>";
					$OperationResult="N";
					}
					
				//解锁
				//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
				//更新金额:按实际结付成功的金额计算（需求单的总额-订金的总额）
				$upSql = "UPDATE $upDataMain SET PayAmount=
				(SELECT SUM(Amount) FROM $upDataSheet WHERE Mid=$Mid)-
				(SELECT SUM(Amount) FROM $DataIn.cw2_fkdjsheet WHERE Did=$Mid),
				djAmount=(SELECT SUM(Amount) FROM cw2_fkdjsheet WHERE Did=$Mid) 
				WHERE Id=$Mid LIMIT 1";
				$upResult = mysql_query($upSql);
				if($upResult && mysql_affected_rows()>0){
					$Log.="&nbsp;&nbsp;4-4 结付金额扣除定金更新成功.<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;4-4 结付金额扣除定金更新失败. $upSql </div><br>";
					$OperationResult="N";
					}
				}
			else{
				//解锁
				//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
				//更新金额:按实际结付成功的金额计算（需求单的总额-订金的总额）
				$upSql = "UPDATE $upDataMain SET PayAmount=(SELECT SUM(Amount) FROM $upDataSheet WHERE Mid=$Mid) WHERE Id=$Mid LIMIT 1";
				$upResult = mysql_query($upSql);
				if($upResult && mysql_affected_rows()>0){
					$Log.="&nbsp;&nbsp;4-4 结付金额更新成功.<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;4-4 结付金额更新失败. $upSql </div><br>";
					$OperationResult="N";
					}
				$Log.="&nbsp;&nbsp;4-3 无订金处理.<br>";
				}
				
			//**********************************************************采购单扣款
			$kkCount=count($checkkk);
			for($i=0;$i<$kkCount;$i++){
				$kkId=$checkkk[$i];
				if ($kkId!=""){
					$kkIds=$kkIds==""?$kkId:($kkIds.",".$kkId);
					}
			    }
			if($kkIds!=""){
				$kkSql = "UPDATE $DataIn.cw15_gyskksheet SET Kid='$Mid' WHERE Mid IN ($kkIds)";
				$kkResult = mysql_query($kkSql);
				if($kkResult && mysql_affected_rows()>0){
					$Log.="&nbsp;&nbsp;4-5 Id在为($kkIds)的供应商扣款扣除成功。<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;4-5 Id在为($kkIds)的供应商扣款扣除失败！$kkSql</div><br>";
					$OperationResult="N";
					}
					
				/*$upSql = "UPDATE $upDataMain SET PayAmount=
				(SELECT SUM(Amount) FROM $upDataSheet WHERE Mid=$Mid)-
				(SELECT SUM(Amount) FROM $DataIn.cw15_gyskksheet WHERE Kid=$Mid) WHERE Id=$Mid LIMIT 1";
				$upResult = mysql_query($upSql);
				if($upResult && mysql_affected_rows()>0){
					$Log.="&nbsp;&nbsp;4-5 结付金额扣除采购扣款更新成功.<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;4-5 结付金额扣除采购扣款更新失败. $upSql </div><br>";
					$OperationResult="N";
					}*/
				    }
				else{
				    $Log.="&nbsp;&nbsp;4-6 无采购单扣款.<br>";
				    }
				
			//**********************************************************供应商货款返利
			$ReturnCount=count($checkReturn);
			for($i=0;$i<$ReturnCount;$i++){
				$ReturnId=$checkReturn[$i];
				if ($ReturnId!=""){
					$ReturnIds=$ReturnIds==""?$ReturnId:($ReturnIds.",".$ReturnId);
					}
			    }
			if($ReturnIds!=""){
				$ReturnSql = "UPDATE $DataIn.cw2_hksheet SET Did='$Mid',Estate=0,Locks=0 WHERE Id IN ($ReturnIds)";
				$ReturnResult = mysql_query($ReturnSql);
				if($ReturnResult && mysql_affected_rows()>0){
					$Log.="&nbsp;&nbsp;4-7 Id在为($ReturnIds)的供应商货款返利扣除成功。<br>";
					}
				else{
					   $Log.="<div class='redB'>&nbsp;&nbsp;4-7 Id在为($ReturnIds)的供应商货款返利扣除失败！$ReturnSql</div><br>";
					   $OperationResult="N";
					   }
				    }
				else{
				      $Log.="&nbsp;&nbsp;4-8 无供应商货款返利.<br>";
				    }

				//子系统供应商货款自动收款
				if ($CompanyId==$SubMainCompanyId){
					//include "../admin/swapsub/cg_cgdsheet_cw.php";
				}
			}
		else{
			//解锁
			//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
			$Log="<div class=redB>主结付单添加失败！$IN_recode</div><br>";
			$OperationResult="N";
			}		
		break;
	case 20://财务更新
			//必选参数	:文件目录
			$Log_Funtion="主结付单资料更新";
			include "../model/subprogram/updated_model_cw.php";
			//订金处理
			$Estate=0;
			break;
	case 919://
		$Log_Funtion="主结付单资料(取消订金)更新";
		$upSql="UPDATE $DataIn.cw1_fkoutmain M ,$DataIn.cw2_fkdjsheet D SET M.PayAmount=M.PayAmount+D.Amount,M.djAmount=M.djAmount-D.Amount,D.Did='0' WHERE D.Id='$Id' AND D.Did=M.Id";
		$upResult=mysql_query($upSql);
		if($upResult && mysql_affected_rows()>0){
			$Log="货款结付单取消抵付订金(Id为$Id)的操作成功.<br>";
			}
		else{
			$Log="<div class=redB>货款结付单取消抵付订金(Id为$Id)的操作失败. $upSql </div><br>";
			$OperationResult="N";
			}
		$Estate=0;
		break;
    case 178:
        if ($DeleteSign==1 && $InvoiceId>0){
	        //删除发票信息
	        $delLog='';
	        $checkResult=mysql_query("SELECT InvoiceFile FROM cw1_fkoutinvoice WHERE Id=$InvoiceId",$link_id);
	        if($checkRows = mysql_fetch_array($checkResult)){
	            $InvoiceFile=$checkRows['InvoiceFile'];
	            if ($InvoiceFile!=''){
		            $FilePath="../download/fkinvoice/" . $InvoiceFile;
			        if(!file_exists($FilePath)){
				         unlink($FilePath);
				         $delLog=";发票文件($InvoiceFile)删除成功!";
			       }
	            }       
	        }

	        $upSql1="update $DataIn.cw1_fkoutsheet SET InvoiceId='0' WHERE InvoiceId='$InvoiceId'";
			$upResult1=mysql_query($upSql1);
			
			$delSql="DELETE FROM $DataIn.cw1_fkoutinvoice  WHERE Id=$InvoiceId";
			$delResult=mysql_query($delSql);
				      
	         if ($delResult && $upResult1){
	             $Log.="ID:$InvoiceId 发票信息删除成功 $delLog<br>";  
             }else{
	             $Log.="<div class=redB>ID:$InvoiceId 发票信息删除失败 $delLog</div><br>";    
             }
        }
        else{
	         $Lens=count($ListId);
	         
			 for($i=0;$i<$Lens;$i++){
				$Id0=$ListId[$i];
				if ($Id0!=""){
					$ListIds=$ListIds==""?$Id0:($ListIds.",".$Id0);
					}
			 }
	
	         $Log_Funtion="发票上传更新";
	         $InvoiceFileSTR='';
	         if ($InvoiceFile!='' && $ListIds!=''){
		         $FilePath="../download/fkinvoice/";
			     if(!file_exists($FilePath)){
				     makedir($FilePath);
			     }
			     $FileName=date('YmdHms').rand(100,999) . '.pdf';
			     $uploadInfo=UploadFiles($InvoiceFile,$FileName,$FilePath);
			     if ($InvoiceId==0){
				     $InvoiceFile=$uploadInfo==''?'':$FileName;
			     }
			     else{
				     $InvoiceFileSTR=$uploadInfo==""?'':",InvoiceFile='$FileName'"; 
			     }
			     if ($uploadInfo){
				     $Log="发票文件上传操作成功.$FileName <br>";
			     }else{
				     $Log="<div class=redB>发票文件上传操作失败.$FileName </div><br>";
			     }
	         }
	         
	         $Remark=$Remark==''?'':$Remark;
	         if ($ListIds!=''){
			         if ($InvoiceId==0){
				        $IN_recode="INSERT INTO $DataIn.cw1_fkoutinvoice (Id,InvoiceNo,InvoiceFile,InvoiceDate,InvoiceAmount,Remark,Estate,Date,Operator,creator,created) 
					VALUES (NULL,'$InvoiceNo','$InvoiceFile','$InvoiceDate','$InvoiceAmount','$Remark','2','$Date','$Operator','$Operator','$DateTime')";
					   // echo $IN_recode;
						$inRes=@mysql_query($IN_recode);
						$InvoiceId=@mysql_insert_id();
						if ($InvoiceId>0){
						    $Log.="发票信息保存成功<br>";
							$upSql="update $DataIn.cw1_fkoutsheet SET InvoiceId='$InvoiceId' WHERE Id IN($ListIds)";
							$upResult=mysql_query($upSql);
							if ($upResult){
							    $Log.="发票文件关联采购单($ListIds)成功 <br>"; 
							}else{
								$Log.="<div class=redB>发票文件关联采购单($ListIds)失败 </div><br>";
							}
							
					    }else{
						        $Log.="<div class=redB>发票信息保存失败</div><br>";
					    }
			         }else{
				         $upSql="UPDATE $DataIn.cw1_fkoutinvoice SET InvoiceNo='$InvoiceNo',InvoiceAmount='$InvoiceAmount',InvoiceDate='$InvoiceDate',Remark='$Remark',modifier='$Operator',modified='$DateTime'  $InvoiceFileSTR WHERE Id='$InvoiceId' ";
				         $upResult=mysql_query($upSql);
				         //echo $upSql;
				         $upSql1="update $DataIn.cw1_fkoutsheet SET InvoiceId='0' WHERE InvoiceId='$InvoiceId'";
				         $upResult1=mysql_query($upSql1);
				         //echo $upSql1 .'<br>';
				         $upSql2="update $DataIn.cw1_fkoutsheet SET InvoiceId='$InvoiceId' WHERE Id IN($ListIds)";
				         $upResult2=mysql_query($upSql2);
				        // echo $upSql2 .'<br>';
				         if ($upResult && $upResult1 && $upResult2){
				             $Log.="$InvoiceId 发票文件重新关联采购单($ListIds)成功 <br>";  
			             }else{
				             $Log.="<div class=redB>$InvoiceId 发票文件重新关联采购单($ListIds)失败</div><br>";    
			             }
	                }
	          }
       }
        $uType=$CompanyId;
        break;
       case 912://财务发票信息确认
            $upSql="update $DataIn.cw1_fkoutinvoice SET Estate='1',modifier='$Operator',modified='$DateTime' WHERE Id='$InvoiceId'";
			$upResult=mysql_query($upSql);
			if (mysql_affected_rows()>0){
				$OperationResult='Y';
			}
         break;
	}
//返回参数
$ALType="From=$From&Estate=$Estate&CompanyId=$uType&Month=$Month&chooseMonth=$chooseMonth";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
if ($ActionId==912){
	 echo $OperationResult;
}else{
	include "../model/logpage.php";
}

?>