<?php 
//生产管理
   $ReadModuleTypeSign=2;
   include "user_access.php";  //用户权限
   $dataArray=array(); 
   $rowHeight=45;
$TestTime = $LoginNumber =="11965"?true:false;
  $czTest=1;
  
  switch($NextPage){
    case 1:       
        if ($TestTime == true) {
			$time1 = microtime(true);
		}
		$workTimesNew = -1;
		if ($czTest && in_array("213",$itemArray) ) {
		 include "../subprogram/worktime_read.php";
		  $wHours=$hour+round($minute/60,1);
		  $curDate=date("Y-m-d");
		  $Valuation=20;
		 $todayShow = mysql_fetch_assoc( mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*G.Price) AS RGAmount  
       	FROM $DataIn.sc1_cjtj S 
       	LEFT JOIN $DataIn.yw1_scsheet Y ON Y.sPOrderId=S.sPOrderId 
       	LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
       	LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
		WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$curDate' AND Y.ActionId=101",$link_id));
		$valueQtyAll = $todayShow["Qty"];
		$valueRGAll = $todayShow["RGAmount"];
		  //检查当天的员工数
		  
		  /*
		   SELECT COUNT(*) AS Nums FROM $DataIn.checkinout  C 
  LEFT JOIN  $DataPublic.staffmain M  ON M.Number=C.Number  
  LEFT JOIN $DataIn.staffgroup G  ON G.GroupId=M.GroupId 
 WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$curDate' AND C.CheckType='I'  AND  G.TypeId='$SC_TYPE'
 AND NOT EXISTS(SELECT K.Number FROM $DataIn.checkinout K WHERE DATE_FORMAT(K.CheckTime,'%Y-%m-%d')='$curDate' AND K.CheckType='O'  AND  K.Number=C.Number )
		  */
		  $GroupNums =0;
	 $GroupNumsq=mysql_fetch_assoc(mysql_query(" SELECT COUNT(*) AS Nums FROM $DataIn.checkinout  C 
  LEFT JOIN  $DataPublic.staffmain M  ON M.Number=C.Number  
  LEFT JOIN $DataIn.staffgroup G  ON G.GroupId=M.GroupId 
 WHERE DATE_FORMAT(C.CheckTime,'%Y-%m-%d')='$curDate' AND C.CheckType='I'  AND  G.TypeId='7100'",$link_id));
 // AND NOT EXISTS(SELECT K.Number FROM $DataIn.checkinout K WHERE DATE_FORMAT(K.CheckTime,'%Y-%m-%d')='$curDate' AND K.CheckType='O'  AND  K.Number=C.Number )
	 $GroupNums = $GroupNumsq["Nums"];
	  $Factual1 =  $Factual = "--";
	 if ($GroupNums>0 && $wHours>0) {
		  $Factual1 = round($valueRGAll/$GroupNums,1);
	 $Factual = round($valueRGAll/$GroupNums/$wHours,1); 
	 } else {
		
	}
	 $Factual2 = number_format($valueRGAll);
	
	 $Valuation1 = $wHours*$Valuation;
	 $Valuation2 = $GroupNums*$Valuation1;
	 $Valuation2 = number_format($Valuation2);
	 $dataArray[]=array("Tag"=>"view","CellID"=>"3","bgColor"=>"ECF5FA",
			           "Cols"=>array("$Factual/$Valuation","$Factual1/$Valuation1","$Factual2/$Valuation2"),
					   "Titles"=>array("时人均","日人均","日产值"),"lblimg"=>"evaluate",
						"$wHours","H"=>"44","LH"=>"27",
						
			          );
	 
	 
		 
			  $workTimesNew = $workTimes;
               $Result213=mysql_fetch_array(mysql_query("SELECT SUM(B.Qty) AS blQty,SUM(B.ScedQty) AS ScedQty 
               FROM (
					 SELECT A.POrderId,A.Qty,SUM(L.Qty) AS ScedQty  
						       FROM (
										  SELECT S0.POrderId,S0.Qty,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty  
										   FROM (      
											           SELECT S.POrderId,S.Qty,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
														FROM $DataIn.yw1_ordermain M
														LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
														LEFT JOIN  $DataIn.yw1_stocksheet G ON G.POrderId=S.POrderId
														LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
														LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
														LEFT JOIN  $DataIn.stuffmaintype SM ON SM.Id=ST.mainType 
														LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
														LEFT JOIN $DataIn.stuffproperty T ON T.StuffId=G.StuffId AND T.Property='8' 
				                        WHERE 1     AND S.scFrom>0 AND S.Estate=1 AND SM.blSign=1  AND T.StuffId is NULL  
														
																												GROUP BY G.StockId 
										 )S0 
										 GROUP BY S0.POrderId 
						 )A 
						LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
						LEFT JOIN $DataIn.yw1_scsheet C ON C.POrderId=S.POrderId AND C.ActionId=101  
						LEFT JOIN $DataIn.sc1_cjtj L ON L.POrderId=C.sPOrderId 
						 
						WHERE A.blQty=A.llQty  AND EXISTS (
						      SELECT ST.mainType 
						       FROM $DataIn.cg1_stocksheet G 
						       LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						       LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						       WHERE G.POrderId=A.POrderId AND ST.mainType=3)
						GROUP BY A.POrderId 
                      )B ",$link_id));
                      //LEFT JOIN  $DataIn.sc1_mission SC on SC.POrderId=S0.POrderId  where SC.Id is not null
                    $iPhone_C213=$Result213["blQty"];
                     $noScQty=$iPhone_C213-$Result213["ScedQty"];
					 $noScQty=number_format($noScQty);	
			           $SumTotalValue=number_format($iPhone_C213);	
					   
					
					 if ($TestTime == true) {
			$time2 = microtime(true);
			$interVal2 = round($time2-$time1,3);
		}   
				$dataArray[]=array(
			            "View"=>"view","Tag"=>"view","Anim"=>"1","TitleB"=>"$SumTotalValue","TitleS"=>"$noScQty","CellID"=>"view",
			             "Id"=>"213","TopLImg"=>"i_dzz","TopL"=>"待组装(pcs)","bgColor"=>"#E0EEF6","TopR"=>"$workTimes",
						 "H"=>"91","Fm1"=>"","Fm2"=>"","Fm3"=>"","Fm4"=>"","Fm5"=>"","Fm6","onTap"=>array("Value"=>1,"Tag"=>"view"),
						 "persons"=>$TestTime==true?$interVal2."秒":"$GroupNums"."人"
			          ); 
			/*
		
					  
					  $tempArray=array(
                     
                      "RowSet"=>array("bgColor"=>"E0EEF6"),
                       "weeks"=>array(),
                      "Title"=>array("Text"=>"$SumTotalValue","Frame"=>"32,36,200,50","FontSize"=>"48","Bold"=>"1","Color"=>"#358FC1"),
                      "Col1"=> array("Text"=>"待组装(pcs)","Color"=>"#858C95","Frame"=>"32,5,100,11","FontSize"=>"11"),
					  "Col2"=> array("Text"=>"/$noScQty","Color"=>"#858C95","Frame"=>"162,49,130,26","FontSize"=>"17"),
					 
                      
                       
                   );
				 $dataArray[] =array("Tag"=>"view","CellID"=>"view","H"=>"118","data"=>$tempArray,
			);
			*/
					   
				   
			
		}
        
	  
	  //new add begin
	  
	   if (in_array("212",$itemArray)){
                    //待分配
                   $onHome = 1; // include "../../desk/subtask/subtask-212.php";
					include "order_dfp_read.php";
					$Hid = 0;
					if (count($dfpList)<1) {
						$dfpList[]=array("Tag"=>"data","data"=>array("RowSet"=>array("bgColor"=>"F6F6F6"),
						"Title"=>array("Text"=>"暂无数据...","Color"=>"#858C95","FontSize"=>"14","Frame"=>"40,12,100,20"),
						),"CellID"=>"nodata");
						$Hid=1;
					}
					$dfpList[count($dfpList)-1]["isLast"]="1";
					   if ($TestTime == true) {
			$time33 = microtime(true);
			$interVal33 = round($time33-$time2,3);
		}   
                    $dataArray[]=array(
			            "View"=>"List","Tag"=>"top","gpList"=>$listGroup,"load"=>"0",
			             "Id"=>"241","Timg"=>"i_dfp","List"=>$dfpList,"sbID"=>"dfp",
			             "onTap"=>array("Title"=>"待分配","Value"=>"1","value"=>"1","Tag"=>"Production2","Args"=>"","hidden"=>"$Hid"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待  分  配","Align"=>"L","Color"=>"#858C95"),
			              "Col_B"=>array("Title"=>"$OverTotalQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>$TestTime==true?$interVal33 ."秒":"$totalQty"."pcs","Align"=>"R","RLColor"=>"$TITLE_GRAYCOLOR")
			          );   
					  if ($Hid==0 && $czTest) {
						  $dataArray = array_merge($dataArray,$dfpList); 
					  }
					         
    				$jsonArray=array();
                    //待备料
                    $dblList;
					$isHome = true;
                 include "order_dbl_read.php";
				 //nativeApp Nati
				 if (count($dblList)<1) {
						$dblList[]=array("Tag"=>"data","data"=>array("RowSet"=>array("bgColor"=>"F6F6F6"),
						"Title"=>array("Text"=>"暂无数据...","Color"=>"#858C95","FontSize"=>"14","Frame"=>"40,12,100,20"),
						),"CellID"=>"nodata");
					}
					$dblList[count($dblList)-1]["isLast"]="1";
							 if ($TestTime == true) {
			$time3 = microtime(true);
			$interVal3 = round($time3-$time33,3);
		}   
                    $dataArray[]=array(
			            "View"=>"List","List"=>$dblList,
			             "Id"=>"242","Timg"=>"i_dbl","Tag"=>"top","sbID"=>"dat1","load"=>"0",
			             "onTap"=>array("Title"=>"待备料","Value"=>"1","value"=>"1","Tag"=>"Production2","Args"=>"","hidden"=>$willShow?"0":"1"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待  备  料","Align"=>"L","Color"=>"#858C95"),
			             "Col_B"=>array("Title"=>"$OverTotalQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>$TestTime==true?$interVal3."秒":"$totalQty"."pcs","Align"=>"R","RLColor"=>"$TITLE_GRAYCOLOR")
			          ); 
					  //
    
                     if ($willShow==true && $czTest) {$dataArray = array_merge($dataArray,$dblList); 
					  }
					         
    				$jsonArray=array();

                   
      }
      
	  
	  
	  //new add end
	  
	  
      $NextPage++; 
      if (count($dataArray)>0)  {
           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"","Data"=>$dataArray); 
           $dataArray=array();
           break;
      }
 
     case 2:         
     if (in_array("213",$itemArray)){
                    //已备料
                    $curDate=date("Y-m-d");
                    /*
	                $Result213=mysql_fetch_array(mysql_query("
	                SELECT SUM(B.Qty) AS blQty,SUM(IF(B.gSign>0 AND B.gScQty<>B.SCQty,B.Qty,0)) AS gQty,SUM(IFNULL(B.scedQty,0)) AS scedQty,
	                      SUM(IF(YEARWEEK(substring(B.Leadtime,1,10),1)<YEARWEEK(CURDATE(),1),B.Qty,0)) AS OverQty,
	                      SUM(IF(B.gSign>0 AND B.gScQty<>B.SCQty AND YEARWEEK(substring(B.Leadtime,1,10),1)<YEARWEEK(CURDATE(),1),B.Qty,0)) AS gOverQty
                        FROM (
                          SELECT A.Qty,A.POrderId,A.Leadtime,SUM(IF(ST.TypeId<>7100,1,0)) AS gSign,IFNULL(L.gScQty,0) AS gScQty,IFNULL(L.scedQty,0) AS scedQty,
                         SUM(IF(D.TypeId<>7100,G.OrderQty,0)) AS scQty
                               FROM (
									SELECT 
									S.POrderId,S.ProductId,S.Qty,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime 
									FROM $DataIn.yw1_ordermain M
									LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
									LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
									LEFT JOIN  $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId   
									LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
									LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
									LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
									LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
									LEFT JOIN (
												 SELECT L.StockId,SUM(L.Qty) AS Qty 
												 FROM $DataIn.yw1_ordersheet S 
												 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
												 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
												 WHERE 1  AND S.scFrom>0 AND S.Estate=1  GROUP BY L.StockId
											 ) L ON L.StockId=G.StockId
									WHERE  S.scFrom>0 AND S.Estate=1 AND ST.mainType<2    
									AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
									GROUP BY S.POrderId 
									) A 
                                 LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=A.POrderId 
                                  LEFT JOIN (
									 SELECT S.POrderId,SUM(IF(L.TypeId<>7100,L.Qty,0)) AS gScQty,SUM(IF(L.TypeId=7100,L.Qty,0)) AS scedQty  
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.sc1_cjtj L ON S.POrderId=L.POrderId
									 WHERE  S.scFrom>0 AND S.Estate=1  GROUP BY S.POrderId 
						         ) L ON A.POrderId=L.POrderId 
                                 LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId  
                                 LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
								WHERE  A.blQty=A.llQty AND ST.mainType=3   GROUP BY A.POrderId 
                      )B  ",$link_id));//AND L.Estate=0 
                   */
				   
				   // AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
               $Result213=mysql_fetch_array(mysql_query("SELECT SUM(B.Qty) AS blQty,SUM(B.ScedQty) AS ScedQty  ,SUM(IF(YEARWEEK(substring(B.Leadtime,1,10),1)<YEARWEEK(CURDATE(),1),B.Qty,0)) AS OverQty 
               FROM (
					 SELECT A.POrderId,A.Qty,SUM(L.Qty) AS ScedQty   ,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime 
						       FROM (
										  SELECT S0.POrderId,S0.Qty,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty  
										   FROM (      
											           SELECT S.POrderId,S.Qty,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
														FROM $DataIn.yw1_ordermain M
														LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
														LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
														LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
														LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
														LEFT JOIN  $DataIn.stuffmaintype SM ON SM.Id=ST.mainType 
														LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
														LEFT JOIN $DataIn.stuffproperty T ON T.StuffId=G.StuffId AND T.Property='8'  
				                        WHERE 1     AND S.scFrom>0 AND S.Estate=1 AND SM.blSign=1   AND T.StuffId is NULL  
											GROUP BY G.StockId 
										 )S0 
										 GROUP BY S0.POrderId 
						 )A 
						LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
						LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
						LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
						LEFT JOIN $DataIn.yw1_scsheet C ON C.POrderId=S.POrderId AND C.ActionId=101  
						LEFT JOIN $DataIn.sc1_cjtj L ON L.POrderId=C.sPOrderId  
						WHERE A.blQty=A.llQty  AND EXISTS (
						      SELECT ST.mainType 
						       FROM $DataIn.cg1_stocksheet G 
						       LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						       LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						       WHERE G.POrderId=A.POrderId AND ST.mainType=3)
						GROUP BY A.POrderId 
                      )B ",$link_id));
                    $iPhone_C213=$Result213["blQty"];
                     $overQty=$Result213["OverQty"]==0?"":number_format($Result213["OverQty"]); 
                     $noScQty=$iPhone_C213-$Result213["ScedQty"];
                     
                     /*
                     $iPhone_C2130=$Result213["gQty"];
                     $overQty0=$Result213["gOverQty"]==0?"":number_format($Result213["gOverQty"]); 
                     $SumTotalValue0=number_format($iPhone_C2130) . "pcs"; 
                     $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"21300",
			             "onTap"=>array("Title"=>"待加工","Value"=>"1","Tag"=>"Production","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待加工","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$overQty0","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$SumTotalValue0","Align"=>"R")
			          );   
			          */
			          
			           $SumTotalValue=number_format($iPhone_C213) . "pcs";
			           $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"213","Timg"=>"i_dzz",
			             "onTap"=>array("Title"=>"待组装","Value"=>"1","Tag"=>"Production2","Args"=>""),
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待  组  装","Align"=>"L","Color"=>"#858C95"),
			             "Col_B"=>array("Title"=>"$overQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R")
			          );   
			          
		               $noScQty=number_format($noScQty)  . "pcs";
			           $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"213",
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"22"),
			             "Col_A"=>array("Title"=>"","Align"=>"L"),
			             "Col_C"=>array("Title"=>"$noScQty","Color"=>"#888888","Align"=>"R","Margin"=>"0,-13,0,0")
			          );   
					  $temA = $dataArray;  
					  if (versionToNumber($AppVersion)>300 || $LoginNumber == "11965") {
						  $showRed  = 1;
						  include "order_dzz_read2.php";
						$temA=array_merge($temA,$showRedArr);  
					  }
                 
				 
				 if ($czTest) {
					   $drkList;
					   $noJson = true;$OverTotalQty=$totalQty="";
                 include "order_rk_read.php";
				
				 	$hiddenRk = 1;
				 if (count($drkList)<1) {
						$drkList[]=array("Tag"=>"data","data"=>array("RowSet"=>array("bgColor"=>"F6F6F6"),
						"Title"=>array("Text"=>"暂无数据...","Color"=>"#858C95","FontSize"=>"14","Frame"=>"40,12,100,20"),
						),"CellID"=>"nodata");
					} else {
						$hiddenRk = 0;
						
					}
					$drkList[count($drkList)-1]["isLast"]="1";
					$dataArray = $temA;
					 $dataArray[]=array(
			            "View"=>"List","List"=>$drkList,"Tag"=>"top",
			             "Id"=>"WLRK","Timg"=>"i_drk","sbID"=>"dat11","load"=>"0",
			             "onTap"=>array("Title"=>"待入库","Value"=>"1","value"=>"1","Tag"=>"Production2","Args"=>"","hidden"=>"$hiddenRk"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待  入  库","Align"=>"L","Color"=>"#858C95"),
			             "Col_B"=>array("Title"=>$OverTotalQty>0?"$OverTotalQty":"","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>$totalQty>0?"$totalQty"."pcs":"0pcs","Align"=>"R")
			          );     
					  if ($hiddenRk == 0) {
					 $dataArray = array_merge($dataArray,$drkList);
				 }
				 }
				 
    }
    
$checkDay=date("Y-m-d");
if (in_array("1011",$modelArray)){
            //5天平均生产数量
            $yDate=date("Y-m-d",strtotime("-1 day"));
			$k=0;$n=0;$DateCheckRows="";
			do{
			   $eDate=date("Y-m-d",strtotime("$yDate  -$n   day"));
			   //判断当天是否有登记生产数量
			   $CheckScState=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.sc1_cjtj  WHERE DATE_FORMAT(Date,'%Y-%m-%d')='$eDate'",$link_id));
			    if ($CheckScState["Qty"]>0){
					   $k++;
				 }else{
					$DateCheckRows.=" AND DATE_FORMAT(S.Date,'%Y-%m-%d')<>'$eDate' ";
				 }
				$n++;
			}while($k<5);
			
			$DateCheckRows=" AND DATE_FORMAT(S.Date,'%Y-%m-%d')>='$eDate' AND DATE_FORMAT(S.Date,'%Y-%m-%d')<='$yDate' " . $DateCheckRows;
			$scResult1=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty FROM $DataIn.sc1_cjtj S WHERE  1 $DateCheckRows AND S.TypeId<>'7100'",$link_id));
			$avg_jg=number_format(round($scResult1["Qty"]/5));
           
           $scResult2=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty FROM $DataIn.sc1_cjtj S WHERE 1 $DateCheckRows AND S.TypeId='7100'",$link_id));
			$avg_zz=number_format(round($scResult2["Qty"]/5));

		                
             $scResult=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.sc1_cjtj WHERE  DATE_FORMAT(Date,'%Y-%m-%d')='$checkDay' AND TypeId<>'7100'",$link_id);
             if($scRow = mysql_fetch_array($scResult)) {
                        $scQty=sprintf("%.0f",$scRow["Qty"]);
               }
              
              /* 
             $SumTotalValue=$scQty==""?0:number_format($scQty)."pcs";
             $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1111",
			             "onTap"=>array("Title"=>"今日加工","Value"=>"1","Tag"=>"OrderExt","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"今日加工","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$avg_jg"),
			             "Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R")
			          );           
              */
              
               $scQty="";
               $scResult=mysql_query("SELECT SUM(S.Qty) AS Qty 
               FROM $DataIn.sc1_cjtj S 
               LEFT JOIN $DataIn.yw1_scsheet C ON C.sPOrderId=S.sPOrderId 
               WHERE  DATE_FORMAT(S.Date,'%Y-%m-%d')='$checkDay' AND C.ActionId=101",$link_id);
             if($scRow = mysql_fetch_array($scResult)) {
                        $scQty=sprintf("%.0f",$scRow["Qty"]);
               }
             $SumTotalValue=$scQty==""?0:number_format($scQty)."pcs";
			// if ($workTimesNew == -1) {
              include "../subprogram/worktime_read.php";
			  $workTimesNew = $workTimes;
			 //}
              //$Tag=$LoginNumber==10868?"OrderExt":"scdj";
                $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1112","Timg"=>"i_zz",
			             "onTap"=>array("Title"=>"今日组装","Value"=>"1","Tag"=>"OrderExt","Args"=>""),
			             "RowSet"=>array("Separator"=>"2","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"今日组装","Align"=>"L","Color"=>"#858C95"),//"TopRight"=>"$workTimesNew"
			             "Col_B"=>array("Title"=>"$avg_zz"),
			             "Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R")
			          );   
}

  if (in_array("1011",$modelArray) && versionToNumber($AppVersion)>300){
				          //待补货
				         $bhResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,COUNT(*) AS Nums 
						FROM $DataIn.ck13_replenish S 
						WHERE S.Estate=1 ",$link_id));	
						 $bhCounts=$bhResult["Nums"]==""?"0":$bhResult["Nums"];
						 $bhQty=number_format($bhResult["Qty"]);

					  $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"244",
			              "Timg"=>"i_bh",
			             "onTap"=>array("Title"=>"补料单","Value"=>"1","Tag"=>"StuffExt","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"补  料  单","Align"=>"L","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$bhQty","Align"=>"R","RLText"=>"($bhCounts)","RLColor"=>"$TITLE_GRAYCOLOR")
			          );   

			         } 

 $NextPage++; 
      if (count($dataArray)>0)  {
           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"","Data"=>$dataArray); 
           $dataArray=array();
           break;
      }
case 3:      
        $orderExtTag=versionToNumber($AppVersion)>=277?"OrderExt2":"OrderExt";//Created by 2014/08/29  
		if (in_array("104",$itemArray)){
		    //已出明细
		        $shipResult = mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount 
		                        FROM $DataIn.ch1_shipmain M 
		                         LEFT JOIN $DataIn.ch1_shipsheet H ON H.Mid=M.Id 
		                        LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=H.POrderId 
		                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
		                        LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
		                        WHERE 1 AND S.Estate='0' AND M.Date=CURDATE()",$link_id);
		            if($shipRow = mysql_fetch_array($shipResult)) {
		                $shipQty=sprintf("%.0f",$shipRow["Qty"]);
		                $shipAmount=sprintf("%.0f",$shipRow["Amount"]);
		            }
		            $SumTotalValue=$shipAmount==""?0:number_format($shipAmount);
		            $SumTotalQty=$shipQty==""?0:number_format(round($shipQty/1000,0)) . "K";
		           
		           $dataArray[]=array(
					            "View"=>"List",
					             "Id"=>"1041","Timg"=>"i_jrch",
					             "onTap"=>array("Title"=>"今日出货","Value"=>"1","Tag"=>"OrderExt","Args"=>""),
					             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
					             "Col_A"=>array("Title"=>"今日出货","Align"=>"L","Color"=>"#858C95"),
					             "Col_B"=>array("Title"=>"$SumTotalQty"),
					             "Col_C"=>array("Title"=>"¥$SumTotalValue","Align"=>"R")
					          ); 
		}

		if (in_array("216",$itemArray)){
		    //待出
		    include "../../desk/subtask/subtask-216.php";
		    $SumTotalQty=number_format($temp_C216) . "K";
		    $SumTotalValue=number_format($iPhoneAmount_C216);
		    $dataArray[]=array(
					            "View"=>"List",
					             "Id"=>"216","Timg"=>"i_dc",
					             "onTap"=>array("Title"=>"待出","Value"=>"1","Tag"=>"OrderExt","Args"=>""),
					             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
					             "Col_A"=>array("Title"=>"待        出","Align"=>"L","Color"=>"#858C95"),
					             "Col_B"=>array("Title"=>"$SumTotalQty"),
					             "Col_C"=>array("Title"=>"¥$SumTotalValue","Align"=>"R")
					          ); 
		}

    if (in_array("104",$itemArray)){  
    //本月出货总额
    $month=date("Y-m");			
     $ShipResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty  
                    FROM $DataIn.ch1_shipmain M
                    LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
                    WHERE  M.Estate='0' AND DATE_FORMAT(M.Date,'%Y-%m')='$month' AND (S.Type=1 OR S.Type=3)",$link_id));

                    $shipQtyValue=number_format(sprintf("%.0f",$ShipResult["Qty"]/1000));		
                    			
    $ShipResult=mysql_fetch_array(mysql_query("SELECT SUM( S.Price*S.Qty*M.Sign*D.Rate) AS Amount 
                    FROM $DataIn.ch1_shipmain M
                    LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
                    LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
                    LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
                    WHERE  M.Estate='0' AND DATE_FORMAT(M.Date,'%Y-%m')='$month' ",$link_id));
                    $shipAmountValue=number_format(sprintf("%.0f",$ShipResult["Amount"]));
                    
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"2105","Timg"=>"i_bych",
			             "onTap"=>array("Title"=>"本月出货","Value"=>"1","Tag"=>"$orderExtTag","Args"=>""),//shiped
			             "RowSet"=>array("Separator"=>"2","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"本月出货","Align"=>"L","Color"=>"#858C95"),
			             "Col_B"=>array("Title"=>"$shipQtyValue" . "K"),
			             "Col_C"=>array("Title"=>"¥$shipAmountValue","Align"=>"R")
			          ); 
         }

          if (count($dataArray)>0){
		       $NextPage="END";
		       $jsonArray[]=array( "Page"=>"$NextPage","GroupName"=>"","Data"=>$dataArray);   
		  } 
          break;
 }

                
                       
  //   $jsonArray[]=array( "GroupName"=>"","Data"=>$dataArray); 
?>