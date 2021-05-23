<?php 
//生产管理
   $ReadModuleTypeSign=2;
   include "user_access.php";  //用户权限
   $dataArray=array(); 
   $rowHeight=45;
 
  switch($NextPage){
    case 1:       
      if (versionToNumber($AppVersion)<=297){
         if (in_array("228",$itemArray) || in_array("215",$itemArray)){
                 //来料签收
                    $Floor=6;//抽检
                     include "production_item_sub_1.php";
                     $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"12760",
			             "onTap"=>array("Title"=>"3A开单","Value"=>"1","Tag"=>"stuff","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"3A开单","Align"=>"L"),//"TopRight"=>"6h"
			             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$TotalQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR"),
			             "AddCols"=>$AddCols
			          );
                   
                   if (versionToNumber($AppVersion)>=295){
                    $Floor=6;  $Nums=0;//抽检 
                     include "production_item_sub_2.php";                   
                     $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"228",
			             "onTap"=>array("Title"=>"3A到达","Value"=>"1","Tag"=>"stuff","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"3A到达","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$TotalQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR"),
			             "AddCols"=>$AddCols
			          );

                      $QtyArray=array(); $OverQtyArray=array(); $NumsArray=array();
		           $qcResult=mysql_query("SELECT C.LineId,COUNT(*) AS Nums,SUM(S.Qty) AS Qty,
		                   SUM(IF (GL.StockId IS NULL  AND TIMESTAMPDIFF(minute,H.shDate,Now())>1200,S.Qty,0)) AS OverQty    
                            FROM $DataIn.gys_shsheet S 
                            LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id  
			                LEFT JOIN $DataIn.gys_shdate H ON H.Sid=S.Id 
			                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			                LEFT JOIN $DataIn.qc_mission C ON C.Sid=S.Id
			                LEFT JOIN $DataIn.cg1_lockstock GL ON S.StockId=GL.StockId  AND GL.Locks=0  
			                WHERE  S.Estate=2   AND S.SendSign IN (0,1)  AND M.Floor='$Floor'  AND C.Id>0 GROUP BY  C.LineId",$link_id);
			      while ($qcRow = mysql_fetch_array($qcResult)){
			              $LineId=$qcRow["LineId"];
			              $NumsArray[$LineId]=$qcRow["Nums"];
			              $QtyArray[$LineId]=$qcRow["Qty"];
			              $OverQtyArray[$LineId]=$qcRow["OverQty"];
			      }
			      
			     $LineResult=mysql_query("SELECT C.Id,C.LineNo,C.Name   FROM  $DataIn.qc_scline C  WHERE  C.Estate=1 AND C.Floor='$Floor' ORDER BY LineNo",$link_id);
			     $LineCount=mysql_num_rows($LineResult);$k=1;
			      while ($LineRow = mysql_fetch_array($LineResult)){
			               $LineId=$LineRow["Id"];
			               $LineName=$LineRow["Name"];
			               $Nums=$NumsArray[$LineId]==""?"0":$NumsArray[$LineId];
			               $LineQty=$QtyArray[$LineId]==""?"0":number_format($QtyArray[$LineId]);
			               $LineOverQty=$OverQtyArray[$LineId]==0?"":number_format($OverQtyArray[$LineId]);
			               
			               $Sepwidth=($k==$LineCount && versionToNumber($AppVersion)<295)?2:0.5;
			               $dataArray[]=array(
								            "View"=>"List",
								             "Id"=>"2280",
								             "onTap"=>array("Title"=>"3A验收","Value"=>"1","Tag"=>"stuff","Args"=>"$LineId"),
								             "RowSet"=>array("Separator"=>"$Sepwidth","Height"=>"$rowHeight"),
								             "Col_A"=>array("Title"=>"3A验收","Align"=>"L"),
								             "Col_B"=>array("Title"=>"$LineOverQty","Color"=>"#FF0000"),
								             "Col_C"=>array("Title"=>"$LineQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
								          );
						  $k++;
			            }
			            
						//品检记录
			               $qcResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums,SUM(IFNULL(A.Qty,0)) AS Qty FROM (
			           SELECT  S.Qty AS shQty,SUM(C.Qty) AS Qty,MAX(C.Date) AS scDate   
						FROM $DataIn.qc_mission H 
						LEFT JOIN $DataIn.gys_shsheet S ON H.Sid=S.Id
						LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
						LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
						LEFT JOIN $DataIn.qc_cjtj  C ON C.Sid=S.Id  
						WHERE  H.rkSign=1 AND S.Estate=2 AND S.SendSign IN (0,1) AND M.Floor='$Floor' AND C.Qty>0 GROUP BY S.Id
						)A  WHERE A.Qty>=A.shQty OR TIMESTAMPDIFF(minute,A.scDate,Now())>=30",$link_id));	
						 $Nums=$qcResult["Nums"]==""?"0":$qcResult["Nums"];
						 $QcQty=number_format($qcResult["Qty"]);
						  $dataArray[]=array(
										            "View"=>"List",
										             "Id"=>"2282",
										             "onTap"=>array("Title"=>"品检记录","Value"=>"1","Tag"=>"stuff","Args"=>""),
										             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"品检记录","Align"=>"L"),
										             "Col_C"=>array("Title"=>"$QcQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );	
				 	}	
					
			            
			             //待入库
			               $rkResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums,SUM(IFNULL(A.Qty,0)) AS Qty,SUM(IF( A.SendSign=1 OR YEARWEEK(A.DeliveryDate,1)<YEARWEEK(CURDATE(),1),A.Qty,0)) AS OverQty FROM (
			           SELECT  SUM(C.Qty) AS Qty,G.DeliveryDate,S.SendSign 
						FROM $DataIn.qc_mission H 
						LEFT JOIN $DataIn.gys_shsheet S ON H.Sid=S.Id
						LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
						LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
						LEFT JOIN $DataIn.qc_cjtj  C ON C.Sid=S.Id  
						WHERE  H.rkSign=1 AND S.Estate=0 AND S.SendSign IN (0,1) AND M.Floor='$Floor' AND C.Qty>0 GROUP BY S.Id
						)A  ",$link_id));	
					
					     $Nums=$rkResult["Nums"]==""?"0":$rkResult["Nums"];
						 $RkQty=number_format($rkResult["Qty"]);
				         $OverQty=$rkResult["OverQty"]==0?"":number_format($rkResult["OverQty"]); 
				         
				         $Separator=versionToNumber($AppVersion)>=298?0.5:1.5;
				         $dataArray[]=array(
										            "View"=>"List",
										             "Id"=>"2281",
										             "onTap"=>array("Title"=>"待入库","Value"=>"1","Tag"=>"stuff","Args"=>""),
										             "RowSet"=>array("Separator"=>"$Separator","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"待入库","Align"=>"L"),
										             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000"),
										             "Col_C"=>array("Title"=>"$RkQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );
			      
			       if (versionToNumber($AppVersion)>=298){
						//退料记录
			            $tlResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,COUNT(*) AS Nums 
						FROM $DataIn.qc_badrecord S 
						LEFT JOIN $DataIn.gys_shmain M ON S.shMid=M.Id
						WHERE S.Estate=1 AND S.Qty>0 AND M.Floor='$Floor'",$link_id));	
						 $Nums=$tlResult["Nums"]==""?"0":$tlResult["Nums"];
						 $TlQty=number_format($tlResult["Qty"]);
						  $dataArray[]=array(
										            "View"=>"List",
										             "Id"=>"1263",
										             "onTap"=>array("Title"=>"退料记录","Value"=>"1","Tag"=>"stuff","Args"=>"0|$Floor"),
										             "RowSet"=>array("Separator"=>"1.5","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"退料记录","Align"=>"L"),
										             "Col_C"=>array("Title"=>"$TlQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );	
				 	}	

			        			          
			      $Floor=3;//全检
                    include "production_item_sub_1.php";
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"12761",
			             "onTap"=>array("Title"=>"3B开单","Value"=>"1","Tag"=>"stuff","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"3B开单","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$TotalQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR"),
			             "AddCols"=>$AddCols
			          );


                     $Floor=3;$Nums=0;
                     include "production_item_sub_2.php";                   
                     $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"215",
			             "onTap"=>array("Title"=>"3B到达","Value"=>"1","Tag"=>"stuff","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"3B到达","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$TotalQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR"),
			             "AddCols"=>$AddCols
			          );
			          
	               $QtyArray=array(); $OverQtyArray=array(); $NumsArray=array();
		           $qcResult=mysql_query("SELECT C.LineId,COUNT(*) AS Nums,SUM(S.Qty) AS Qty,
		                   SUM(IF (GL.StockId IS NULL  AND TIMESTAMPDIFF(minute,H.shDate,Now())>1200,S.Qty,0)) AS OverQty    
                            FROM $DataIn.gys_shsheet S 
                            LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id  
			                LEFT JOIN $DataIn.gys_shdate H ON H.Sid=S.Id 
			                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			                LEFT JOIN $DataIn.qc_mission C ON C.Sid=S.Id
			                LEFT JOIN $DataIn.cg1_lockstock GL ON S.StockId=GL.StockId  AND GL.Locks=0  
			                WHERE  S.Estate=2   AND S.SendSign IN (0,1)  AND M.Floor='$Floor'  AND C.Id>0 GROUP BY  C.LineId",$link_id);
			      while ($qcRow = mysql_fetch_array($qcResult)){
			              $LineId=$qcRow["LineId"];
			              $NumsArray[$LineId]=$qcRow["Nums"];
			              $QtyArray[$LineId]=$qcRow["Qty"];
			              $OverQtyArray[$LineId]=$qcRow["OverQty"];
			      }
			      
			     $LineResult=mysql_query("SELECT C.Id,C.LineNo,C.Name   FROM  $DataIn.qc_scline C  WHERE  C.Estate=1 AND C.Floor='$Floor'  ORDER BY LineNo",$link_id);
			     $LineCount=mysql_num_rows($LineResult);$k=1;
			      while ($LineRow = mysql_fetch_array($LineResult)){
			               $LineId=$LineRow["Id"];
			               $LineName=$LineRow["Name"];
			               $Nums=$NumsArray[$LineId]==""?"0":$NumsArray[$LineId];
			               $LineQty=$QtyArray[$LineId]==""?"0":number_format($QtyArray[$LineId]);
			               $LineOverQty=$OverQtyArray[$LineId]==0?"":number_format($OverQtyArray[$LineId]);
			               
			               $Sepwidth=($k==$LineCount && versionToNumber($AppVersion)<295)?2:0.5;
			               $dataArray[]=array(
								            "View"=>"List",
								             "Id"=>"2150",
								             "onTap"=>array("Title"=>"3B验收","Value"=>"1","Tag"=>"stuff","Args"=>"$LineId"),
								             "RowSet"=>array("Separator"=>"$Sepwidth","Height"=>"$rowHeight"),
								             "Col_A"=>array("Title"=>"$LineName","Align"=>"L"),
								             "Col_B"=>array("Title"=>"$LineOverQty","Color"=>"#FF0000"),
								             "Col_C"=>array("Title"=>"$LineQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
								          );
						  $k++;
			      }
			      
						//品检记录
			               $qcResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums,SUM(IFNULL(A.Qty,0)) AS Qty FROM (
			           SELECT  S.Qty AS shQty,SUM(C.Qty) AS Qty,MAX(C.Date) AS scDate   
						FROM $DataIn.qc_mission H 
						LEFT JOIN $DataIn.gys_shsheet S ON H.Sid=S.Id
						LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
						LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
						LEFT JOIN $DataIn.qc_cjtj  C ON C.Sid=S.Id  
						WHERE  H.rkSign=1 AND S.Estate=2 AND S.SendSign IN (0,1) AND M.Floor='$Floor' AND C.Qty>0 GROUP BY S.Id
						)A  WHERE A.Qty>=A.shQty OR TIMESTAMPDIFF(minute,A.scDate,Now())>=30",$link_id));	
						 $Nums=$qcResult["Nums"]==""?"0":$qcResult["Nums"];
						 $QcQty=number_format($qcResult["Qty"]);
						  $dataArray[]=array(
										            "View"=>"List",
										             "Id"=>"2152",
										             "onTap"=>array("Title"=>"品检记录","Value"=>"1","Tag"=>"stuff","Args"=>""),
										             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"品检记录","Align"=>"L"),
										             "Col_C"=>array("Title"=>"$QcQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );	
					
					      
						   //待入库
			               $rkResult=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums,SUM(IFNULL(A.Qty,0)) AS Qty,SUM(IF( A.SendSign=1 OR YEARWEEK(A.DeliveryDate,1)<YEARWEEK(CURDATE(),1),A.Qty,0)) AS OverQty FROM (
			           SELECT  SUM(C.Qty) AS Qty,G.DeliveryDate,S.SendSign 
						FROM $DataIn.qc_mission H 
						LEFT JOIN $DataIn.gys_shsheet S ON H.Sid=S.Id
						LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id 
						LEFT JOIN $DataIn.cg1_stocksheet  G ON G.StockId=S.StockId 
						LEFT JOIN $DataIn.qc_cjtj  C ON C.Sid=S.Id  
						WHERE  H.rkSign=1 AND S.Estate=0 AND S.SendSign IN (0,1) AND M.Floor='3' AND C.Qty>0 GROUP BY S.Id
						)A  ",$link_id));	
					
					     $Nums=$rkResult["Nums"]==""?"0":$rkResult["Nums"];
						 $RkQty=number_format($rkResult["Qty"]);
				         $OverQty=$rkResult["OverQty"]==0?"":number_format($rkResult["OverQty"]); 
				         $Separator=versionToNumber($AppVersion)>=298?0.5:1.5;
				         $dataArray[]=array(
										            "View"=>"List",
										             "Id"=>"2151",
										             "onTap"=>array("Title"=>"待入库","Value"=>"1","Tag"=>"stuff","Args"=>""),
										             "RowSet"=>array("Separator"=>"$Separator","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"待入库","Align"=>"L"),
										             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000"),
										             "Col_C"=>array("Title"=>"$RkQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );
										          
				 if (versionToNumber($AppVersion)>=298){
						//退料记录
			            $tlResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,COUNT(*) AS Nums 
						FROM $DataIn.qc_badrecord S 
						LEFT JOIN $DataIn.gys_shmain M ON S.shMid=M.Id
						WHERE S.Estate=1 AND S.Qty>0 AND M.Floor='$Floor'",$link_id));	
						 $Nums=$tlResult["Nums"]==""?"0":$tlResult["Nums"];
						 $TlQty=number_format($tlResult["Qty"]);
						  $dataArray[]=array(
										            "View"=>"List",
										             "Id"=>"1263",
										             "onTap"=>array("Title"=>"退料记录","Value"=>"1","Tag"=>"stuff","Args"=>"0|$Floor"),
										             "RowSet"=>array("Separator"=>"1.5","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"退料记录","Align"=>"L"),
										             "Col_C"=>array("Title"=>"$TlQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );	
				 	}	
					          					      
         }

         
          if (in_array("212",$itemArray)){
                    //可占用
                     include "../../desk/subtask/subtask-212.php";
                     
                    $SumTotalValue=number_format($iPhone_C212);
                    $overQty=$OverQty_C212==0?"":number_format($OverQty_C212);
                   /*
                   $blTime="";
                    $blTimeResult=mysql_query("SELECT MIN(S.ableDate) AS ableDate  FROM $DataIn.ck_bldatetime S WHERE  S.Estate=1
                       AND EXISTS (SELECT POrderId From $DataIn.yw1_ordersheet Y WHERE Y.POrderId=S.POrderId AND Y.Estate=1)",$link_id);
			        if ($blTimeRow = mysql_fetch_array($blTimeResult)){
			                $blTime=date("d/H:i",strtotime($blTimeRow["ableDate"]));
			       }
                    */
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"212",
			             "onTap"=>array("Title"=>"可占用","Value"=>"1","Tag"=>"Production","Args"=>""),
			             "RowSet"=>array("Separator"=>"1","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"可占用","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$overQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R","RLText"=>"($blCounts)","RLColor"=>"$TITLE_GRAYCOLOR")
			          );            
      }
 }      
	  
	  //new add begin
	  if ($LoginNumber == '11965') {
	   if (in_array("241",$itemArray)){
                    //待分配
                    // include "../../desk/subtask/subtask-212.php";
					
					$SumTotalValue = $overQty = 0;
					$curDate = date("Y-m-d");
					$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS NextWeek",$link_id));
 					$curWeek=$dateResult["NextWeek"];
					$blCounts = 0;
					$mySql241= mysql_query( "SELECT A.*,C.Forshort, C.PickNumber,P.cName,P.TestStandard, P.Weight, P.eCode,PI.Leadtime,PIL.LeadTime as aLeadTime FROM
			(
				SELECT M.CompanyId,M.OrderPO,M.OrderDate,S.Id,S.POrderId,S.ProductId, S.sgRemark, S.PackRemark,S.Qty,S.Price,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty, SUM(L.Estate) as llEstate,Count(S.POrderId) as count, S.ShipType
						FROM $DataIn.yw1_ordersheet S
						INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
						INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
						INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						INNER JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						LEFT JOIN (
									 SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(L.Estate) as Estate
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
									 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
									 WHERE  S.scFrom>0 AND S.Estate=1 GROUP BY L.StockId
								 ) L ON L.StockId=G.StockId
						WHERE S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  GROUP BY S.POrderId 
						) A 
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId  
			            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=A.Id 
			            LEFT Join $DataIn.yw3_pileadtime PIL On PIL.POrderId = A.POrderId
                        LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
					WHERE A.blQty=A.llQty ORDER BY A.llEstate Desc, PI.Leadtime, A.Qty Desc");
                     
                    
					while ($Row241 = mysql_fetch_assoc($mySql241)) {
						$POrderId = $Row241["POrderId"];
						$llItemCountResult = mysql_query("Select Count(*) as count From $DataIn.ck5_llsheet Where POrderId = '$POrderId'");
						$llItemCountRow = mysql_fetch_assoc($llItemCountResult);
						$llItemCount = $llItemCountRow["count"];
						$llEstate = $Row241["llEstate"];
						$count = $Row241["count"];
						if($llEstate == "" || $llItemCount<$count){
							continue;
						} else if($llEstate == 0){
							$canUsed= "ready";
							continue;
						} else if($llEstate > 0 && $llItemCount >= $count){
							$canUsed = "yes";	
							$blCounts ++;
		$piLeadTimeHolder = ($Row241["Leadtime"] == "")?$Row241["aLeadTime"]:$Row241["Leadtime"];
		$piDate = str_replace("*", "", $piLeadTimeHolder);
		$piDate = date("Y-m-d", strtotime($piDate));
	
		$piWeekResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$piDate',1) AS Week",$link_id));
		$piWeek = $piWeekResult["Week"];
		$qty = $Row241["Qty"];
		if ($piWeek < $curWeek)  { 
		$overQty+=$qty;
		}
		$SumTotalValue += $qty;
						} else{ continue; }
					}
					 $SumTotalValue=number_format($SumTotalValue);
                    $overQty=$overQty==0?"":number_format($overQty);
                   /*
                   $blTime="";
                    $blTimeResult=mysql_query("SELECT MIN(S.ableDate) AS ableDate  FROM $DataIn.ck_bldatetime S WHERE  S.Estate=1
                       AND EXISTS (SELECT POrderId From $DataIn.yw1_ordersheet Y WHERE Y.POrderId=S.POrderId AND Y.Estate=1)",$link_id);
			        if ($blTimeRow = mysql_fetch_array($blTimeResult)){
			                $blTime=date("d/H:i",strtotime($blTimeRow["ableDate"]));
			       }
                    */
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"241",
			             "onTap"=>array("Title"=>"待分配","Value"=>"1","Tag"=>"Production","Args"=>""),
			             "RowSet"=>array("Separator"=>"1","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待分配","Align"=>"L"),
			              "Col_B"=>array("Title"=>"$overQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R","RLText"=>"($blCounts)","RLColor"=>"$TITLE_GRAYCOLOR")
			          );            
      }
	  
	   if (in_array("242",$itemArray)){
                    //待备料
                    
                 
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"242",
			             "onTap"=>array("Title"=>"待备料","Value"=>"1","Tag"=>"Production","Args"=>""),
			             "RowSet"=>array("Separator"=>"1","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待备料","Align"=>"L"),
			             //"Col_B"=>array("Title"=>"$overQty","Color"=>"#FF0000"),
			             //"Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R","RLText"=>"($blCounts)","RLColor"=>"$TITLE_GRAYCOLOR")
			          );            
      }
      	   if (in_array("243",$itemArray)){
                    //待备料
                    
                 
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"243",
			             "onTap"=>array("Title"=>"外发备料","Value"=>"1","Tag"=>"Production","Args"=>""),
			             "RowSet"=>array("Separator"=>"1","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"外发备料","Align"=>"L"),
			             //"Col_B"=>array("Title"=>"$overQty","Color"=>"#FF0000"),
			             //"Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R","RLText"=>"($blCounts)","RLColor"=>"$TITLE_GRAYCOLOR")
			          );            
      }
      
	  
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
               $Result213=mysql_fetch_array(mysql_query("SELECT SUM(B.Qty) AS blQty,SUM(B.ScedQty) AS ScedQty,SUM(IF(YEARWEEK(substring(B.Leadtime,1,10),1)<YEARWEEK(CURDATE(),1),B.Qty,0)) AS OverQty  
               FROM (
					 SELECT A.POrderId,A.Qty,SUM(L.Qty) AS ScedQty,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime    
						       FROM (
										  SELECT S0.POrderId,S0.Qty,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty  
										   FROM (      
											           SELECT S.POrderId,S.Qty,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
														FROM $DataIn.yw1_ordermain M
														LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
														LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
														LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
														LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
														LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
														WHERE 1 AND S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  AND NOT EXISTS(SELECT T.StuffId FROM  $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8') 
														GROUP BY G.StockId 
										 )S0 GROUP BY S0.POrderId 
						 )A 
						LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
						LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
						LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
						LEFT JOIN $DataIn.sc1_cjtj L ON L.POrderId=S.POrderId AND L.TypeId='7100'  
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
			             "Id"=>"213",
			             "onTap"=>array("Title"=>"待组装","Value"=>"1","Tag"=>"Production","Args"=>""),
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待组装","Align"=>"L"),
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
               $scResult=mysql_query("SELECT SUM(Qty) AS Qty FROM $DataIn.sc1_cjtj WHERE  DATE_FORMAT(Date,'%Y-%m-%d')='$checkDay' AND TypeId='7100'",$link_id);
             if($scRow = mysql_fetch_array($scResult)) {
                        $scQty=sprintf("%.0f",$scRow["Qty"]);
               }
             $SumTotalValue=$scQty==""?0:number_format($scQty)."pcs";
              include "../subprogram/worktime_read.php";
              
              //$Tag=$LoginNumber==10868?"OrderExt":"scdj";
                $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1112",
			             "onTap"=>array("Title"=>"今日组装","Value"=>"1","Tag"=>"OrderExt","Args"=>""),
			             "RowSet"=>array("Separator"=>"2","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"今日组装","Align"=>"L","TopRight"=>"$workTimes"),
			             "Col_B"=>array("Title"=>"$avg_zz"),
			             "Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R")
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
					             "Id"=>"1041",
					             "onTap"=>array("Title"=>"今日出货","Value"=>"1","Tag"=>"OrderExt","Args"=>""),
					             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
					             "Col_A"=>array("Title"=>"今日出货","Align"=>"L"),
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
					             "Id"=>"216",
					             "onTap"=>array("Title"=>"待出","Value"=>"1","Tag"=>"OrderExt","Args"=>""),
					             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
					             "Col_A"=>array("Title"=>"待出","Align"=>"L"),
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
			             "Id"=>"2105",
			             "onTap"=>array("Title"=>"本月出货","Value"=>"1","Tag"=>"$orderExtTag","Args"=>""),//shiped
			             "RowSet"=>array("Separator"=>"2","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"本月出货","Align"=>"L"),
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