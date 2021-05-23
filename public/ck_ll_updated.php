<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="领料记录";		//需处理
$upDataSheet="$DataIn.ck5_llsheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";	include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";	include "../model/subprogram/updated_model_3d.php";		break;
		
	default:	
		$Log_Funtion="领料数据更新";
		$checkRow=mysql_fetch_array(mysql_query("SELECT L.StockId,L.POrderId,L.StuffId,D.StuffCname,L.sPOrderId,L.RkId 
			FROM $DataIn.ck5_llsheet L 
			LEFT JOIN $DataIn.stuffdata D ON L.StuffId=D.StuffId 
			WHERE L.Id=$Id LIMIT 1",$link_id)); 
			$StuffId = $checkRow["StuffId"];
			$POrderId = $checkRow["POrderId"];
			$StockId = $checkRow["StockId"];
			$sPOrderId = $checkRow["sPOrderId"];
			$StuffCname = $checkRow["StuffCname"];
			$thisRkId  = $checkRow["RkId"];
		if($OperatorAction ==0){ //领料数量减少
			//1.更新领料数量
			$upSql = "UPDATE $DataIn.ck5_llsheet  SET Qty=Qty-$changeQty WHERE Id=$Id  ";
			$upResult = mysql_query($upSql);		
			if($upResult && mysql_affected_rows()>0){
				$Log="3-1:领料数据更新成功,已返回相应在库. <br>";
				$UpSql2="UPDATE $DataIn.ck1_rksheet SET llQty = llQty-$changeQty,llSign=2  
				        WHERE Id IN (SELECT RkId FROM $DataIn.ck5_llsheet  WHERE Id=$Id) ";
						$UpResult2 = mysql_query($UpSql2);
						if($UpResult2){
							$Log.="&nbsp;&nbsp;3-2:配件名称:$StuffCname 的库存领料数据还原成功.<br>";
							}
						else{
							$Log.="<div class='redB'>&nbsp;&nbsp;3-2:配件名称:$StuffCname 的库存领料数据失败.$UpSql2 </div><br>";
							$OperationResult="N";
							}	
				
				}
			else{
				$Log="<div class='redB'>3-1:领料数据更新失败! $upSql </div><br>";
				$OperationResult="N";
				}
		}else{
			
			$NextllQty = $changeQty;
			
			
			$checkRkRow = mysql_fetch_array(mysql_query("SELECT (Qty-llQty) AS lastQty FROM $DataIn.ck1_rksheet WHERE Id='$thisRkId'",$link_id));
			$thislastQty = $checkRkRow["lastQty"];
			
			if($thislastQty>$changeQty){
				$upSql = "UPDATE $DataIn.ck5_llsheet  SET Qty=Qty+$changeQty WHERE Id=$Id  ";
			    $upResult = mysql_query($upSql);
			    if($upResult && mysql_affected_rows()>0){
				    $Log="3-1:领料数据更新成功 <br>";
				    $UpdateSql2 = "UPDATE  ck1_rksheet  SET llQty = llQty + $changeQty  WHERE Id = $thisRkId";
			        $UpdateResult2 = mysql_query($UpdateSql2);
					if($UpdateResult2){
						$Log.="&nbsp;&nbsp;3-2:配件名称:$StuffCname 的出库数据更新成功.<br>";
						}
					else{
						$Log.="<div class='redB'>&nbsp;&nbsp;3-2:配件名称:$StuffCname 的出库数据更新失败.$UpdateSql2 </div><br>";
						$OperationResult="N";
						}	
				
				}
			    else{
				    $Log="<div class='redB'>3-1:领料数据更新失败! $upSql </div><br>";
				    $OperationResult="N";
				}
				
				
			}else{
				
			   $CheckResult = mysql_query("SELECT  Id ,(Qty-llQty) AS lastQty,Price FROM $DataIn.ck1_rksheet WHERE StuffId = '$StuffId' AND Qty>llQty ORDER BY Id ASC",$link_id);
				while($CheckRow = mysql_fetch_array($CheckResult)){
					
					$RkId = $CheckRow["Id"];
					$lastQty = $CheckRow["lastQty"];
					$Price = $CheckRow["Price"];
					if($NextllQty<=0){
					     break;
				     }
				    if($lastQty>$changeQty){
					     $llQty  = $changeQty;
					     $llSign = 2;
					     $NextllQty = 0 ;
					    
				     }else{   
					     $llQty = $lastQty;
					     $llSign = 0 ;
					     $NextllQty = $changeQty - $llQty;
				     }	    
				     
				     $InSql = "INSERT INTO $DataIn.ck5_llsheet (POrderId, sPOrderId, StockId, StuffId, Price, Qty, ComboxSign,Type, FromId, FromFunction, RkId, Locks, Estate, Date, Operator, Receiver, Received, PLocks, creator, created) 
			                     VALUES ('$POrderId','$sPOrderId','$StockId','$StuffId','$Price','$llQty',0,'1','0','工单领料','$RkId','0',0,'$DateTime','$Operator','0','0000-00-00','0',$Operator,'$DateTime')";
			         $InResult = mysql_query($InSql);
			         
			         if($InResult && mysql_affected_rows()>0){
			            $Log="3-1:领料数据更新成功 <br>";
				
				        $UpdateSql = "UPDATE  ck1_rksheet  SET llQty = llQty + $llQty  WHERE Id = $RkId";
				        $UpdateResult = mysql_query($UpdateSql);
			         }
			         else{
						$Log="<div class='redB'>3-1:领料数据更新失败! $InSql </div><br>";
						$OperationResult="N";
				    }       
					
				}
			}
			
		}
		
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseMonth=$chooseMonth&TypeId=$TypeId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
  