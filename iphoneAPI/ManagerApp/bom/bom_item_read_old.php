<?php 
//BOM采购管理
 include "user_access.php";  //用户权限

$rowHeight=45; 
$groupArray1=array();
  
 if (in_array("1184",$modelArray)){
		 	//待下单
			 $Result1184 =  mysql_query("SELECT S.BuyerId,M.Name,SUM(IF(S.Estate=0,1,0)) AS unCounts,SUM(IF(S.Estate=0 and TIMESTAMPDIFF(HOUR,S.ywOrderDTime,NOW())>='4',1,0)) as unOvers,SUM(IF(S.Estate>0,1,0)) AS unAudits 
			FROM $DataIn.cg1_stocksheet S 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
			LEFT JOIN $DataPublic.staffmain M ON M.Number=S.BuyerId 
			LEFT JOIN $DataIn.yw2_orderexpress H ON H.POrderId =S.POrderId
			LEFT JOIN $DataIn.cg1_lockstock I ON I.StockId =S.StockId
			WHERE S.Mid=0 and  T.mainType<2 and (S.FactualQty>0 OR S.AddQty>0) and M.BranchId=4 and S.CompanyId<>'2166' 
			AND NOT EXISTS (SELECT StuffId FROM $DataIn.stuffdevelop P WHERE P.StuffId=A.StuffId AND P.Estate>0)  
			AND NOT ( (H.Type='2' AND H.Type is NOT NULL ) or (I.Locks=0 AND I.Locks is NOT NULL)) 
			AND NOT ((A.ForcePicSpe=1 OR (T.ForcePicSign=1 AND A.ForcePicSpe=-1)) AND  A.Picture!=1)  
			AND NOT ((A.ForcePicSpe=2 OR (T.ForcePicSign=2 AND A.ForcePicSpe=-1)) AND  (A.Gstate!=1  OR A.Gfile=''))  
			AND NOT ((A.ForcePicSpe=3 OR (T.ForcePicSign=3 AND A.ForcePicSpe=-1)) AND  ((A.Gstate!=1  OR A.Gfile='') OR  A.Picture!=1))
			  GROUP BY S.BuyerId ORDER BY S.BuyerId",$link_id);//S.Estate=0 and 
			 
		    while($cgRow = mysql_fetch_array($Result1184)){
		        $BuyerId=$cgRow["BuyerId"];
		        $numSql="SELECT count(*) AS nums  FROM  $DataIn.cg1_stockmain M 
								   LEFT JOIN $DataIn.cg1_stocksheet S ON S.Mid=M.Id 
								   LEFT JOIN $DataIn.stuffproperty P ON P.StuffId=S.StuffId 
								   WHERE M.BuyerId='$BuyerId'  AND P.Property<>2 AND P.Property<>4 
								             AND NOT EXISTS (SELECT R.Mid FROM $DataIn.cg1_stockreview R WHERE  R.Mid=M.Id ) 
								             AND M.CompanyId IN (SELECT DISTINCT B.CompanyId FROM $DataIn.UserTable A 
																				             LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
																				             WHERE A.Estate=1 and A.uType=3 and B.CompanyId<>'2270')";
            $numResult = mysql_fetch_array(mysql_query($numSql,$link_id));
            $B_nums=$numResult["nums"]==""?"":$numResult["nums"];
            
            $BuyerName=$cgRow["Name"]==""?"":$cgRow["Name"];
            
            $groupArray1[]=array(
	             "View"=>"List",
	             "Id"=>"1184",
	             "RowSet"=>array("Cols"=>"5","Height"=>"$rowHeight"),
	             "onTap"=>array("Title"=>"下单","Value"=>"1","Tag"=>"ext","Args"=>"$BuyerId"),
	             "Col_A"=>array("Title"=>"$BuyerName","Align"=>"L","R_Upper"=>"4h"),
	             "Col_B"=>array("Title"=>$B_nums,"Color"=>"#00A945","Align"=>"R","IconType"=>"3","onTap"=>"Audit"),
	             "Col_C"=>array("Title"=>$cgRow["unOvers"],"Color"=>"#FF0000","Align"=>"R","IconType"=>"1","onTap"=>"Over"), 
	             "Col_D"=>array("Title"=>number_format($cgRow["unAudits"]) ,"Color"=>"#0000FF","Align"=>"R","IconType"=>"2","onTap"=>"None"),
	             "Col_E"=>array("Title"=>number_format($cgRow["unCounts"]),"Align"=>"R")
	          );
		    }
		    
		    if ($BuyerId==""){
			     $numSql="SELECT count(*) AS nums,M.BuyerId,F.Name  FROM  $DataIn.cg1_stockmain M 
								   LEFT JOIN $DataIn.cg1_stocksheet S ON S.Mid=M.Id 
								   LEFT JOIN $DataIn.stuffproperty P ON P.StuffId=S.StuffId 
								   LEFT JOIN $DataPublic.staffmain F ON F.Number=M.BuyerId 
								   WHERE F.BranchId=4 AND P.Property<>2 AND P.Property<>4  
								             AND NOT EXISTS (SELECT R.Mid FROM $DataIn.cg1_stockreview R WHERE  R.Mid=M.Id) 
								             AND M.CompanyId IN (SELECT DISTINCT B.CompanyId FROM $DataIn.UserTable A 
																				             LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
																				             WHERE A.Estate=1 and A.uType=3 and B.CompanyId<>'2270')
								  GROUP BY S.BuyerId ORDER BY S.BuyerId";
					$numResult =mysql_query($numSql,$link_id);			  
					 while($numRow = mysql_fetch_array($numResult)){
					         $BuyerId=$numRow["BuyerId"];
					          $BuyerName=$numRow["Name"]==""?"":$numRow["Name"];
					         $groupArray1[]=array(
					             "View"=>"List",
					             "Id"=>"1184",
					              "RowSet"=>array("Cols"=>"5","Height"=>"$rowHeight"),
					             "onTap"=>array("Title"=>"下单","Value"=>"1","Tag"=>"ext","Args"=>"$BuyerId"),
					             "Col_A"=>array("Title"=>"$BuyerName","Align"=>"L","R_UpTitle"=>"4h"),
					             "Col_B"=>array("Title"=>$numResult["nums"],"Color"=>"#00A945","Align"=>"R","IconType"=>"3","onTap"=>"Audit"),
					             "Col_C"=>array("Title"=>"0","Color"=>"#FF0000","Align"=>"R","IconType"=>"1","onTap"=>"Over"), 
					             "Col_D"=>array("Title"=>"0" ,"Color"=>"#0000FF","Align"=>"R","IconType"=>"2","onTap"=>"None"),
					             "Col_E"=>array("Title"=>number_format($cgRow["unCounts"]),"Align"=>"R")
					          );
					 }			  
		    }       
}
		
  
$groupArray2=array();
 if (in_array("165",$itemArray) || in_array("1184",$modelArray)){
         //未收
         $curDate=date("Y-m-d");
         $curSeconds=strtotime("$curDate");   
         $Result165=mysql_query("SELECT CM.BuyerId,M.Name,S.StuffId,S.POrderId,S.DeliveryDate,PI.Leadtime  
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN  $DataIn.cg1_stockmain CM ON CM.Id=S.Mid  
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
		LEFT JOIN $DataPublic.staffmain M ON M.Number=CM.BuyerId 
		LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
		LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=Y.Id 
		WHERE  S.rkSign>0 AND S.Mid>0 AND M.BranchId=4  AND A.Estate=1   AND CM.CompanyId<>'2166' 
		AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C WHERE C.StockId=S.StockId) 
		ORDER BY CM.BuyerId",$link_id);
		$dataArray=array();
		 if($cgRow = mysql_fetch_array($Result165)){
		     $OldBuyerId=$cgRow["BuyerId"];
		     $OldName=$cgRow["Name"];
		     $unCount=0;$unOvers=0;$unOvers5=0;$lastCount=0;$lastOver=0;
		     do{
		         $BuyerId=$cgRow["BuyerId"];
		         if ($BuyerId!=$OldBuyerId){
			           $dataArray[]=array($OldBuyerId,$OldName,$unOvers5,$unOvers,$lastOver,$unCount,$lastCount);
			           $OldBuyerId=$cgRow["BuyerId"];
		               $OldName=$cgRow["Name"];
		               $unCount=0;$unOvers=0;$unOvers5=0;$lastCount=0;$lastOver=0;
		         }
		         $StuffId=$cgRow["StuffId"];
		         $POrderId=$cgRow["POrderId"];
		         //检查是否订单中最后一个需备料的配件 传入参数:$StuffId/$POrderId
				include "../../model/subprogram/stuff_blcheck.php";
		         if ($LastBlSign==1)  $lastCount++;
		         
		         $DeliveryDate=$cgRow["DeliveryDate"];
		         $Leadtime=$cgRow["Leadtime"];
		         if ($DeliveryDate!="0000-00-00"){
			         if ($curSeconds-strtotime($DeliveryDate)>0){
				           $unOvers++;
				            if ($LastBlSign==1) $lastOver++;
			         }
		         }
		         if ($Leadtime!="" && $Leadtime!="0000-00-00"){
		             $Leadtime=str_replace("*", "", $Leadtime);
			         if ($curSeconds-strtotime($Leadtime)>0){
				           $unOvers5++;
			         }
		         }
		          $unCount++;  
		    }while($cgRow = mysql_fetch_array($Result165));
		     $dataArray[]=array($OldBuyerId,$OldName,$unOvers5,$unOvers,$lastOver,$unCount,$lastCount);
		     for($i=0;$i<count($dataArray);$i++){
		          $tempArray=$dataArray[$i];
		          $groupArray2[]=array(
					             "View"=>"List",
					             "Id"=>"165",
					              "RowSet"=>array("Cols"=>"4","Height"=>"$rowHeight"),
					             "onTap"=>array("Title"=>"未收","Value"=>"1","Tag"=>"ext","Args"=>$tempArray[0]),
					             "Col_A"=>array("Title"=>"$tempArray[1]","Align"=>"L"),
					             "Col_B"=>array("Title"=>"$tempArray[2]","Color"=>"#FF0000","Align"=>"R","IconType"=>"7","onTap"=>"Over5",
					                                        "Margin"=>"-18,0,0,0"),
					             "Col_C"=>array("Title"=>"$tempArray[3]","Color"=>"#FF0000","Align"=>"R","IconType"=>"8","onTap"=>"Over",
					                                        "RightTitle"=>"(" . $tempArray[4]. ")","RightColor"=>"#00A945","Margin"=>"-10,5,0,0"),
					             "Col_D"=>array("Title"=>"$tempArray[5]","Align"=>"R",
					                                         "RightTitle"=>"(" . $tempArray[6]. ")","RightColor"=>"#00A945","Margin"=>"0,5,0,0")
					          );
		         }
		 }
/*
		       $Result165=mysql_query("SELECT CM.BuyerId,M.Name,COUNT(*) AS unCounts,SUM(IF(TIMESTAMPDIFF(DAY,S.DeliveryDate,'$curDate')>'0',1,0)) as unOvers,SUM(IF(TIMESTAMPDIFF(DAY,S.DeliveryDate,'$curDate')>'5',1,0)) as unOvers5
		FROM $DataIn.cg1_stocksheet S 
		LEFT JOIN  $DataIn.cg1_stockmain CM ON CM.Id=S.Mid  
		LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
		LEFT JOIN $DataIn.stufftype T ON T.TypeId=A.TypeId 
		LEFT JOIN $DataPublic.staffmain M ON M.Number=CM.BuyerId 
		WHERE  S.rkSign>0 AND S.Mid>0 AND M.BranchId=4  and CM.CompanyId<>'2166' 
		AND (S.AddQty+S.FactualQty)>(SELECT IFNULL(SUM(C.Qty),0) AS Qty  FROM $DataIn.ck1_rksheet C WHERE C.StockId=S.StockId) 
		GROUP BY CM.BuyerId ORDER BY CM.BuyerId",$link_id);
		 while($cgRow = mysql_fetch_array($Result165)){
		     $unOvers=$cgRow["unOvers"];
		     $unOvers5=$cgRow["unOvers5"];
		     $unOvers-=$unOvers5;
		     $groupArray2[]=array(
					             "View"=>"List",
					             "Id"=>"165",
					              "RowSet"=>array("Cols"=>"4","Height"=>"$rowHeight"),
					             "onTap"=>array("Title"=>"未收","Value"=>"1","Tag"=>"ext","Args"=>$cgRow["BuyerId"]),
					             "Col_A"=>array("Title"=>$cgRow["Name"],"Align"=>"L"),
					             "Col_B"=>array("Title"=>"$unOvers5","Color"=>"#FF0000","Align"=>"R","IconType"=>"7","onTap"=>"Over5"),
					             "Col_C"=>array("Title"=>"$unOvers","Color"=>"#FF0000","Align"=>"R","IconType"=>"8","onTap"=>"Over"), 
					             "Col_D"=>array("Title"=>number_format($cgRow["unCounts"]) ,"Align"=>"R")
					             //,"RightTitle"=>"(6)","RightColor"=>"#00FF00","Margin"=>"0,5,0,0"),
					          );
		   }
		 */
 }
$groupArray3=array();			      
        if (in_array("1184",$modelArray)){
          //未补仓
          $Result1182=mysql_query("SELECT F.BuyerId, M.Name, SUM( A.thQty - IFNULL(B.bcQty,0)) AS unCounts,
             SUM(IF(A.OverQty>A.thQty-IFNULL(B.bcQty,0),0,A.thQty-IFNULL(B.bcQty,0)-A.OverQty)) AS OverQty FROM (
						SELECT S.Id,S.StuffId,M.Date,M.CompanyId,SUM( S.Qty ) AS thQty,SUM(IF(TIMESTAMPDIFF(day,M.Date,Now())<15,S.Qty,0)) AS OverQty 
						FROM $DataIn.ck2_thsheet S
						LEFT JOIN $DataIn.ck2_thmain M ON M.Id=S.Mid 
				        GROUP BY M.CompanyId,S.StuffId
				)A
				LEFT JOIN (
				   SELECT S.StuffId,M.CompanyId,SUM( IFNULL( S.Qty, 0 ) ) AS bcQty FROM 
				   $DataIn.ck3_bcsheet S 
				   	LEFT JOIN $DataIn.ck3_bcmain M ON M.Id=S.Mid 
				   GROUP BY M.CompanyId,S.StuffId 
				)B ON B.StuffId=A.StuffId  AND B.CompanyId=A.CompanyId
				LEFT JOIN $DataIn.bps F ON F.StuffId = A.StuffId
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=A.StuffId 
				LEFT JOIN $DataPublic.staffmain M ON M.Number = F.BuyerId 
				WHERE M.BranchId =4  AND D.StuffId>0 AND A.thQty>IFNULL(B.bcQty,0)   GROUP BY F.BuyerId",$link_id);
				while($cgRow = mysql_fetch_array($Result1182)){		
				    $BuyerId=$cgRow["BuyerId"];
				    $thSql=mysql_fetch_array(mysql_query("SELECT count(*) AS nums FROM $DataIn.ck2_thmain M 
				           LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid=M.Id
				           LEFT JOIN $DataIn.bps F ON F.StuffId = S.StuffId
				            WHERE  F.BuyerId='$BuyerId' AND NOT EXISTS (SELECT R.Mid FROM $DataIn.ck2_threview R WHERE  R.Mid=S.Id  AND R.Estate<>2)
				            AND M.CompanyId IN (SELECT DISTINCT B.CompanyId FROM $DataIn.UserTable A 
																				             LEFT JOIN $DataIn.linkmandata B ON B.Id=A.Number 
																				             WHERE A.Estate=1 and A.uType=3 and B.CompanyId<>'2270')
",$link_id));
				     $groupArray3[]=array(
					             "View"=>"List",
					             "Id"=>"1182",
					              "RowSet"=>array("Cols"=>"4","Height"=>"$rowHeight"),
					             "onTap"=>array("Title"=>"未补","Value"=>"1","Tag"=>"ext","Args"=>$cgRow["BuyerId"]),
					             "Col_A"=>array("Title"=>$cgRow["Name"],"Align"=>"L","TopRight"=>"15d"),
					             "Col_B"=>array("Title"=>$thSql["nums"],"Color"=>"#00A945","Align"=>"R","IconType"=>"3",
					             "onTap"=>"thSign","Margin"=>"-10,0,0,0"),
					             "Col_C"=>array("Title"=>number_format($cgRow["OverQty"]),"Color"=>"#FF0000","Align"=>"R","IconType"=>"1",
					             "onTap"=>"Over","Margin"=>"-10,0,0,0"), 
					             "Col_D"=>array("Title"=>number_format($cgRow["unCounts"])."pcs" ,"Align"=>"R"),
					          );
				}	
        }              
        
$groupArray4=array();		     
       if (in_array("227",$itemArray)){
          //未付货款
          $Result1183=mysql_query("SELECT A.* FROM (
						SELECT S.Month,SUM(IF(S.Estate=3,S.Amount*D.Rate,0)) AS NoPay,SUM(S.Amount*D.Rate) AS Amount
						FROM $DataIn.cw1_fkoutsheet S
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
						LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency
				        WHERE (S.Estate=3 OR S.Estate=0) AND S.Month<>'' GROUP BY S.Month)A WHERE A.NoPay>0  Order By Month",$link_id);
				while($cgRow = mysql_fetch_array($Result1183)){
				$groupArray4[]=array(
					             "View"=>"List",
					             "Id"=>"1183",
					              "RowSet"=>array("Cols"=>"3","Height"=>"$rowHeight"),
					             "onTap"=>array("Title"=>"货款","Value"=>"1","Tag"=>"multi","Args"=>$cgRow["Month"]),
					             "Col_A"=>array("Title"=>$cgRow["Month"],"Align"=>"L"),
					             "Col_B"=>array("Title"=>"¥".number_format($cgRow["NoPay"]),"Color"=>"#FF0000","Align"=>"R","onTap"=>"NoPay"),
					             "Col_C"=>array("Title"=>"¥".number_format($cgRow["Amount"]) ,"Align"=>"R"),
					          );
				}	
}

if (count($groupArray1)>0){
      $jsonArray[]=array( "GroupName"=>"下单","Data"=>$groupArray1); 
  } 

if (count($groupArray2)>0){
      $jsonArray[]=array( "GroupName"=>"未收","Data"=>$groupArray2); 
  }

if (count($groupArray3)>0){
      $jsonArray[]=array( "GroupName"=>"未补","Data"=>$groupArray3); 
  }
  
 if (count($groupArray4)>0){
      $jsonArray[]=array( "GroupName"=>"货款","Data"=>$groupArray4); 
  }
     
?>