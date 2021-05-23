<?php 
//待组装、加工明细
$curDate=date("Y-m-d");
$curDateTime=date("Y-m-d H:i:s");
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS NextWeek",$link_id));
 $curWeek=$dateResult["NextWeek"];
 $groupsSQL = "Select B.GroupName,B.Id as GPid,B.GroupLeader, C.Name From $DataIn.staffgroup B 
				   Left Join $DataIn.staffmain C On B.GroupLeader = C.Number
 				   Where B.Estate=1 and  B.TypeId = '7100' ";
	$groupsRs = mysql_query($groupsSQL);
	$listGroup = array();
	while ($groupsRow = mysql_fetch_array($groupsRs)) {
		$GroupName = $groupsRow["GroupName"];
		$GroupLeaderNum =$groupsRow["GroupLeader"];
		$GroupLeader = $groupsRow["GPid"];
		//$GroupName = substr($GroupName,1);
		$GroupName = str_replace("组装", "", $GroupName);
		//$GroupName = mb_substr($GroupName,2,1,'utf-8');
		$Name = $groupsRow["Name"];
		$listGroup[] = array($GroupName,$Name,$GroupLeaderNum,$GroupLeader);
		//$numberArr[] = array("$GroupName"=>"$GroupLeaderNum");
}
$bagevalue = 0;
$showRed ;$showRedArr = array();
 $SearchWeek=$checkWeek>0 ?" AND YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)='$checkWeek'":"";
 $checkWeekSign=$checkWeek=="Over"?1:0;
 $SearchWeek=$checkWeek=="Over"?" AND YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)<'$curWeek'":$SearchWeek;

$OrderBySTR=$checkWeekSign==1?" ORDER BY Weeks,CompanyId,Leadtime ":" ORDER BY CompanyId,Leadtime ";
 
$SearchCompany=$CompnayId>0? " AND M.CompanyId='$CompnayId' ":"";

$finalFor = "";
$LockTotalQty=0;$OverTotalQty=0;$OverCount=0;
//SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2 
  if (1){
  //待组装
				  $mySql="SELECT M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,S.sgRemark ,C.Forshort,P.cName,P.TestStandard,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,P.ProductId,P.Weight,P.eCode,P.Description,
							                YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks 
				 FROM ( 
				     SELECT S1.* FROM (
				          SELECT S0.POrderId,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty,SUM(S0.llEstate) AS llEstate FROM (      
				             SELECT 
										S.POrderId,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(SUM(L.Estate),0) AS llEstate   
				                        FROM $DataIn.yw1_ordermain M
										LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
										LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				                        LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
										LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
										LEFT JOIN  $DataIn.stuffmaintype SM ON SM.Id=ST.mainType 
				                        LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
									    LEFT JOIN  $DataIn.stuffproperty T ON T.StuffId=G.StuffId AND T.Property='8'  
				                        WHERE 1  $SearchCompany   AND S.scFrom>0 AND S.Estate=1 AND G.Level=1  AND SM.blSign=1  AND T.StuffId IS NULL  
				                        GROUP BY G.StockId 
				               )S0 GROUP BY S0.POrderId 
				     )S1 WHERE S1.blQty=S1.llQty OR EXISTS(SELECT C.POrderId FROM $DataIn.sc1_cjtj C WHERE C.POrderId=S1.POrderId AND C.Qty>0)
				)A  
				LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId  
				LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
				LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
				LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
				LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId   
				LEFT JOIN  $DataIn.productdata P ON P.ProductId=S.ProductId
				WHERE 1 $SearchWeek $OrderBySTR ";
         /*
			$mySql="SELECT A.*,C.Forshort,P.cName,P.TestStandard,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,
			                YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks FROM (
						SELECT 
						M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(L.llEstate,0) AS llEstate  
						FROM $DataIn.yw1_ordermain M
						LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						LEFT JOIN (
									 SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(L.Estate) AS llEstate  
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
									 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
									 WHERE  S.scFrom>0 AND S.Estate=1 GROUP BY L.StockId
								 ) L ON L.StockId=G.StockId
						WHERE S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  
						AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
						 GROUP BY S.POrderId 
						) A 
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId  
			            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=A.Id 
			            LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=A.POrderId   
                        LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
					WHERE  A.blQty=A.llQty  $SearchWeek $OrderBySTR";// and L.Estate=0 A.K1>=A.K2 AND
		   */
		}

  //if ($LoginNumber==10868) echo $mySql;
    $curDate=date("Y-m-d");
    $dataArray=array(); $jsondata=array(); 
    $viewHidden=0;$SortArray=array();
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            $sumQty=0;$sumQtyO=0;$sumQty1=0;$totalQty=0;
            $sumAmount=0;$sumAmount1=0;$totalAmount=0;
            
            $SortId=$checkWeekSign==1?$myRow["Weeks"]:$myRow["CompanyId"];
            $SortName=$checkWeekSign==1?"Week " . substr($myRow["Weeks"],4,2):$myRow["Forshort"];
            $oId=$SortId;
            $m=0;$pos=0;
            
            $CompanyId=$myRow["CompanyId"];
            $Forshort=$myRow["Forshort"];
            $oldCompanyId=$CompanyId;
             include "../subprogram/currency_read.php";//$Rate、$PreChar
            do 
            {	
                    $POrderId=$myRow["POrderId"];
                    $OrderPO=$myRow["OrderPO"];
                    $cName=$myRow["cName"];
                    $Qty = $myRow["Qty"];
                    $Unit=$myRow["Unit"]=="PCS"?"pcs":$myRow["Unit"];
                    $Price=$myRow["Price"];
                    $Amount=sprintf("%.2f",$Qty*$Price);		

                    $OrderDate=$myRow["OrderDate"];
                    $Leadtime=str_replace("*", "", $myRow["Leadtime"]);
                    $TestStandard=$myRow["TestStandard"];
                    include "order/order_TestStandard.php";
                     $pEcode = $myRow["eCode"];
					   $ProductId = $myRow["ProductId"];
					   $AppFilePath="http://www.middlecloud.com/download/teststandard/T" .$ProductId.".jpg";
					    $Weight=(float)$myRow["Weight"];
                    $WeightSTR="";
                    $productId=$ProductId;
                      include "../../model/subprogram/weightCalculate.php";
                      if ($Weight>0){
	                       $extraWeight=$extraWeight == "error"?"":$extraWeight+($Weight*$boxPcs); 
	                       $WeightSTR=$Weight>0?"$pEcode|$Weight|$boxPcs|$extraWeight":"";
                      }
                      
                    /*
                     if ($ActionId==21302 || $ActionId==21301){
		                     if ($Leadtime>=$curDate) continue;
	                  }
	                */
	                    
                    if ($ActionId==213 || $ActionId==21302){
	                    $checkMainType=mysql_query("SELECT ST.mainType FROM $DataIn.cg1_stocksheet G 
		                     LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
							 LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
		                     WHERE G.POrderId='$POrderId' AND ST.mainType=3",$link_id);
					    if(mysql_num_rows($checkMainType)<=0){	
					       continue;
					    }
				    }
	  
	                    //取得备料时间
                 	   $FromWebPage="LBL";
	                   include "../../admin/order_datetime.php";
	                   $BlDate=$lbl_Date;
	                   $workHours=$lbl_Hours==""?0:$lbl_Hours;
	                    //$Date=substr($BlDate, 5, 2) ."/". substr($BlDate, 8, 2) . " " . substr($BlDate, 11,5);
                       $Date=GetDateTimeOutString($BlDate,'');
	                    if ($ActionId==213){
		                      $DateColor=$workHours>=$default_schours?"#FF0000":"";
		                      
	                    }else{
		                      $DateColor=$workHours>=$default_jghours?"#FF0000":""; 
	                    }
	                    
	                   
	                   //下单到现在时间
	                  
                        $odDays=(strtotime($curDate)-strtotime($OrderDate))/3600/24;
	                     if ($Leadtime!=""){
		                     $colorSign=$curDate>=$Leadtime?4:0;
	                     }
	                     else{
		                      $colorSign=0;
	                     }
                    
                    $OrderSignColor=0;$cgRemark="";
                       //检查BOM表配件是否锁定
                    $checkcgLockSql=mysql_query("SELECT count(*) AS Locks,SUM(if(GL.Locks=0 AND ST.mainType=3 and D.TypeId<>7100,1,0)) AS gLocks,GL.Remark,GL.Date,M.Name  FROM $DataIn.cg1_stocksheet G 
LEFT JOIN $DataIn.cg1_lockstock GL  ON G.StockId=GL.StockId 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
LEFT JOIN $DataPublic.staffmain  M ON M.Number=GL.Operator 
WHERE  G.POrderId='$POrderId' AND GL.Locks=0 ",$link_id);
				    if($checkcgLockRow = mysql_fetch_array($checkcgLockSql)){
				        $cgRemark=$checkcgLockRow["Remark"];
				        $RemarkDate=date("Y/m/d",strtotime($checkcgLockRow["Date"]));
			            $RemarkOperator=$checkcgLockRow["Name"];
						if ($checkcgLockRow["Locks"]>0){
							if ( $ActionId==21301 || $ActionId==21300) {
							    if ($checkcgLockRow["gLocks"]>0){
								    $OrderSignColor=2; 
								    if ($ActionId==21301) continue;
							    }
							    else{
								   $OrderSignColor=6;
							    }   
							}
							else{
								  $OrderSignColor=2;
								   if ($ActionId==21302) continue;
							}
						  } 
					}
					
					$Remark=$myRow["sgRemark"];$RemarkDate="";
                    $checkExpress=mysql_query("SELECT S.Type,S.Remark,S.Date,M.Name FROM $DataIn.yw2_orderexpress  S 
                    LEFT JOIN $DataPublic.staffmain  M ON M.Number=S.Operator
                    WHERE S.POrderId='$POrderId' AND S.Type=2 LIMIT 1",$link_id);
                   if($checkExpressRow = mysql_fetch_array($checkExpress)){
						    $OrderSignColor=4;
						    $Remark=$checkExpressRow["Remark"];
						    $RemarkDate=date("Y/m/d",strtotime($checkExpressRow["Date"]));
			                $RemarkOperator=$checkExpressRow["Name"];
						     if ( $ActionId==21301 || $ActionId==21302) continue;
						    // continue;
						}
					$Remark.=$cgRemark;	
					$CompanyId=$myRow["CompanyId"];	
$Forshort = $myRow["Forshort"];
$finalFor = $Forshort;
$newWeeks = $myRow["Weeks"];
					$SortId=$checkWeekSign==1?$myRow["Weeks"]:$myRow["CompanyId"];
                      if ($checkWeekSign==1 && ($oldCompanyId!=$CompanyId || $oId!=$SortId)){
                              $sumQty1=number_format($sumQty1);
	                          $sumAmount1=number_format($sumAmount1);
	                          $tempArray=array(
				                      "Id"=>"$oldCompanyId",
				                      "Title"=>array("Text"=>"$Forshort","Color"=>"#358FC1","Frame"=>"16,10,100,15","FontSize"=>"13.5"),
				                      "Col3"=>array("Text"=>"$sumQty1","Frame"=>"230,9,80,18"),
				                     // "Col5"=>array("Text"=>"$PreChar$sumAmount1")
				                   );
						       $tempArray1[]=array("Tag"=>"total","data"=>$tempArray,"CellID"=>"ss");
						       array_splice($dataArray,$pos,0,$tempArray1);
						        $pos=count($dataArray);
						       $tempArray1=array();
						       $sumQty1=0;$sumAmount1=0;
						       $oldCompanyId=$CompanyId;
						       $Forshort=$myRow["Forshort"];
						       include "../subprogram/currency_read.php";//$Rate、$PreChar
	                          
                      }
                      
                     if ($oId!=$SortId){  
                             // if (!in_array($oId, $PickIdArray)) $PickArray[]=array($oId,$SortName);
                             $SortArray[$oId]=$sumQty;
	                          $sumQty=number_format($sumQty);
							  $sumQtyO=number_format($sumQtyO);
	                          $sumAmount=number_format($sumAmount);
	                          if ($checkWeekSign==1){
	                              $bgColor=$oId==$curWeek?"#CCFF99":"";
	                              $dateArray= GetWeekToDate($oId,"m/d");
                                   $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
								   if ($LoginNumber==11965 || versionToNumber($AppVersion)>300) {
									  $astr = substr($SortName,5,2);
									   $TitleD = array("bgColor"=>"#FF0000","Week"=>$astr?"$astr":"","WeekDate"=>"$dateSTR");
								   } else {
									   $TitleD = array("Text"=>"$SortName","Color"=>"#358FC1","Frame"=>"16,10,100,15","FontSize"=>"13.5");
								   }
		                          $headArray=array(
					                      "Id"=>"$oId",
					                      "onTap"=>"1",
					                      "RowSet"=>array("bgColor"=>"$bgColor"),
										  "iNum"=>array(),
					                      "Title"=>$TitleD,//,"BelowTitle"=>"$dateSTR"
										  "Col2"=>array("Text"=>$sumQtyO>0?"$sumQtyO":"","Color"=>"#FF0000","Frame"=>"112,9,70,18","FontSize"=>"13.5"),
					                      "Col3"=>array("Text"=>"$sumQty($m)","Frame"=>"230,9,80,18")
					                   );
									   
                             }
                             else{
	                             $headArray=array(
					                      "Id"=>"$oId",
					                      "onTap"=>"1",
					                      "Title"=>array("Text"=>"$SortName","Color"=>"#358FC1","Frame"=>"16,10,100,15","FontSize"=>"13.5"),"Col2"=>array("Text"=>$sumQtyO>0?"$sumQtyO":"","Color"=>"#FF0000","Frame"=>"112,9,70,18","FontSize"=>"13.5"),
					                      "Col3"=>array("Text"=>"$sumQty($m)","Frame"=>"230,9,80,18")
					                   );
						          include "../subprogram/currency_read.php";//$Rate、$PreChar   
                             }
                              $m=0;
                             
	                          $jsondata[]=array("List"=>$dataArray,
					               "Tag"=>"total","CellID"=>"Total0",
								    "data"=>$headArray,
									"onTap"=>array("value"=>"1","hidden"=>($checkWeekSign==1)?"0":"1","CellID"=>"sec"));
									//$stuffOutArray[$sectionCount]["Col3"]["Text"]+=$mainQty;
									if ($checkWeekSign==1){
			$jsondata = array_merge($jsondata,$dataArray);
		}
                              // $viewHidden=$viewHidden==0?1:$viewHidden;

		                      $sumQty=0;$sumAmount=0;
							  $sumQtyO=0;
		                      $dataArray=array();
		                      $pos=0;
		                      $oId=$SortId;
                              $SortName=$checkWeekSign==1?"Week " . substr($myRow["Weeks"],4,2):$myRow["Forshort"];
                     }
                            $Locks=0;
			                 if ($OrderSignColor==4 || $OrderSignColor==2 || $OrderSignColor==6)
		                      {
			                       $Locks=$OrderSignColor==4?1:2;
			                       $Locks=$OrderSignColor==6?3:$Locks;
			                       $LockTotalQty+=$Qty;
			                      // $odDays="锁";
		                      }
		               $rowColor="";
					   $onlyRemark = false;
					   
					   $isred = 0;
		               //生产数量
		               $ScQty="";$ScLine="";
					   $MaxDate = "";
		               $ScQtyResult=mysql_query("SELECT boxId,SUM(Qty) AS Qty,MAX(Date) as MaxDate  FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId='7100'",$link_id);
						if($ScQtyRow = mysql_fetch_array($ScQtyResult)){
							 
							 if ($ScQtyRow["Qty"] == $Qty) {
								 continue;//$bagevalue ++;
							 }
							 
							 
						      $ScQty=$ScQtyRow["Qty"]==0?"":number_format($ScQtyRow["Qty"]);
						      $ScLine=substr($ScQtyRow["boxId"], 0,1);
							   $MaxDate=$ScQtyRow["MaxDate"];
						}
						if ($ScQty==""){
							  $ScLineResult=mysql_query("SELECT G.GroupName FROM $DataIn.sc1_mission S
							   LEFT JOIN $DataIn.staffgroup G ON G.Id=S.Operator 
							   WHERE S.POrderId='$POrderId' AND G.Id>0",$link_id);
							if($ScLineRow = mysql_fetch_array($ScLineResult)){
							      $GroupName=$ScLineRow ["GroupName"];
							      $ScLine=substr($GroupName,-1);
							}
					 }
					 else{
						 $rowColor="#CCFFCC";$onlyRemark=true;
						  if ((strtotime($curDateTime)-strtotime($MaxDate))/60 > 30) {
								 // $onlyRemark = true;
								  $rowColor="#FFE2E9";//FFE2E9
								 $isred = 1;
								  //heara
								// $redTime = date("Y-m-d H:i:s",strtotime($MaxDate)+1800);
								 $Date = GetDateTimeOutString($MaxDate,'');
								}
					 }
					 if ( $ScLine == "") {
						 continue;
					 }
					 $leadNum = "";
					 foreach ($listGroup as $lineArr) {
							if ($ScLine == $lineArr[0]) {
								$leadNum = $lineArr[2];
								break;
							}
						}
					// $ScLine=$ScLine=="null"?"":$ScLine;
					 
                      $ShipType=$myRow["ShipType"];
                      $timeColor=$curDate>=$Leadtime?"#FF0000":"";
                      $Leadtime=date("m/d",strtotime($Leadtime)) . "|$timeColor";
                      $QtySTR=number_format($Qty);
                      $Weeks=substr($myRow["Weeks"],4,2);
                      //$cName=date("Y-m-d H:m:s");
                      if ($myRow["Weeks"]<$curWeek){
	                       $OverTotalQty+=$Qty;$OverCount++;
                      }
                      $bgColor=$myRow["Weeks"]<$curWeek?"#FF0000":"";//#00BA61
                      
                      $WeekCount="Week_" . $myRow["Weeks"];
                      $$WeekCount=$$WeekCount==""?1:$$WeekCount+1;
					 
					  $pcName=$CompanyId==100145?$myRow["Description"]:$cName;//RJSINGER 
					   
                      $configDict = array("productCode"=>"$pEcode",
											 "ProductId"=>"$ProductId","productName"=>"$pcName");
                       include "submodel/stuff_factualqty_bgcolor.php";
					   	//"Title2"=>"$title2","2Color"=>"358FC1";
                      $tempArray=array(
                      "Id"=>"$POrderId","line"=>"$ScLine","leadNum"=>"$leadNum","ship"=>"ship"."$ShipType",
                      "RowSet"=>array("bgColor"=>"$rowColor"),
                       "weeks"=>array("Text"=>"$Weeks","bg"=>"$bgColor","iIcon"=>"$Locks"),
                      "Title"=>array("Text"=>$showRed==1?"".$myRow["Forshort"]."-$cName":"$cName","Color"=>"$TestStandardColor"),
                      "CompanyId"=>"$CompanyId",
					  	"Title2"=>$showRed==1?"".$myRow["Forshort"]."-":"","2Color"=>"358FC1",
						"Forshort"=>$myRow["Forshort"],
                      "Col1"=> array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
                      "Col2"=>array("Text"=>"$OrderPO","Frame"=>"47,27,58,15"),
                      "Col3"=>array("Text"=>"$QtySTR","bgColor"=>"$FactualQty_Color"),
					     "Col4"=>array("Text"=>"$ScQty"),"icon4"=>"scdj_12",
                      "Col5"=>array("Text"=>"$Date","Color"=>"#858888"),"icon3"=>"scdj_11",
                      "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
					  "config"=>$configDict
                       // "rTopTitle"=>array("Text"=>"$odDays"."d","Margin"=>"-22,0,0,0","Color"=>"#358FC1"),
                        //"rIcon"=>"ship$ShipType"
                   );
				    $swapDict = array("Right"=>"358FC1-操作");//
					 $sheet = "设置当前任务,备注,更改拉线,取消生产";
				   $noStar=1;  $distriStar = true;
				    $showZZ = true;
				   //include "order_detail_items.php";
				    
				   if ($onlyRemark) 
				    $sheet = "设置当前任务,备注";
					if ($showRed==1 && $isred==1) {
						//include "order_detail_items.php";
						$singleDic = array("Tag"=>"data","data"=>$tempArray,"CellID"=>$onlyRemark?"data1-1":"dat1",
						"Args"=>"$POrderId|yes|yes",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_gray","value"=>"1","Args"=>"$POrderId|-1","Frame"=>"3,10,8.5,8.5"),"List"=>array(),"Swap"=>$swapDict,"sheet"=>$sheet,"load"=>"",
				   "Tap"=>"0","sbID"=>"nono",
				   "TapImg"=>array("File"=>"$AppFilePath","Args"=>"$WeightSTR")); 
					} else {
				   		// $swapDict = array("Right"=>"FF0000-设置当前任务,358FC1-备注");
						$singleDic = array("Tag"=>"data","data"=>$tempArray,"CellID"=>$onlyRemark?"data1-1":"dat1",
						"Args"=>"$POrderId","load"=>"",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_gray","value"=>"1","Args"=>"$POrderId","Frame"=>"3,10,8.5,8.5"),"List"=>array(),"Swap"=>$swapDict,"sheet"=>$sheet,
				   "Tap"=>"0","sbID"=>"$cellIder",
				   "TapImg"=>array("File"=>"$AppFilePath","Args"=>"$WeightSTR")); 
                   //$dataArray[]=array("Tag"=>"data","onEdit"=>"2","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);
					}
				   $dataArray[]=$singleDic;
				    if ($showRed==1 && $isred==1) {
									  $showRedArr[]=$singleDic;
								  }
				   
				   
                     $sumQty+=$Qty;
                     $sumAmount+=$Amount;
                     $sumQty1+=$Qty;
                     $sumAmount1+=$Amount;
                     
                     $totalQty+=$Qty;
                     $RMBAmount=$Amount*$Rate;
		             $totalAmount+=$RMBAmount;
					 if ($curWeek >$myRow["Weeks"]) {
						 $sumQtyO+=$Qty;
					 }
					 
                    $m++;
            } while($myRow = mysql_fetch_assoc($myResult));
            
           if ($checkWeekSign==1){
                              $sumQty1=number_format($sumQty1);
	                          $sumAmount1=number_format($sumAmount1);
							  
							 
							   if ($LoginNumber==11965 || versionToNumber($AppVersion)>300) {
								   $astr = substr($SortName,5,2);
								   if ($astr==false) $astr ="";
									   $TitleD = array("bgColor"=>"#FF0000","Week"=>"$astr"."","WeekDate"=>"$dateSTR");
								} else {
									   $TitleD = array("Text"=>"$finalFor ","Color"=>"#358FC1","Frame"=>"16,10,100,15","FontSize"=>"13.5");
								 }
	                          $tempArray=array(
				                      "Id"=>"$oldCompanyId",
				                      "Title"=>$TitleD ,
									  "iNum"=>array(),
				                      "Col3"=>array("Text"=>"$sumQty1","Frame"=>"230,9,80,18")
				                   );
				                $tempArray1[]=array("List"=>"[]",
					               "Tag"=>"total","CellID"=>"Total01",
								    "data"=>$tempArray,
									);
								
						        array_splice($dataArray,$pos,0,$tempArray1);	                          
                      }
          $SortArray[$oId]=$sumQty;
           $sumQty=number_format($sumQty);
		   $sumQtyO = number_format($sumQtyO);
           $sumAmount=number_format($sumAmount);
           if ($checkWeekSign==1){
               $bgColor=$SortId==$curWeek?"#CCFF99":"";
               $dateArray= GetWeekToDate($oId,"m/d");
                $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
				 if ($LoginNumber==11965 || versionToNumber($AppVersion)>300) {
					 $astr=substr($SortName,5,2);
					  if ($astr==false) $astr ="";
									   $TitleD = array("bgColor"=>"#FF0000","Week"=>$astr,"WeekDate"=>"$dateSTR");
								   } else {
									   $TitleD = array("Text"=>"$SortName","Color"=>"#358FC1","Frame"=>"16,10,100,15","FontSize"=>"13.5");
								   }
								   
								   
              $headArray=array(
                      "Id"=>"$SortId",
                      "onTap"=>"1",
                      "RowSet"=>array("bgColor"=>"$bgColor"),
                      "Title"=>$TitleD,
					  "iNum"=>array(),
					  "Col2"=>array("Text"=>$sumQtyO>0?"$sumQtyO":"","Color"=>"#FF0000","Frame"=>"112,9,70,18","FontSize"=>"13.5"),
                      "Col3"=>array("Text"=>"$sumQty($m)","FontSize"=>"14","Frame"=>"230,9,80,18")
                   );
         }
         else{
             $headArray=array(
                      "Id"=>"$SortId",
                      "onTap"=>"1",
                      "Title"=>array("Text"=>"$SortName","Color"=>"#358FC1","Frame"=>"16,10,100,15","FontSize"=>"13.5"),
					  "Col2"=>array("Text"=>$sumQtyO>0?"$sumQtyO":"","Color"=>"#FF0000","Frame"=>"112,9,70,18","FontSize"=>"13.5"),
                      "Col3"=>array("Text"=>"$sumQty($m)","Color"=>"#000000","Frame"=>"230,9,80,18")
                   );
         }
         $jsondata[]=array("List"=>$dataArray,
					               "Tag"=>"total","CellID"=>"Total0",
								    "data"=>$headArray,
									"onTap"=>array("value"=>"1","hidden"=>$checkWeekSign==1?"0":"1","CellID"=>"secoo"));
        if ($checkWeekSign==1){
			$jsondata = array_merge($jsondata,$dataArray);
		}
       if ($checkWeekSign!=1){
	        $sortdata=array();
			arsort($SortArray,SORT_NUMERIC);
			while(list($key,$val)= each($SortArray))
			{
			    $tempdata=$jsondata;
			   while(list($key1,$val1)= each($tempdata)){
				      $array_1=$val1["data"];
					  if ($key==$array_1["Id"]){
						     $sortdata[]=$val1;
						     unset($jsondata[$key1]); 
						    break;
					   }
			   }
			}
			$jsondata=$sortdata;
		}
		
		
         $totalQty=number_format($totalQty);
         $LockTotalQty=$LockTotalQty>0?number_format($LockTotalQty):"";
         $OverTotalQty=$OverTotalQty>0?number_format($OverTotalQty):"";
	     //$totalAmount=number_format(sprintf("%.2f",$totalAmount));
        $tempArray=array(
				                      "Id"=>"total",
				                      "Title"=>array("Text"=>"总计","FontSize"=>"14","Bold"=>"1"),
				                      "Col1"=>array("Text"=>"$LockTotalQty","IconType"=>"12","Frame"=>"70,9,55,18","Color"=>"#FF0000","FontSize"=>"14"),
				                      "Col2"=>array("Text"=>"$OverTotalQty","Color"=>"#FF0000","Frame"=>"112,9,70,18","FontSize"=>"14"),
				                      "Col3"=>array("Text"=>"$totalQty","FontSize"=>"14","Frame"=>"230,9,80,18")
				                   );
		 $tempArray2[]=array("Tag"=>"total","data"=>$tempArray,"CellID"=>"all");
        // $totalArray[]=array("data"=>$tempArray2); 
          array_splice($jsondata,0,0,$tempArray2);
          /*
          arsort($AmountArray); //按金额进行倒序排列
           while(list($key, $val) = each($AmountArray)) {
	           if ($val>0){
	               $TotalPre=sprintf("%.1f",$val/$totalAmount*100);
	               $Forshort= $NameArray[$key];           
	               $dataArray=$dataAllArray[$key];
			       $jsonArray[]=array( "$Forshort  $TotalPre%","","",$dataArray);    
			       }
           }
           
           if ($totalQty>0){
	            $totalQty=number_format($totalQty);
	            $totalAmount=number_format(sprintf("%.2f",$totalAmount));
	            $totaldataArray[]=array( "总计","$totalQty","¥$totalAmount"); 
	            $totalArray[]=array("","","",$totaldataArray);
	             array_splice($jsonArray,0,0,$totalArray);
           }
           else{
	           $jsonArray=array();
           }
           */
   }
 
$SegmentArray=array("逾期($OverCount)");
$SegmentIdArray=array("Over");

 //$WeekCount="Week_" .$curWeek;
 //$SegmentArray[]="本周(" . $$WeekCount . ")"; $SegmentIdArray[]=$curWeek;
$AllQty=$OverCount;
$nextDate=date("Y-m-d");
for($i=1;$i<3;$i++){
    $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextDate',1) AS NextWeek",$link_id));
    $nextWeek=$dateResult["NextWeek"];
    $WeekCount="Week_" .$nextWeek;
    $$WeekCount=$$WeekCount ==""?0:$$WeekCount;
    $AllQty+=$$WeekCount;
	 $SegmentArray[]=$i==1?"本周(". $$WeekCount . ")":substr($nextWeek,4,2) . "周(" . $$WeekCount . ")";
	 $SegmentIdArray[]=$nextWeek;
	 $nextDate=date("Y-m-d",strtotime("$nextDate  +7 day"));
}

array_splice($SegmentArray,0,0,"全部($AllQty)");
array_splice($SegmentIdArray,0,0,"0");
//$SegmentArray[]="全部($AllQty)";
//$SegmentIdArray[]="0";
$spList = array();
for ($i=1;$i<8;$i++) {
	$spList[]=array("分拣口$i","$i");
}
//

//"picker"=>array("Style"=>"2","Planar"=>"1","data"=>$PickArray),
if ($showRed != 1)
  $jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray),"cellList"=>$jsondata,"groupList"=>$listGroup,"spList"=>$spList); 
  //,"R-BarButton"=>"1","badgeNum"=>"$bagevalue"
?>