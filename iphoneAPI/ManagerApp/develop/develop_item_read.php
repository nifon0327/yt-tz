<?php 
//开发管理
 $curDate=date("Y-m-d");
 $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS NextWeek",$link_id));
 $curWeek=$dateResult["NextWeek"];
  
  if ($GroupId==""){
          $GroupResult=mysql_query("SELECT GroupId FROM $DataPublic.staffmain WHERE Number='$LoginNumber' LIMIT 1",$link_id);
          if($GroupRow = mysql_fetch_assoc($GroupResult)){
               $GroupId=$GroupRow["GroupId"];
          }
  }
  //$GroupId=$GroupId==""?"501":$GroupId;
  switch($GroupId){
     case "102":
        $GroupName="设计";$Jobid="N";
        $SegmentIndex=3;  
         break;
	 case "502":
	      $Jobid="27";$GroupName="开发B";
	      $SegmentIndex=1;
	     break;
	 case "503":
	      $SegmentIndex=2;
	      $Jobid="34";$GroupName="开发C";
	     break;
	  default:
	      $GroupId=501;
	      $Jobid="5";$GroupName="开发A";
	      $SegmentIndex=0;
	     break;
  }
  $SearchRows=" AND A.GroupId='$GroupId' ";
  $SearchPicRows=" AND A.PicJobid='$Jobid' ";
  $SearchGicRows=" AND A.GicJobid='$Jobid' ";
  
  //新产品开发
  
 //图标设置    
  $Layout=array( "Title"=>array("Frame"=>"40, 2, 250, 25"),
                          "Col1"=>array("Frame"=>"40,32,100, 15","Align"=>"L"));                                         
 $IconSet=array("Col3"=>array("Name"=>"scdj_1")); 
  $viewHidden=0;$jsondata=array();
  
  if ($Jobid=="N"){
        include "develop_item_sub.php"; 
  }
  else{
		 $mySql="SELECT A.Id,A.GroupId,A.Remark,YEARWEEK(A.Targetdate,1)  AS Weeks,YEARWEEK(A.Date,1)  AS xdWeeks,
		 A.Number,A.Date,A.Finishdate,S.StuffId,S.Price,S.StuffCname,P.Forshort,M.Name AS OperatorName 
									    FROM  $DataIn.stuffdevelop A
										LEFT JOIN  $DataIn.stuffdata S  ON S.StuffId=A.StuffId 
										LEFT JOIN $DataIn.bps B ON B.StuffId=A.StuffId
										LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
										 LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator 
										WHERE  A.Estate>0 AND S.DevelopState=1 $SearchRows ORDER BY Weeks,Number";
		$myResult = mysql_query($mySql,$link_id);
		 if($myRow = mysql_fetch_assoc($myResult)){
		       $tempArray=array(
						                      "Id"=>"Total",
						                      "Title"=>array("Text"=>"开发中","FontSize"=>"14","Color"=>"#0066FF")
						                   );
				$tempArray2[]=array("Tag"=>"Total","data"=>$tempArray);
				$jsondata[]=array("data"=>$tempArray2); 
				
				$oldWeeks=$myRow["Weeks"];
				$oldNumber=$myRow["Number"];
				$dataArray=array();
				$weekCount=0;$overCount=0;$pos=0;$posCount=0;
		    do{
		          $StuffId=$myRow["StuffId"];
		         $Forshort=$myRow["Forshort"];
		         $StuffCname=$myRow["StuffCname"];//配件名称
		         $Price=$myRow["Price"];
		         $Remark=$myRow["Remark"];
			     $Weeks=$myRow["Weeks"];
			      
			    $overCount=$overCount==0?"":$overCount;
			    
			     $Number=$myRow["Number"];
		       if ($oldNumber!=$Number || $Weeks!=$oldWeeks){
		           $Operator=$oldNumber;
		          include '../../model/subprogram/staffname.php';
			        $tempArray=array(
						                      "Id"=>"$oldNumber",
						                      "Title"=>array("Text"=>"$Operator","Color"=>"#000000","FontSize"=>"14","Bold"=>"1")
						                       //"Col3"=>array("Text"=>"$posCount")
						                   );
			        $tempArray1[]=array("Tag"=>"Total","data"=>$tempArray);
			        array_splice($dataArray,$pos,0,$tempArray1);
			        $pos=count($dataArray);
			        
			        $oldNumber=$myRow["Number"];
			        $posCount=0;
			        $tempArray1=array();
		       } 
		
		
		        if ($Weeks!=$oldWeeks){
		                 $WeekSTR="Week " . substr($oldWeeks,4,2);
		                 $dateArray= GetWeekToDate($oldWeeks,"m/d");
		                 $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		                 
		                $bgColor2=$oldWeeks==$curWeek?$CURWEEK_BGCOLOR:"";
		                $Color3=$oldWeeks<$curWeek?"#FF0000":"";
		                $headArray=array(
		                  "Id"=>"$GroupId",
		                  "onTap"=>"1",
		                   "RowSet"=>array("bgColor"=>"$bgColor2"),
		                  "Title"=>array("Text"=>"$WeekSTR","FontSize"=>"14","Bold"=>"1","BelowTitle"=>"$dateSTR"),
		                  "Col1"=>array("Text"=>"$overCount","Color"=>"#FF0000"),
		                  "Col3"=>array("Text"=>"$weekCount","FontSize"=>"14","Color"=>"$Color3")
		               );
		               $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","Layout"=>$Layout,"IconSet"=>$IconSet,"data"=>$dataArray); 
		               $oldWeeks=$myRow["Weeks"];
		               $weekCount=0;$overCount=0;
		               $dataArray=array();$pos=0;
		     }
		      $WeekSTR=substr($myRow["Weeks"],4,2); 
		      $bgColor=$myRow["Weeks"]<$curWeek?"#FF0000":"";
		      $Id=$myRow["Id"];
		      $Date=$myRow["Date"];
		      $xdWeekSTR=substr($myRow["xdWeeks"],4,2) . "周";
		      
		      $QtyResult=mysql_fetch_array(mysql_query("SELECT SUM(OrderQty) AS Qty FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId'",$link_id));
		      $Qty=$QtyResult["Qty"];
		      
		      $OperatorName=$myRow["OperatorName"];
		        $tempArray=array(
		                       "Id"=>"$Id",
		                       "Index"=>array("Text"=>"$WeekSTR","bgColor"=>"$bgColor"),
		                      "Title"=>array("Text"=>"$StuffId-$StuffCname"),
		                      "Col1"=> array("Text"=>"$Forshort"),
		                      "Col3"=>array("Text"=>"$Qty"),
		                      "Col5"=>array("Text"=>"$xdWeekSTR","Color"=>"#0000FF"),
		                      "Remark"=>array("Text"=>"$Remark","Date"=>"$Date","Operator"=>"$OperatorName")
		                      //"rIcon"=>"ship$ShipType"
		                   );
		                   $dataArray[]=array("Tag"=>"data","onEdit"=>"1","data"=>$tempArray);
		         //if ($LoginNumber==10868){ 
		           $logResult=mysql_query("SELECT A.Date,A.Remark,M.Name   
		                                FROM  $DataIn.stuffdevelop_log A
		                                LEFT JOIN $DataPublic.staffmain M ON M.Number=A.Operator
										WHERE  A.Mid='$Id' ORDER BY A.Date DESC,A.Id DESC LIMIT 1",$link_id);
					 if($logRow = mysql_fetch_array($logResult)){
					         $tempArray=array(
		                       "Title"=>array("Text"=>$logRow["Remark"]),
		                       "Col1"=> array("Text"=>$logRow["Date"]),
		                       "Col3"=>array("Text"=>$logRow["Name"])
		                   );
		                   $dataArray[]=array("Tag"=>"Log","onTap"=>array("Target"=>"Log","Args"=>"$Id"),"data"=>$tempArray); 
					 } 
				// }    
		          $weekCount++;       $posCount++;     
		     }while($myRow = mysql_fetch_assoc($myResult));
				           $Operator=$oldNumber;
				          include '../../model/subprogram/staffname.php';
					        $tempArray=array(
								                      "Id"=>"$oldNumber",
								                      "Title"=>array("Text"=>"$Operator","Color"=>"#000000","FontSize"=>"14","Bold"=>"1")
								                       //"Col3"=>array("Text"=>"$posCount","Margin"=>"-8,0,0,0")
								                   );
					        $tempArray1[]=array("Tag"=>"Total","data"=>$tempArray);
					        array_splice($dataArray,$pos,0,$tempArray1);
		
					     $overCount=$overCount==0?"":$overCount;
					     $WeekSTR="Week " . substr($oldWeeks,4,2);   
					     $dateArray= GetWeekToDate($oldWeeks,"m/d");
		                 $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		                 
		                $bgColor2=$oldWeeks==$curWeek?$CURWEEK_BGCOLOR:"";
		                $Color3=$oldWeeks<$curWeek?"#FF0000":"";
		                $headArray=array(
		                  "Id"=>"$GroupId",
		                  "onTap"=>"1",
		                   "RowSet"=>array("bgColor"=>"$bgColor2"),
		                  "Title"=>array("Text"=>"$WeekSTR","FontSize"=>"14","Bold"=>"1","BelowTitle"=>"$dateSTR"),
		                  "Col1"=>array("Text"=>"$overCount","Color"=>"#FF0000"),
		                  "Col3"=>array("Text"=>"$weekCount","FontSize"=>"14","Color"=>"$Color3")
		               );
		     $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","Layout"=>$Layout,"IconSet"=>$IconSet,"data"=>$dataArray); 
		}
    							
		//无图片 
		 $viewHidden=1;
		 $Layout=array( "Title"=>array("Frame"=>"40, 2, 250, 25"),
		                          "Col1"=>array("Frame"=>"40,32,100, 15","Align"=>"L"),
		                          "Col5"=>array("Frame"=>"210,32,110, 15","Align"=>"R"));
		                    
		 $curDateTime=date("Y-m-d H:m:s");
		 $checkPicSql=mysql_query("SELECT A.*,
               IF(A.shId>0 OR (A.Mid=0 AND (A.ForcePicSign=1 OR A.ForcePicSign=3)),0,1) AS  OrderSign
   FROM (
               SELECT S.StockId,S.StuffId,P.Forshort,(S.FactualQty+S.AddQty) AS Qty,S.ywOrderDTime,A.StuffCname,HM.Date AS shDate,YEARWEEK(S.DeliveryDate,1) AS DeliveryWeek,
               IF(A.ForcePicSpe=-1,T.ForcePicSign,A.ForcePicSpe) AS ForcePicSign,IF(A.Pjobid=-1,T.PicNumber,A.PicNumber) as PicNumber,
				IF(A.Pjobid=-1,T.PicJobid ,A.Pjobid) as PicJobid,S.Mid,H.Id as shId,IF((E.Type=2 OR K.Locks=0) and S.Mid=0,1,0) AS Locks   
				FROM $DataIn.cg1_stocksheet S 
				LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
                LEFT JOIN $DataIn.gys_shsheet H ON H.StockId=S.StockId  
                LEFT JOIN $DataIn.gys_shmain HM ON HM.Id=H.Mid 
                LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId =S.POrderId 
				LEFT JOIN $DataIn.cg1_lockstock  K ON K.StockId =S.StockId  
				WHERE S.rkSign>0 AND A.Estate=1 AND  T.mainType<2  AND A.Picture<>1  and (S.FactualQty>0 OR S.AddQty>0) GROUP BY S.StockId  
         )A 
         WHERE   A.Locks=0 $SearchPicRows ORDER BY PicNumber,OrderSign,DeliveryWeek",$link_id);
       if($checkPicRow = mysql_fetch_array($checkPicSql)){
               $tempArray=array(
                      "Id"=>"Total",
                      "Title"=>array("Text"=>"无图片","FontSize"=>"14","Color"=>"#0066FF")
                   );
				$tempArray3[]=array("Tag"=>"Total","data"=>$tempArray);
				$jsondata[]=array("data"=>$tempArray3); 
   
                 $oldPicNumber=$checkPicRow["PicNumber"];
                  $PIC_COUNT=0; $PIC_UNCOUNT=0; 
                  $dataArray=array();
         do{
                $StockId=$checkPicRow["StockId"];
                $StuffId=$checkPicRow["StuffId"];
	            $Forshort=$checkPicRow["Forshort"];
	            $StuffCname=$checkPicRow["StuffCname"];//配件名称
	            $Qty=number_format($checkPicRow["Qty"]);    //采购数量
	            $xdDate=$checkPicRow["ywOrderDTime"]; //下单日期$checkPicRow["shId"]>0?$checkPicRow["shDate"]:
	            $Days=floor((strtotime($curDateTime)-strtotime($xdDate))/3600/24);
	            $Days=$Days>=0?$Days:0;
	            $DateSTR=substr($xdDate, 2, 2) ."/". substr($xdDate, 5, 2) ."/". substr($xdDate, 8, 2) . " " . substr($xdDate, 11, 5);
               
               $DeliveryWeek=$checkPicRow["DeliveryWeek"]==""?" ":substr($checkPicRow["DeliveryWeek"], 4, 2);
                $bgColor=$curWeek>$checkPicRow["DeliveryWeek"] && $DeliveryWeek>0 ?"#FF0000":"";
               $bgColor=$checkPicRow["DeliveryWeek"]==""?"#DDDDDD":$bgColor;
               
                $rowColor=$checkPicRow["OrderSign"]==0?"#F3EBC4":"";
                $PicNumber=$checkPicRow["PicNumber"];
                if ($PicNumber!=$oldPicNumber){
		                 if ($PicNumber>0){
		                    $Operator=$oldPicNumber;
		                    include "../../model/subprogram/staffname.php";
		                }
		                else{
			                 $Operator="未指定";
		                }
                    $PIC_UNCOUNT=$PIC_UNCOUNT==0?"":$PIC_UNCOUNT;
	                $headArray=array(
                      "Id"=>"$GroupId",
                      "onTap"=>"1",
                      "Title"=>array("Text"=>"$Operator","FontSize"=>"14","Bold"=>"1"),
                      "Col1"=>array("Text"=>"$PIC_UNCOUNT","Color"=>"#FF0000"),
                      "Col3"=>array("Text"=>"$PIC_COUNT","FontSize"=>"14")
                   );
	               $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","Layout"=>$Layout,"data"=>$dataArray); 
	               $oldPicNumber=$checkPicRow["PicNumber"];
                   $PIC_COUNT=0; $PIC_UNCOUNT=0; 
                   $dataArray=array();
                }
                       
	            $tempArray=array(
                       "Id"=>"$StockId",
                       "RowSet"=>array("bgColor"=>"$rowColor"),
                       "Index"=>array("Text"=>"$DeliveryWeek","bgColor"=>"$bgColor"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Frame"=>"40, 2, 250, 25"),
                      "Col1"=> array("Text"=>"$Forshort"),
                      "Col3"=>array("Text"=>"$Qty"),
                      "Col5"=>array("Text"=>"$DateSTR","Color"=>"$DateColor"),
                      "rTopTitle"=>array("Text"=>"$Days"."d","Color"=>"#0000FF")
                      //"Remark"=>array("Text"=>"$Remark"),
                      //"rIcon"=>"ship$ShipType"
                   );
                   $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"StuffDetail","Args"=>"$StockId"),"onEdit"=>"0","data"=>$tempArray);
                   
                   if ($checkPicRow["OrderSign"]==0)  $PIC_UNCOUNT++;
                   $PIC_COUNT++;
             }while ($checkPicRow = mysql_fetch_array($checkPicSql));
             
              if ($PicNumber>0){
		                    $Operator=$oldPicNumber;
		                    include "../../model/subprogram/staffname.php";
		                }
		                else{
			                 $Operator="未指定";
		                }
                     $PIC_UNCOUNT=$PIC_UNCOUNT==0?"":$PIC_UNCOUNT;
	                $headArray=array(
                      "Id"=>"$GroupId",
                      "onTap"=>"1",
                      "Title"=>array("Text"=>"$Operator","FontSize"=>"14","Bold"=>"1"),
                      "Col1"=>array("Text"=>"$PIC_UNCOUNT","Color"=>"#FF0000"),
                      "Col3"=>array("Text"=>"$PIC_COUNT","FontSize"=>"14")
                   );
	             $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","Layout"=>$Layout,"data"=>$dataArray);   
         }


  
//无图档

    $checkGicSql=mysql_query("SELECT A.*,IF(TIMESTAMPDIFF(HOUR,A.ywOrderDTime,NOW())>='48',1,0)  AS  OrderSign
               FROM (SELECT S.StockId,S.StuffId,S.CompanyId,P.Forshort,(S.FactualQty+S.AddQty) AS Qty,S.ywOrderDTime,A.StuffCname,HM.Date AS shDate,M.PurchaseID, YEARWEEK(S.DeliveryDate,1) AS DeliveryWeek,
               IF(A.ForcePicSpe=-1,T.ForcePicSign,A.ForcePicSpe) AS ForcePicSign,IF(A.Jobid=-1,T.GicJobid,A.Jobid) AS GicJobid,S.Mid,H.Id as shId,IF((E.Type=2 OR K.Locks=0) and S.Mid=0,1,0) AS Locks,A.Gstate,A.Picture 
				FROM $DataIn.cg1_stocksheet S 
				LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
				LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
				LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
                LEFT JOIN $DataIn.gys_shsheet H ON H.StockId=S.StockId  
                 LEFT JOIN $DataIn.gys_shmain HM ON HM.Id=H.Mid 
                LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId =S.POrderId 
				LEFT JOIN $DataIn.cg1_lockstock  K ON K.StockId =S.StockId  
				WHERE S.rkSign>0 AND A.Estate=1 AND  T.mainType<2  AND A.Gstate<>1  and (S.FactualQty>0 OR S.AddQty>0) GROUP BY S.StockId  
         )A WHERE   A.Locks=0  $SearchGicRows ORDER BY CompanyId,ywOrderDTime",$link_id);


            if($checkGicRow = mysql_fetch_array($checkGicSql)){
               $tempArray=array(
                      "Id"=>"Total",
                      "Title"=>array("Text"=>"无图档","FontSize"=>"14","Color"=>"#0066FF")
                   );
				$tempArray4[]=array("Tag"=>"Total","data"=>$tempArray);
				$jsondata[]=array("data"=>$tempArray4); 
				$dataArray=array();
				
                 $oldForshort=$checkGicRow["Forshort"];
                  $GIC_COUNT=0; $GIC_UNCOUNT=0; 
         do{
                $StockId=$checkGicRow["StockId"];
                $StuffId=$checkGicRow["StuffId"];
                $PurchaseID=$checkGicRow["PurchaseID"];
	            $StuffCname=$checkGicRow["StuffCname"];//配件名称
	            $Picture=$checkGicRow["Picture"];
	             include "submodel/stuffname_color.php";
	              
	          //配件属性$StuffProperty
              include "submodel/stuff_property.php";
             
	            $Qty=number_format($checkGicRow["Qty"]);    //采购数量
	            $xdDate=$checkGicRow["ywOrderDTime"]; //下单日期$checkGicRow["shId"]>0?$checkGicRow["shDate"]:
	            $Days=floor((strtotime($curDateTime)-strtotime($xdDate))/3600/24);
	            $Days=$Days>=0?$Days:0;
	            $DateSTR=substr($xdDate, 2, 2) ."/". substr($xdDate, 5, 2) ."/". substr($xdDate, 8, 2) . " " . substr($xdDate, 11, 5);
               
               $DeliveryWeek=$checkGicRow["DeliveryWeek"]==""?" ":substr($checkGicRow["DeliveryWeek"], 4, 2);
                $bgColor=$curWeek>$checkGicRow["DeliveryWeek"] && $DeliveryWeek>0 ?"#FF0000":"";
                $bgColor=$checkGicRow["DeliveryWeek"]==""?"#DDDDDD":$bgColor;
                
               $rowColor=$checkGicRow["OrderSign"]==1?"#F3EBC4":"";
               $Forshort=$checkGicRow["Forshort"];
                if ($Forshort!=$oldForshort){
                     $GIC_UNCOUNT=$GIC_UNCOUNT==0?"":$GIC_UNCOUNT;
	                $headArray=array(
                      "Id"=>"$GroupId",
                      "onTap"=>"1",
                      "Title"=>array("Text"=>"$oldForshort","FontSize"=>"14","Bold"=>"1"),
                      "Col1"=>array("Text"=>"$GIC_UNCOUNT","Color"=>"#FF0000"),
                      "Col3"=>array("Text"=>"$GIC_COUNT","FontSize"=>"14")
                   );
	               $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","Layout"=>$Layout,"data"=>$dataArray); 
	                $oldForshort=$checkGicRow["Forshort"];
                   $GIC_COUNT=0; $GIC_UNCOUNT=0; 
                   $dataArray=array();
                }
                       
	            $tempArray=array(
                       "Id"=>"$StockId",
                       "RowSet"=>array("bgColor"=>"$rowColor"),
                       "Index"=>array("Text"=>"$DeliveryWeek","bgColor"=>"$bgColor"),
                      "Title"=>array("Text"=>"$StuffId-$StuffCname","Color"=>"$StuffColor","GysIcon"=>"$StuffProperty","Frame"=>"40, 2, 250, 25"),
                      "Col1"=> array("Text"=>"$PurchaseID"),
                      "Col3"=>array("Text"=>"$Qty"),
                      "Col5"=>array("Text"=>"$DateSTR","Color"=>"$DateColor"),
                      "rTopTitle"=>array("Text"=>"$Days"."d","Color"=>"#0000FF")
                      //"Remark"=>array("Text"=>"$Remark"),
                      //"rTopTitle"=>array("Text"=>"$odDays"."d","Margin"=>"-22,0,0,0","Color"=>"#0000FF"),
                      //"rIcon"=>"ship$ShipType"
                   );
                   $dataArray[]=array("Tag"=>"data","onTap"=>array("Target"=>"StuffDetail","Args"=>"$StockId"),"onEdit"=>"0","data"=>$tempArray);
                   
                  // if ($checkGicRow["OrderSign"]==0)  $PIC_UNCOUNT++;
                   $GIC_COUNT++;
             }while ($checkGicRow = mysql_fetch_array($checkGicSql));
                    $GIC_UNCOUNT=$GIC_UNCOUNT==0?"":$GIC_UNCOUNT;
	                $headArray=array(
                      "Id"=>"$GroupId",
                      "onTap"=>"1",
                      "Title"=>array("Text"=>"$Forshort","FontSize"=>"14","Bold"=>"1"),
                      "Col1"=>array("Text"=>"$GIC_UNCOUNT","Color"=>"#FF0000"),
                      "Col3"=>array("Text"=>"$GIC_COUNT","FontSize"=>"14")
                   );
	             $jsondata[]=array("head"=>$headArray,"hidden"=>"$viewHidden","Layout"=>$Layout,"data"=>$dataArray);   
         }
 } 
 $Sum_501=0;    $Sum_502=0;   $Sum_503=0; $Sum_102=0; 
 $checkResult=mysql_query("SELECT P.GroupId,COUNT(*) AS Count 
							    FROM  $DataIn.stuffdevelop P
								LEFT JOIN  $DataIn.stuffdata S  ON P.StuffId=S.StuffId 
								WHERE S.DevelopState=1 AND P.Estate>0 GROUP BY P.GroupId",$link_id);
 while($checkRow = mysql_fetch_array($checkResult)){
	  $SumSTR="Sum_" . $checkRow["GroupId"];
	  $$SumSTR=$checkRow["Count"];
 }
								                    
 $SegmentArray=array("开发A($Sum_501)","开发B($Sum_502)","开发C($Sum_503)","图档($Sum_102)");
 $SegmentIdArray=array("501","502","503","102");
$jsonArray=array("Segment"=>array("Segmented"=>$SegmentArray,"SegmentedId"=>$SegmentIdArray,"SegmentIndex"=>"$SegmentIndex"),"data"=>$jsondata); 
?>