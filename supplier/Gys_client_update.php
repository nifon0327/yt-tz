<?php  
include "../model/modelhead.php";
ChangeWtitle("$SubCompany 生成送货单");//需处理
$Date=date("Y-m-d");
$DateTime=date("Y-m-d H:i:s");
$DateTemp = date("Ymd");
$Mid=0;
$Lens=count($checkid);
$j=1;
$Log="";
if ($SendWay==""){
	 echo "<SCRIPT LANGUAGE=JavaScript>alert('请填写送货信息');history.back();</script>"; 
}
else{
		$sendFloor=3;$MidArray=array();
		for($i=0;$i<$Lens;$i++){
			$ValueArray=explode("-",$checkid[$i]);
			$Row=$ValueArray[0]-1;
			$StuffId=$ValueArray[1];
			
		    $floorResult = mysql_fetch_assoc(mysql_query("Select SendFloor From $DataIn.stuffdata Where StuffId='$StuffId' LIMIT 1"));
		    $sendFloor  = $floorResult["SendFloor"] == ""?"3":$floorResult["SendFloor"];
		    $Mid=$MidArray[$sendFloor]==""?0:$MidArray[$sendFloor];
			  
			$SumQty=$sendQty[$Row]*1;   ///本次送货数
			
			//  把补货也插入 如果有未补货的，则先送补货的,如果不够，则从本次送货那里拿
			$tmpBSQty=$BSQty[$Row]*1;  //取得补货总数
			//退货的总数量 
			$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
										   LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
										   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
			$thQty=mysql_result($thSql,0,"thQty");
			$thQty=$thQty==""?0:$thQty;
			//补货的数量 
			$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
										   LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
										   WHERE M.CompanyId = '$myCompanyId' AND S.StuffId = '$StuffId' ",$link_id);
			$bcQty=mysql_result($bcSql,0,"bcQty");
			$bcQty=$bcQty==""?0:$bcQty;
			//待送货数量
			$shQty=0;
			$shSql=mysql_query("SELECT SUM( S.Qty ) AS Qty FROM $DataIn.gys_shmain M
								LEFT JOIN $DataIn.gys_shsheet S ON S.Mid = M.Id
								WHERE 1 AND M.CompanyId = '$myCompanyId' AND S.Estate>0 AND S.StuffId=$StuffId AND (S.StockId='-1' or S.SendSign='1')",$link_id); 
			$shQty=mysql_result($shSql,0,"Qty");
			$shQty=$shQty==""?0:$shQty;	
			
			$webQty=$thQty-$bcQty-$shQty; //未补数量	
			//echo "$webQty=$thQty-$bcQty-$shQty"; //未补数量	
			if($tmpBSQty>=$webQty) {
				$tmpBSQty=$webQty;  //最多只能送未补数量
			}
			else {  // add by zx 2013-11-06 如果不够，则从本次送货那里拿,
				if( ($SumQty>0) && ($webQty>0) ) {  // add by zx 2013-11-06 如果有本次送货
					$LendQty=$tmpBSQty+$SumQty;  //本次补货+本次送货
					if($LendQty>$webQty){  // 本次补货+本次送货超过未补
						$tmpBSQty=$webQty;  //最多只能送未补数量
						//$SumQty=$SumQty-($webQty-$tmpBSQty);
						$SumQty=$LendQty-$webQty;  //剩下的本次送货数
					}
					else {  // 本次补货+本次送货不够未补
						$tmpBSQty=$LendQty;
						$SumQty=0;  //本次送货全部当补货
					}
				}
			}
			
			
			if($tmpBSQty>0 ) {     //
				if($Mid==0){//如果没生成主送货单就先生成主送货单
					$MaxBillNumberResult  = mysql_fetch_array(mysql_query("SELECT MAX(BillNumber) AS MaxBillNumber FROM $DataIn.gys_shmain WHERE  BillNumber LIKE'$DateTemp%'",$link_id));
			        $MaxBillNumber  = $MaxBillNumberResult["MaxBillNumber"]; 
			        if($MaxBillNumber){
				        $MaxBillNumber = $MaxBillNumber+1;
			        }else{
				        $MaxBillNumber = $DateTemp."0001";
			        }
					$maxGysNumberSql = mysql_query("SELECT MAX(GysNumber) AS GysNumber FROM $DataIn.gys_shmain WHERE CompanyId=$myCompanyId",$link_id);
					$GysNumber=mysql_result($maxGysNumberSql,0,"GysNumber");
					if($GysNumber){
						$GysNumber=$GysNumber+1;
						}
					else{
						$GysNumber=$myCompanyId."0001";//默认
						}
								
					$inRecode="INSERT INTO $DataIn.gys_shmain (Id,BillNumber,GysNumber,CompanyId,Locks,Date,Remark, Floor) 
								VALUES (NULL,'$MaxBillNumber','$GysNumber','$myCompanyId','1','$DateTime','', '$sendFloor')";
					$inAction=@mysql_query($inRecode);
					$Mid=mysql_insert_id();
					$MidArray[$sendFloor]=$Mid;
				}
				if($Mid!=0){//可以全部补货
					$Log.="$j - 本次补货 $StuffId - $tmpBSQty";
					$addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,StockId,StuffId,Qty,SendSign,Estate,Locks)
								 VALUES (NULL,'$Mid','-1','$StuffId','$tmpBSQty','1','1','1')";   //SendSign: 0送货，1补货, 2备品  
					$addAction=@mysql_query($addRecodes);
				}				
		}

		 	   if ($nextWeek==""){
						$curDate=date("Y-m-d");
						$nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
						$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
						$nextWeek=$dateResult["NextWeek"];
				}

	if($SumQty*1>0 ){
		   
				   
		    $checkSql=mysql_query("SELECT S.StockId, (S.AddQty + S.FactualQty) AS Qty 
								   FROM $DataIn.cg1_stocksheet S
								   WHERE 1 
						           AND S.rkSign >0
								   AND S.StuffId ='$StuffId'
								   AND S.CompanyId ='$myCompanyId' 
								   AND S.DeliveryDate<>'0000-00-00' 
		                           AND  YEARWEEK(S.DeliveryDate,1)<='$nextWeek' 
								   ORDER BY S.Id",$link_id);
			if($checkRow=mysql_fetch_array($checkSql)){
				do{
					$StockId=$checkRow["StockId"];
					$Qty=$checkRow["Qty"];
					//已收货总数
					$rkTemp=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.ck1_rksheet WHERE StuffId='$StuffId' AND StockId=$StockId",$link_id);
					$rkQty=mysql_result($rkTemp,0,"Qty");
					$rkQty=$rkQty==""?0:$rkQty;
		
					//待送货数量
					$shSql=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.gys_shsheet WHERE 1 AND Estate>0 AND StuffId=$StuffId AND StockId=$StockId",$link_id);  
					$shQty=mysql_result($shSql,0,"Qty");
					$shQty=$shQty==""?0:$shQty;
					$NoQty=$Qty-$rkQty-$shQty;  //减掉未送货的单，省得出错
					
					if($NoQty>0 && $SumQty>0){//该单未送完货
						if($Mid==0){//如果没生成主送货单就先生成主送货单
							$MaxBillNumberResult  = mysql_fetch_array(mysql_query("SELECT MAX(BillNumber) AS MaxBillNumber FROM $DataIn.gys_shmain WHERE  BillNumber LIKE'$DateTemp%'",$link_id));
					        $MaxBillNumber  = $MaxBillNumberResult["MaxBillNumber"]; 
					        if($MaxBillNumber){
						        $MaxBillNumber = $MaxBillNumber+1;
					        }else{
						        $MaxBillNumber = $DateTemp."0001";
					        }
							$maxGysNumberSql = mysql_query("SELECT MAX(GysNumber) AS GysNumber FROM $DataIn.gys_shmain WHERE CompanyId=$myCompanyId",$link_id);
							$GysNumber=mysql_result($maxGysNumberSql,0,"GysNumber");
							if($GysNumber){
								$GysNumber=$GysNumber+1;
								}
							else{
								$GysNumber=$myCompanyId."0001";//默认
								}
							$inRecode="INSERT INTO $DataIn.gys_shmain (Id,BillNumber,GysNumber,CompanyId,Locks,Date,Remark, Floor) VALUES (NULL,'$MaxBillNumber','$GysNumber','$myCompanyId','1','$DateTime','', '$sendFloor')";
							$inAction=@mysql_query($inRecode);
							$Mid=mysql_insert_id();
							$MidArray[$sendFloor]=$Mid;
						}
						//分析：送货数量与该数量的比较
						if($SumQty>=$NoQty && $Mid!=0){//可以全部送货
							$SumQty-=$NoQty;
							$Log.="$j - 全部送货 $StockId - $NoQty";
							$addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,StockId,StuffId,Qty,SendSign,Estate,Locks) VALUES (NULL,'$Mid','$StockId','$StuffId','$NoQty','0','1','1')";   //SendSign: 0送货，1补货, 2备品  
							$addAction=@mysql_query($addRecodes);
							}
						else{//部分送货
							$Log.="$j - 全部送货 $StockId - $SumQty";
							$addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,StockId,StuffId,Qty,SendSign,Estate,Locks) VALUES (NULL,'$Mid','$StockId','$StuffId','$SumQty','0','1','1')";  //SendSign: 0送货，1补货, 2备品  
							$addAction=@mysql_query($addRecodes);
							break;//当该送货数量已经分配完，则跳出
							}
						}
					$j++;
					}while($checkRow=mysql_fetch_array($checkSql));
				}
			}  //if($SumQty>0 )
			
			
			
			
			
			$tmpBPQty=$BPQty[$Row];  //取得备品总数
			if($tmpBPQty>0) {     //
				if($Mid==0){//如果没生成主送货单就先生成主送货单
					$maxSql = mysql_query("SELECT MAX(BillNumber) AS BillNumber FROM $DataIn.gys_shmain WHERE CompanyId=$myCompanyId",$link_id);
					$MaxBillNumberResult  = mysql_fetch_array(mysql_query("SELECT MAX(BillNumber) AS MaxBillNumber FROM $DataIn.gys_shmain WHERE  BillNumber LIKE'$DateTemp%'",$link_id));
			        $MaxBillNumber  = $MaxBillNumberResult["MaxBillNumber"]; 
			        if($MaxBillNumber){
				        $MaxBillNumber = $MaxBillNumber+1;
			        }else{
				        $MaxBillNumber = $DateTemp."0001";
			        }
					$maxGysNumberSql = mysql_query("SELECT MAX(GysNumber) AS GysNumber FROM $DataIn.gys_shmain WHERE CompanyId=$myCompanyId",$link_id);
					$GysNumber=mysql_result($maxGysNumberSql,0,"GysNumber");
					if($GysNumber){
						$GysNumber=$GysNumber+1;
						}
					else{
						$GysNumber=$myCompanyId."0001";//默认
						}
					$inRecode="INSERT INTO $DataIn.gys_shmain (Id,BillNumber,GysNumber,CompanyId,Locks,Date,Remark, Floor)VALUES (NULL,'$MaxBillNumber','$GysNumber','$myCompanyId','1','$DateTime','', '$sendFloor')";
					$inAction=@mysql_query($inRecode);
					$Mid=mysql_insert_id();
					$MidArray[$sendFloor]=$Mid;
				}
				if($Mid!=0){//可以全部补货
					$Log.="$j - 本次备品 $StuffId - $tmpBPQty";
					$addRecodes="INSERT INTO $DataIn.gys_shsheet (Id,Mid,StockId,StuffId,Qty,SendSign,Estate,Locks)
								 VALUES (NULL,'$Mid','-2','$StuffId','$tmpBPQty','2','1','1')";   //SendSign: 0送货，1补货, 2备品  
					$addAction=@mysql_query($addRecodes);
				}			
			}		
		}
		if ($Log==""){
		    //echo $inRecode,$addRecodes;
			 echo "<SCRIPT LANGUAGE=JavaScript>alert('生成送货单失败 $SumQty');history.back();</script>"; 
		}
		else{
			 //自动插入送货信息 
			 if ($myCompanyId!="2270" && $myCompanyId!="2683" ){
			         $SendDate=$SendDate==""?$Date:$SendDate;
					 $inRecode="INSERT INTO  $DataPublic.come_data (Id ,cSign ,TypeId ,Name ,Persons ,ComeDate ,Remark ,InTime ,InOperator ,OutTime ,OutOperator ,CompanyId ,Mid ,Estate ,Locks ,Date ,Operator)VALUES (NULL,  '7',  '1',  '$SendWay',  '1',  '$SendDate', NULL, NULL ,  '0', NULL ,  '0',  '$myCompanyId',  '$Mid',  '1',  '0',  '$Date',  '$Login_P_Number')";
		             $inAction=@mysql_query($inRecode);
             }
			 echo "<meta http-equiv=\"Refresh\" content='0;url=gys_sh_read.php'>";
		}
}
?>