<?php 
//待组装、加工明细
$curDate=date("Y-m-d");$curDateTime=date("Y-m-d H:i:s");
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS NextWeek",$link_id));
 $curWeek=$dateResult["NextWeek"];
 
 $SearchWeek=$checkWeek>0 ?" AND YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)='$checkWeek'":"";
 $checkWeekSign=$checkWeek=="Over"?1:0;
 $SearchWeek=$checkWeek=="Over"?" AND YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)<'$curWeek'":$SearchWeek;

$OrderBySTR=$checkWeekSign==1?" ORDER BY Weeks,CompanyId,Leadtime ":" ORDER BY CompanyId,Leadtime ";
 
$SearchCompany=$CompnayId>0? " AND M.CompanyId='$CompnayId' ":"";
$newData=array();
//$PickArray[]=array("0","全 部");
//$PickIdArray=array();
$numberArr = array();
$drkList= array();
$groupsSQL = "Select B.GroupName,B.Id as GPid,B.GroupLeader, C.Name From $DataIn.staffgroup B 
				   Left Join $DataPublic.staffmain C On B.GroupLeader = C.Number
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
		$numberArr[] = array("$GroupName"=>"$GroupLeaderNum");
}
$LockTotalQty=0;$OverTotalQty=0;$OverCount=0;
//$SearchRows=$LoginNumber==10868?" OR A.tqty>0":"";
//SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2 
  if (1){
  //待组装
				  $mySql="select * from (SELECT sum(T.Qty) as tqty,S.Qty, M.CompanyId,M.OrderPO,M.OrderDate,
				  S.Id,S.POrderId,S.ProductId,S.sgRemark,C.Forshort,P.cName, P.Weight,P.InspectionSign,
				  P.eCode,P.TestStandard,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,
				  YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1)  AS Weeks 
				  FROM $DataIn.yw1_ordersheet S 
				LEFT JOIN  $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
				LEFT JOIN  $DataIn.trade_object C ON C.CompanyId=M.CompanyId  
				LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
				LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId   
				LEFT JOIN  $DataIn.productdata P ON P.ProductId=S.ProductId
				LEFT JOIN  $DataIn.sc1_cjtj  T ON T.POrderId=S.POrderId
				WHERE 1  AND S.Estate=1 and T.TypeId='7100' group by S.POrderId ) A where A.tqty=A.Qty  order by Weeks";
         
		}

  //if ($LoginNumber==10868) echo $mySql;
    $curDate=date("Y-m-d");
    $dataArray=array(); $jsondata=array(); 
    $viewHidden=0;$SortArray=array();
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
            do 
            {	
			$onlyRemark=false;
                    $POrderId=$myRow["POrderId"];
                    $OrderPO=$myRow["OrderPO"];
                    $cName=$myRow["cName"];
					
					
					 $ProductId=$myRow["ProductId"];
					
					$productEcode = $myRow["eCode"];
					$Weight = $myRow["Weight"];
                     $productId=$myRow["ProductId"];
					$AppFilePath="http://www.middlecloud.com/download/teststandard/T" .$ProductId.".jpg";
					    $Weight=(float)$Weight;
                    $WeightSTR="";
                   
                      include "../../model/subprogram/weightCalculate.php";
                      if ($Weight>0){
	                       $extraWeight=$extraWeight == "error"?"":$extraWeight+($Weight*$boxPcs); 
	                       $WeightSTR=$Weight>0?"$productEcode|$Weight|$boxPcs|$extraWeight":"";
                      }
                    $Qty = $myRow["Qty"];
                    $Unit=$myRow["Unit"]=="PCS"?"pcs":$myRow["Unit"];
                    $Price=$myRow["Price"];
                    $Amount=sprintf("%.2f",$Qty*$Price);		

                    $OrderDate=$myRow["OrderDate"];
                    $Leadtime=str_replace("*", "", $myRow["Leadtime"]);
                    $TestStandard=$myRow["TestStandard"];
                    include "order/order_TestStandard.php";
                    
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
	  
	                  
	                   
	                   //下单到现在时间
	                  
                        $odDays=(strtotime($curDate)-strtotime($OrderDate))/3600/24;
	                     if ($Leadtime!=""){
		                     $colorSign=$curDate>=$Leadtime?4:0;
	                     }
	                     else{
		                      $colorSign=0;
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
		               $rowColor=$noJson?"858C95":"";//F3EBC4
		               //生产数量
					  
		               $ScQty=$Qty;$ScLine="";$ScBoxs=0;
		               $ScQtyResult=mysql_query("SELECT boxId,MAX(Date) as MaxDate,COUNT(*) AS boxs FROM $DataIn.sc1_cjtj WHERE POrderId='$POrderId' AND TypeId='7100' limit 0,1",$link_id);
						if($ScQtyRow = mysql_fetch_array($ScQtyResult)){
						       $ScLine=substr($ScQtyRow["boxId"], 0,1);
							   $MaxDate = $ScQtyRow["MaxDate"];
							   $ScBoxs= $ScQtyRow["boxs"];
						}
					
					if ($leadNum == NULL || $leadNum == "") {
						foreach ($listGroup as $lineArr) {
							if ($ScLine == $lineArr[0]) {
								$leadNum = $lineArr[2];
								break;
							}
						}
					}
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
                      
                      $InspectionSign=$myRow["InspectionSign"];//客户检验标记
                      $Inspection=0;
                      if ($InspectionSign==1){
	                      $checkInspection=mysql_query("SELECT Inspection FROM $DataIn.yw1_productinspection WHERE POrderId='$POrderId' ORDER BY Id DESC LIMIT 1",$link_id);
	                      if($checkInspectionRow = mysql_fetch_array($checkInspection)){
	                            $Inspection=$checkInspectionRow["Inspection"];
	                      }
                      }
                      
                      // $InspectionSign=$LoginNumber==10868?1:$InspectionSign;
                     //$Inspection =$LoginNumber==10868?2:$Inspection;
                      //箱数
                     
                      $RLText=versionToNumber($AppVersion)>302?"($ScBoxs)":"";
                      
                       include "submodel/stuff_factualqty_bgcolor.php";
                    $Date=GetDateTimeOutString($MaxDate,'');
				    $Forshort=$myRow["Forshort"];
					   $title2 = (($Forshort!=NULL && $Forshort!="")?"$Forshort-":"");
					  // $title2="";
                      $tempArray=array(
                      "Id"=>"$POrderId","line"=>"$ScLine","leadNum"=>"$leadNum",
                      "RowSet"=>array("bgColor"=>""),
                       "weeks"=>array("Text"=>"$Weeks","bg"=>"$bgColor","iIcon"=>"$Locks"),
                      "Title"=>array("Text"=>"$title2$cName","Color"=>"$TestStandardColor"),
					  	"Title2"=>"$title2","2Color"=>"358FC1",
                      "Col1"=> array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
                      "Col2"=>array("Text"=>"$OrderPO","Frame"=>"47,27,58,15"),
                      "Col3"=>array("Text"=>"$QtySTR","bgColor"=>"$FactualQty_Color"),
					  "Col4"=>array("Text"=>"$ScQty","RLText"=>"$RLText","RLColor"=>"#358FC1"),"icon4"=>"scdj_12",
                      "Col5"=>array("Text"=>"$Date","Color"=>"358FC1"),"icon3"=>"scdj_11",
                      "Remark"=>array("Text"=>"$Remark","Date"=>"$RemarkDate","Operator"=>"$RemarkOperator"),
                      "Inspection"=>array("Sign"=>"$InspectionSign","Estate"=>"$Inspection")
                       // "rTopTitle"=>array("Text"=>"$odDays"."d","Margin"=>"-22,0,0,0","Color"=>"#0000FF"),
                        //"rIcon"=>"ship$ShipType"
                   );
                   
                   if ($InspectionSign==1){
                       switch($Inspection){
                            case 2:
                               $swapDict=array("Right"=>"358FC1-入库,FF0000-检验");
                               $CellID="dat112";
                               break;
                           case 0:
                               $swapDict=array();
                               $CellID="no";
                               break;
                           default:
                              $swapDict=array("Right"=>"358FC1-入库");
                              $CellID="dat11";
                              break;
                       }
                   }
                   else{
	                    $swapDict =array("Right"=>"358FC1-入库");
	                    $CellID="dat11";
                   }
                   
				   $onEdit=-1;
				   $noStar=1;  $distriStar = true;
				   include "order_detail_items.php";
				
				   	//	 $swapDict = array("Right"=>"FF0000-设置当前任务,358FC1-备注");
				   		$newData[]=array("Tag"=>"data","data"=>$tempArray,"CellID"=>"$CellID","sbID"=>"data2-",
						"Args"=>"$POrderId",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_blue","value"=>"1","Args"=>"$POrderId"),"List"=>$products,"Swap"=>$swapDict,
				    "Tap"=>"0",
				   "TapImg"=>array("File"=>"$AppFilePath","Args"=>"$WeightSTR")
				   );
				   
                  // $dataArray[]=array("Tag"=>"data","onEdit"=>"2","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);
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
	                         
						        //array_splice($dataArray,$pos,0,$tempArray1);	                          
                      }
          $SortArray[$oId]=$sumQty;
           $sumQty=number_format($sumQty);
           $sumAmount=number_format($sumAmount);
           if ($checkWeekSign==1){
               $bgColor=$SortId==$curWeek?"#CCFF99":"";
               $dateArray= GetWeekToDate($oId,"m/d");
                $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
             
         }
         else{
            
         }
    
        
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
		$drkList =  $newData;
		
         $totalQty=number_format($totalQty);
         $LockTotalQty=$LockTotalQty>0?number_format($LockTotalQty):"";
         $OverTotalQty=$OverTotalQty>0?number_format($OverTotalQty):"";
		 $overQty = $OverTotalQty;
		 $SumTotalValue = $totalQty."pcs";
	     //$totalAmount=number_format(sprintf("%.2f",$totalAmount));
        $tempArray=array(
				                      "Id"=>"Total",
				                      "Title"=>array("Text"=>"总计","FontSize"=>"14","Bold"=>"1"),
				                      
				                      "Col2"=>array("Text"=>"$OverTotalQty","Color"=>"#FF0000","FontSize"=>"14"),
				                      "Col3"=>array("Text"=>"$totalQty","FontSize"=>"14")
				                   );
		 $tempArray2[]=array("Tag"=>"total","data"=>$tempArray,"CellID"=>"Tal");
         //$totalArray[]=array("data"=>$tempArray2); 
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
 

if ($noJson!=true)
  $jsonArray=array("cellList"=>$newData); 
?>