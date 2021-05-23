<?php
//获得新的退货主单号
 function newThBillNumber($DataIn,$link_id){
          $DtateTemp=date("Y");
          $maxSql = mysql_query("SELECT MAX(A.BillNumber) AS Mid FROM(
				SELECT MAX(BillNumber) AS BillNumber FROM $DataIn.ck2_thmain WHERE BillNumber LIKE '$DtateTemp%'
				UNION ALL
				SELECT MAX(BillNumber) AS BillNumber FROM $DataIn.ck12_thmain WHERE BillNumber LIKE '$DtateTemp%' 
				)A",$link_id);
	     $BillNumberTemp=mysql_result($maxSql,0,"Mid");
	     if($BillNumberTemp>0){
		      $BillNumber=$BillNumberTemp+1;
		   }
	     else{
		    $BillNumber=$DtateTemp."00001";//默认
		 }
        return $BillNumber;
 }

//取得不良原因
function getQcBadRecordCause($Id,$DataIn,$link_id){
       $cause_Result=mysql_query("SELECT T.Cause,B.CauseId,B.Reason FROM $DataIn.qc_badrecordsheet B LEFT JOIN $DataIn.qc_causetype T ON T.Id=B.CauseId  AND T.Type=1 WHERE B.Mid='$Id'",$link_id);
      while ( $cause_row = mysql_fetch_array($cause_Result)){
                $CauseId=$cause_row["CauseId"];
                if ($CauseId=="-1"){
                    $Reason.=$Reason==""?$cause_row["Reason"] : " / " . $cause_row["Reason"];
                }else{
                    $Reason.=$Reason==""?$cause_row["Cause"] : " / " . $cause_row["Cause"];
                }
       }
       return $Reason;
}

//送货单整单退回,生成全部不良的品检记录
function shBack($Id,$Remark,$DataIn, $link_id,$Operator)
{
	$OperationResult = "N";
	$checkResult=mysql_query("SELECT Mid,StockId,StuffId,Qty FROM $DataIn.gys_shsheet WHERE  Id='$Id' AND Estate>0",$link_id);
     if ( $checkRow = mysql_fetch_array($checkResult)){
           $Mid=$checkRow["Mid"];
           $StockId=$checkRow["StockId"];
           $StuffId=$checkRow["StuffId"];
           $Qty=$checkRow["Qty"];

           $inRecode="INSERT INTO $DataIn.qc_badrecord (Id,shMid,StockId,StuffId,shQty,checkQty,Qty,AQL,Remark,Estate,Locks,Date,Operator)VALUES(NULL,'$Mid','$StockId','$StuffId','$Qty','0','$Qty','','','1','0',NOW(),'$Operator')";
	       $inAction=@mysql_query($inRecode);
	       $inMid=mysql_insert_id();
	       if ($inMid>0){
	            //生成明细表
               $insheetSql="INSERT INTO $DataIn.qc_badrecordsheet  (Id, Mid, CauseId, Qty, Reason,Picture) VALUES (NULL, '$inMid', '-1', '$Qty', '$Remark','0')";
               $insheetAction=@mysql_query($insheetSql,$link_id);
               if ($insheetAction){
                    $upSH="UPDATE  $DataIn.gys_shsheet S  SET S.Estate=0,S.Locks=0 WHERE 1 AND S.Id='$Id'";
                	$upSHAction=mysql_query($upSH);
	                $OperationResult = "Y";

	                 //更新相同送货单、相同配件Id的备品状态
                     $checkResult= mysql_query("SELECT StuffId,Mid,COUNT(*) AS Nums FROM $DataIn.gys_shsheet WHERE  Id='$Id' GROUP BY StuffId,Mid",$link_id);
                      while($checkRow = mysql_fetch_array($checkResult)) {
                                   $upNums=$checkRow["Nums"];
		                          if ($upNums==1){
				                             $upMid=$checkRow["Mid"];
				                             $upStuffId=$checkRow["StuffId"];
				                             $upSql2 = "UPDATE $DataIn.gys_shsheet SET Estate=0  WHERE  Mid='$upMid' AND StuffId='$upStuffId' AND Estate=2 AND SendSign=2";
							                 $upResult2 = mysql_query($upSql2,$link_id);
					               }
                        }
               }
	       }
     }
    return $OperationResult;
}

//送货单入库
function shRk($Id, $DataIn, $link_id, $Login_P_Number){

         $OperationResult = "N";
         $Date=date("Y-m-d");
         $DateTime=date("Y-m-d H:i:s");
         $Operator=$Login_P_Number;
         $SendSql=mysql_query("SELECT S.StockId,S.SendSign,S.Qty,M.CompanyId,M.BillNumber FROM $DataIn.gys_shsheet S 
                          	  LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
                          	  where  S.Id='$Id'",$link_id);
        $SendSign=mysql_result($SendSql,0,"SendSign");// SendSign: 0送货，1补货, 2备品
        $SendQty=mysql_result($SendSql,0,"Qty");//送货数量
        $BillNumber=mysql_result($SendSql,0,"BillNumber");
        $CompanyId=mysql_result($SendSql,0,"CompanyId");
        $StockId=mysql_result($SendSql,0,"StockId");

        //已登记合格数量
        $djQtyResult=mysql_query("SELECT SUM(C.Qty) AS Qty FROM $DataIn.gys_shsheet S 
                          	  LEFT JOIN $DataIn.qc_cjtj C ON C.Sid=S.Id AND C.StuffId=S.StuffId 
                          	  WHERE  S.Id='$Id'",$link_id);
        $Qty=mysql_result($djQtyResult,0,"Qty");

        if ($Qty>$SendQty) $Qty=$SendQty;
        if ($Qty==0) return $OperationResult;

        switch($SendSign){
	        case 1:$TableName="$DataIn.ck3_bcmain";break;
	        case 2:$TableName="$DataIn.ck11_bpmain";break;
	        default:$TableName="$DataIn.ck1_rkmain";break;
        }
        $Mid="";
        //检查该送货单是否存在，如果已存在，提取ID，如果不存在则新增主入库单，需锁定
        $checkBillSql=mysql_query("SELECT Id FROM $TableName  WHERE  CompanyId='$CompanyId' AND Date='$Date' AND  BillNumber='$BillNumber'  LIMIT 1",$link_id);
        if($checkBillRow=mysql_fetch_array($checkBillSql)){//追加
            	$Mid=$checkBillRow["Id"];
         }

        switch ($SendSign){
         case 1:
          {   //补货
             if($Mid==""){
                	$inRecode="INSERT INTO $DataIn.ck3_bcmain (Id,BillNumber,CompanyId,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','$DateTime','$Login_P_Number')";
                    $inAction=@mysql_query($inRecode);
                    $Mid=mysql_insert_id();
             }

            if($Mid>0)
            {//记录入库   modify by zx 2011-01-16
               if($DataIn=="ac"){
       			      $addRecodes="INSERT INTO $DataIn.ck3_bcsheet SELECT NULL,'$Mid',StuffId,'$Qty','','0' ,'1','0','$Operator','$DateTime','$Operator','$DateTime','$DateTime','$Operator'
                        FROM $DataIn.gys_shsheet WHERE Id='$Id'  AND Estate=2";
               }else{
       			    $addRecodes="INSERT INTO $DataIn.ck3_bcsheet SELECT NULL,'$Mid',StuffId,'$Qty','','0' FROM $DataIn.gys_shsheet WHERE Id='$Id'  AND Estate=2";
                     }

       			$addAction=@mysql_query($addRecodes);
       			if($addAction)
       			{//入库成功
            		$upSH="UPDATE  $DataIn.gys_shsheet S SET S.Estate=0,S.Locks=0 WHERE 1 AND S.Id='$Id'";
            		$upSHAction=mysql_query($upSH);
                                //更新入库状态
                    $OperResult="配件（补货）入库成功！\n";
                    $OperationResult="Y";
                }
            }
        }
        	break;
        	case 2:
        	{    //备品
               if($Mid==""){
                    $inRecode="INSERT INTO $DataIn.ck11_bpmain (Id,BillNumber,CompanyId,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','$DateTime','$Login_P_Number')";
                    $inAction=@mysql_query($inRecode);
                    $Mid=mysql_insert_id();
               }

             if($Mid>0){//记录入库   modify by zx 2011-01-16
               if($DataIn=="ac"){
                  	$addRecodes="INSERT INTO $DataIn.ck11_bpsheet SELECT NULL,'$Mid',StuffId,'$Qty','','0' ,'1','0','$Operator','$DateTime','$Operator','$DateTime','$DateTime','$Operator'
                         FROM $DataIn.gys_shsheet WHERE Id='$Id' AND Estate=2";
                 }else{
                    $addRecodes="INSERT INTO $DataIn.ck11_bpsheet SELECT NULL,'$Mid',StuffId,'$Qty','','0' FROM $DataIn.gys_shsheet WHERE Id='$Id' AND Estate=2";
                      }

                $addAction=@mysql_query($addRecodes);
                if($addAction)
                {//入库成功
                	$upSH="UPDATE  $DataIn.gys_shsheet S  SET S.Estate=0,S.Locks=0 WHERE 1 AND S.Id='$Id'";
                	$upSHAction=mysql_query($upSH);
                	$OperResult="配件（备品）入库成功！\n";
                	$OperationResult="Y";
                }
             }
          }
        	break;
        	default:
        	{	//入库
               if($Mid==""){
                	$inRecode="INSERT INTO $DataIn.ck1_rkmain (Id,BillNumber,CompanyId,BuyerId,Remark,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','','0','$Date','$Login_P_Number')";
                    $inAction=@mysql_query($inRecode);
                    $Mid=mysql_insert_id();
              }

            if($Mid>0)
            {//记录入库   modify by zx 2011-01-16
               if($DataIn=="ac"){
            	    $addRecodes="INSERT INTO $DataIn.ck1_rksheet SELECT NULL,'$Mid',StockId,StuffId,'$Qty',Id,'0','1','0','$Operator','$DateTime','$Operator','$DateTime','$DateTime','$Operator'
                          FROM $DataIn.gys_shsheet WHERE Id='$Id' AND Estate=2";
                 }else{
            	$addRecodes="INSERT INTO $DataIn.ck1_rksheet SELECT NULL,'$Mid',StockId,StuffId,'$Qty',Id,'0' FROM $DataIn.gys_shsheet WHERE Id='$Id' AND Estate=2";
                           }

                $addAction=@mysql_query($addRecodes);
                if($addAction)
                {//入库成功
                	$upSH="UPDATE  $DataIn.gys_shsheet S  SET S.Estate=0,S.Locks=0 WHERE 1 AND S.Id='$Id'";
                    $upSHAction=mysql_query($upSH);
                    if ($upSHAction && $StockId>0){
	                       updateParentStock($StockId, $Mid,$DataIn, $link_id, $Login_P_Number);
                    }
                    //更新入库状态
                    $uprkSign="UPDATE $DataIn.cg1_stocksheet G 
                               LEFT JOIN $DataIn.gys_shsheet S ON S.StockId=G.StockId
                               SET G.rkSign=(CASE 
                                                WHEN 
                                                G.FactualQty+G.AddQty>(
                                                        SELECT IFNULL(SUM(R.Qty),0) AS Qty FROM $DataIn.ck1_rksheet R 
                                                        LEFT JOIN $DataIn.gys_shsheet SS ON R.StockId=SS.StockId WHERE SS.Id='$Id'
                                                ) THEN 2
                                        ELSE 0 END) WHERE S.Id='$Id'";
                        $upRkAction=mysql_query($uprkSign);
                        $OperResult="配件入库成功！\n";
                        $OperationResult="Y";
                }
           }
      }
      break;
 }
  return $OperationResult;
}

//采购单请款
function autoPayment($Id, $DataIn, $link_id, $Log,$Sign=0)
{
		$Ids="";
		$AutoSign=3; $OperationResult="N";
		$checkProperty=mysql_query(" SELECT T.StuffId FROM $DataIn.cg1_stocksheet G 
			                     INNER JOIN  $DataIn.stuffproperty T ON T.StuffId=G.StuffId  				
							     WHERE G.StockId='$Id' AND T.Property='2'  ",$link_id);
		if (mysql_num_rows($checkProperty)>0){
			return  false;
		}

		if ($Sign==1){//来自子母配件请款
			$SQLResult=mysql_query(" SELECT G.Id,IFNULL(G.rkSign,1) as rkSign,G.StockId,G.StuffId,M.CompanyId 
			                    FROM $DataIn.cg1_stocksheet G 
								 LEFT JOIN $DataIn.cg1_stockmain M ON G.Mid=M.Id 					
							     WHERE G.StockId='$Id' AND (G.AddQty+G.FactualQty)=(SELECT SUM(Qty) FROM $DataIn.ck1_rksheet WHERE StockId='$Id') ",$link_id);
		}
		else{
		   $SQLResult=mysql_query(" SELECT G.Id,IFNULL(G.rkSign,-1) as rkSign,Y.StockId,Y.StuffId,M.CompanyId FROM $DataIn.gys_shsheet Y
								 LEFT JOIN $DataIn.gys_shmain M ON Y.Mid=M.Id 					
							     LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=Y.StockId
							     WHERE Y.Id='$Id' 
							   ",$link_id);
		}

		if($StockIdRow2=mysql_fetch_array($SQLResult))
		{
			$rkSign=$StockIdRow2["rkSign"];
			$StockId=$StockIdRow2["StockId"];
			$CompanyId=$StockIdRow2["CompanyId"];
			if($CompanyId ==2270)//研砼皮套不自动请款
			{
				return false;
			}

			if ( ($rkSign==0)  || ($StockId==-1) )
			{  //如果入完库的标志，且已补完货，或补完货，把已送完未请款的也自动请款
				$Ids=$StockIdRow2["Id"];
				$Month=date("Y-m");


				$StuffId=$StockIdRow2["StuffId"];

				/*
				//是否最后一条记录，如果是，则怕它有退货，不能自动请款
				$LastSql=mysql_query("SELECT C.Id,C.StockId FROM $DataIn.cg1_stocksheet C WHERE C.CompanyId = '$CompanyId' AND C.StuffId = '$StuffId' ORDER BY C.ID DESC LIMIT 1  ",$link_id);
				$LastStockId=mysql_result($LastSql,0,"StockId");
				$LastId=mysql_result($LastSql,0,"Id");
				if($StockId==$LastStockId){
					return false;
				}
				*/

				//退货的总数量 add by zx 2013-11-6
				$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
											   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
											   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$thQty=mysql_result($thSql,0,"thQty");
				$thQty=$thQty==""?0:$thQty;
				//补货的数量 add by zx 2013-11-6
				$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
											   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
											   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
				$bcQty=mysql_result($bcSql,0,"bcQty");
				$bcQty=$bcQty==""?0:$bcQty;
				if ($thQty!=$bcQty)
				{  //未补完，不给自动请款,
					return false;
				}
				else
				{
					if ($StockId==-1)
					{  //如果是补完货，则扫描未请款,实现倒推法，先取最后一个已请款的stockId的Id,Id大于它的id，才扫，以免扫全表，但可能会有先请款的，只能手动请
						$Ids="";
						$ScanSQL="SELECT C.Id FROM  $DataIn.cg1_stocksheet  C
						 		  LEFT JOIN $DataIn.cw1_fkoutsheet D ON  D.StockId=C.StockId
						 		  where D.Id is null AND C.CompanyId = '$CompanyId' AND C.StuffId = '$StuffId' AND C.rkSign=0 
						 		  AND C.Id>(
                                  SELECT G.ID FROM $DataIn.cw1_fkoutsheet M
						          LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=M.StockId
						          where   M.CompanyId = '$CompanyId' AND M.StuffId = '$StuffId' ORDER BY M.ID DESC LIMIT 1)";
						$checkSql=mysql_query($ScanSQL,$link_id);
						while($checkRow=mysql_fetch_array($checkSql))
						{
							$Id=$checkRow["Id"];
							if($Id!=$LastId){//不是最后一单，可以请款
								$Ids=$Ids==""?$Id:($Ids.",".$Id);
							}
						}
						if($Ids=="")
						{
							return false;
						}

					} //if ($StockId==-1) {
				}

			}  //if ( ($rkSign==0)  || ($rkSign==-1) ){
			else
			{
				return false;
			}
		}  //if($StockIdRow2=mysql_fetch_array($SQLResult)){
		else
		{
			return false;
		}

		//请款动作
		$Log_Funtion="采购请款";
		//将记录复制到请款明细表(客户退款类配件只按订单数计算)

		//echo "break to here";
		if(strlen($Month)==7)
		{
	    	$cgStockResult=mysql_query("SELECT G.StockId,G.POrderId,G.StuffId,G.Price,G.OrderQty,
										G.StockQty,G.AddQty,G.FactualQty,G.CompanyId,G.BuyerId,S.TypeId,A.GysPayMode,S.Price as NowPrice
										FROM $DataIn.cg1_stocksheet G
										LEFT JOIN $DataIn.stuffdata S ON S.StuffId=G.StuffId
										LEFT JOIN  $DataIn.trade_object A ON A.CompanyId=G.CompanyId 
										WHERE G.Id IN ($Ids)",$link_id);

			if($cgStockRow=mysql_fetch_array($cgStockResult))
			{
		    	do
		    	{
		        	$StockId=$cgStockRow["StockId"];
					$POrderId=$cgStockRow["POrderId"];
					$StuffId=$cgStockRow["StuffId"];
					$Price=$cgStockRow["Price"];
					$OrderQty=$cgStockRow["OrderQty"];
					$StockQty=$cgStockRow["StockQty"];
					$AddQty=$cgStockRow["AddQty"];
					$FactualQty=$cgStockRow["FactualQty"];
					$CompanyId=$cgStockRow["CompanyId"];
					$BuyerId=$cgStockRow["BuyerId"];
					$TypeId=$cgStockRow["TypeId"];

				    if($CompanyId ==2270)//研砼皮套不自动请款
				    {
						continue;
					}
					//Begin  zx 2013-11-6
					$GysPayMode=$cgStockRow["GysPayMode"];  //add by zx 2013-11-6
					$NowPrice=$cgStockRow["NowPrice"];  ////add by zx 2013-11-6

					if($AutoSign==1)
					{ ////1表示手动请款，3表示自动请款, 自动请款，计算在前面了
						//退货的总数量 add by zx 2013-11-6
						$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
												   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
												   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
						$thQty=mysql_result($thSql,0,"thQty");
						$thQty=$thQty==""?0:$thQty;
						//补货的数量 add by zx 2013-11-6
						$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
												   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
												   WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
						$bcQty=mysql_result($bcSql,0,"bcQty");
						$bcQty=$bcQty==""?0:$bcQty;
					}

					$Estate=2;
					if(($thQty==$bcQty) && ($GysPayMode!=1) && ($NowPrice==$Price) )
					{  //无未补货，非现金，价钱相等则不用审核
				    	if ($AutoSign==1)
				    	{ ////1表示手动请款审核,2.表示手动请款自动通过,3表示自动请款审核,4表示自动请款自动通过
							$AutoSign=2;
						}
						if ($AutoSign==3)
						{ ////1表示手动请款审核,2.表示手动请款自动通过,3表示自动请款审核,4表示自动请款自动通过
							$AutoSign=4;
						}
						$Estate=3;
					}

					//End

					if($TypeId=='9104')
					{//配件为客户退款的，金额为订单数*单价。否则为实际购买数*单价
				    	$Qty=$OrderQty;
						$Amount=$Qty*$Price;
					}
					else
					{
				    	$Qty=$FactualQty+$AddQty;
						$Amount=$Qty*$Price;
				    }

					$inRecode="INSERT INTO $DataIn.cw1_fkoutsheet(Id, Mid, StockId, POrderId, StuffId, Qty, Price, OrderQty, StockQty, AddQty, FactualQty, CompanyId, BuyerId, Amount, Month,AutoSign,Estate, Locks)VALUES(NULL,'0','$StockId','$POrderId','$StuffId','$Qty','$Price','$OrderQty','$StockQty','$AddQty','$FactualQty','$CompanyId','$BuyerId','$Amount','$Month','$AutoSign','$Estate','1')";
					//echo $inRecode."\n";
		        	$inAction=@mysql_query($inRecode);
					if($inAction)
					{
					    $OperationResult="Y";
			        	$Log.="Id号在(".$Ids.")的".$TitleSTR."成功!\n";
			        }
					else
					{
			        	$Log.="Id号在(".$Ids.")的".$TitleSTR."失败!\n";

					}
		       }while($cgStockRow=mysql_fetch_array($cgStockResult));
		    }
		}
		else{
		    $Log.="请款月份不能为空!\n";
			$OperationResult="N";
		}
         return $OperationResult;
}

function isCustomerSupplier($gysShSheetId, $DataIn, $link_id)
{
	$isCustomerSupply = false;
	$stuffTypeSql = "Select B.Property From $DataIn.gys_shsheet A
					Left Join $DataIn.stuffproperty B On A.StuffId = B.StuffId
					Where A.Id = '$gysShSheetId' ";
	$stuffTypeResult = mysql_query($stuffTypeSql, $link_id);
	$stuffTypeRow = mysql_fetch_assoc($stuffTypeResult);
	$stuffProperty = $stuffTypeRow["Property"];

	if($stuffproperty == "2")
	{
		$isCustomerSupply = true;
	}

	return $isCustomerSupply;
}

function updateParentStock($StockId, $Mid,$DataIn, $link_id, $Login_P_Number){
	   $checkResult=mysql_query("SELECT mStockId,mStuffId FROM $DataIn.cg1_stuffcombox WHERE  StockId='$StockId' LIMIT 1",$link_id);
	   if($checkRow=mysql_fetch_array($checkResult)){
		      $mStockId=$checkRow["mStockId"];
		      $mStuffId=$checkRow["mStuffId"];

		      $checkRkQty=mysql_fetch_array(mysql_query("SELECT ROUND(MIN(A.Qty/A.Relation)) AS Qty FROM  (
					        SELECT  S.StockId,SUM(IFNULL(S.Qty,0)) AS Qty,G.Relation 
					        FROM $DataIn.cg1_stuffcombox G 
							LEFT JOIN $DataIn.ck1_rksheet S ON S.StockId=G.StockId AND S.StuffId=G.StuffId 
							WHERE G.mStockId='$mStockId' GROUP BY G.StockId
				)A",$link_id));
				$Qty=$checkRkQty["Qty"]==""?0:$checkRkQty["Qty"];

		        $rkId=0;
			    $rkResult=mysql_query("SELECT Id FROM $DataIn.ck1_rksheet WHERE StockId='$mStockId' LIMIT 1",$link_id);
			    if($rkRow=mysql_fetch_array($rkResult)){
			           $rkId=$rkRow["Id"];
			           $upSql="UPDATE  $DataIn.ck1_rksheet  SET Qty=$Qty   WHERE Id='$rkId'";
                       $upAction=mysql_query($upSql,$link_id);
			      }
			      else{
			          if ($Qty>0){
					      	 $addRecodes="INSERT INTO $DataIn.ck1_rksheet (Id,Mid,StockId,StuffId,Qty,gys_Id,Locks) VALUES(NULL,'$Mid','$mStockId','$mStuffId','$Qty',NULL,'0')";
	                         $addAction=@mysql_query($addRecodes);
	                         $rkId=mysql_insert_id();
                         }
			      }

			     if ($rkId>0){
				      //更新入库状态
                      $uprkSign="UPDATE $DataIn.cg1_stocksheet G 
                               SET G.rkSign=(CASE 
                                                WHEN 
                                                G.FactualQty+G.AddQty>(
                                                        SELECT IFNULL(SUM(R.Qty),0) AS Qty FROM $DataIn.ck1_rksheet R WHERE R.StockId='$mStockId '
                                                ) THEN 2
                                        ELSE 0 END) WHERE G.StockId='$mStockId'";
                        $upRkAction=mysql_query($uprkSign);

                        if(!isCustomerSupplier($rkId, $DataIn, $link_id)){
                            autoPayment($mStockId, $DataIn, $link_id, "",1);//自动请款
                         }
			     }
			    /*
		      $checkStockQty=mysql_query("SELECT Min(ROUND(K.tStockQty/S.Relation)) AS tStockQty FROM $DataIn.cg1_stuffcombox S
																	LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId
																	WHERE S.mStockId='$mStockId' AND S.mStuffId='$mStuffId'",$link_id);
			 if($StockQtyRow=mysql_fetch_array($checkStockQty)){
			      $tStockQty=$StockQtyRow["tStockQty"];

			     // $upSH="UPDATE  $DataIn.ck9_stocksheet  SET tStockQty=$tStockQty   WHERE StuffId='$mStuffId'";
                 // $upSHAction=mysql_query($upSH,$link_id);
			 }
			 */
	   }
}

?>