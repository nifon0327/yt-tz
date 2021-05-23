<?php
//补料单	
include "../../basic/downloadFileIP.php";
	$curDate=date("Y-m-d");
	$COUNT_0=$COUNT_1=0;	
	
   $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS ThisWeek",$link_id));
   $thisWeek = $dateResult["ThisWeek"];
	
	if ($SegmentIndex==1 && $FromMainPage=="production"){
		include "ck_replenish_sub1.php";
	}
	else{
	$LimitCz = "";
		   if ($FromMainPage=="production"){
		       $SearchRows=" R.Estate>=1 ";
		       if ($LoginNumber == 11965) {
			     //  $SearchRows = " 1 ";
			       //$LimitCz = " limit 20 ";
		       }
		   }
		   else{
		      $SearchRows=$SegmentIndex==1 ?" R.Estate=1  AND R.Lid>0":" R.Estate=1 AND R.Lid=0 ";
		   }
			
		   $mySql="SELECT R.Id,R.POrderId,R.StuffId,R.Qty,R.Remark,R.Estate,R.OPdatetime,R.Operator,R.Lid,R.ReturnReasons,
						   S.OrderPO,S.Qty AS OrderQty,S.ProductId,D.cName,D.TestStandard,P.Forshort,
						    IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks,
						    A.StuffCname,A.Picture,K.tStockQty 
						    FROM $DataIn.ck13_replenish R
						    LEFT JOIN $DataIn.yw1_ordersheet  S ON S.POrderId=R.POrderId
						    LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
						    LEFT JOIN $DataIn.productdata D ON D.ProductId=S.ProductId
						    LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
							LEFT JOIN $DataIn.yw3_pileadtime PL On PL.POrderId = S.POrderId
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId 
							LEFT JOIN $DataIn.stuffdata A ON A.StuffId=R.StuffId
							LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=R.StuffId 
							WHERE $SearchRows  ORDER BY R.POrderId,R.Id $LimitCz";
		
			$Result=mysql_query($mySql, $link_id); 
			$oldPOrdrId="";$TotalQty=0;$m=0;$dataArray=array();
			
			while($myRow = mysql_fetch_array($Result)) 
			{
				$eachLeft = 0;
			     $Id= $myRow["Id"];
			     $POrderId = $myRow["POrderId"];
			     if ($POrderId!=$oldPOrdrId){
				     
				        $cName=$myRow["cName"];
				        $OrderQty=number_format($myRow["OrderQty"]);
				        $Forshort=$myRow["Forshort"];
				        $OrderPO=$myRow["OrderPO"];
				        
				        $ProductId=$myRow["ProductId"];
				        $TestStandard=$myRow["TestStandard"];
				        include "order/order_TestStandard.php";
				        $Weeks=$myRow["Weeks"]==""?" ":substr($myRow["Weeks"],4,2);
				        $bgColor=$thisWeek>$myRow["Weeks"]?"#FF0000":"#FF0000";
				        
				       $addArray=array();
					   $addArray[]=array("Copy"=>"Title","Text"=>"$Forshort","Color"=>"$FORSHORT_COLOR");
					   $addArray[]=array("Copy"=>"Col_2","Text"=>"$OrderPO","Margin"=>"-50,0,0,0");
					   $addArray[]=array("Copy"=>"Col_3","Text"=>"$OrderQty","LIcon"=>"scdj_1","Margin"=>"-20,0,0,0");
						
						$ScLine="";
						$ScLineResult=mysql_query("SELECT G.GroupName FROM $DataIn.sc1_mission S
									   LEFT JOIN $DataIn.staffgroup G ON G.Id=S.Operator 
									   WHERE S.POrderId='$POrderId' AND G.Id>0",$link_id);
									if($ScLineRow = mysql_fetch_array($ScLineResult)){
									      $GroupName=$ScLineRow ["GroupName"];
									      $ScLine=substr($GroupName,-1);
									}
						$iconArray=array();			
						$iconArray[]=array("Text"=>"$ScLine","bgColor"=>"$FORSHORT_COLOR") ; 
						if ($FromPage=="production"){
							 $iconArray[]=$myRow["Lid"]>0?array("Text"=>"备","bgColor"=>"$FORSHORT_COLOR"):array("Text"=>"备") ; 
							 $iconArray[]=$myRow["Estate"]==1?array("Text"=>"审","bgColor"=>"$FORSHORT_COLOR"):array("Text"=>"审") ; 
						}
							 $Estate = $myRow['Estate'];
							 $Estate = $Estate == '0' ? "1":$Estate;
				        $tempArray=array(
					        "left_sper"=>"40",
						      "Id"=>"$POrderId",
						       "Index"=>array("Text"=>"$Weeks","bgColor"=>"$bgColor"),
						       "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
						       "AddRow"=>$addArray,
						       "RoundIcons"=>$iconArray,'Estate'=>array('val'=>$Estate,'Frame'=>'300,20,12,12')
						   );
					
						   $dataArray[]=array("Tag"=>"Total","onTap"=>array("Target"=>"Order","Args"=>"$POrderId"),"data"=>$tempArray);
						   $oldPOrdrId=$POrderId;
			    } else {
				    $eachLeft = 45;
				    $numsall = count($dataArray);
				    if($dataArray[$numsall-1]["Tag"]=="remark1") {
					    $dataArray[$numsall-1]["left_sper"] = "50";
				    }
			    }
			    
			    $StuffId=$myRow["StuffId"];
			    $StuffCname=$myRow["StuffCname"];
			    $Picture = $myRow["Picture"];
			    include "submodel/stuffname_color.php";
			    $ImagePath=$Picture>0?"$donwloadFileIP/download/stufffile/".$StuffId. "_s.jpg":"";
			     
			    $Qty=$myRow["Qty"];
			    $TotalQty+=$Qty;
			    
			    if ($FromMainPage=="production"){
				     $onEdit=$myRow["Lid"]>0?5:7;  
				       if ($LoginNumber == 11965) {
				       $onEdit = 7;
				       }
			    }
			    else{
				    $onEdit=$myRow["tStockQty"]>=$Qty && $SegmentIndex==0?4:0;    
			    } 	    
			    
			    $llQty=0;
			    if ($myRow["Lid"]>0){
				    $llQtyResult=mysql_fetch_array(mysql_query("SELECT Qty FROM $DataIn.ck5_llsheet WHERE Id='" .$myRow["Lid"] . "'",$link_id));
				    $llQty=$llQtyResult["Qty"]==""?0:number_format($llQtyResult["Qty"]);
			    }
	
			    $tStockQty=number_format( $myRow["tStockQty"]);
			    $Remark=$myRow["Remark"];
			    $Operator=$myRow["Operator"];
			    include "../../model/subprogram/staffname.php";    
			    $OperDate=$myRow["OPdatetime"];
			    $OperDate = GetDateTimeOutString($OperDate,'');
			    $ReturnReasons = $myRow["ReturnReasons"];
			    $tempArray=array(
						       "Id"=>"$Id",'has2'=>'1',
						       "Litimg"=>array("Path"=>"$ImagePath"),
						       "Title"=>array("Text"=>"$StuffId-$StuffCname      ","Color"=>"$StuffColor"),
						       "Col1"=> array("Text"=>"$tStockQty","LIcon"=>"itstock_gray","Margin"=>"10,0,0,0"),
						       "Col2"=>array("Text"=>" $Qty","LIcon"=>"ibh_gray","Margin"=>"14,0,0,0",'Color'=>'#FF0000'),
						       "Col5"=>array("Text"=>" $llQty","LIcon"=>"ibl_gray","Margin"=>"8,0,0,0","Align"=>"L","Color"=>"$TEXT_GREENCOLOR"),
						        "Remark"=>array("Text"=>"","Date"=>"","Operator"=>"",
						                                      "Color"=>"","Icon"=>""),
						   );
						   $dataArray[]=array("Tag"=>"stuff", "onTap"=>array("Target"=>"NewStuffDetail","Args"=>"$StuffId","File"=>"$ImagePath"),"data"=>$tempArray,"onEdit"=>"$onEdit",'rmk'=>"$Remark");
						   $m++;
						   
						   
						   $dataArray[]=array("Tag"=>"remark1",
						   	"RID" => $Remark==""?$Remark:"-1",
						   	"Record" => "\n$Remark\n",
						   	"Recorder" => "$OperDate",
						   	"anti_oper"=>"$Operator",
						   	"headline"=>"补料原因：",
						   	"reason"=>$ReturnReasons!=""?"\n"."$ReturnReasons":"",
						   	'left_sper'=>"0"
						   	);
						   
						   /*
							   $eventList[] = array(
			"Xib"=>"1","ID"=>"$eventId",
			"RID" => "$RecordId",
			"Record" => "$Record",
			"Recorder" => "$Recorder",
			"EndTime" => "$EndTime", 
			"Files" =>"$FileNames",
			"EventType" => $myRow["EventType"],
			"EventDateTime" => $myRow["EventDateTime"],
			"FileArray"=>$FileArr,"CanEdit"=>"$canEditRecord",
			"Remark" => "","AllowEdit" =>"","NotifyTime" =>"",
			"Share"=>"$ShareWithNms","ShareIds"=>"$ShareWith","CanShare"=>"$canShare"
		);

						   */
						   
			 }
			 
			 $TotalQty=number_format($TotalQty);
			 $headArray=array(
			                                  "RowSet"=>array("height"=>"30"),
						                      "Title"=>array("Text"=>" 合计","FontSize"=>"14"),
						                      "Col3"=>array("Text"=>"$TotalQty($m)","Frame"=>"205, 2, 100, 30")
						                   ); 
						                   
		      $CountSTR="COUNT_" . $SegmentIndex;
		      $$CountSTR=$m;
		    
		      if ($SegmentIndex==0){
		          if ($FromMainPage=="production"){
			           $curMonth=date("Y-m");
				       $CountResult=mysql_fetch_array(mysql_query("SELECT  COUNT(*) AS Counts   
						FROM  $DataIn.ck13_replenish R   
						WHERE  DATE_FORMAT(R.Date,'%Y-%m')='$curMonth' AND R.Estate=0",$link_id));
			           $COUNT_1=$CountResult["Counts"]==""?0:$CountResult["Counts"];
		          }
		          else{
			          $CountResult=mysql_fetch_array(mysql_query("SELECT  COUNT(*) AS Counts   
						FROM  $DataIn.ck13_replenish R   
						WHERE   R.Estate>0 AND R.Lid>0 ",$link_id));
			           $COUNT_1=$CountResult["Counts"]==""?0:$CountResult["Counts"];
		          }
		      }
		      
		      $jsondata[]=array("head"=>$headArray,"data"=>$dataArray);
      }
      
	  
	  $SegmentArray=$FromMainPage=="production"?array("待处理($COUNT_0)","已处理($COUNT_1)"):array("待备料($COUNT_0)","待领料($COUNT_1)");
	  $SegmentIdArray=array("0","1");
	  $jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray,"SegmentIndex"=>"$SegmentIndex"),"data"=>$jsondata); 
?>