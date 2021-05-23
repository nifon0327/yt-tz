<?php
	
	function shBack($Id, $DataIn, $link_id)
	{
		$backResult = "";
		$updateSQL = "UPDATE $DataIn.gys_shsheet Q SET Estate=1,Locks=1 WHERE Q.Id='$Id'";
		$updateResult = mysql_query($updateSQL,$link_id);
		if ($updateResult && mysql_affected_rows()>0)
		{
			$backResult = "记录退回";
        }
        
        return $backResult;
	}
	
	function shRk($Id, $DataIn, $link_id, $Login_P_Number)
	{
		$OperResult="配件入库失败";
		$result = "N";
		$SendSql=mysql_query("SELECT S.SendSign,M.CompanyId FROM $DataIn.gys_shsheet S 
                          	  LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
                          	  where  S.Id='$Id'",$link_id); 
                          
        $SendSign=mysql_result($SendSql,0,"SendSign");// SendSign: 0送货，1补货, 2备品 
        $SendCompanyId=mysql_result($SendSql,0,"CompanyId");

        $Date=date("Y-m-d");
        $DateTime=date("Y-m-d H:i:s");
        
        switch ($SendSign)
        {
        	case 1: 
        	{   //补货
                //检查该送货单是否存在，如果已存在，提取ID，如果不存在则新增主入库单，需锁定
                /*
                $checkBillSql=mysql_query("SELECT Id,BillNumber,CompanyId FROM $DataIn.ck3_bcmain WHERE BillNumber=(SELECT M.BillNumber FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC) ORDER BY BillNumber DESC LIMIT 1",$link_id);
                */
            $checkBillSql=mysql_query("SELECT Id,BillNumber,CompanyId FROM $DataIn.ck3_bcmain  WHERE  CompanyId='$SendCompanyId' AND Date='$Date' AND  BillNumber=(SELECT M.BillNumber FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC) ORDER BY BillNumber DESC LIMIT 1",$link_id);				
            if($checkBillRow=mysql_fetch_array($checkBillSql))
            {//追加
            	$Mid=$checkBillRow["Id"];
            	$BillNumber=$checkBillRow["BillNumber"];
            	$CompanyId=$checkBillRow["CompanyId"];
            }
            else
            {//新增

             	$checkBillSql2=mysql_query("SELECT M.BillNumber,M.CompanyId FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC LIMIT 1",$link_id);
                if($checkBillRow2=mysql_fetch_array($checkBillSql2))
                {
                	$BillNumber=$checkBillRow2["BillNumber"];
                	$CompanyId=$checkBillRow2["CompanyId"];
                	$inRecode="INSERT INTO $DataIn.ck3_bcmain (Id,BillNumber,CompanyId,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','$DateTime','$Login_P_Number')";
                    $inAction=@mysql_query($inRecode);
                    $Mid=mysql_insert_id();
                }//end if($checkBillRow2=mysql_fetch_array($checkBillSql2))
            }//end if($checkBillRow=mysql_fetch_array($checkBillSql))

            if($Mid>0)
            {//记录入库   modify by zx 2011-01-16
		    if($DataIn=="ac"){
       		   $addRecodes="INSERT INTO $DataIn.ck3_bcsheet SELECT NULL,'$Mid',StuffId,Qty,'','0' ,'1','0','$Operator','$DateTime','$Operator','$DateTime','$Date','$Operator'
                                                 FROM $DataIn.gys_shsheet WHERE Id='$Id'  AND Estate=2";
            }else{
       			$addRecodes="INSERT INTO $DataIn.ck3_bcsheet SELECT NULL,'$Mid',StuffId,Qty,'','0' FROM $DataIn.gys_shsheet WHERE Id='$Id'  AND Estate=2";
                 }
       			$addAction=@mysql_query($addRecodes);
       			if($addAction)
       			{//入库成功
            		$upSH="UPDATE  $DataIn.gys_shsheet S LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId SET S.Estate=0,S.Locks=0,K.tStockQty=K.tStockQty+S.Qty WHERE 1 AND S.Id='$Id'";
            		$upSHAction=mysql_query($upSH);
                                //更新入库状态
                    $OperResult="（配件（补货）入库成功！\n";
                }
            }
        }			
        	break;
        	case 2:
        	{    //备品
                //检查该送货单是否存在，如果已存在，提取ID，如果不存在则新增主入库单，需锁定
                /*
                $checkBillSql=mysql_query("SELECT Id,BillNumber,CompanyId FROM $DataIn.ck11_bpmain WHERE BillNumber=(SELECT M.BillNumber FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC) ORDER BY BillNumber DESC LIMIT 1",$link_id);
                */
            $checkBillSql=mysql_query("SELECT Id,BillNumber,CompanyId FROM $DataIn.ck11_bpmain WHERE CompanyId='$SendCompanyId' AND Date='$Date' AND  BillNumber=(SELECT M.BillNumber FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC) ORDER BY BillNumber DESC LIMIT 1",$link_id);

            if($checkBillRow=mysql_fetch_array($checkBillSql))
            {//追加
            	$Mid=$checkBillRow["Id"];
                $BillNumber=$checkBillRow["BillNumber"];
                $CompanyId=$checkBillRow["CompanyId"];
            }
            else
            {//新增

            	$checkBillSql2=mysql_query("SELECT M.BillNumber,M.CompanyId FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC LIMIT 1",$link_id);
                if($checkBillRow2=mysql_fetch_array($checkBillSql2))
                {
                	$BillNumber=$checkBillRow2["BillNumber"];
                	$CompanyId=$checkBillRow2["CompanyId"];
                    $inRecode="INSERT INTO $DataIn.ck11_bpmain (Id,BillNumber,CompanyId,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','$DateTime','$Login_P_Number')";
                    $inAction=@mysql_query($inRecode);
                    $Mid=mysql_insert_id();
                }//end if($checkBillRow2=mysql_fetch_array($checkBillSql2))
             }//end if($checkBillRow=mysql_fetch_array($checkBillSql))

             if($Mid>0)
             {//记录入库   modify by zx 2011-01-16
if($DataIn=="ac"){
             	$addRecodes="INSERT INTO $DataIn.ck11_bpsheet SELECT NULL,'$Mid',StuffId,Qty,'','0','1','0','$Operator','$DateTime','$Operator','$DateTime','$Date','$Operator'
              FROM $DataIn.gys_shsheet WHERE Id='$Id' AND Estate=2";
}else{
             	$addRecodes="INSERT INTO $DataIn.ck11_bpsheet SELECT NULL,'$Mid',StuffId,Qty,'','0' FROM $DataIn.gys_shsheet WHERE Id='$Id' AND Estate=2";
}
                $addAction=@mysql_query($addRecodes);
                if($addAction)
                {//入库成功
                	$upSH="UPDATE  $DataIn.gys_shsheet S  SET S.Estate=0,S.Locks=0 WHERE 1 AND S.Id='$Id'";
                	$upSHAction=mysql_query($upSH);
                	$OperResult="（配件（备品）入库成功！\n";
                }
             }				
        }
        	break;
        	default:
        	{	//入库
                //检查该送货单是否存在，如果已存在，提取ID，如果不存在则新增主入库单，需锁定
                /*
                $checkBillSql=mysql_query("SELECT Id,BillNumber,CompanyId FROM $DataIn.ck1_rkmain WHERE BillNumber=(SELECT M.BillNumber FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC) ORDER BY BillNumber DESC LIMIT 1",$link_id);
                */
            $checkBillSql=mysql_query("SELECT Id,BillNumber,CompanyId FROM $DataIn.ck1_rkmain WHERE CompanyId='$SendCompanyId' AND Date='$Date' AND  BillNumber=(SELECT M.BillNumber FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC) ORDER BY BillNumber DESC LIMIT 1",$link_id);
            if($checkBillRow=mysql_fetch_array($checkBillSql))
            {//追加
            	$Mid=$checkBillRow["Id"];
            	$BillNumber=$checkBillRow["BillNumber"];
                $CompanyId=$checkBillRow["CompanyId"];
            }
            else
            {//新增

            	$checkBillSql2=mysql_query("SELECT M.BillNumber,M.CompanyId FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC LIMIT 1",$link_id);
                if($checkBillRow2=mysql_fetch_array($checkBillSql2))
                {
                	$BillNumber=$checkBillRow2["BillNumber"];
                	$CompanyId=$checkBillRow2["CompanyId"];
                	$inRecode="INSERT INTO $DataIn.ck1_rkmain (Id,BillNumber,CompanyId,BuyerId,Remark,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','','0','$Date','$Login_P_Number')";
                    $inAction=@mysql_query($inRecode);
                    $Mid=mysql_insert_id();
                }//end if($checkBillRow2=mysql_fetch_array($checkBillSql2))
            }//end if($checkBillRow=mysql_fetch_array($checkBillSql))

            if($Mid>0)
            {//记录入库   modify by zx 2011-01-16
           if($DataIn=="ac"){
            	$addRecodes="INSERT INTO $DataIn.ck1_rksheet SELECT NULL,'$Mid',StockId,StuffId,Qty,Id,'0' ,'1','0','$Operator','$DateTime','$Operator','$DateTime','$Date','$Operator'
                 FROM $DataIn.gys_shsheet WHERE Id='$Id' AND Estate=2";
               }else{
            	$addRecodes="INSERT INTO $DataIn.ck1_rksheet SELECT NULL,'$Mid',StockId,StuffId,Qty,Id,'0' 
                 FROM $DataIn.gys_shsheet WHERE Id='$Id' AND Estate=2";
                 }
                $addAction=@mysql_query($addRecodes);
                if($addAction)
                {//入库成功
                	$upSH="UPDATE  $DataIn.gys_shsheet S LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId SET S.Estate=0,S.Locks=0,K.tStockQty=K.tStockQty+S.Qty 
                		   WHERE 1 AND S.Id='$Id'";
                    $upSHAction=mysql_query($upSH);
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
						/*
						$inRecode="INSERT INTO $DataIn.cw1_fkoutsheet(Id, Mid, StockId, POrderId, StuffId, Qty, Price, OrderQty, StockQty, AddQty, FactualQty, CompanyId, BuyerId, Amount, Month, Estate, Locks)
						              VALUES(NULL,'0','$StockId','$POrderId','$StuffId','$Qty','$Price','$OrderQty','$StockQty','$AddQty','$FactualQty','$CompanyId','$BuyerId','$Amount','$Month','$Estate','1')";
						*/			  
						
                }
           }
      }
      break;
      }
      
      return $OperResult;
  }
	
	
	function autoPayment($Id, $DataIn, $link_id, $Log)
	{
		$Ids="";
		$AutoSign=3;
		$SQLResult=mysql_query(" SELECT G.Id,IFNULL(G.rkSign,-1) as rkSign,Y.StockId,Y.StuffId,M.CompanyId FROM $DataIn.gys_shsheet Y
								 LEFT JOIN $DataIn.gys_shmain M ON Y.Mid=M.Id 					
							     LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=Y.StockId
							     WHERE Y.Id='$Id' 
							   ",$link_id);		
		if($StockIdRow2=mysql_fetch_array($SQLResult))
		{
			$rkSign=$StockIdRow2["rkSign"];
			$StockId=$StockIdRow2["StockId"];
			if ( ($rkSign==0)  || ($StockId==-1) )
			{  //如果入完库的标志，且已补完货，或补完货，把已送完未请款的也自动请款
				$Ids=$StockIdRow2["Id"];
				$Month=date("Y-m");
				
				$CompanyId=$StockIdRow2["CompanyId"];
				if(($CompanyId == $SubCompanyId) || ($CompanyId == $SubCP_1))
				{
					return false;
				}
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
			        	$Log.="Id号在(".$Ids.")的".$TitleSTR."成功!\n";
			        } 
					else
					{ 
			        	$Log.="Id号在(".$Ids.")的".$TitleSTR."失败!\n";
						$OperationResult="N";
					}
		       }while($cgStockRow=mysql_fetch_array($cgStockResult));
		    }
		}	
		else{
		    $Log.="请款月份不能为空!\n";
			$OperationResult="N";
		}	
		
		$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
		$IN_res=@mysql_query($IN_recode);

	}
	
function isCustomerSupplier($gysShSheetId, $DataIn, $link_id)
{
	$isCustomerSupply = false;
	$stuffTypeSql = "Select B.Property From $DataIn.gys_shsheet A
					Left Join $DataIn.stuffproperty B On A.StuffId = B.StuffId
					Where A.Id = $gysShSheetId";
	$stuffTypeResult = mysql_query($stuffTypeSql, $link_id);
	$stuffTypeRow = mysql_fetch_assoc($stuffTypeResult);
	$stuffProperty = $stuffTypeRow["Property"];

	if($stuffproperty == "2")
	{
		$isCustomerSupply = true;
	}

	return $isCustomerSupply;
}

?>