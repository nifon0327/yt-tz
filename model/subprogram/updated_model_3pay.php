<?php 
/*
已更新电信---yang 20120801
*/
$Date=date("Y-m-d");
	if($BankId!=""){
	       if($cSign>0){
			   $InSql=$InSql==""?"INSERT INTO $upDataMain (Id,cSign,BankId,PayDate,PayAmount,Payee,Receipt,Checksheet,Remark,
			   Date,Locks,Operator) VALUES (NULL,'$cSign','$BankId','$Date','0','0','0','0','','$Date','0','$Operator')":$InSql;
			    
		    }else{
			    $InSql=$InSql==""?"INSERT INTO $upDataMain (Id,BankId,PayDate,PayAmount,Payee,Receipt,Checksheet,Remark,
			    Date,Locks,Operator) VALUES (NULL,'$BankId','$Date','0','0','0','0','','$Date','0','$Operator')":$InSql;
		    }
		}
	else{
		    if($cSign>0){
		        $InSql=$InSql==""?"INSERT INTO $upDataMain (Id,cSign,PayDate,PayAmount,Payee,Receipt,Checksheet,Remark,
		        Date,Locks,Operator) VALUES (NULL,'$cSign','$Date','0','0','0','0','','$Date','0','$Operator')":$InSql;
		    }else{
			    $InSql=$InSql==""?"INSERT INTO $upDataMain (Id,PayDate,PayAmount,Payee,Receipt,Checksheet,Remark,
			    Date,Locks,Operator) VALUES (NULL,'$Date','0','0','0','0','','$Date','0','$Operator')":$InSql;
		    }
	 }
	$InRes=@mysql_query($InSql);
	$Mid=mysql_insert_id();
if($Log_Item=="中港报关费用"){
	$UpAmountSql = "UPDATE $upDataMain SET $PayeeSTR PayAmount=(SELECT SUM(Amount+depotCharge+declarationCharge+checkCharge+carryCharge+xyCharge+wfqgCharge+ccCharge+djCharge+stopcarCharge+expressCharge+otherCharge) AS Amount FROM $upDataSheet WHERE Mid=$Mid) WHERE Id=$Mid LIMIT 1";
	
	}
if($Log_Item=="报关费用"){
	$UpAmountSql = "UPDATE $upDataMain SET $PayeeSTR PayAmount=(SELECT SUM(declarationCharge) AS Amount FROM $upDataSheet WHERE Mid=$Mid),checkCharge=(SELECT SUM(checkCharge) FROM $upDataSheet WHERE Mid=$Mid) WHERE Id=$Mid LIMIT 1";
	}	

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
					if($Log_Item=="员工薪资"){//来自于薪资，则记录
						$upJZsign="UPDATE $DataIn.cwygjz SET InDate='$Date' WHERE Mid='$Id' LIMIT 1";
						$upJZresult = mysql_query($upJZsign);//更新借支还款日期
						}
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
			$Estate=0;
			}
		else{
			$Log="<div class=redB>主结付单添加失败！$InSql </div><br>";
			}
		?>