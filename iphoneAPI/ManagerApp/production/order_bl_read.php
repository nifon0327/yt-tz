<?php 
//可备料
$curDate=date("Y-m-d");
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS NextWeek",$link_id));
 $curWeek=$dateResult["NextWeek"];
 
 $nextWeekDate=date("Y-m-d",strtotime("$curDate  +7   day"));
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek",$link_id));
$nextWeek=$dateResult["NextWeek"];

$SearchRows="";
switch($checkWeek){
        case "Over":
          $checkWeekSign=1; 
         break;
        case "Later":
          $SearchRows=" AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)='$nextWeek'";
           $checkWeekSign=1; 
         break;
      default:
          $SearchRows=" AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1)<='$checkWeek'";
        break;
}

if ($LoginNumber == '11965') {
	
	
}
$OrderBySTR=$checkWeekSign==1?" ORDER BY Weeks,CompanyId,POrderId ":" ORDER BY CompanyId,Weeks,POrderId ";
$SearchCompany=$CompnayId>0? " AND M.CompanyId='$CompnayId' ":"";

 //布局设置
$Layout=array( "Title"=>array("Frame"=>"40, 2, 230, 25"),
                          "Col3"=>array("Frame"=>"150,32,48, 15"));

$LockTotalQty=0;$OverTotalQty=0;$OverCount=0;$blCount=0;$curCount=0;

$mySql="SELECT M.CompanyId,M.OrderDate,M.OrderPO,S.POrderId,S.ProductId,S.Qty,S.ShipType,C.Forshort,P.cName,P.TestStandard,S.Price,U.Name AS Unit,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,E.Type,E.Remark, YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks 
FROM $DataIn.yw1_ordermain M
LEFT JOIN $DataIn.yw1_ordersheet S  ON M.OrderNumber=S.OrderNumber
LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
LEFT JOIN $DataPublic.productunit U ON U.Id=P.Unit 
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId
WHERE 1 and S.scFrom>0 AND S.Estate=1  AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) >0 $SearchRows   $SearchCompany
 GROUP BY S.POrderId $OrderBySTR";

    //echo $mySql;
    $curDate=date("Y-m-d");
    //$CountArray=array();
    $nextCount=0;$laterCount=0;
    $dataArray=array(); $jsondata=array(); 
    $viewHidden=0;
    $myResult = mysql_query($mySql,$link_id);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            $sumQty=0;$sumQty1=0;$totalQty=0;
            $sumAmount=0;$sumAmount1=0;$totalAmount=0;
            $SortId=$checkWeekSign==1?$myRow["Weeks"]:$myRow["CompanyId"];
            $SortName=$checkWeekSign==1?"Week " . substr($myRow["Weeks"],4,2):$myRow["Forshort"];
            $oId=$SortId;
            $m=0;$pos=0;
            
            $CompanyId=$myRow["CompanyId"];
            $Forshort=$myRow["Forshort"];
            $oldCompanyId=$CompanyId;
             include "../subprogram/currency_read.php";//$Rate、$PreChar
             $ProductArray=array();
            do 
            {	
                    $POrderId=$myRow["POrderId"];
                    $ProductId=$myRow["ProductId"];
                    
                     $R_EType=$myRow["Type"]==2?2:0;
                     $Remark=$R_EType==2?$myRow["Remark"]:"";
                     
                      $KBLSign=0;$ScLine="";
					        //检查订单备料情况
						$CheckblState=mysql_query("
								SELECT SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1, SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2, SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IF(GL.Id>0,1,0)) AS  Locks,SUM(IFNULL(L.llEstate,0)) AS llEstate
								FROM $DataIn.cg1_stocksheet G 
								LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
								LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
								LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
								LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId=GL.StockId  AND GL.Locks=0 
								LEFT JOIN ( 
								    SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(IF(L.Estate=1,1,0)) AS llEstate 
								    FROM  $DataIn.ck5_llsheet L 
								    WHERE  L.POrderId='$POrderId'  GROUP BY L.StockId 
								  )L ON L.StockId=G.StockId 
								WHERE G.POrderId='$POrderId' AND ST.mainType<2 
								AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')",$link_id);
								//SELECT * FROM () A WHERE A.K1>=A.K2 AND A.blQty!=A.llQty  AND A.Locks=0
						        //if (mysql_num_rows($CheckblState)<=0)	 continue;
						 if($blStateRow = mysql_fetch_array($CheckblState)){
						      $R_K1=$blStateRow["K1"];
						      $R_K2=$blStateRow["K2"];
							  $R_blQty=$blStateRow["blQty"];
							  $R_llQty=$blStateRow["llQty"];
							  $R_llEstate=$blStateRow["llEstate"];
							  $R_Locks=$blStateRow["Locks"];
							
							 $R_llEstate=0;$KBLSign=1;	
							 						 
							  if ($R_Locks>0) $OrderSignColor=2; 
							  
							  if ($R_EType==2) $KBLSign=0;
							  
							  if ($R_blQty==$R_llQty) {
							        if ($R_llEstate==0) $KBLSign=0;// && $R_EType==0   
							  }
							  else{
							           //是否已存在有可备料订单
									  if (in_array($ProductId, $ProductArray)) {
									        $KBLSign=0;  
									   }
									   else{
										      if ($R_K1>=$R_K2 &&  $R_blQty!=$R_llQty && $R_Locks==0){
										              if ($R_EType!=2) $ProductArray[]=$ProductId;   
											  }
											  else{
												    if ($R_EType!=2)  $ProductArray[]=$ProductId; $KBLSign=0;
											  }
									}
							  }
							  $rowColor=($R_blQty==$R_llQty && $R_llEstate>0)?"#F3EBC4":"";
					 } 
				 
				  $ScLineResult=mysql_query("SELECT G.GroupName,S.Estate FROM $DataIn.sc1_mission S
											   LEFT JOIN $DataIn.staffgroup G ON G.Id=S.Operator 
											   WHERE S.POrderId='$POrderId' AND G.Id>0",$link_id);
					if($ScLineRow = mysql_fetch_array($ScLineResult)){
					      $GroupName=$ScLineRow ["GroupName"];
					      $Sc_Estate=$ScLineRow ["Estate"];
					      $ScLine=substr($GroupName,-1);
					      if ($Sc_Estate==1){
						       $blCount++; 
						       if ($checkWeek=="BL")  $KBLSign=1;
					    }
					}
					else{
						   if ($checkWeek=="BL")  $KBLSign=0;
					}
				  
				  if ($KBLSign==1){
				     // if ($myRow["Weeks"]==$curWeek) $curCount++;  if ($myRow["Weeks"]=$nextWeek) $nextCount++; else if ($myRow["Weeks"]>$nextWeek) $laterCount++;
				     if ($myRow["Weeks"]==$curWeek) $curCount++;  else  if ($myRow["Weeks"]==$nextWeek) $nextCount++; 
				       //$WeekSTR= substr($myRow["Weeks"],4,2);
					   //$CountArray[$WeekSTR]=$CountArray[$WeekSTR]==""?1:$CountArray[$WeekSTR]+1;
                      //$$WeekCount=$$WeekCount==""?1:$$WeekCount+1;
                   }
                   
				  if ($myRow["Weeks"]>=$curWeek && $checkWeek=="Over")	 continue;
				  
                  if ($KBLSign==1){
                    if ($checkWeek>0 && $checkWeek!=$myRow["Weeks"])  continue;
                   // if ($checkWeek=="Later" && $myRow["Weeks"]<$nextWeek) continue;
	                //if ($checkWeek=="Later" && $myRow["Weeks"]<=$nextWeek) continue;
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
                  
	  
	                   //取得备料时间
                 	  $FromWebPage=$R_blQty==$R_llQty?"LBL":"KBL";
	                   include "../../admin/order_datetime.php";
	                   $BlDate=$R_blQty==$R_llQty?$lbl_Date:$kbl_Date;
	                   //$workHours=$kbl_Hours==""?0:$kbl_Hours;
	                   //$colorSign=$kbl_Hours>=$default_blhours?4:0;
	                     //$Date=substr($BlDate, 5, 2) ."/". substr($BlDate, 8, 2) . " " . substr($BlDate, 11,5);
	                     $Date=GetDateTimeOutString($BlDate,'');
	                     $DateColor=$kbl_Hours>=$default_blhours?"#FF0000":"";
	                     //$Date=$kbl_Hours>=$default_blhours?"$Date"."|#FF0000":$Date;	 
	                    
	                   //下单到现在时间
                        $odDays=(strtotime($curDate)-strtotime($OrderDate))/3600/24;
	                     if ($Leadtime!=""){
		                     $colorSign=$curDate>=$Leadtime?4:0;
	                     }
	                     else{
		                      $colorSign=0;
	                     }
                    
                    $OrderSignColor=0;$cgRemark="";
                    
                    if ($Locks==2)  $OrderSignColor=4; 					
					$Remark.=$cgRemark;	
					$CompanyId=$myRow["CompanyId"];	

					$SortId=$checkWeekSign==1?$myRow["Weeks"]:$myRow["CompanyId"];
                      if ($checkWeekSign==1 &&  ($oldCompanyId!=$CompanyId || $oId!=$SortId)){
                         if ($sumQty1>0){
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
						      }
						       $tempArray1=array();
						       $sumQty1=0;$sumAmount1=0;
						       $oldCompanyId=$CompanyId;
						       $Forshort=$myRow["Forshort"];
						       include "../subprogram/currency_read.php";//$Rate、$PreChar
	                          
                      }
                      
                     if ($oId!=$SortId){  
                             // if (!in_array($oId, $PickIdArray)) $PickArray[]=array($oId,$SortName);
                             if($sumQty>0){
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
		                             }
		                           $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","Layout"=>$Layout,"data"=>$dataArray); 
                             }

                             $m=0;
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
						
                      $ShipType=$myRow["ShipType"];
                      $timeColor=$curDate>=$Leadtime?"#FF0000":"";
                      $Leadtime=date("m/d",strtotime($Leadtime)) . "|$timeColor";
                      $QtySTR=number_format($Qty);
                      $Weeks=substr($myRow["Weeks"],4,2);
                      //$cName=date("Y-m-d H:m:s");
                      if ($myRow["Weeks"]<$curWeek){
	                       $OverTotalQty+=$Qty;
                      }
                       if ($myRow["Weeks"]<$curWeek){
                            $OverCount++;
                      }
                      $bgColor=$myRow["Weeks"]<$curWeek?"#FF0000":"";//#00BA61

                       include "submodel/stuff_factualqty_bgcolor.php";
                      $tempArray=array(
                      "Id"=>"$POrderId",
                      "RowSet"=>array("bgColor"=>"$rowColor"),
                       "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor","iIcon"=>"$Locks","Badge"=>"$ScLine"),
                      "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                      "Col1"=> array("Text"=>"$OrderPO"),
                      "Col3"=>array("Text"=>"$QtySTR","bgColor"=>"$FactualQty_Color"),
                      "Col5"=>array("Text"=>"$Date","Color"=>"$DateColor"),
                      "Remark"=>array("Text"=>"$Remark"),
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
                 }
            } while($myRow = mysql_fetch_assoc($myResult));
            
           if ($checkWeekSign==1 && $sumQty1>0){
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
   
          if ($sumQty>0){
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
		         $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","Layout"=>$Layout,"data"=>$dataArray); 
         }
         $totalQty=number_format($totalQty);
         $LockTotalQty=$LockTotalQty>0?number_format($LockTotalQty):"";
         $OverTotalQty=$OverTotalQty>0?number_format($OverTotalQty):"";
	     //$totalAmount=number_format(sprintf("%.2f",$totalAmount));
        $tempArray=array(
				                      "Id"=>"Total",
				                      "Title"=>array("Text"=>"总计","FontSize"=>"14","Bold"=>"1"),
				                      "Col1"=>array("Text"=>"$LockTotalQty","IconType"=>"12","Margin"=>"-15,0,0,0","Color"=>"#FF0000","FontSize"=>"14"),
				                      "Col2"=>array("Text"=>"$OverTotalQty","Margin"=>"-10,0,0,0","Color"=>"#FF0000","FontSize"=>"14"),
				                      "Col3"=>array("Text"=>"$totalQty","Margin"=>"-10,0,0,0","FontSize"=>"14")
				                   );
		 $tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
         $totalArray[]=array("data"=>$tempArray2); 
          array_splice($jsondata,0,0,$totalArray);
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

 $WeekSTR= substr($curWeek,4,2);
 $SegmentArray[]="本周(" . $curCount . ")";
 $SegmentIdArray[]=$curWeek;
 
 $WeekSTR= substr($nextWeek,4,2);
 $SegmentArray[]=$WeekSTR . "周(" . $nextCount . ")";
 $SegmentIdArray[]="Later";
 //$SegmentIdArray[]=$nextWeek;
 
 /*
 $laterDate=date("Y-m-d",strtotime("$curDate  +14 day"));
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$laterDate',1) AS laterWeek",$link_id));
 $laterWeek=$dateResult["laterWeek"];
 $WeekSTR= substr($laterWeek,4,2);
     
 $SegmentArray[]=$WeekSTR . "周+(" . $laterCount . ")";
 $SegmentIdArray[]="Later";
*/

 $ActionArray=array();
  if (($LoginNumber=='10008' || $LoginNumber=='10868' || $LoginNumber=='10341' || $LoginNumber=='11965') &&  $checkWeek=="Later"){
	        $checkLockResult = mysql_query("SELECT * FROM $DataIn.yw9_blunlock WHERE WeekName='$nextWeek' AND Locks=0",$link_id);
	         if($checkLockRow = mysql_fetch_array($checkLockResult)){
	                $ActionArray=array("Id"=>"Lock","Title"=>"锁定","Value"=>"$nextWeek|1","oTitle"=>"解锁","Width"=>"45");
	         }
	         else{
	                $ActionArray=array("Id"=>"Lock","Title"=>"解锁","Value"=>"$nextWeek|0","oTitle"=>"锁定","Width"=>"45");
	         }
 }
 //$WeekCount="Week_" .$curWeek;
 //$SegmentArray[]="本周(" . $$WeekCount . ")"; $SegmentIdArray[]=$curWeek;
 /*

$AllQty=$OverCount;
$nextDate=date("Y-m-d");
for($i=1;$i<3;$i++){
    $nextDate=date("Y-m-d",strtotime("$nextDate  +7 day"));
    $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextDate',1) AS NextWeek",$link_id));
    $nextWeek=$dateResult["NextWeek"];
    //$WeekCount="Week_" .$nextWeek;
    $WeekSTR= substr($nextWeek,4,2);
    $WeekCount=$CountArray[$WeekSTR]==""?0:$CountArray[$WeekSTR];
   // $$WeekCount=$$WeekCount ==""?0:$$WeekCount;
    $AllQty+=$WeekCount;
	 $SegmentArray[]=$WeekSTR . "周(" . $WeekCount . ")";
	 $SegmentIdArray[]=$nextWeek;
	 
	 if (($LoginNumber=='10008' || $LoginNumber=='10868' || $LoginNumber=='10341') &&  $checkWeek==$nextWeek && $i==2){
	        $checkLockResult = mysql_query("SELECT * FROM $DataIn.yw9_blunlock WHERE WeekName='$checkWeek' AND Locks=0",$link_id);
	         if($checkLockRow = mysql_fetch_array($checkLockResult)){
	                $ActionArray=array("Id"=>"Lock","Title"=>"锁定","Value"=>"$checkWeek|1","oTitle"=>"解锁","Width"=>"45");
	         }
	         else{
	               $ActionArray=array("Id"=>"Lock","Title"=>"解锁","Value"=>"$checkWeek|0","oTitle"=>"锁定","Width"=>"45");
	         }
	 }
}
*/
//$SegmentArray[]="待备料($blCount)";
//$SegmentIdArray[]="BL";

//array_splice($SegmentArray,0,0,"全部($AllQty)");
//array_splice($SegmentIdArray,0,0,"0");
//$SegmentArray[]="全部($AllQty)";
//$SegmentIdArray[]="0";
//"picker"=>array("Style"=>"2","Planar"=>"1","data"=>$PickArray),
  $jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray),"Action"=>$ActionArray,"data"=>$jsondata); 
?>