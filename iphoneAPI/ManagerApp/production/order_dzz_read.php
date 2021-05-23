<?php 
//待组装
$noEstate = " and S.Estate=1 ";
if ($LoginNumber == "11965") {
	$noEstate = "";	
}
$showZZ =1;
 function sortByMe($a, $b) {

if ($a["data"]["RowSet"]["S"] == $b["data"]["RowSet"]["S"] ) {

return 0;

} else {

return ($a["data"]["RowSet"]["S"] > $b["data"]["RowSet"]["S"]) ? 1 : -1;

}

}
$curDate=date("Y-m-d");
$curDateTime=date("Y-m-d H:i:s");
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS NextWeek",$link_id));
 $curWeek=$dateResult["NextWeek"];
 
 $SearchWeek=$checkWeek>0 ?" AND YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)='$checkWeek'":"";
 $checkWeekSign=$checkWeek=="Over"?1:0;
 $SearchWeek=$checkWeek=="Over"?" AND YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)<'$curWeek'":$SearchWeek;

$OrderBySTR=$checkWeekSign==1?" ORDER BY Weeks,CompanyId,Leadtime ":" ORDER BY CompanyId,Leadtime ";
 
$SearchCompany=$CompnayId>0? " AND M.CompanyId='$CompnayId' ":"";
$checkLine=$info[1];
$lineSign = $checkLine==""?0:1;


$newData=array();$bagevalue = 0;
$alterData = array(); 
$lineArray=array();
$lineArrayIds=array();
$listGroup=array();

$AGroupLeader = "";

	$SearchWeek = "";
				
				
if ($lineSign) {
	$SearchWeek = " and GP.Id=$checkLine";				 
}
/*
$groupsSQL = "Select B.GroupName,B.Id as GPid, C.Name,B.GroupLeader From $DataIn.staffgroup B 
				   Left Join $DataPublic.staffmain C On B.GroupLeader = C.Number
 				   Where B.Estate=1 and  B.TypeId = '7100' order by B.GroupName";
	$groupsRs = mysql_query($groupsSQL);
	$listGroup = array();
	$ti = 0;
	$rowSeg = 0;
	while ($groupsRow = mysql_fetch_array($groupsRs)) {
		$GroupName = $groupsRow["GroupName"];
		$GroupLeaderNum =$groupsRow["GroupLeader"];
		$GroupLeader = $groupsRow["GPid"];
		//$GroupName = substr($GroupName,1);
		if ($lineSign) {
			if ($GroupLeader == $checkLine) {
				$AGroupLeader = $GroupLeaderNum;
			}
		} else if($ti == 0){
			$AGroupLeader = $GroupLeaderNum;
			 $SearchWeek = " and GP.Id=$GroupLeader";
		}
		
		$GroupName = str_replace("组装", "", $GroupName);
		//$GroupName = mb_substr($GroupName,2,1,'utf-8');
		$Name = $groupsRow["Name"];
		$listGroup[] = array($GroupName,$Name,$GroupLeaderNum,$GroupLeader);
		if (!$lineSign) {
			
		}
		
		$sqlCount = mysql_query("SELECT sum(1) as gpCount
				 FROM ( 
				     SELECT S1.* FROM (
				          SELECT S0.POrderId,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty,SUM(S0.llEstate) AS llEstate FROM (      
				             SELECT 
										S.POrderId,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(SUM(L.Estate),0) AS llEstate   
				                        FROM $DataIn.yw1_ordermain M
										LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
										Left join  $DataIn.sc1_mission SC on SC.POrderId=S.POrderId
				                        LEFT JOIN  $DataIn.staffgroup GP ON GP.Id=SC.Operator 
										LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				                        LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
										LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
				                        LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
				                        LEFT JOIN  $DataIn.stuffproperty T ON T.StuffId=G.StuffId AND T.Property='8' 
				                        WHERE 1  and GP.Id='$GroupLeader'   AND S.scFrom>0 $noEstate AND ST.mainType<2  AND T.StuffId IS NULL 
				                        GROUP BY G.StockId 
				               )S0 GROUP BY S0.POrderId 
				     )S1 WHERE S1.blQty=S1.llQty 
				)A  
				LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId  
				LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
				LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
				LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
				LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId   
				LEFT JOIN  $DataIn.productdata P ON P.ProductId=S.ProductId
				");
				$clineCount = 0;
				if ($rowCountL = mysql_fetch_assoc($sqlCount)) {
					$clineCount =$rowCountL["gpCount"];
				}
				
				$clineCount = ($clineCount>0 && $clineCount !=NULL)?$clineCount:0;
				 if ($lineSign==0 && ($LoginNumber ==$GroupLeaderNum  || ($LoginNumber == "11965" && $ti==2))) {
						$checkLine=$GroupLeader ;
						$lineSign=1;
						$SearchWeek = " and GP.Id=$GroupLeader";
						$rowSeg =$ti;
					}
		
					
					$lineArray[]="$GroupName($clineCount)";
					
					$lineArrayIds[]=$GroupLeader ;
					$ti ++;
				
		
}
*/
if ($checkLine==""){
	$groupsSQL = "Select B.GroupName,B.Id as GPid, C.Name,B.GroupLeader From $DataIn.staffgroup B 
				   Left Join $DataPublic.staffmain C On B.GroupLeader = C.Number
 				   Where B.Estate=1 and  B.TypeId = '7100' order by B.GroupName";
	$groupsRs = mysql_query($groupsSQL);
	while ($groupsRow = mysql_fetch_array($groupsRs)) {
		$GroupName = $groupsRow["GroupName"];
		$GroupLeaderNum =$groupsRow["GroupLeader"];
		$GroupLeader = $groupsRow["GPid"];
		$GroupName = str_replace("组装", "", $GroupName);
		$Name = $groupsRow["Name"];
		$listGroup[] = array($GroupName,$Name,$GroupLeaderNum,$GroupLeader);
   }
}
	
$groupsRs = mysql_query("SELECT SC.Operator,GP.GroupLeader,GP.GroupName,B.Name,COUNT(*) as gpCount
				 FROM ( 
				     SELECT S1.* FROM (
				          SELECT S0.POrderId,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty,SUM(S0.llEstate) AS llEstate FROM (      
				             SELECT 
										S.POrderId,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(SUM(L.Estate),0) AS llEstate   
				                        FROM $DataIn.yw1_ordermain M
										INNER JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
										LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				                        LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
										LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
				                        LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
				                        LEFT JOIN  $DataIn.stuffproperty T ON T.StuffId=G.StuffId AND T.Property='8' 
				                        WHERE 1   AND S.scFrom>0 and S.Estate=1 AND ST.mainType<2  AND T.StuffId IS NULL 
				                        GROUP BY G.StockId 
				               )S0 GROUP BY S0.POrderId 
				     )S1 WHERE S1.blQty=S1.llQty 
				)A  
				INNER JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId  
				INNER JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
				INNER JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
				INNER JOIN  $DataIn.productdata P ON P.ProductId=S.ProductId 
				INNER JOIN  $DataIn.sc1_mission SC ON SC.POrderId=S.POrderId
                INNER JOIN  $DataIn.staffgroup GP ON GP.Id=SC.Operator 
                INNER JOIN $DataPublic.staffmain B On GP.GroupLeader =B.Number
				LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
				LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId   
				WHERE  1 $SearchWeek  GROUP BY SC.Operator  ORDER BY GroupName");
while ($groupsRow = mysql_fetch_array($groupsRs)) {
        $GroupName = str_replace("组装", "", $groupsRow["GroupName"]);
		$GroupLeaderNum =$groupsRow["GroupLeader"];
		$Name = $groupsRow["Name"];
		$GroupLeader = $groupsRow["Operator"];
		$clineCount=$groupsRow["gpCount"];
		
		if ($AGroupLeader=="") {
				$AGroupLeader = $GroupLeaderNum;
		}
		
		$lineArray[]="$GroupName($clineCount)";
		$lineArrayIds[]=$GroupLeader;
		//if (!$lineSign){
		//	$listGroup[] = array($GroupName,$Name,$GroupLeaderNum,$GroupLeader);
			
		//}
		
		if ($SearchWeek == ""){
			$SearchWeek = " and GP.Id=$GroupLeader ";
		}
		
}

$LockTotalQty=0;$OverTotalQty=0;$OverCount=0;
//SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2
$segmentSql ="";
//待组装
//IF((S.sgRemark='' or S.sgRemark is null),S.PackRemark,S.sgRemark) as sgRemark 
$mySql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.ShipType,S.sgRemark,C.Forshort,P.cName,
				  P.TestStandard,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime, GP.GroupName,P.ProductId,P.Weight,P.eCode,P.Description, 
							                YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks 
				 FROM ( 
				     SELECT S1.* FROM (
				          SELECT S0.POrderId,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty,SUM(S0.llEstate) AS llEstate FROM (      
				             SELECT 
										S.POrderId,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(SUM(L.Estate),0) AS llEstate   
				                        FROM $DataIn.yw1_ordermain M
										INNER JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
										LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
				                        LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
										LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
				                        LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
				                        LEFT JOIN  $DataIn.stuffproperty T ON T.StuffId=G.StuffId AND T.Property='8'  
				                        WHERE 1  $SearchCompany  $SearchWeek  AND S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  AND T.StuffId IS NULL 
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
				Left join  $DataIn.sc1_mission SC on SC.POrderId=S.POrderId
				LEFT JOIN  $DataIn.staffgroup GP ON GP.Id=SC.Operator
				WHERE 1  order by GP.GroupName,Weeks,CompanyId,Leadtime ";
				/*
				SELECT G.GroupName FROM $DataIn.sc1_mission S
							   LEFT JOIN $DataIn.staffgroup G ON G.Id=S.Operator 
							   WHERE S.POrderId='$POrderId' AND G.Id>0
				*/
       
    $curDate=date("Y-m-d");
    $dataArray=array(); $jsondata=array(); 
    $viewHidden=0;$SortArray=array();
    $myResult = mysql_query($mySql,$link_id);
	$lastGP = "-1";
    if($myRow = mysql_fetch_assoc($myResult))
    {
            $sumQty=0;$sumQty1=0;$totalQty=0;
            $sumAmount=0;$sumAmount1=0;$totalAmount=0;
            $aLine = str_replace("组装", "", $myRow["GroupName"]);
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
					  $ProductId = $myRow["ProductId"];
					  $AppFilePath="http://www.middlecloud.com/download/teststandard/T" .$ProductId.".jpg";
                    $POrderId=$myRow["POrderId"];
                    $OrderPO=$myRow["OrderPO"];
                    $cName=$myRow["cName"];
                    $Qty = $myRow["Qty"];
                    $Unit=$myRow["Unit"]=="PCS"?"pcs":$myRow["Unit"];
                    $Price=$myRow["Price"];
                    $Amount=sprintf("%.2f",$Qty*$Price);		
$onlyRemark = false;
                    $OrderDate=$myRow["OrderDate"];
                    $Leadtime=str_replace("*", "", $myRow["Leadtime"]);
                    $TestStandard=$myRow["TestStandard"];
                    include "order/order_TestStandard.php";
                    
                    /*
                     if ($ActionId==21302 || $ActionId==21301){
		                     if ($Leadtime>=$curDate) continue;
	                  }
					  
					   if ($ActionId==213 || $ActionId==21302){
	                    $checkMainType=mysql_query("SELECT ST.mainType FROM $DataIn.cg1_stocksheet G 
		                     LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
							 LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
		                     WHERE G.POrderId='$POrderId' AND ST.mainType=3",$link_id);
					    if(mysql_num_rows($checkMainType)<=0){	
					       continue;
					    }
				    }
	                */
	                    
                   
	  
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
								   // if ($ActionId==21301) continue;
							    }
							    else{
								   $OrderSignColor=6;
							    }   
							}
							else{
								  $OrderSignColor=2;
								   //if ($ActionId==21302) continue;
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
						     //if ( $ActionId==21301 || $ActionId==21302) continue;
						    // continue;
						}
					$Remark.=$cgRemark;	
					$CompanyId=$myRow["CompanyId"];	

					$SortId=$checkWeekSign==1?$myRow["Weeks"]:$myRow["CompanyId"];
                      if ($checkWeekSign==1 && ($oldCompanyId!=$CompanyId || $oId!=$SortId)){
                              $sumQty1=number_format($sumQty1);
	                          $sumAmount1=number_format($sumAmount1);
	                          $tempArray=array(
				                      "Id"=>"$oldCompanyId",
				                      "Title"=>array("Text"=>"$Forshort"),
				                      "Col3"=>array("Text"=>"$sumQty1"),
				                      "Col5"=>array("Text"=>"$PreChar$sumAmount1")
				                   );
						       $tempArray1[]=array("Tag"=>"Total","data"=>$tempArray);
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
	                          $sumAmount=number_format($sumAmount);
	                          if ($checkWeekSign==1){
	                              $bgColor=$oId==$curWeek?"#CCFF99":"";
	                              $dateArray= GetWeekToDate($oId,"m/d");
                                   $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		                          $headArray=array(
					                      "Id"=>"$oId",
					                      "onTap"=>"1",
					                      "RowSet"=>array("bgColor"=>"$bgColor"),
					                      "Title"=>array("Text"=>"$SortName","FontSize"=>"14","Bold"=>"1","BelowTitle"=>"$dateSTR"),
					                      "Col3"=>array("Text"=>"$sumQty($m)","FontSize"=>"14")
					                   );
                             }
                             else{
	                             $headArray=array(
					                      "Id"=>"$oId",
					                      "onTap"=>"1",
					                      "Title"=>array("Text"=>"$SortName","FontSize"=>"14","Bold"=>"1"),
					                      "Col1"=>array("Text"=>"$sumQty($m)","Margin"=>"0,0,20,0","Color"=>"#000000"),
					                      "Col3"=>array("Text"=>"$PreChar$sumAmount","FontSize"=>"14")
					                   );
						          include "../subprogram/currency_read.php";//$Rate、$PreChar   
                             }
                              $m=0;
                             
	                          $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
                              // $viewHidden=$viewHidden==0?1:$viewHidden;

		                      $sumQty=0;$sumAmount=0;
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
		               $rowColor=$myRow["llEstate"]==0?"":"";$sortNum=3;
		               //生产数量
		               $ScQty="";$ScLine="";$MaxDate = "";
		               $ScQtyResult=mysql_query("SELECT S.boxId,SUM(S.Qty) AS Qty,MAX(S.Date) as MaxDate 
		               FROM $DataIn.sc1_cjtj  S 
		               INNER JOIN  $DataIn.yw1_scsheet  A ON A.sPOrderId=S.sPOrderId  AND A.ActionId=101 
		               WHERE S.POrderId='$POrderId' ",$link_id);
						if($ScQtyRow = mysql_fetch_array($ScQtyResult)){
			
						      $ScQty=$ScQtyRow["Qty"];
						      $ScLine=substr($ScQtyRow["boxId"], 0,1);
							  $MaxDate = $ScQtyRow["MaxDate"];
							 
							   if ($ScQtyRow["Qty"] == $Qty) {
							       continue;      //$bagevalue++;
							   }
			
						}
						
						
						
$Relation=0;
$RelationResult=mysql_query("SELECT Relation FROM $DataIn.sc1_newrelation  
						  WHERE POrderId='$POrderId' LIMIT 1",$link_id);
if($RelationRows = mysql_fetch_array($RelationResult)){
          $Relation=$RelationRows["Relation"];
          // latest relationrows 
          //  	
}			  
else{					  
		$BoxResult = mysql_query("SELECT P.Relation FROM $DataIn.pands P 
								  LEFT JOIN $DataIn.stuffdata D ON D.StuffId=P.StuffId 
								  LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
								  WHERE 1 and P.ProductId='$ProductId' AND P.ProductId>0 and T.TypeId='9040' ",$link_id);
		
		if($BoxRows = mysql_fetch_array($BoxResult)){
			// boxrows mysql_fetch_array 
			$Relation=$BoxRows["Relation"];
		 	if ($Relation!=""){
				$RelationArray=explode("/",$Relation);
				$Relation=$RelationArray[1];	
			}
		}
}




	$djboxes = "";
		if ($Relation>0 && $ScQty!="") {
			
			$djboxes = intval($ScQty/$Relation) + (($ScQty%$Relation)>0 ? 1 :0);
			
			
		}	
		if ($djboxes >0) {
			$djboxes = "($djboxes)";
		}
		
		
		 $ScQty=$ScQty==0?"":number_format($ScQty);			
						
						if ($ScQty==""){
							
							
					 }
					 else{
						 $rowColor="#CCFFCC";//FFE2E9
						 $sortNum = 2;
						  if ((strtotime($curDateTime)-strtotime($MaxDate))/60 > 30) {
								  //$onlyRemark = true;
								  $sortNum=1;
								  $rowColor="#FFE2E9";//FFE2E9#CCFFCC
								  	  $redTime = date("Y-m-d H:i:s",strtotime($MaxDate)+1800);
								 $Date = GetDateTimeOutString($redTime,'');
								}
						 $onlyRemark = true;
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
                      
                       include "submodel/stuff_factualqty_bgcolor.php";
					   //include "order_detail_items.php";
					   /*
					    $tempArray=array(
                      "Id"=>"$POrderId",
                      "RowSet"=>array("bgColor"=>"$rowColor"),
                       "weeks"=>array("Text"=>"$Weeks","bg"=>"$bgColor","iIcon"=>"$Locks","Badge"=>"$ScLine"),
                      "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                      "Col1"=> array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
					  "Col2"=> array("Text"=>$myRow["Forshort"],"Color"=>"#358FC1"),"Col3"=> array("Text"=>"$OrderPO"),
                      "Col4"=>array("Text"=>"$QtySTR","bgColor"=>"$FactualQty_Color"),
                      "Col5"=>array("Text"=>"$Date","Color"=>"#358FC1"),
                      "Remark"=>array("Text"=>"$Remark"),"icon4"=>"scdj_11",
                        "rTopTitle"=>array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
                       
                   );
					   */
					   //$Forshort = ($Forshort!=NULL && $Forshort!="")?"$Forshort-":"";
					   $pEcode = $myRow["eCode"];
					   
					    $Weight=(float)$myRow["Weight"];
                    $WeightSTR="";
                    $productId=$ProductId;
                      include "../../model/subprogram/weightCalculate.php";
                      if ($Weight>0){
	                       $extraWeight=$extraWeight == "error"?"":$extraWeight+($Weight*$boxPcs); 
	                       $WeightSTR=$Weight>0?"$pEcode|$Weight|$boxPcs|$extraWeight":"";
                      }
                      
					   $pcName=$CompanyId==100145?$myRow["Description"]:$cName;//RJSINGER 
					   
					   $configDict = array("productCode"=>"$pEcode",
											 "ProductId"=>"$ProductId","productName"=>"$pcName");
					   $Forshort=$myRow["Forshort"];
					   $title2 = (($Forshort!=NULL && $Forshort!="")?"$Forshort-":"");
					   $Forshort=$CompanyId==100139?"":$Forshort;
					   
					   $zuPrice = 0;
					   if ($showZZ>0) {
						   
						   $checkZuPrice = "";
						   
						   $checkPrice = mysql_query("
						   select G.Price 
						   FROM $DataIn.cg1_stocksheet G 
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						where G.POrderId=$POrderId and D.TypeId=7100 limit 1");
						if ($checkPriceRow = mysql_fetch_array($checkPrice)) {
							$zuPrice = round($checkPriceRow["Price"],2);
						}
						   
					   }
					   
					   
					   	$ToOutName="";
			
			$OutResult = mysql_query("SELECT D.ToOutName  FROM $DataIn.yw7_clientOutData O
									  LEFT JOIN $DataIn.yw7_clientToOut D ON O.ToOutId=D.Id
									  WHERE  O.POrderId='$POrderId' AND O.Mid=0 ",$link_id);
			//echo "";
			if ($Outmyrow = mysql_fetch_array($OutResult)) {
				//删除数据库记录
				//$Forshort=$myRow["Forshort"]; 
				$ToOutName= " ".$Outmyrow["ToOutName"];
			}
			
			
			
					   
					   
					   
                      $tempArray=array(
	                      
	                      "FrameCapacity"=>"$Relation","Unit"=>"pcs","ProductId"=>"$ProductId",
	                      
                      "Id"=>"$POrderId","line"=>"$aLine","ship"=>"ship"."$ShipType",
                      "RowSet"=>array("bgColor"=>"$rowColor","S"=>$sortNum),
                       "weeks"=>array("Text"=>"$Weeks","bg"=>"$bgColor","iIcon"=>"$Locks"),
                      "Title"=>array("Text"=>"$title2$cName","Color"=>"$TestStandardColor"),
					  	"Title2"=>"$title2","2Color"=>"358FC1",
					  	"Forshort"=>"$Forshort"."$ToOutName",
                      "Col1"=> array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
                      "Col2"=>array("Text"=>"$OrderPO","Frame"=>"89,27,58,15"),
                      "Col3"=>array('fit'=>'1',"Text"=>"$QtySTR","bgColor"=>"$FactualQty_Color"),
					     "Col4"=>array("Text"=>"$ScQty",'fit'=>'1',"DjTimes"=>"$djboxes"),"icon4"=>"scdj_12",
                      "Col5"=>array("Text"=>"$Date","Color"=>"#858888"),"icon3"=>"scdj_11",
                      "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
					  "config"=>$configDict
                       // "rTopTitle"=>array("Text"=>"$odDays"."d","Margin"=>"-22,0,0,0","Color"=>"#358FC1"),
                        //"rIcon"=>"ship$ShipType"
                   );
                   
                   if ($showZZ>0) {
	                   $tempArray["zu_price"] = array("Text"=>"¥"."$zuPrice","Swap"=>"Col5");
                   }
                   
				  // $swapDict = array("Right"=>"358FC1-更改拉线,FF0000-设置当前任务,358FC1-备注");//358FC1-更改拉线,FF0000-设置当前任务,358FC1-备注,
				   $swapDict = array("Right"=>"358FC1-操作");
				   $sheet = "设置当前任务,备注,更改拉线";
				   if ($LoginNumber==11965 || $LoginNumber==10200 || $LoginNumber==10341) {
					    $sheet = "设置当前任务,备注,更改拉线,生产登记";
				   }
				   $noStar = 1;  $distriStar = true;
				   
				   $showZZ = true;
				  // include "order_detail_items.php";
				   if ($onlyRemark) {
				   		//$swapDict = array("Right"=>"FF0000-设置当前任务,358FC1-备注");
						$sheet = "设置当前任务,备注";
							   if ($LoginNumber==11965 || $LoginNumber==10200 || $LoginNumber==10341) {
					    $sheet = "设置当前任务,备注,生产登记";
					    
				   }
				   }
				   if ($sortNum!=3) {
					   $alterData[]=array("Tag"=>"data","data"=>$tempArray,"W1"=>$myRow["Weeks"],"T1"=>$BlDate,
						"CellID"=>$onlyRemark?"data1-1":"dat1","load"=>"",
						"Args"=>"$POrderId",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_blue","value"=>"1","Args"=>"$CompanyId|$ProductId"),"List"=>array(),"Swap"=>$swapDict,"sheet"=>$sheet,
				   "Tap"=>"0",
				   "TapImg"=>array("File"=>"$AppFilePath","Args"=>"$WeightSTR"),
				   "Picture"=>"1",
				   "productImg"=>"http://www.ashcloud.com/download/productIcon/80003.jpg"
				   );
				   } else {
				   		$newData[]=array("Tag"=>"data","data"=>$tempArray,"W1"=>$myRow["Weeks"],"T1"=>$BlDate,
						"CellID"=>$onlyRemark?"data1-1":"dat1",
						"Args"=>"$POrderId","load"=>"",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_blue","value"=>"1","Args"=>"$CompanyId|$ProductId"),"List"=>array(),"Swap"=>$swapDict,"sheet"=>$sheet,
				   "Tap"=>"0",
				   "TapImg"=>array("File"=>"$AppFilePath","Args"=>"$WeightSTR"),
				   "Picture"=>"0",
				   "productImg"=>""
				   );
				   }
                   //$dataArray[]=array("Tag"=>"data","onEdit"=>"2","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);
                     $sumQty+=$Qty;
                     $sumAmount+=$Amount;
                     $sumQty1+=$Qty;
                     $sumAmount1+=$Amount;
                     
                     $totalQty+=$Qty;
                     $RMBAmount=$Amount*$Rate;
		             $totalAmount+=$RMBAmount;
                    $m++;
            } while($myRow = mysql_fetch_assoc($myResult));
            
           if ($checkWeekSign==1){
                              $sumQty1=number_format($sumQty1);
	                          $sumAmount1=number_format($sumAmount1);
	                          $tempArray=array(
				                      "Id"=>"$oldCompanyId",
				                      "Title"=>array("Text"=>"$Forshort"),
				                      "Col3"=>array("Text"=>"$sumQty1"),
				                      "Col5"=>array("Text"=>"$sumAmount1")
				                   );
				                $tempArray1[]=array("Tag"=>"Total","data"=>$tempArray);
						        array_splice($dataArray,$pos,0,$tempArray1);	                          
                      }
          $SortArray[$oId]=$sumQty;
           $sumQty=number_format($sumQty);
           $sumAmount=number_format($sumAmount);
           if ($checkWeekSign==1){
               $bgColor=$SortId==$curWeek?"#CCFF99":"";
               $dateArray= GetWeekToDate($oId,"m/d");
                $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
              $headArray=array(
                      "Id"=>"$SortId",
                      "onTap"=>"1",
                      "RowSet"=>array("bgColor"=>"$bgColor"),
                      "Title"=>array("Text"=>"$SortName","FontSize"=>"14","Bold"=>"1","BelowTitle"=>"$dateSTR"),
                      "Col3"=>array("Text"=>"$sumQty($m)","FontSize"=>"14")
                   );
         }
         else{
             $headArray=array(
                      "Id"=>"$SortId",
                      "onTap"=>"1",
                      "Title"=>array("Text"=>"$SortName","FontSize"=>"14","Bold"=>"1"),
                      "Col1"=>array("Text"=>"$sumQty($m)","Margin"=>"0,0,20,0","Color"=>"#000000"),
                      "Col3"=>array("Text"=>"$PreChar$sumAmount","FontSize"=>"14")
                   );
         }
         $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
        
       if ($checkWeekSign!=1){
	        $sortdata=array();
			arsort($SortArray,SORT_NUMERIC);
			while(list($key,$val)= each($SortArray))
			{
			    $tempdata=$jsondata;
			   while(list($key1,$val1)= each($tempdata)){
				      $array_1=$val1["head"];
					  if ($key==$array_1["Id"]){
						     $sortdata[]=$val1;
						     unset($jsondata[$key1]); 
						    break;
					   }
			   }
			}
			$jsondata=$sortdata;
		}
		
		
	//$tt=array_multisort($newData,$newData["W1"], SORT_NUMERIC, SORT_ASC,$newData["T2"], SORT_REGULAR, SORT_ASC);
		usort($alterData, 'sortByMe');
		
		$newData=   array_merge($alterData,$newData);
		
		
         $totalQty=number_format($totalQty);
         $LockTotalQty=$LockTotalQty>0?number_format($LockTotalQty):"";
         $OverTotalQty=$OverTotalQty>0?number_format($OverTotalQty):"";
	     //$totalAmount=number_format(sprintf("%.2f",$totalAmount));
        $tempArray=array(
				                      "Id"=>"Total",
				                      "Title"=>array("Text"=>"总计","FontSize"=>"14","Bold"=>"1"),
				                      "Col1"=>array("Text"=>"$LockTotalQty","IconType"=>"12","Color"=>"#FF0000","FontSize"=>"14"),
				                      "Col2"=>array("Text"=>"$OverTotalQty","Color"=>"#FF0000","FontSize"=>"14"),
				                      "Col3"=>array("Text"=>"$totalQty","FontSize"=>"14")
				                   );
		 $tempArray2[]=array("Tag"=>"total","data"=>$tempArray,"CellID"=>"alls");
         
          array_splice($newData,0,0,$tempArray2);
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


  $jsonArray=array("Segment"=>array("Segmented"=>$lineArray,"SegmentedId"=>$lineArrayIds,"SegmentIndex"=>"$rowSeg"),"cellList"=>$newData,"groupList"=>$listGroup,"spList"=>$spList,"GroupLeader"=>"$AGroupLeader"); 
  //"R-BarButton"=>"1","badgeNum"=>"$bagevalue",
?>