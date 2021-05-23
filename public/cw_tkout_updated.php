<?php 
//电信-zxq 2012-08-01
/*$DataIn.cw1_tkoutsheet/$DataIn.cw1_tkoutmain二合一已更新*/
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="客户退款配件货款";		//需处理
$Log_Funtion="更新";
$upDataSheet="$DataIn.cw1_tkoutsheet";	//需处理
$upDataMain="$DataIn.cw1_tkoutmain";	//需处理
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
				$ALType="From=$From&CompanyId=$CompanyId";
				}
			else{
				$Log="<div class='redB'>请款ID在($Ids)的需求单请款退回失败. $delRecode</div>";
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
				 	//解锁
				//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
				//更新金额:按实际结付成功的金额计算（需求单的总额-订金的总额）
				$upSql = "UPDATE $upDataMain SET PayAmount=
				(SELECT SUM(Amount) FROM $upDataSheet WHERE Mid=$Mid)
				WHERE Id=$Mid LIMIT 1";
				$upResult = mysql_query($upSql);
				if($upResult && mysql_affected_rows()>0){
					$Log.="&nbsp;&nbsp;4-4 结付金额更新成功.<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;4-4 结付金额更新失败. $upSql </div><br>";
					$OperationResult="N";
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
			$Estate=0;
			break;
	}
//返回参数
$ALType="From=$From&Estate=$Estate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>