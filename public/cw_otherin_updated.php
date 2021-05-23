<?php
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="其他收入";		//需处理
$upDataSheet="$DataIn.cw4_otherinsheet";	//需处理
$upDataMain="$DataIn.cw4_otherinmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$x=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}

$FileDir="otherin";
switch($ActionId){
	case 17:
         $upDataSheet="$DataIn.cw4_otherin";	//需处理
		  $Log_Funtion="审核";	$SetStr="Estate=3,Locks=0";		include "../model/subprogram/updated_model_3d.php";		
           $fromWebPage=$funFrom."_m";		
         break;
	case 15://???
		$Log_Funtion="退回修改";//未结付退回
      if($fromWebPage=="cw_otherin_read"){
                $UpSql1="UPDATE $DataIn.cw4_otherin SET Mid=0 ,Estate=1 WHERE Id IN ($Ids)";
                $UpResult1=@mysql_query($UpSql1);
		if($UpResult1 && mysql_affected_rows()>0){
				$Log="请款ID在($Ids)的收货单请款退回成功.";
				$ALType="From=$From";
				}
			else{
				$Log="<div class='redB'>请款ID在($Ids)的收货单请款退回失败. $UpResult1</div>";
				$OperationResult="N";
				}
        }
else{
		if($Estate==3 ){					//未结付退回
			$delRecode="DELETE FROM $upDataSheet WHERE Id IN ($Ids)";
			$delAction = mysql_query($delRecode);
			if($delAction && mysql_affected_rows()>0){
				$Log="请款ID在($Ids)的收货单请款退回成功.";
                $UpSql1="UPDATE $DataIn.cw4_otherin SET Mid=0 ,Estate=1 WHERE Mid IN ($Ids)";
                $UpResult1=@mysql_query($UpSql1);
				$ALType="From=$From";
				}
			else{
				$Log="<div class='redB'>请款ID在($Ids)的收货单请款退回失败. $delRecode</div>";
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
						$Log.="$x 3-1:请款收货单 $Id 已退回,请款记录删除成功.<br>";
                       $UpSql1="UPDATE $DataIn.cw4_otherin SET Mid=0 ,Estate=1 WHERE Mid IN ($Id)";
                       $UpResult1=@mysql_query($UpSql1);
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
						$Log="<div class='redB'>$x 3-1:请款的收货单 $Id 退回失败.</div>";
						$OperationResult="N";
						}
					$x++;
					}//end if($Id!="")
				}//end for
			////////////////////////////////
			}
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
					$Log.="$x 3-1:请款收货单 $Id 取消结付成功.<br>";
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
					$Log="<div class='redB'>$x 3-1:请款的收货单 $Id 取消结付失败.</div>";
					$OperationResult="N";
					}
				$x++;
				}//end if($Id!="")
			}//end for
		break;
	case 18://结付
		$Log_Funtion="结付";		//include "../model/subprogram/updated_model_3pay.php";$chooseMonth
      //$Date=$chooseMonth."-28";
      $Date=date("Y-m-d");
	if($BankId!=""){
		$InSql=$InSql==""?"INSERT INTO $upDataMain (Id,BankId,PayDate,PayAmount,Payee,Receipt,Checksheet,Remark,Date,Locks,Operator) VALUES (NULL,'$BankId','$Date','0','0','0','0','','$Date','0','$Operator')":$InSql;
		}
	else{
		$InSql=$InSql==""?"INSERT INTO $upDataMain (Id,PayDate,PayAmount,Payee,Receipt,Checksheet,Remark,Date,Locks,Operator) VALUES (NULL,'$Date','0','0','0','0','','$Date','0','$Operator')":$InSql;
		}
	$InRes=@mysql_query($InSql);
	$Mid=mysql_insert_id();
	if($InRes){
		$Log.="结付主单记录成功入库！明细帐目处理如下:<br>";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){							
				$sql = "UPDATE $upDataSheet SET Estate=0,Mid=$Mid,Locks=0 WHERE Id=$Id LIMIT 1";
				$result = mysql_query($sql);
				if ($result){
					$Log.="Id为 $Id 的 $Log_Item 结付成功。<br>";
					}
				else{
					$Log.="<div class='redB'>&nbsp;&nbsp;Id为 $Id 的 $Log_Item 结付失败！</div><br>";
					$OperationResult="N";
					}
				}//end if($Id!="")
			}//end for		
			//更新金额:按实际结付成功的金额计算
			if($AmountValue==""){
				$AmountValue="Amount";
				}
			$UpAmountSql =$UpAmountSql==""?"UPDATE $upDataMain SET $PayeeSTR PayAmount=(SELECT SUM($AmountValue) FROM $upDataSheet WHERE Mid=$Mid) WHERE Id=$Mid LIMIT 1":$UpAmountSql;
			//echo $sql;
			$UpAmountResult = mysql_query($UpAmountSql);
			if($UpAmountResult){
				$Log.="&nbsp;&nbsp;结付金额更新成功<br>";
				}
			else{
				$Log.="<div class='redB'>&nbsp;&nbsp;结付金额更新失败. $UpAmountSql </div><br>";
				}
			//$Estate=0;
			}
		else{
			$Log="<div class=redB>主结付单添加失败！$InSql </div><br>";
			}
		break;
	case 26:
		$Log_Funtion="收款单重置";
        $UpdateSql="UPDATE  $DataIn.cw4_otherinsheet   M 
         LEFT JOIN ( SELECT  Mid,SUM(Amount) AS SumAmount FROM $DataIn.cw4_otherin WHERE Mid=$Id ) S  ON S.Mid=M.Id 
        SET  M.Amount=S.SumAmount   WHERE M.Id=$Id";
        $UpdateResult=@mysql_query($UpdateSql);
		$pUpSql=mysql_query("UPDATE $DataIn.cw4_otherin SET Estate='0' WHERE Mid IN ($Id)");
        include "cw_otherin_topdf.php";
    break;
	case 20://财务更新
			//必选参数	:文件目录
			$Log_Funtion="主结付单资料更新";
			include "../model/subprogram/updated_model_cw.php";
			break;
	default:
        $upDataSheet="$DataIn.cw4_otherin";	//需处理
		$FilePath="../download/otherin/";
		$PreFileName1="O".$Id.".jpg";
		if($Attached!=""){
			$OldFile1=$Attached;
			
			$uploadInfo1=UploadFiles($OldFile1,$PreFileName1,$FilePath);
			$BillSTR=$uploadInfo1==""?",Bill='0'":",Bill='1'";
			}
		if($BillSTR=="" && $oldAttached!=""){//没有上传文件并且已选取删除原文件
			$FilePath1=$FilePath."/$PreFileName1";
			if(file_exists($FilePath1)){
				unlink($FilePath1);
				}
			$BillSTR=",Bill='0'";
			}
		$SetStr="TypeId='$TypeId',Amount='$Amount',Currency='$Currency',Date='$getDate',Remark='$Remark',Locks='0',Operator='$Operator' $BillSTR";
		include "../model/subprogram/updated_model_3a.php";
		break;
}

		//	$ALType="From=$From&Pagination=$Pagination&Page=$Page&Estate=$Estate&chooseMonth=$chooseMonth";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>