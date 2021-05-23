&nbsp;
<?php 
//EWEN 2013-03-04 OK
include "../model/modelhead.php";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非bom配件申购单";		//需处理
$upDataSheet="$DataIn.nonbom6_cgsheet";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$FileDir="cwnonbom";
$x=1;
switch($ActionId){
	case 7://OK
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8://OK
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	/*	
    case 14://OK
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$Month=date("Y-m");
		$Log_Funtion="请款";
		$inRecode="INSERT INTO $DataIn.nonbom12_cwsheet SELECT NULL,'0',S.Id,S.GoodsId,M.CompanyId,S.Qty,S.Price,S.Qty*S.Price,'$Month','2','1','$DateTime','$Operator'  FROM $DataIn.nonbom6_cgsheet S 
		LEFT JOIN $DataIn.nonbom6_cgmain M ON M.Id=S.Mid  WHERE S.rkSign='0' AND S.Id IN ($Ids)";
		$inAction=@mysql_query($inRecode);
		if($inAction){ 
			$Log.="Id号在(".$Ids.")的".$TitleSTR."成功!<br>";
			//更新需求单的退回信息
			
			} 
		else{ 
			$Log.="<div class=redB>Id号在(".$Ids.")的".$TitleSTR."失败! $inRecode</div><br>";
			$OperationResult="N";
			}
		break;
	*/	
	case 15://退回 OK 2013-11-19
		$Log_Item="非bom采购请款单";		//需处理
	    $Estate=1;
		if($fromWebPage==$funFrom."_m"){
			$Log_Funtion="审核退回";
			$fromWebPage=$funFrom."_m";//$SetStr="ReturnReasons='$ReturnReasons',Estate=1,Locks=1";	
			}
		else{
				$Log_Funtion="未结付退回";
				$fromWebPage=$funFrom."_cw";
			}
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$UpSql="UPDATE $DataIn.nonbom11_qksheet SET Estate='$Estate',ReturnReasons='$ReturnReasons' WHERE Id IN ($Ids)";
		$UpResult = mysql_query($UpSql);	
		if ($UpResult && mysql_affected_rows()>0){
				$Log.="ID号在($Ids) 的".$Log_Item.$Log_Funtion."成功.<br>";
				}
		else{
			$OperationResult="N";
			$Log.="<div class='redB'>ID号在($Ids) 的".$Log_Item.$Log_Funtion."失败.</div><br>";
			}
		break;
	case 16://取消结付 2013-11-19 ewen OK
		$Log_Funtion="取消结付";
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if($Id!=""){
				$checkRow=mysql_fetch_array(mysql_query("SELECT Mid FROM $DataIn.nonbom11_qksheet WHERE Id=$Id LIMIT 1",$link_id));
				$Mid=$checkRow["Mid"];	//主结付单
				$UpSql="UPDATE $DataIn.nonbom11_qksheet A 
				LEFT JOIN $DataIn.nonbom11_qkmain B ON A.Mid=B.Id 
				SET A.Mid='0',A.Estate='3',
				B.PayAmount=B.PayAmount-A.Amount,
				B.hkAmount=B.hkAmount-A.hkAmount,
				B.taxAmount=B.taxAmount-A.taxAmount,
				B.shipAmount=B.shipAmount-A.shipAmount
				WHERE A.Id='$Id'";
				$UpResult = mysql_query($UpSql);
				if($UpResult && mysql_affected_rows()>0){
					$Log.="$x 1、请款需求单 $Id 取消结付成功.<br>";
					$checkMain=mysql_query("SELECT Id FROM $DataIn.nonbom12_cwsheet WHERE Mid=$Mid",$link_id);
					if($checkMainRow=mysql_fetch_array($checkMain)){//还有记录，更新
						$Log.="$x 2、结付金额已随之更新.<br>";
						}
					else{//没有明细记录，则删除主单记录：删除前需退回订单及金额	
						$DelSql="DELETE FROM $DataIn.nonbom11_qkmain WHERE Id='$Mid'";
						$DelResult = mysql_query($DelSql);
						if($DelResult && mysql_affected_rows()>0){
							$Log.="$x 2、主结付单删除成功.<br>";
							//删除结付图档
							$FilePathC="../download/$FileDir/C".$Mid.".jpg";		if(file_exists($FilePathC))unlink($FilePathC);
							$FilePathP="../download/$FileDir/P".$Mid.".jpg";		if(file_exists($FilePathP))unlink($FilePathP);
							$FilePathR="../download/$FileDir/R".$Mid.".jpg";		if(file_exists($FilePathR))unlink($FilePathR);
							}
						else{
							$Log.="<div class='redB'>$x 2、主单删除失败！ $DelSql </div><br>";
							$OperationResult="N";
							}
						}
					}
				else{
					$Log="<div class='redB'>$x 1、请款的需求单 $Id 取消结付失败. $UpSql</div>";
					$OperationResult="N";
					}
				$x++;
				}//end if($Id!="")
			}//end for
			$Estate=0;
			$fromWebPage=$funFrom."_cw";
		break;
	case 17://OK
		$upDataSheet="$DataIn.nonbom11_qksheet";	//需处理
		$Log_Funtion="审核通过";
		$SetStr="Estate=3";
		include "../model/subprogram/updated_model_3d.php";
		$fromWebPage=$funFrom."_m";
		break;
	case 18://结付(改为主采购单请款) ewen 2013-11-19 OK
		$Lens=count($checkid);
		for($i=0;$i<$Lens;$i++){
			$Id=$checkid[$i];
			if ($Id!=""){
				$Ids=$Ids==""?$Id:($Ids.",".$Id);
				}
			}
		$Log_Funtion="结付";
		$Remark=FormatSTR($ReturnReasons);
		$IN_recode="INSERT INTO $DataIn.nonbom11_qkmain (Id,BankId,CompanyId,PayDate,hkAmount,taxAmount,shipAmount,PayAmount,Payee,Receipt,Checksheet,Remark,Date,Locks,Operator) VALUES 
		(NULL,'$BankId','$CompanyId','$DateTime','0','0','0','0','0','0','0','$Remark','$DateTime','0','$Operator')";
		$inRes=@mysql_query($IN_recode);
		$Mid=mysql_insert_id();		
		if($inRes && $Mid>0){
			$Log="1、结付主单记录成功入库！明细帐目处理如下:<br>";
			//需求单处理
			$sql = "UPDATE $DataIn.nonbom11_qksheet SET Estate=0,Mid=$Mid,Locks=0 WHERE Id IN ($Ids)";
			$result = mysql_query($sql);
			if($result){
				$Log.="2、Id在为($Ids)的 $Log_Item 结付成功。<br>";
				/////////////////////////////////////////////
				//更新结付金额
				$upSql = "UPDATE $DataIn.nonbom11_qkmain A
				LEFT JOIN (SELECT Mid,SUM(hkAmount) AS hkAmount,SUM(taxAmount) AS taxAmount,SUM(shipAmount) AS shipAmount,SUM(Amount) AS Amount FROM $DataIn.nonbom11_qksheet WHERE Mid='$Mid') B ON B.Mid=A.Id
				SET A.hkAmount=B.hkAmount,A.taxAmount=B.taxAmount,A.shipAmount=B.shipAmount,A.PayAmount=B.Amount WHERE A.Id='$Mid'";
				$upResult = mysql_query($upSql);
				if($upResult && mysql_affected_rows()>0){
					$Log.="3、结付金额更新成功.$upSql <br>";
					}
				else{
					$Log.="<div class='redB'>3、结付金额更新失败. $upSql </div><br>";
					$OperationResult="N";
					}
				/////////////////////////////////////////////
				}
			else{
				$Log.="<div class='redB'>2、Id在为($Ids)的 $Log_Item 结付失败！$sql</div><br>";
				$OperationResult="N";
				}
			}
		else{
			$Log="<div class=redB>1、主结付单添加失败！$IN_recode</div><br>";
			$OperationResult="N";
			}
		$fromWebPage=$funFrom."_cw";
	break;
	case 20://主结付单更新  2013-11-19 ewen OK
			//必选参数	:文件目录
			$upDataMain="$DataIn.nonbom11_qkmain";	//需处理
			$Log_Funtion="主结付单资料更新";
			include "../model/subprogram/updated_model_cw.php";
			$Estate=0;
			$fromWebPage=$funFrom."_cw";
			break;
	case 27://OK
		$Log_Funtion="申购单还原";	//条件：未收货、未结付：结付需先收完货，故只检查rkSign就可以，如果为了稳妥可以直接检查入库表(速度慢)
		$UpSql= "UPDATE $upDataSheet A
		LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=A.GoodsId
		LEFT JOIN (SELECT cgId,GoodsId FROM $DataIn.nonbom7_insheet GROUP BY cgId ) C ON C.cgId=A.Id AND C.GoodsId=A.GoodsId
		SET A.Mid='0',A.rkSign='1',B.oStockQty=B.oStockQty-A.Qty 
		WHERE A.Id='$Id' AND A.Locks='1' AND B.oStockQty>=A.Qty AND A.rkSign='1' AND C.cgId IS NULL 
		              AND NOT EXISTS(SELECT S.cgMid FROM $DataIn.nonbom11_qksheet S WHERE S.Estate>1 AND S.cgMid=A.Mid)";
		$UpResult = mysql_query($UpSql);
		if($UpResult && mysql_affected_rows()>0){
			$Log.="需求单还原成功(ID号 $Id)!<br>"; 
			 //删除主单
			$DelSql="DELETE A FROM $DataIn.nonbom6_cgmain A LEFT JOIN $upDataSheet B ON B.Mid=A.Id WHERE B.Mid IS NULL";
			$DelResult=mysql_query($DelSql);
			if($DelResult && mysql_affected_rows()>0){
				$Log.="主单 $Mid 下已没有明细，删除主单成功.<br>";
				}
			else{
				$Log.="<div class='redB'>主单 $Mid 下已没有明细，删除主单失败.$DelSql</div><br>";
				$OperationResult="N";
				}
			}
		else{
			$Log.="<div class='redB'>申购单还原失败(ID号 $Id),已锁定或已收货! $UpSql</div><br>"; 
			$OperationResult="N";
			}
		break;
	case 99:
		    $Log_Item="采购单主单";
		    $Id=$Mid;
		    $Remark=FormatSTR($Remark);
			$upDataSheet="$DataIn.nonbom6_cgmain";
			// 加入采购合同文件	nonbom_contract
			$contractStr="";			
			if($Attached!=""){//有上传文件
				$FileType=substr("$Attached_name", -4, 4);
				$OldFile=$Attached;	
				$FilePath="../download/nonbom_contract/";	
				if(!file_exists($FilePath)){
					makedir($FilePath);
				}
				$PreFileName=$Id.$FileType;
				$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
				if ($Attached!=""){ //上传成功
					$contractStr=' Attached=1, ';
				}
			}			
			$SetStr=" $contractStr Remark='$Remark',Date='$Date',Locks='0'";
			include "../model/subprogram/updated_model_3a.php";

          if($SIdList!=""){
                    $tempArray=explode("|",$SIdList);
                    $tempCount=count($tempArray);
                     for($k=0;$k<$tempCount;$k++){
                           $tempId=$tempArray[0];
                           $Sql = "UPDATE $DataIn.nonbom6_cgsheet A
                           LEFT JOIN $DataPublic.nonbom5_goodsstock B ON B.GoodsId=A.GoodsId
                           SET A.Mid='$Id',A.Locks='0',B.oStockQty=B.oStockQty+A.Qty WHERE A.Id IN ($tempId) AND A.Estate='1' AND A.Mid='0'";//已审核的才加入采购单，同时更新采购库存
                           $Result = mysql_query($Sql);
                           if($Result && mysql_affected_rows()>0 && $Mid>0){
	                           	$Log.="需求单明细 ($tempId) 加入主采购单 $Id 成功!<br>";
                           }
                     }
              }
	/*	if($ActionFrom==1){//更新处理
			$upDataSheet="$DataIn.nonbom6_cgmain";
			// 加入采购合同文件	nonbom_contract
			$contractStr="";
			
			if($Attached!=""){//有上传文件
				$FileType=substr("$Attached_name", -4, 4);
				$OldFile=$Attached;	
				$FilePath="../download/nonbom_contract/";	
				if(!file_exists($FilePath)){
					makedir($FilePath);
				}
				$PreFileName=$Id.$FileType;
				//echo "UploadFiles($OldFile,$PreFileName,$FilePath)";
				$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
				if ($Attached!=""){ //上传成功
					$contractStr=' Attached=1, ';
				}
			}			
			//$SetStr="taxAmount='$taxAmount',shipAmount='$shipAmount',Remark='$Remark',Date='$Date',Locks='0'";
			$SetStr=" $contractStr Remark='$Remark',Date='$Date',Locks='0'";
			include "../model/subprogram/updated_model_3a.php";
			
		}
		else{//请款处理

			$checkHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Qty*Price),0) AS HKAmount FROM $DataIn.nonbom6_cgsheet WHERE Mid='$Mid' ",$link_id));		
			$HKAmount=$checkHk["HKAmount"];   //货款
		
			$checkHavedHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS HavedAmount FROM $DataIn.nonbom11_qksheet WHERE CgMid='$Mid' ",$link_id));		
		    $HavedAmount=$checkHavedHk["HavedAmount"];  //已请款：连接请款记
		    $TotalAmount=$HavedAmount+$qkHK;
          echo "TotalAmount:".$TotalAmount."----------".$HKAmount;
			if($TotalAmount-$HKAmount>0.1) {
					$Log="<span class='redB'>严重错语，请与管理员联系！！货款是：$HKAmount ,已请款：$HavedAmount </span>";
					$OperationResult="N";				
			}
			else{
				//taxAmount,  ShipAmount 已弃用 modify by zx 2013-11-25
				$Amount=$qkHK;
				$sheetInSql="INSERT INTO $DataIn.nonbom11_qksheet (Id,Mid,cgMid,mainType,TypeId,CompanyId,hkAmount,taxAmount,ShipAmount,Amount,Month,Remark,ReturnReasons,Estate,Locks,Date,Operator) VALUES 
				(NULL,'0','$Mid','$mainType','$TypeId','$CompanyId','$qkHK','0','0','$Amount','$Month','$Remark','','2','1','$DateTime','$Operator') ";
				
				$sheetInAction=@mysql_query($sheetInSql);
				if($sheetInAction && mysql_affected_rows()>0){
					$Log="请款成功";
					}
				else{
					$Log="<span class='redB'>请款失败 $sheetInSql</span>";
					$OperationResult="N";
					}
			}		
	}*/
	break;
  case "154":
		$Lens=count($checkqkid);
		for($i=0;$i<$Lens;$i++){
			$Mid=$checkqkid[$i];
			$checkHk=mysql_fetch_array(mysql_query("SELECT M.CompanyId,M.PurchaseID,M.mainType, IFNULL(SUM(G.Qty*G.Price),0) AS HKAmount 
            FROM $DataIn.nonbom6_cgsheet  G 
           LEFT JOIN $DataIn.nonbom6_cgmain M ON M.Id=G.Mid
           WHERE G.Mid='$Mid' ",$link_id));	
			$CompanyId=$checkHk["CompanyId"];   
			$PurchaseID=$checkHk["PurchaseID"];   
			$HKAmount=$checkHk["HKAmount"];   //货款	
			$mainType=$checkHk["mainType"];   
			$checkHavedHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS HavedAmount FROM $DataIn.nonbom11_qksheet WHERE CgMid='$Mid' ",$link_id));	
		    $HavedAmount=$checkHavedHk["HavedAmount"];  //已请款：连接请款记
            if($HavedAmount>0 && $HavedAmount<$HKAmount){
                  $Log.="<div class='redB'>采购单号为$PurchaseID 已部分请款，不能使用全额请款功能,请使用分批请款功能</div>";
                   }
            else{      
			    $Amount=$HKAmount;
				$sheetInSql="INSERT INTO $DataIn.nonbom11_qksheet (Id,Mid,cgMid,mainType,TypeId,CompanyId,hkAmount,taxAmount,ShipAmount,Amount,Month,Remark,ReturnReasons,Estate,Locks,Date,Operator) VALUES 
				(NULL,'0','$Mid','$mainType','1','$CompanyId','$HKAmount','0','0','$Amount','$qkMonth','$Remark','','2','1','$DateTime','$Operator') ";
				$sheetInAction=@mysql_query($sheetInSql);
                 $qkId=mysql_insert_id();
				if($qkId>0){
					  $Log="采购单号为$PurchaseID 请款成功";
                      $UpdateSql="UPDATE  $DataIn.nonbom6_cgsheet SET qkId='$qkId'  WHERE  Mid='$Mid'";
                      $UpdateResult=@mysql_query($UpdateSql);
					}
				else{
					$Log="<span class='redB'>采购单号为$PurchaseID 请款失败 $sheetInSql</span>";
					$OperationResult="N";
					}
                 }
        }
	break;

     case "155"://分批请款，有预付款，尾款，和分批款三种形式
			$checkHk=mysql_fetch_array(mysql_query("SELECT M.CompanyId,M.PurchaseID,M.mainType, IFNULL(SUM(G.Qty*G.Price),0) AS HKAmount 
            FROM $DataIn.nonbom6_cgsheet  G 
           LEFT JOIN $DataIn.nonbom6_cgmain M ON M.Id=G.Mid
           WHERE G.Mid='$Mid' ",$link_id));	
			$HKAmount=$checkHk["HKAmount"];   //货款	
			$mainType=$checkHk["mainType"];   
		
			$checkHavedHk=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(Amount),0) AS HavedAmount FROM $DataIn.nonbom11_qksheet WHERE CgMid='$Mid' ",$link_id));		
		    $HavedAmount=$checkHavedHk["HavedAmount"];  //已请款：连接请款记
		    $TotalAmount=$HavedAmount+$qkHK;
          echo "TotalAmount:".$TotalAmount."----------".$HKAmount;
			if($TotalAmount-$HKAmount>0.1) {
					$Log="<span class='redB'>严重错误，请与管理员联系！！货款是：$HKAmount ,已请款：$HavedAmount </span>";
					$OperationResult="N";				
			}
			else{
				//taxAmount,  ShipAmount 已弃用
				$Amount=$qkHK;
				$sheetInSql="INSERT INTO $DataIn.nonbom11_qksheet (Id,Mid,cgMid,mainType,TypeId,CompanyId,hkAmount,taxAmount,ShipAmount,Amount,Month,Remark,ReturnReasons,Estate,Locks,Date,Operator) VALUES 
				(NULL,'0','$Mid','$mainType','$TypeId','$CompanyId','$qkHK','0','0','$Amount','$qkMonth','$Remark','','2','1','$DateTime','$Operator') ";				
				$sheetInAction=@mysql_query($sheetInSql);
               $qkId=mysql_insert_id();
				if($qkId>0){
					    $Log="请款成功";
                    if($TypeId==4){
                          $Lens=count($PayCgId);
                            for($k=0;$k<$Lens;$k++){
                                     $cgId=$PayCgId[$k];
                                     $UpdateSql="UPDATE  $DataIn.nonbom6_cgsheet SET qkId='$qkId'  WHERE  Id='$cgId'";$UpdateResult=@mysql_query($UpdateSql);
                                }
                          }
				  }
				else{
					   $Log="<span class='redB'>请款失败 $sheetInSql</span>";
					   $OperationResult="N";
					}
               }
	      break;
	      
    case 179:
             if ($DeleteSign==1 && $OldInvoiceFile!=""){
					$delSql="DELETE FROM $DataIn.nonbom6_invoice  WHERE InvoiceFile='$OldInvoiceFile'";
					$delResult=mysql_query($delSql);
						      
			         if ($delResult){
			             $Log.="$OldInvoiceFile 发票文件删除成功<br>";  
			             $FilePath="../download/nonbom_cginvoice/" . $OldInvoiceFile;
					        if(!file_exists($FilePath)){
						         unlink($FilePath);
						         $Log.="删除发票文件($OldInvoiceFile)删除成功!";
					       }
		             }else{
			             $Log.="<div class=redB>$OldInvoiceFile 发票文件删除失败</div><br>";    
		             }
        }
        else{
	         $Lens=count($ListId);
	         $Log_Funtion="发票上传更新";
	         $InvoiceFileSTR='';
	         if($InvoiceFile!='' && $Lens>0){
		         $FilePath="../download/nonbom_cginvoice/";
			     if(!file_exists($FilePath)){
				     makedir($FilePath);
			     }
			     $FileName=date('YmdHms').rand(100,999) . '.pdf';
			     $uploadInfo=UploadFiles($InvoiceFile,$FileName,$FilePath);
			     
				 $InvoiceFileSTR=$uploadInfo==""?'':",InvoiceFile='$uploadInfo'"; 
	
			     if ($uploadInfo){
				     $Log="发票文件上传操作成功.$FileName <br>";
			     }else{
				     $Log="<div class=redB>发票文件上传操作失败.$FileName </div><br>";
			     }
	        }
         
           $Remark=$Remark==''?'':$Remark;
           for($i=0;$i<$Lens;$i++){
			     $cgMid=$ListId[$i];
			     $CheckRow = mysql_fetch_array(mysql_query("SELECT Id FROM $DataIn.nonbom6_invoice 
			     WHERE cgMid ='$cgMid'",$link_id));
			     $InvoiceId = $CheckRow["Id"]==""?0:$CheckRow["Id"];
		         if ($InvoiceId==0){
		         
			        $IN_recode="INSERT INTO $DataIn.nonbom6_invoice (Id,cgMid,InvoiceNo,InvoiceFile,InvoiceDate,InvoiceAmount,Remark,Date,Operator)VALUES (NULL,'$cgMid','$InvoiceNo','$FileName','$InvoiceDate','$InvoiceAmount','$Remark','$DateTime','$Operator')";
			
					$inRes=@mysql_query($IN_recode);
					if ($inRes>0){
					    $Log.="发票信息保存成功<br>";
						
				    }else{
					        $Log.="<div class=redB>发票信息保存失败$IN_recode</div><br>";
				    }
		         }else{
			         $upSql="UPDATE $DataIn.nonbom6_invoice SET InvoiceNo='$InvoiceNo',InvoiceAmount='$InvoiceAmount',
			         InvoiceDate='$InvoiceDate',Remark='$Remark' $InvoiceFileSTR WHERE Id='$InvoiceId' ";
			         $upResult=mysql_query($upSql);
			         
			         if ($upResult ){
			             $Log.="$InvoiceId 发票文件重新关联采购单($cgMid)成功 <br>";  
		             }else{
			             $Log.="<div class=redB>$InvoiceId 发票文件重新关联采购单($cgMid)失败</div><br>";    
		             }
                }
          }
       }   
       break;   
     case 'upFiles'://合同上传
             $Log_Funtion="合同上传"; 
             $Id=$Mid;		
             $upDataSheet="$DataIn.nonbom6_cgmain";
             
			 if($Attached!=""){//有上传文件
				  $FileType=substr("$Attached_name", -4, 4);
				  $OldFile=$Attached;	
				 $FilePath="../download/nonbom_contract/";	
				if(!file_exists($FilePath)){
					makedir($FilePath);
				}
				$PreFileName=$Id.$FileType;
				$Attached=UploadFiles($OldFile,$PreFileName,$FilePath);
				if ($Attached!=""){ //上传成功
					$SetStr=" Attached=1";
			        include "../model/subprogram/updated_model_3a.php";
				}
			}
        break;
	      
	default://更新已下单的申购记录：数量、单价、供应商、备注	OK
	   $checkOldSql=mysql_query("SELECT * FROM $upDataSheet WHERE Id='$Id'",$link_id);
	   if($checkOldRow=mysql_fetch_array($checkOldSql)){
		$Mid=$checkOldRow["Mid"];
		$CompanyId=$checkOldRow["CompanyId"];
		$Price=$checkOldRow["Price"];
		$Qty=$checkOldRow["Qty"];
		$Estate="";
		if($CompanyId!=$newCompanyId || $Price!=$newPrice || $Qty!=$newQty){
			$Estate=",A.Estate=2";
			}
		$Remark=FormatSTR($Remark);
		//数量变化：未收款的情况下
		$changeQty=$Qty-$newQty;//如果是正数，则为减少采购数量，采购库存减少，并且需要采购库存的数量>=减少的数量
		$updateSQL = "UPDATE $upDataSheet A
		LEFT JOIN $DataPublic.nonbom5_goodsstock B ON A.GoodsId=B.GoodsId
		LEFT JOIN $DataIn.nonbom12_cwsheet C ON C.cgId=A.Id
		SET A.fromMid='$fromMid',A.CompanyId='$newCompanyId',A.Price='$newPrice',A.Qty='$newQty',A.AddTaxValue='$AddTaxValue',
        A.Remark='$Remark',A.Date='$DateTime',A.Operator='$Operator',A.Locks='0',B.oStockQty=B.oStockQty-'$changeQty'  $Estate
		WHERE A.Id='$Id' AND A.Locks='1' AND B.oStockQty>='$changeQty' AND C.cgId IS NULL";		
		$updateResult = mysql_query($updateSQL);
		if($updateResult && mysql_affected_rows()>0){
			$Log=$Log_Item.$Log_Funtion."成功.<br>";
			
			$sql = "UPDATE $DataIn.nonbom6_cgmain SET CompanyId='$newCompanyId' WHERE Id=$Mid";
			//echo "$sql:<br>";
			$result = mysql_query($sql,$link_id);
			$Log.="申购编号Id: $Mid 主采购单供应商更新成功.<br>";
			//如果数量有变注，要看收货数量是否已经全收，是则要更新入库状态
			if($newQty!=$Qty){
				// 3 入库状态:有入库则2，最后才统一更新状态?
				$uprkSign="UPDATE $DataIn.nonbom6_cgsheet SET rkSign=(CASE 
					WHEN Qty>(
									SELECT SUM( Qty ) AS Qty FROM $DataIn.nonbom7_insheet WHERE cgId = '$Id'
								 ) THEN 2
						ELSE 0 END) WHERE Id='$Id'";
				$upRkAction=mysql_query($uprkSign);	
				if($upRkAction){
					$Log.="申购编号 $Id 的入库标记更新成功.<br>";
					}
				else{
					$Log.="<div class='redB'>申购编号 $Id 的入库标记更新失败. $uprkSign </div><br>";
					}
				}
			}
		else{
			$Log="<div class='redB'>".$Log_Item.$Log_Funtion."失败.$updateSQL</div>";
			$OperationResult="N";
			}
		}
	else{
		$Log="<div class='redB'>没有找到ID为 $Id 的记录.</div>";
		$OperationResult="N";
		}	
	break;
	}
if($fromWebPage==""){
	$fromWebPage=$funFrom."_read";
	}
$ALType="From=$From&Estate=$Estate&chooseDate=$chooseDate&CompanyId=$CompanyId";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>