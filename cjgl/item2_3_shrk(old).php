<?php  

$myResult=$myPDO->query("CALL proc_ck1_rksheet_save('$Id',$Operator);");
$myRow = $myResult->fetch(PDO::FETCH_ASSOC); 
$myResult=null;$myRow=null; 
$OperResult = $myRow['OperationResult'];



//审核通过，记录入库	
/*$OperResult="配件入库失败！";
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");


$SendSql=mysql_query("SELECT S.SendSign,M.CompanyId 
                      FROM $DataIn.gys_shsheet S 
                      LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid
                      WHERE S.Id='$Id'",$link_id);  

$SendSign=mysql_result($SendSql,0,"SendSign");// SendSign: 0送货，1补货, 2备品 
$SendCompanyId=mysql_result($SendSql,0,"CompanyId");

switch ($SendSign){
        case 1:    //补货
                //检查该送货单是否存在，如果已存在，提取ID，如果不存在则新增主入库单，需锁定
                $checkBillSql=mysql_query("SELECT Id,BillNumber,CompanyId 
                FROM $DataIn.ck3_bcmain  WHERE  CompanyId='$SendCompanyId' AND Date='$Date' AND  BillNumber=(SELECT M.BillNumber FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC) ORDER BY BillNumber DESC LIMIT 1",$link_id);				
                if($checkBillRow=mysql_fetch_array($checkBillSql)){//追加
                        $Mid=$checkBillRow["Id"];
                        $BillNumber=$checkBillRow["BillNumber"];
                        $CompanyId=$checkBillRow["CompanyId"];
                        }
                else{//新增

                        $checkBillSql2=mysql_query("SELECT M.BillNumber,M.CompanyId 
                        FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid 
                        WHERE S.Id='$Id' ORDER BY S.Id DESC LIMIT 1",$link_id);
                        if($checkBillRow2=mysql_fetch_array($checkBillSql2)){
                                $BillNumber=$checkBillRow2["BillNumber"];
                                $CompanyId=$checkBillRow2["CompanyId"];
                                $inRecode="INSERT INTO $DataIn.ck3_bcmain  
                                (Id,BillNumber,CompanyId,Locks,Date,Operator) VALUES 
                                (NULL,'$BillNumber','$CompanyId','0','$DateTime','$Login_P_Number')";
                                $inAction=@mysql_query($inRecode);
                                $Mid=mysql_insert_id();
                                }//end if($checkBillRow2=mysql_fetch_array($checkBillSql2))
                        }//end if($checkBillRow=mysql_fetch_array($checkBillSql))

                if($Mid>0){
                        $addRecodes="INSERT INTO $DataIn.ck3_bcsheet SELECT NULL,'$Mid',StuffId,Qty,'','0' FROM $DataIn.gys_shsheet WHERE Id='$Id'  AND Estate=2";
                        $addAction=@mysql_query($addRecodes);
                        if($addAction){//入库成功
                                $upSH="UPDATE  $DataIn.gys_shsheet S 
                                LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
                                SET S.Estate=0,S.Locks=0,K.tStockQty=K.tStockQty+S.Qty 
                                WHERE 1 AND S.Id='$Id'";
                                $upSHAction=mysql_query($upSH);
                                //更新入库状态
                               $OperResult="（配件（补货）入库成功！";
                         }
                 }			

                break;
        case 2:    //备品
                //检查该送货单是否存在，如果已存在，提取ID，如果不存在则新增主入库单，需锁定

                $checkBillSql=mysql_query("SELECT Id,BillNumber,CompanyId 
                FROM $DataIn.ck11_bpmain WHERE CompanyId='$SendCompanyId' AND Date='$Date' 
                AND  BillNumber=(SELECT M.BillNumber FROM $DataIn.gys_shsheet S 
                                LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid 
                                WHERE S.Id='$Id' ORDER BY S.Id DESC) 
                                ORDER BY BillNumber DESC LIMIT 1",$link_id);

                if($checkBillRow=mysql_fetch_array($checkBillSql)){//追加
                        $Mid=$checkBillRow["Id"];
                        $BillNumber=$checkBillRow["BillNumber"];
                        $CompanyId=$checkBillRow["CompanyId"];
                        }
                else{//新增

                        $checkBillSql2=mysql_query("SELECT M.BillNumber,M.CompanyId FROM $DataIn.gys_shsheet S LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' ORDER BY S.Id DESC LIMIT 1",$link_id);
                        if($checkBillRow2=mysql_fetch_array($checkBillSql2)){
                                $BillNumber=$checkBillRow2["BillNumber"];
                                $CompanyId=$checkBillRow2["CompanyId"];
                                $inRecode="INSERT INTO $DataIn.ck11_bpmain (Id,BillNumber,CompanyId,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','0','$DateTime','$Login_P_Number')";
                                $inAction=@mysql_query($inRecode);
                                $Mid=mysql_insert_id();
                                }//end if($checkBillRow2=mysql_fetch_array($checkBillSql2))
                        }//end if($checkBillRow=mysql_fetch_array($checkBillSql))

                if($Mid>0){//记录入库   modify by zx 2011-01-16
                        $addRecodes="INSERT INTO $DataIn.ck11_bpsheet SELECT NULL,'$Mid',StuffId,Qty,'','0' FROM $DataIn.gys_shsheet WHERE Id='$Id' AND Estate=2";
                        $addAction=@mysql_query($addRecodes);
                        if($addAction){//入库成功
                                $upSH="UPDATE  $DataIn.gys_shsheet S  SET S.Estate=0,S.Locks=0 WHERE 1 AND S.Id='$Id'";
                                $upSHAction=mysql_query($upSH);
                                $OperResult="（配件（备品）入库成功！";
                        }

                        }				

            break;
        default:	//入库
                //检查该送货单是否存在，如果已存在，提取ID，如果不存在则新增主入库单，需锁定
                $checkBillSql=mysql_query("SELECT Id,BillNumber,CompanyId 
                FROM $DataIn.ck1_rkmain WHERE CompanyId='$SendCompanyId' AND Date='$Date' 
                AND  BillNumber=(SELECT M.BillNumber FROM $DataIn.gys_shsheet S 
                      LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid 
                      WHERE S.Id='$Id' ORDER BY S.Id DESC) 
                      ORDER BY BillNumber DESC LIMIT 1",$link_id);
                if($checkBillRow=mysql_fetch_array($checkBillSql)){//追加
	                
                        $Mid=$checkBillRow["Id"];
                        $BillNumber=$checkBillRow["BillNumber"];
                        $CompanyId=$checkBillRow["CompanyId"];
                 
                        }
                else{//新增

                        $checkBillSql2=mysql_query("SELECT M.BillNumber,M.CompanyId 
                        FROM $DataIn.gys_shsheet S 
                        LEFT JOIN $DataIn.gys_shmain M ON M.Id=S.Mid WHERE S.Id='$Id' 
                        ORDER BY S.Id DESC LIMIT 1",$link_id);
                        if($checkBillRow2=mysql_fetch_array($checkBillSql2)){
                                $BillNumber=$checkBillRow2["BillNumber"];
                                $CompanyId=$checkBillRow2["CompanyId"];
                                $inRecode="INSERT INTO $DataIn.ck1_rkmain (Id,BillNumber,CompanyId,Remark,Locks,Date,Operator) VALUES (NULL,'$BillNumber','$CompanyId','','0','$Date','$Login_P_Number')";
                                $inAction=@mysql_query($inRecode);
                                $Mid=mysql_insert_id();
                                }//end if($checkBillRow2=mysql_fetch_array($checkBillSql2))
                        }//end if($checkBillRow=mysql_fetch_array($checkBillSql))

                if($Mid>0){//记录入库   modify by zx 2011-01-16
                      $Price = 0 ; //入库时要计算价格
                      
                      $addRecodes="INSERT INTO $DataIn.ck1_rksheet 
                      SELECT NULL,'$Mid',sPOrderId,StockId,StuffId,'$Price',Qty,'0','1',Id,'1',
                      '0','1','0','$Login_P_Number','$Date','$Login_P_Number','$Date',
                      '$Date','$Login_P_Number' FROM $DataIn.gys_shsheet 
                      WHERE Id='$Id' AND Estate=2";
                      $addAction=@mysql_query($addRecodes);
                      if($addAction){//入库成功
                          
                              $upSH="UPDATE  $DataIn.gys_shsheet S  SET S.Estate=0,S.Locks=0 WHERE S.Id='$Id'";
                              $upSHAction=mysql_query($upSH);
                               //更新入库状态
                              $uprkSign="UPDATE $DataIn.cg1_stocksheet G 
                                         LEFT JOIN $DataIn.gys_shsheet S ON S.StockId=G.StockId
                                         SET G.rkSign=(CASE 
                                                WHEN 
                                                G.FactualQty+G.AddQty>(
                                                        SELECT IFNULL(SUM(R.Qty),0) AS Qty 
                                                        FROM $DataIn.ck1_rksheet R 
                                                        LEFT JOIN $DataIn.gys_shsheet SS ON R.StockId=SS.StockId WHERE SS.Id='$Id'
                                                ) THEN 2
                                        ELSE 0 END) WHERE S.Id='$Id'";
                                $upRkAction=mysql_query($uprkSign);	
                                $OperResult="配件入库成功！";
                                 $AutoRkSign=1;
                                }
                        }
                break;
} */
      
 
?>