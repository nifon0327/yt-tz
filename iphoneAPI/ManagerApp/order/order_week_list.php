<?php 
//未出按周显示明细
//权限
$ReadPower=1;
  if ($LoginNumber!=""){
			    $TResult = mysql_query("SELECT Id FROM $DataIn.taskuserdata WHERE ItemId=144 and UserId='$LoginNumber' LIMIT 1",$link_id);
			    if($TRow = mysql_fetch_array($TResult)){
			       $ReadPower=1;
			    }
			    else{
			       $ReadPower=0;
			    }
} 

 $SearchWeek=$checkWeek>0 ?" AND IFNULL(PI.LeadWeek,PL.LeadWeek)='$checkWeek'":"";
 $SearchWeek=$checkWeek=="TBC" ?" AND  IFNULL(PI.LeadWeek,0)=0  AND  IFNULL(PL.LeadWeek,0)=0  ":$SearchWeek;
 $SearchCompany=$checkCompanyId>0? " AND M.CompanyId='$checkCompanyId' ":"";
 $OrderBySTR=" ORDER BY CompanyId,Leadtime ";
 
//$PickArray[]=array("0","全 部");
//布局设置
$Layout=array( "Title"=>array("Frame"=>"40, 2, 230, 25"),
                          "Col2"=>array("Frame"=>"115,32,48, 15","Align"=>"L"),
                          "Col3"=>array("Frame"=>"180,32,48, 15","Align"=>"L"),
                          "Col4"=>array("Frame"=>"230,32,43, 15"));
 //图标设置                        
$IconSet=array("Col2"=>array("Name"=>"scdj_1","Frame"=>"105,35,8.5,10"),
                          "Col3"=>array("Name"=>"scdj_2","Frame"=>"165,35,13,10")
                          );

$LockTotalQty=0;$OverTotalQty=0;$BlTotalQty=0;

 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeek=$dateResult["CurWeek"];

$mySql="SELECT M.CompanyId,M.OrderPO,M.OrderDate,
        S.Id,S.POrderId,S.ProductId,(S.Qty-S.shipQty) AS Qty,S.Price,S.ShipType,S.Estate,S.scFrom,
        P.cName,P.TestStandard,C.Forshort,PI.Leadtime,IFNULL(PI.LeadWeek,PL.LeadWeek)  AS Weeks 
			FROM (SELECT S.Id,S.POrderId,S.OrderNumber,S.Qty,S.Price,S.ShipType,S.Estate,S.scFrom,SUM(IFNULL(C.Qty,0)) AS shipQty 
               FROM $DataIn.yw1_ordersheet S 
               LEFT JOIN $DataIn.ch1_shipsheet C ON C.POrderId=S.POrderId 
               WHERE S.Estate>0 GROUP BY S.POrderId
            )S 
			LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
             LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
            LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		    WHERE S.Estate>0  $SearchCompany $SearchWeek $OrderBySTR";
    $curDate=date("Y-m-d");
    $dataArray=array(); $jsondata=array(); 
    $viewHidden=0;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            $sumQty=0;$sumQty1=0;$totalQty=0;
            $sumAmount=0;$sumAmount1=0;$totalAmount=0;
            
            $SortId=$checkWeekSign==1?$myRow["Weeks"]:$myRow["CompanyId"];
            $SortName=$checkWeekSign==1?"Week" . substr($myRow["Weeks"],4,2):$myRow["Forshort"];
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
							    $OrderSignColor=$checkcgLockRow["gLocks"]>0?2:6;   
							  }
					}
					
					$Remark="";
                    $checkExpress=mysql_query("SELECT S.Type,S.Remark,S.Date,M.Name FROM $DataIn.yw2_orderexpress  S 
                    LEFT JOIN $DataPublic.staffmain  M ON M.Number=S.Operator
                    WHERE S.POrderId='$POrderId' AND S.Type=2 LIMIT 1",$link_id);
						if($checkExpressRow = mysql_fetch_array($checkExpress)){
						    $OrderSignColor=4;
						    $Remark=$checkExpressRow["Remark"];
						     $RemarkDate=date("Y/m/d",strtotime($checkExpressRow["Date"]));
			                 $RemarkOperator=$checkExpressRow["Name"];
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
	                          $sumQty=number_format($sumQty);
	                          $sumAmount=number_format($sumAmount);
	                          if ($checkWeekSign==1){
	                              $bgColor=$oId==$curWeek?"#CCFF99":"#FFFFFF";
		                          $headArray=array(
					                      "Id"=>"$oId",
					                      "onTap"=>"1",
					                      "RowSet"=>array("bgColor"=>"$bgColor"),
					                      "Title"=>array("Text"=>"$SortName","FontSize"=>"14","Bold"=>"1"),
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
                              $SortName=$checkWeekSign==1?"Week" . substr($myRow["Weeks"],4,2):$myRow["Forshort"];
                     }
                            $Locks=0;
			                 if ($OrderSignColor==4 || $OrderSignColor==2 || $OrderSignColor==6)
		                      {
			                       $Locks=$OrderSignColor==4?1:2;
			                       $Locks=$OrderSignColor==6?3:$Locks;
			                       if ($OrderSignColor==4)$LockTotalQty+=$Qty;
			                      // $odDays="锁";
		                      }
		               //生产数量
		               $ScQty=""; $rowColor="#FFFFFF";
		               $ScQtyResult=mysql_query("SELECT boxId,SUM(Qty) AS Qty FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId='7100'",$link_id);
						if($ScQtyRow = mysql_fetch_array($ScQtyResult)){
						      $ScQty=$ScQtyRow["Qty"]==0?"":number_format($ScQtyRow["Qty"]);
						      $ScLine=substr($ScQtyRow["boxId"], 0,1);
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
						
					  $Estate=$myRow["Estate"];	
					  $scFrom=$myRow["scFrom"];	
						if ($scFrom>0){
							 //备料数量
		                      $blResult=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IFNULL(llEstate,0)) AS llEstate  
		                        FROM $DataIn.yw1_ordersheet S 
		                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
		                        LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
								LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
								LEFT JOIN (
								             SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(L.Estate) AS llEstate  
											 FROM $DataIn.yw1_ordersheet S 
											 LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
											 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
											 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
											 WHERE  S.POrderId='$POrderId'  and L.Estate=0 GROUP BY L.StockId
										 ) L ON L.StockId=G.StockId 
								WHERE S.POrderId='$POrderId' AND ST.mainType<2  AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
		                        ",$link_id));
		                      $blQty=$blResult["blQty"]==""?0:$blResult["blQty"];
		                      $llQty=$blResult["llQty"]==""?0:$blResult["llQty"];
		                      $llEstate=$blResult["llEstate"]==""?0:$blResult["llEstate"];
			    			  if ($blQty==$llQty && $blQty>0){
				    			    $BlTotalQty+=$Qty; 
				    			    $rowColor=$llEstate>0?"#F3EBC4":"#CCFFCC";
			    			  }
						}
						else{
							  if ($scFrom==0) $rowColor="#CCFF99";
						}
						
                      $ShipType=$myRow["ShipType"];
                      $timeColor=$curDate>=$Leadtime?"#FF0000":"";
                      $Leadtime=date("m/d",strtotime($Leadtime)) . "|$timeColor";
                      $QtySTR=number_format($Qty);
                      $Weeks=$myRow["Weeks"]==""?" ":substr($myRow["Weeks"],4,2);
                      //$cName=date("Y-m-d H:m:s");
                      if ($myRow["Weeks"]<$curWeek && $checkWeek<>"TBC"){
	                       $OverTotalQty+=$Qty;$OverCount++;
                      }
                      $bgColor=($myRow["Weeks"]<$curWeek && $myRow["Weeks"]!="") ?"#FF0000":"";//#00BA61
                      
                      $WeekCount="Week_" . $myRow["Weeks"];
                      $$WeekCount=$$WeekCount==""?1:$$WeekCount+1;
                      
                      if ($ReadPower==1){
		                     /*毛利计算*//////////// 
		                    $saleRmbAmount=sprintf("%.3f",$Amount*$Rate);//转成人民币的卖出金额
		                    include "order_Profit.php";
                      }
                      
                      $Price=number_format($Price,2);
                      $tempArray=array(
                      "Id"=>"$POrderId",
                       "RowSet"=>array("bgColor"=>"$rowColor"),
                       "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor","iIcon"=>"$Locks","Badge"=>"$ScLine"),
                      "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                      "Col1"=> array("Text"=>"$OrderPO"),
                      "Col2"=>array("Text"=>"$QtySTR"),
                      "Col3"=>array("Text"=>"$ScQty"),
                      "Col4"=>array("Text"=>"$PreChar$Price"),
                      "Col5"=>array("Text"=>"$profitRMB2PC%","Color"=>"$profitColor"),
                      "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                        "rTopTitle"=>array("Text"=>"$odDays"."d","Margin"=>"-22,0,0,0","Color"=>"#0000FF"),
                        "rIcon"=>"ship$ShipType"
                   );
                   $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);
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
   
           $sumQty=number_format($sumQty);
           $sumAmount=number_format($sumAmount);
           if ($checkWeekSign==1){
               $bgColor=$SortId==$curWeek?"#CCFF99":"";
              $headArray=array(
                      "Id"=>"$SortId",
                      "onTap"=>"1",
                      "RowSet"=>array("bgColor"=>"$bgColor"),
                      "Title"=>array("Text"=>"$SortName","FontSize"=>"14","Bold"=>"1"),
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
           if (($checkWeek==0 && $checkWeek!='TBC') || $checkCompanyId>0) {$headArray=array();}
         $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","IconSet"=>$IconSet,"Layout"=>$Layout,"data"=>$dataArray); 
         
         $totalQty=number_format($totalQty);
         $totalAmount=$totalAmount>0?number_format(sprintf("%.2f",$totalAmount)):"";
         
	     $AddArray=array();
	     if ($LockTotalQty>0 || $OverTotalQty>0 || $BlTotalQty>0){
			     $LockTotalQty=$LockTotalQty>0?number_format($LockTotalQty):"";
		         $OverTotalQty=$OverTotalQty>0?number_format($OverTotalQty):"";
		         $BlTotalQty=$BlTotalQty>0?number_format($BlTotalQty):"";
			     $AddArray= array(
		                         array("Text"=>"$LockTotalQty","Copy"=>"Col_1","Color"=>"#FF0000","IconType"=>"12",
		                                   "Align"=>"R","FontSize"=>"13","Margin"=>"-40,0,0,0"),
			                     array("Text"=>"$BlTotalQty","FontSize"=>"13","Copy"=>"Col_2","Color"=>"#00A945","IconType"=>"13"),
			                     array("Text"=>"$OverTotalQty","FontSize"=>"13","Copy"=>"Col_3","Color"=>"#FF0000","IconType"=>"1")
			                     );
	      }
        $tempArray=array(
				                      "Id"=>"Total",
				                      "Title"=>array("Text"=>"合计","FontSize"=>"14","Bold"=>"1"),
				                      "Col2"=>array("Text"=>"$totalQty","Margin"=>"-30,0,0,0","FontSize"=>"14"),
				                      "Col3"=>array("Text"=>"¥$totalAmount","Margin"=>"-10,0,0,0","FontSize"=>"14"),
				                      "AddRow"=>$AddArray
				                   );
		 $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$tempArray2); 
          array_splice($jsondata,0,0,$totalArray);

   }

$PickArray[]=array("0","全部");   
$PickArray[]=array("TBC","待定");  
$WeekResult = mysql_query("
	 SELECT YEARWEEK(substring(PI.Leadtime,1,10),1)  AS Weeks
     FROM $DataIn.yw1_ordersheet S
     LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
     LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
    WHERE S.Estate >0 AND Year(substring(PI.Leadtime,1,10))>0 $SearchCompany GROUP BY YEARWEEK(substring(PI.Leadtime,1,10),1) ORDER BY Weeks 
",$link_id);
while($WeekRow = mysql_fetch_array($WeekResult)) {
	     $Weeks=$WeekRow["Weeks"];
	      $weekName="Week " . substr($Weeks, 4,2);
	      $PickArray[]=array("$Weeks","$weekName");
 }
   $PickName=($checkWeek==0 || $checkCompanyId>0)?"$Forshort":"Week " . substr($checkWeek, 4,2);
   $PickName=($checkWeek=="TBC" && $checkCompanyId=="")?"待定":$PickName;
  $jsonArray=array("picker"=>array("Style"=>"2","Planar"=>"1","data"=>$PickArray,"Text"=>"$PickName"),"data"=>$jsondata); 
?>