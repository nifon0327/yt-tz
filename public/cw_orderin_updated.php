<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw6_orderinsheet
$DataIn.cw6_orderinmain
$DataIn.ch1_shipmain
$DataIn.ch1_shipsheet
$DataIn.cw6_advancesreceived
二合一已更新
*/
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="客户货款";		//需处理
$upDataSheet="$DataIn.cw6_orderinsheet";	//需处理
$upDataMain="$DataIn.cw6_orderinmain";	//需处理
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
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	case 16:
		$Log_Funtion="取消结付";	
		for($i=0;$i<count($checkid);$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$checkAmout=mysql_fetch_array(mysql_query("SELECT Mid,Amount,chId FROM $DataIn.cw6_orderinsheet WHERE Id='$Id' LIMIT 1",$link_id));
				$Mid=$checkAmout["Mid"];
				$chId=$checkAmout["chId"];
				$ReceivedAmount=$checkAmout["Amount"];
				//1状态更新????分批时

				$upShipmain="UPDATE $DataIn.ch1_shipmain SET cwSign=(CASE WHEN 
				(SELECT SUM(Qty*Price) AS Amount FROM $DataIn.ch1_shipsheet WHERE Mid='$chId')>$ReceivedAmount THEN 2 ELSE 1 END) WHERE Id='$chId'";
		
				
				$upRkAction=mysql_query($upShipmain);
				if($upRkAction){					
					//2删除记录
					$delSql="DELETE FROM $DataIn.cw6_orderinsheet WHERE Id='$Id' LIMIT 1";
					$delAction=mysql_query($delSql);					
					if($delAction && mysql_affected_rows()>0){			//主记录检查
						$Log.="收款明细单 $Id 取消成功,收款记录删除成功.<br>";
						//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw6_orderinsheet");
						$checkMain=mysql_query("SELECT Id FROM $DataIn.cw6_orderinsheet WHERE Mid='$Mid'",$link_id);
						if($MainRow=mysql_fetch_array($checkMain)){
							//4主记录收款金额处理
							$upAmount="UPDATE $DataIn.cw6_orderinmain SET PayAmount=PayAmount-$ReceivedAmount WHERE Id='$Mid' LIMIT 1";
							$upAction=mysql_query($upAmount);
							if($upAction && mysql_affected_rows()>0){
								$Log.="&nbsp;&nbsp;主收款单的金额更新成功.<br>";
								}
							else{
								$Log.="<div class='redB'>&nbsp;&nbsp;主收款单的金额更新失败. $upAmount </div>";
								$OperationResult="N";
								}//更新主单金额结束
							}
						else{//3主记录已没有其它明细，删除主记录处理
							$delMain="DELETE FROM $DataIn.cw6_orderinmain WHERE Id='$Mid'";
							$delAction=mysql_query($delMain);
							if($delAction && mysql_affected_rows()>0){
								//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw6_orderinmain");
								$Log.="&nbsp;&nbsp;主收款单已经没有收货明细，删除成功. $delMain <br>";
								//预付项目还原
								$UpSql2 = "UPDATE $DataIn.cw6_advancesreceived SET Mid='0' WHERE Mid='$Mid'";
								$UpResult2 = mysql_query($UpSql2);
								
								}
							else{
								$Log.="<div class='redB'>&nbsp;&nbsp;主收款单已经没有收货明细，删除失败. $delMain </div>";
								$OperationResult="N";
								}//删除主收货单结束
							}//主记录处理完毕if(  $delMain !!!!)
						}  // if($delAction && mysql_affected_rows()>0){	
					else{
						$Log.="<div class='redB'>收款明细单 $Id 取消成功,收款记录删除成失败.</div>";
						$OperationResult="N";
						}
					}//状态更新完毕if($upRkAction && mysql_affected_rows()>0)
				else{
					$Log.="<div class='redB'>收款明细单 $Id 取消失败. $upShipmain </div>";
					$OperationResult="N";
					}
				}//end if($Id!="")
			}//end for ($i=1;$i<=$IdCount;$i++)
		break;
	case 18:
		$Log_Funtion="结付";
		$Ids="";$djIds="";
		//待收款ID
		for($i=0;$i<count($checkid);$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				$x++;
				}
			}
		//待抵付ID
		for($j=0;$j<count($checkdj);$j++){
			$djId=$checkdj[$j];
			if($djId!=""){
				$djIds=$djIds==""?$djId:($djIds.",".$djId);
				}
			}
			
		//检查应收与预付之间的关系
		$Check1Row=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*M.Sign) AS fkAmount FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id WHERE M.Id IN ($Ids)",$link_id));
		$fkAmount=$Check1Row["fkAmount"];
		if ($djIds!=""){
		     $Check2Row=mysql_fetch_array(mysql_query("SELECT SUM(Amount) AS djAmount FROM $DataIn.cw6_advancesreceived WHERE Id IN ($djIds)",$link_id));
		     $djAmount=$Check2Row["djAmount"]==""?0:$Check2Row["djAmount"];	
		}else{
			$djAmount=0;
		}
		
		//echo"$fkAmount-$djAmount";
		$sumAmount=$fkAmount-$djAmount;		//实际收款
		//扣款为负数时的情况
		if($djAmount<=$fkAmount || $djAmount==0){
			//$sql=" LOCK TABLES $upDataMain WRITE";$res=@mysql_query($sql);
			$IN_recode="INSERT INTO $upDataMain (Id,BankId,CompanyId,PreAmount,PayAmount,Handingfee,Remark,PayDate,Locks,Operator) VALUES (NULL,'$BankId','$CompanyId','$djAmount','$sumAmount','$Handingfee','$Remark','$PayDate','0','$Operator')";
			//echo "$IN_recode";
			$inRes=@mysql_query($IN_recode);
			$Mid=mysql_insert_id();
			//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
			if($inRes){
				$Log.="1-收款主单 $Mid 成功入库！<br>";
				//1.明细入库
				$sheetInsert=$DataIn == 'ac' ? "INSERT INTO $DataIn.cw6_orderinsheet SELECT NULL,'$Mid',M.Id,SUM(S.Qty*S.Price*M.Sign) AS Amount,'0' FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id WHERE M.Id IN ($Ids) GROUP BY M.Id" :
				                                "INSERT INTO $DataIn.cw6_orderinsheet SELECT NULL,'$Mid',M.Id,SUM(S.Qty*S.Price*M.Sign) AS Amount,'0',1,0,'$Operator', '$DateTime','$Operator', '$DateTime','$DateTime','$Operator'  FROM $DataIn.ch1_shipmain M LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id WHERE M.Id IN ($Ids) GROUP BY M.Id";
				$sheetRes=@mysql_query($sheetInsert);
				if($sheetRes && mysql_affected_rows()>0){
					$Log.="2-收款明细 $Ids 入库成功.<br>";
					}
				else{
					$Log.="<div class='redB'>2-收款明细入库失败. $sheetInsert</div><br>";
					$OperationResult="N";
					}
				//2.更新出货单收款状态
				$UpSql1 = "UPDATE $DataIn.ch1_shipmain SET cwSign=0 WHERE Id IN ($Ids)";
				$UpResult1 = mysql_query($UpSql1);
				if($UpResult1 && mysql_affected_rows()>0){
					$Log.="3-出货单收款状态更新成功.<br>";
					}
				else{
					$Log.="<div class='redB'>3-出货单收款状态更新失败. $UpSql1</div><br>";
					$OperationResult="N";
					}
				//3.更新预收款
				if($djAmount>0){
					$UpSql2 = "UPDATE $DataIn.cw6_advancesreceived SET Mid=$Mid WHERE Id IN ($djIds)";
					$UpResult2 = mysql_query($UpSql2);
					if($UpResult2 && mysql_affected_rows()>0){
						$Log.="4-预收款项目 $djIds 状态更新成功.<br>";
						}
					else{
						$Log.="<div class='redB'>4-预收款项目状态更新失败. $UpSql2</div><br>";
						$OperationResult="N";
						}
					}
                                 //4.上传进帐凭证        
	                         if($Attached!=""){
                                    $FilePath="../download/cwjzpz/";
                                    if(!file_exists($FilePath)){makedir($FilePath);}
	                            $PreFileName="Z".$Mid.".pdf";
	                            $OldFile=$Attached;
	                            $uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
                                    if ($uploadInfo!=""){
                                        $Log.="5-进帐凭证附件上传成功.<br>";  
                                    }else{
                                        $Log.="5-进帐凭证附件上传失败.<br>"; 
                                    }
	                         }
				$Estate=0;			$cwSign=0;
				}
			else{
				$Log.="<div class=redB>1-主收款单添加失败！</div><br>";
				$OperationResult="N";
				}
			}
		else{
			$Log.="<div class='redB'>应收货款少于预收货款，请先拆分预收货款.</div><br>";
			$OperationResult="N";
			}
		break;
	case 20://生成请款Invoice
		/*$Log_Funtion="生成请款Invoice";
		for($A=0;$A<count($checkid);$A++){
			$Id=$checkid[$A];
			if($Id!=""){
				$CheckSign=mysql_fetch_array(mysql_query("SELECT Sign,ShipType FROM $DataIn.ch1_shipmain WHERE Id='$Id'",$link_id));
				$ShipSign=$CheckSign["Sign"];
				$ShipType=$CheckSign["ShipType"];		
				if(($ShipType=='credit') || ($ShipType=='debit') ){  //说明是Credit note 或者 是debit note	
					include "cw_orderin_creditnote.php";
					}
				else{
					include "cw_orderin_invoice.php";
					}
				}
			}*/
		break;
	case 200:
			$Log_Funtion="主结付单资料更新";
                       //上传进帐凭证        
	                 if($Attached!=""){
                                    $FilePath="../download/cwjzpz/";
                                    if(!file_exists($FilePath)){makedir($FilePath);}
	                            $PreFileName="Z".$Mid.".pdf";
                                    $pdfFile=$FilePath . $PreFileName;
                                    if(file_exists($pdfFile)){
			                unlink($pdfFile);
                                     }
	                            $OldFile=$Attached;
	                            $uploadInfo=UploadFiles($OldFile,$PreFileName,$FilePath);
                                    if ($uploadInfo!=""){
                                        $Log.="进帐凭证附件上传成功.<br>";  
                                    }else{
                                        $Log.="进帐凭证附件上传失败.<br>"; 
                                    }
                             }
			//待抵付ID
			for($j=0;$j<count($checkdj);$j++){
				$djId=$checkdj[$j];
				if($djId!=""){
					$djIds=$djIds==""?$djId:($djIds.",".$djId);
					}
				}
			//更新预付项目的抵付状态
		//检查应收与预付之间的关系
        if($djIds!=""){
		$Check2Row=mysql_fetch_array(mysql_query("SELECT SUM(Amount) AS djAmount FROM $DataIn.cw6_advancesreceived WHERE Id IN ($djIds)",$link_id));
		     $djAmount=$Check2Row["djAmount"]==""?0:$Check2Row["djAmount"];
        }
       else {
             $djAmount=0;
            }
		if($fkAmount-$djAmount>0){
		    	$AmountSTR=",PayAmount=PayAmount-'$djAmount',PreAmount=PreAmount+'$djAmount'";
			}
		else{
			$Log.="<div class='redB'>预付金额大于应收货款,使用预付款失败.</div><br>";
			$OperationResult="N";
			}
			/************************/
			$HandingfeeSTR=$Handingfee!=""?",Handingfee='$Handingfee'":"";
			$PayDateSTR=$PayDate!=""?",PayDate='$PayDate'":"";
			$Locks=$Locks==1?",Locks='1'":",Locks='0'";
			$Remark=addslashes(FormatSTR($Remark));
			$BankSTR=$BankId!=""?",BankId='$BankId'":"";
			$upSql="UPDATE $upDataMain SET Remark='$Remark',modifier='$Operator',modified='$DateTime' $HandingfeeSTR $PayDateSTR $Locks $AmountSTR $BankSTR
			WHERE Id=$Mid";
			$upResult = mysql_query($upSql);
			if($upResult && mysql_affected_rows()>0){
				//更新预收款状态
				if($djIds!=""){
					$upSql2="UPDATE $DataIn.cw6_advancesreceived SET Mid='$Mid' WHERE Id IN ($djIds)";
					$upResult2 = mysql_query($upSql2);
					if($upResult2 && mysql_affected_rows()>0){
						$Log.="预收款项目抵付状态更新成功.<br>";
						}
					else{
						$Log.="<div class='redB'>预收款项目抵付状态更新失败. $upSql2</div><br>";
						$OperationResult="N";
						}
					}
				$Log.="$Log_Funtion 成功!<br>";
				}
			else{
				$Log.="<div class='redB'>$Log_Funtion 失败！$upSql </div><br>";
				$OperationResult="N";
				}
			$cwSign=0;
		break;
	case 916://取消预收项目
			$upSql="UPDATE $DataIn.cw6_advancesreceived D LEFT JOIN $DataIn.cw6_orderinmain M ON M.Id=D.Mid SET M.PayAmount=M.PayAmount+D.Amount,M.PreAmount=M.PreAmount-D.Amount,D.Mid='0' WHERE D.Id='$djId'";
			$upResult = mysql_query($upSql);
			if($upResult && mysql_affected_rows()>0){
				$Log="$Log_Funtion 成功！$upSql <br>";
				}
			else{
				$Log="<div class='redB'>$Log_Funtion 失败！$upSql </div><br>";
				$OperationResult="N";
				}
		break;
	default:
		$SetStr="CompanyId='$CompanyId',Termini='$Termini',ExpressNO='$ExpressNO',BoxQty='$BoxQty',mcWG='$mcWG',Price='$Price',depotCharge='$depotCharge',Remark='$Remark',Date='$DateTime',Locks='0',Operator='$Operator'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&cwSign=$cwSign";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

