<?php 
//生产管理
   $ReadModuleTypeSign=2;
   include "user_access.php";  //用户权限
   $dataArray=array(); 
   $rowHeight=45;
 
 $bgColor="#E0EEF6";
 $TitleColor="#858C95";
  switch($NextPage){
 
	  	
	  	case 1: {
		  	
		  	 if (in_array("212",$itemArray)){
                    //可占用
                     include "../../desk/subtask/subtask-212.php";
                     
                    $SumTotalValue=number_format($iPhone_C212);
                    $overQty=$OverQty_C212==0?"":number_format($OverQty_C212);
                    
                    
                   
                           
$SumTotalValue = $overQty>0?"/$SumTotalValue":$SumTotalValue;
					$cellDict = array("kzy"=>array(array("$overQty",'#FF0000','12'),array("$SumTotalValue",'#000000','12'),array(".","#FFFFFF",'3'),array("($blCounts)","$TITLE_GRAYCOLOR",'9')));
					
					$SumTotalValue = $overQty = 0;
					$curDate = date("Y-m-d");
					$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS NextWeek",$link_id));
 					$curWeek=$dateResult["NextWeek"];
					$blCounts = 0;
					$mySql241= mysql_query( "SELECT A.*,PI.Leadtime,PIL.LeadTime as aLeadTime FROM
			(
				SELECT  M.CompanyId,S.Id,S.POrderId,S.ProductId,S.Qty,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty, SUM(L.Estate) as llEstate,Count(S.POrderId) as count
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
					WHERE A.blQty=A.llQty ");
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
							//continue;
						} else{ 
							//continue; llestate llitem
						 }
						$mission = "";
						$missionQeury = mysql_query("Select B.GroupName From $DataIn.sc1_mission A
							   		INNER Join $DataIn.staffgroup B On B.Id = A.Operator
							   		Where A.POrderId = '$POrderId' And B.Estate = '1' Limit 1");
						if($missionResult = mysql_fetch_assoc($missionQeury)){
							$mission = $missionResult["GroupName"];
						}
		//已有分配拉线 不在待分配里面
						if ($mission != "") {
							continue;
						}
						$canUsed = "yes";	
							$blCounts ++;
							$piLeadTimeHolder = ($Row241["Leadtime"] == "")?$Row241["aLeadTime"]:$Row241["Leadtime"];
							$piDate = str_replace("*", "", $piLeadTimeHolder);
							$piDate = date("Y-m-d", strtotime($piDate));
	
							$piWeekResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$piDate',1) AS Week",$link_id));
							$piWeek = $piWeekResult["Week"];
							$qty = $Row241["Qty"];
							if ($piWeek < $curWeek)  {  $overQty+=$qty; }
							$SumTotalValue += $qty;
					}
					 $SumTotalValue=number_format($SumTotalValue);
                    $overQty=$overQty==0?"":number_format($overQty);
					        
  $SumTotalValue = $overQty>0?"/$SumTotalValue":$SumTotalValue;
  $cellDict["dfp"]= array(array("$overQty",'#FF0000','12'),array("$SumTotalValue",'#000000','12'),array(".","#FFFFFF",'3'),array("($blCounts)","$TITLE_GRAYCOLOR",'9'));
                    //待备料
           $SumTotalValue = $overQty = $blCounts =0;
			$curDate = date("Y-m-d"); 
                  include "inware_item_sub_3.php";
                  
                  $SumTotalValue = $overQty>0?"/$SumTotalValue":$SumTotalValue;
                    
$cellDict["dbl"]= array(array("$overQty",'#FF0000','12'),array("$SumTotalValue",'#000000','12'),array(".","#FFFFFF",'3'),array("($blCounts)","$TITLE_GRAYCOLOR",'9'));
                    //待备料
                      $SumTotalValue = $overQty = $blCounts =0;
					 include "inware_item_sub_4.php";
					 			       
			       $cellDict["wfbl"]= array(array("$SumTotalValue","$FORSHORT_COLOR",'14'),array("($blCounts)","$TITLE_GRAYCOLOR",'11'));
			       
			 {
				          //待补货
				         $bhResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,COUNT(*) AS Nums 
						FROM $DataIn.ck13_replenish S 
						WHERE S.Estate=1 AND S.Lid=0",$link_id));	
						 $bhCounts=$bhResult["Nums"]==""?"0":$bhResult["Nums"];
						 $bhQty=number_format($bhResult["Qty"]);

					                 $cellDict["dbh"]= array(array("$bhQty",'#FF0000','14'),array("($bhCounts)","$TITLE_GRAYCOLOR",'11'));     
                    
                    // $cellDict["dbh"]= array("$bhQty","($bhCounts)");
                     $dataArray[]=array('CellID'=>"?","Tag"=>'5','data'=>$cellDict,'ids'=>array('133|244','133|243','133|212','133|241','133|242'),'titles'=>array('待补料','外发备料','可占用','待分配','待备料'));
			         } 
					
      }
      $NextPage ++;
          if (count($dataArray)>0){
		     if ($LoginNumber == 11965) {
			    // $NextPage = "4";
		     }
		       $jsonArray[]=array( "Page"=>"$NextPage","GroupName"=>"","GroupColor"=>"$FORSHORT_COLOR","bgColor"=>"$bgColor","Data"=>$dataArray);   
		  } 
          //break;

	  	}  break;
	     case 2:
                  //来料签收
                if ( in_array("228",$itemArray)){
                  $Floor=6;//抽检
                      include "inware_item_sub_1.php";
                     $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"12760",
			              "Timg"=>"i_kd",
			             "onTap"=>array("Title"=>"3A开单","Value"=>"1","Tag"=>"stuff","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"开       单","Align"=>"L","Color"=>"$TitleColor"),//"TopRight"=>"6h"
			             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000","Margin"=>"20,0,0,0"),
			             "Col_C"=>array("Title"=>"$TotalQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR"),
			             "AddCols"=>$AddCols
			          );
                   
                    $Floor=6;  $Nums=0;//抽检 
                /*
     include "inware_item_sub_2.php";                   
                     $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"228",
			             "Timg"=>"i_dd",
			             "onTap"=>array("Title"=>"3A到达","Value"=>"1","Tag"=>"stuff","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"到       达","Align"=>"L","Color"=>"$TitleColor"),
			             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000","Margin"=>"20,0,0,0"),
			             "Col_C"=>array("Title"=>"$TotalQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR"),
			             "AddCols"=>$AddCols
			          );
*/

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
								             "Timg"=>"i_ysh",
								             "onTap"=>array("Title"=>"3A验收","Value"=>"1","Tag"=>"stuff","Args"=>"$LineId"),
								             "RowSet"=>array("Separator"=>"$Sepwidth","Height"=>"$rowHeight"),
								             "Col_A"=>array("Title"=>"验       收","Align"=>"L","Color"=>"$TitleColor"),
								             "Col_B"=>array("Title"=>"$LineOverQty","Color"=>"#FF0000","Margin"=>"30,0,0,0"),
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
										             "Timg"=>"i_bjjl",
										             "onTap"=>array("Title"=>"品检记录","Value"=>"1","Tag"=>"stuff","Args"=>""),
										             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"品检记录","Align"=>"L","Color"=>"$TitleColor"),
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
						WHERE  H.rkSign=1 AND S.Estate=0 AND S.SendSign IN (0,1) AND M.Floor='$Floor' AND C.Qty<>0 GROUP BY S.Id
						)A  ",$link_id));	
					
					     $Nums=$rkResult["Nums"]==""?"0":$rkResult["Nums"];
						 $RkQty=number_format($rkResult["Qty"]);
				         $OverQty=$rkResult["OverQty"]==0?"":number_format($rkResult["OverQty"]); 
				         
				         $Separator=versionToNumber($AppVersion)>=298?0.5:1.5;
				         $dataArray[]=array(
										            "View"=>"List",
										             "Id"=>"2281",
										             "Timg"=>"i_drkc",
										             "onTap"=>array("Title"=>"待入库","Value"=>"1","Tag"=>"stuff","Args"=>""),
										             "RowSet"=>array("Separator"=>"$Separator","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"待  入  库","Align"=>"L","Color"=>"$TitleColor"),
										             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000"),
										             "Col_C"=>array("Title"=>"$RkQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );
			      
			      
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
										             "Timg"=>"i_tui",
										             "onTap"=>array("Title"=>"退料记录","Value"=>"1","Tag"=>"stuff","Args"=>"0|$Floor"),
										             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"退料记录","Align"=>"L","Color"=>"$TitleColor"),
										             "Col_C"=>array("Title"=>"$TlQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );	

	      }
	      $NextPage++;
	      if (count($dataArray)>0)  {
	           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"          3A","GroupColor"=>"$FORSHORT_COLOR","bgColor"=>"$bgColor","Data"=>$dataArray); 
	           $dataArray=array();
	           break;
	      }
	     case 3:   	
	         if ( in_array("215",$itemArray)){	        			          
			      $Floor=3;//全检
                    include "inware_item_sub_1.php";
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"12761",
			             "Timg"=>"i_kd",
			             "onTap"=>array("Title"=>"3B开单","Value"=>"1","Tag"=>"stuff","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"开       单","Align"=>"L","Color"=>"$TitleColor"),
			             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000","Margin"=>"30,0,0,0"),
			             "Col_C"=>array("Title"=>"$TotalQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR"),
			             "AddCols"=>$AddCols
			          );


                     $Floor=3;$Nums=0;
                     include "inware_item_sub_2.php";                   
                     $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"215",
			             "Timg"=>"i_dd",
			             "onTap"=>array("Title"=>"3B到达","Value"=>"1","Tag"=>"stuff","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"到       达","Align"=>"L","Color"=>"$TitleColor"),
			             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000","Margin"=>"30,0,0,0"),
			             "Col_C"=>array("Title"=>"$TotalQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR"),
			             "AddCols"=>$AddCols
			          );
			          
	               $QtyArray=array(); $OverQtyArray=array(); $NumsArray=array();
		           $qcResult=mysql_query("SELECT A.LineId,COUNT(*) AS Nums,SUM(A.Qty) AS Qty,
		                   SUM(IF (A.UnLocks=1  AND TIMESTAMPDIFF(minute,A.shDate,Now())>1200,A.Qty,0)) AS OverQty
                       FROM (  
                            SELECT C.LineId,S.Qty,IFNULL(GL.StockId,1) AS UnLocks,Max(H.shDate) AS shDate      
                            FROM $DataIn.gys_shsheet S 
                            LEFT JOIN $DataIn.gys_shmain M ON S.Mid=M.Id  
			                LEFT JOIN $DataIn.gys_shdate H ON H.Sid=S.Id 
			                LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
			                LEFT JOIN $DataIn.qc_mission C ON C.Sid=S.Id
			                LEFT JOIN $DataIn.cg1_lockstock GL ON S.StockId=GL.StockId  AND GL.Locks=0  
			                WHERE  S.Estate=2   AND S.SendSign IN (0,1)  AND M.Floor='$Floor'  AND C.Id>0 GROUP BY S.Id
			             )A GROUP BY  A.LineId",$link_id);
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
			
			              $NameArray=mbstringtoarray($LineName,"utf-8");
                          $LineTitle=count($NameArray)>0?join("  ",$NameArray):$LineName;
                          switch($LineId){
	                          case 1:$Timg="i_qjA";break;
	                          case 2:$Timg="i_qjB";break;
	                          case 3:$Timg="i_chj";break;
                          }
			               $Sepwidth=($k==$LineCount && versionToNumber($AppVersion)<295)?2:0.5;
			               $dataArray[]=array(
								            "View"=>"List",
								             "Id"=>"2150",
								             "Timg"=>"$Timg",
								             "onTap"=>array("Title"=>"$LineName","Value"=>"1","Tag"=>"stuff","Args"=>"$LineId"),
								             "RowSet"=>array("Separator"=>"$Sepwidth","Height"=>"$rowHeight"),
								             "Col_A"=>array("Title"=>"$LineTitle","Align"=>"L","Color"=>"$TitleColor"),
								             "Col_B"=>array("Title"=>"$LineOverQty","Color"=>"#FF0000","Margin"=>"30,0,0,0"),
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
										             "Timg"=>"i_bjjl",
										             "onTap"=>array("Title"=>"品检记录","Value"=>"1","Tag"=>"stuff","Args"=>"0"),
										             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"品检记录","Align"=>"L","Color"=>"$TitleColor"),
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
						WHERE  H.rkSign=1 AND S.Estate=0 AND S.SendSign IN (0,1) AND M.Floor='3' AND C.Qty<>0 GROUP BY S.Id
						)A  ",$link_id));	
					
					     $Nums=$rkResult["Nums"]==""?"0":$rkResult["Nums"];
						 $RkQty=number_format($rkResult["Qty"]);
				         $OverQty=$rkResult["OverQty"]==0?"":number_format($rkResult["OverQty"]); 
				         $Separator=versionToNumber($AppVersion)>=298?0.5:1.5;
				         $dataArray[]=array(
										            "View"=>"List",
										             "Id"=>"2151",
										             "Timg"=>"i_drkc",
										             "onTap"=>array("Title"=>"待入库","Value"=>"1","Tag"=>"stuff","Args"=>""),
										             "RowSet"=>array("Separator"=>"$Separator","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"待  入  库","Align"=>"L","Color"=>"$TitleColor"),
										             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000"),
										             "Col_C"=>array("Title"=>"$RkQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );
										          
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
										             "Timg"=>"i_tui",
										             "onTap"=>array("Title"=>"退料记录","Value"=>"1","Tag"=>"stuff","Args"=>"0|$Floor"),
										             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"退料记录","Align"=>"L","Color"=>"$TitleColor"),
										             "Col_C"=>array("Title"=>"$TlQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );	
				 	}	
      $NextPage++; 
      if (count($dataArray)>0)  {
           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"          3B","GroupColor"=>"$FORSHORT_COLOR","bgColor"=>"$bgColor","Data"=>$dataArray); 
           $dataArray=array();
           break;
      }
     case 4:       
          if (in_array("228",$itemArray) ){
                 //来料签收
                     $Floor=12;//1A抽检
                      include "inware_item_sub_1.php";
                     $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1276",
			             "Timg"=>"i_kd",
			             "onTap"=>array("Title"=>"1A开单","Value"=>"1","Tag"=>"stuff","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"开       单","Align"=>"L" ,"Color"=>"$TitleColor"),//"TopRight"=>"6h"
			             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000","Margin"=>"30,0,0,0"),
			             "Col_C"=>array("Title"=>"$TotalQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR"),
			             "AddCols"=>$AddCols
			          );
                   
                    $Floor=12;  $Nums=0;//1A抽检 
                     include "inware_item_sub_2.php";                   
                     $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"2285",
			             "Timg"=>"i_dd",
			             "onTap"=>array("Title"=>"1A到达","Value"=>"1","Tag"=>"stuff","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"到       达","Align"=>"L","Color"=>"$TitleColor"),
			             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000","Margin"=>"30,0,0,0"),
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
			           
			               $dataArray[]=array(
								            "View"=>"List",
								             "Id"=>"2286",
								             "Timg"=>"i_ysh",
								             "onTap"=>array("Title"=>"1A验收","Value"=>"1","Tag"=>"stuff","Args"=>"$LineId"),
								             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
								             "Col_A"=>array("Title"=>"验       收","Align"=>"L","Color"=>"$TitleColor"),
								             "Col_B"=>array("Title"=>"$LineOverQty","Color"=>"#FF0000","Margin"=>"30,0,0,0"),
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
										             "Id"=>"2287",
										             "Timg"=>"i_bjjl",
										             "onTap"=>array("Title"=>"品检记录","Value"=>"1","Tag"=>"stuff","Args"=>""),
										             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"品检记录","Align"=>"L","Color"=>"$TitleColor"),
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
						WHERE  H.rkSign=1 AND S.Estate=0 AND S.SendSign IN (0,1) AND M.Floor='$Floor' AND C.Qty>0 GROUP BY S.Id
						)A  ",$link_id));	
					
					     $Nums=$rkResult["Nums"]==""?"0":$rkResult["Nums"];
						 $RkQty=number_format($rkResult["Qty"]);
				         $OverQty=$rkResult["OverQty"]==0?"":number_format($rkResult["OverQty"]); 
				         
				         $Separator=versionToNumber($AppVersion)>=298?0.5:1.5;
				         $dataArray[]=array(
										            "View"=>"List",
										             "Id"=>"2288",
										             "Timg"=>"i_drkc",
										             "onTap"=>array("Title"=>"待入库","Value"=>"1","Tag"=>"stuff","Args"=>""),
										             "RowSet"=>array("Separator"=>"$Separator","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"待  入  库","Align"=>"L","Color"=>"$TitleColor"),
										             "Col_B"=>array("Title"=>"$OverQty","Color"=>"#FF0000"),
										             "Col_C"=>array("Title"=>"$RkQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );
			      
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
										             "Timg"=>"i_tui",
										             "onTap"=>array("Title"=>"退料记录","Value"=>"1","Tag"=>"stuff","Args"=>"0|$Floor"),
										             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
										             "Col_A"=>array("Title"=>"退料记录","Align"=>"L","Color"=>"$TitleColor"),
										             "Col_C"=>array("Title"=>"$TlQty","Align"=>"R","RLText"=>"($Nums)","RLColor"=>"$TITLE_GRAYCOLOR")
										          );	
            }
	          $NextPage++; 
		      if (count($dataArray)>0)  {
		           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"          1A","GroupColor"=>"$FORSHORT_COLOR","bgColor"=>"$bgColor","Data"=>$dataArray); 
		           $dataArray=array();
		           break;
		      }
     case 5:         
             if (in_array("107",$itemArray)){
               //在库
               $NoCompanySTR="AND P.CompanyId!='2166' ";
                $tStockResult = mysql_fetch_array(mysql_query("
						SELECT SUM(K.tStockQty) AS tStockQty,SUM(K.tStockQty*D.Price*C.Rate) AS Amount 
						FROM $DataIn.ck9_stocksheet K
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
						WHERE  K.tStockQty>0  AND T.mainType<2 $NoCompanySTR",$link_id));//AND D.Estate>0 ,
						//SUM(IF(K.oStockQty=0,K.tStockQty,0)) AS oStockQty,SUM(IF(K.oStockQty=0,K.tStockQty*D.Price*C.Rate,0)) AS oAmount 
			   $SumQty=number_format($tStockResult["tStockQty"]); 
			   //$oStockQty=$tStockResult["oStockQty"]; 
			   //$oAmount=$tStockResult["oAmount"]; 
			   $SumTotal=number_format($tStockResult["Amount"]);
			   
			  			          
			 //三个月以上未下采单
			$QtyResult= mysql_fetch_array(mysql_query("SELECT SUM(A.tStockQty) AS YearQty
			FROM (
					SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
					FROM $DataIn.ck9_stocksheet K
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
					LEFT JOIN $DataIn.bps B ON B.StuffId=K.StuffId   
					LEFT JOIN $DataIn.cg1_stocksheet S ON S.StuffId=K.StuffId
					LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
					LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
			        LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
					WHERE  K.tStockQty>0  AND T.mainType<2  GROUP BY K.StuffId 
			)A 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId = A.StuffId
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
			WHERE  TIMESTAMPDIFF(MONTH,A.DTime,Now())>3 $NoCompanySTR",$link_id));//AND D.Estate>0  
			
			$SumQty_12=$QtyResult["YearQty"]>0?number_format($QtyResult["YearQty"]):"0"; 
			//$SumTotal_12=$QtyResult["YearQty"]>0?number_format($QtyResult["YearAmount"]):"";
			
			$SepValue=$SumQty_12!=""?0:0.5;
			 $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"107",
			             "Timg"=>"i_zaiku",
			             "onTap"=>array("Title"=>"在库","Value"=>"1","Tag"=>"ext","Args"=>""),
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"在       库","Align"=>"L","Color"=>"$TitleColor"),
			             "Col_B"=>array("Title"=>"","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"$SumQty".'pcs','Color'=>"","Align"=>"R")
			          );
			          
            //1-3
            $lastYear=date("Y")-1;
           $oStockResult = mysql_fetch_array(mysql_query("SELECT SUM(A.tStockQty) AS YearQty
			FROM (
					SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
					FROM $DataIn.ck9_stocksheet K
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
					LEFT JOIN $DataIn.bps B ON B.StuffId=K.StuffId   
					LEFT JOIN $DataIn.cg1_stocksheet S ON S.StuffId=K.StuffId
					LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
					LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
			        LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
					WHERE  K.tStockQty>0  AND T.mainType<2  GROUP BY K.StuffId 
			)A 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId = A.StuffId
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
			WHERE  TIMESTAMPDIFF(MONTH,A.DTime,Now())<=3  and  TIMESTAMPDIFF(MONTH,A.DTime,Now())>=1 $NoCompanySTR",$link_id));
            
            $oStockQty=$oStockResult["YearQty"]; 
			//$oAmount=$oStockResult["OrderAmount"]; 
			if ($oStockQty>0){
			     $oStockQty=number_format($oStockQty); 
			     $oAmount=number_format($oAmount); 
				$dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"107",
			             
			             "RowSet"=>array("Separator"=>"0","Height"=>"22"),
			             "Col_B"=>array("Title"=>"","Color"=>"#00A945","Align"=>"R","Margin"=>"0,-13,0,0","DateIcon"=>
			             array("Type"=>"10",
			                                          				 "Title"=>"3m",
			                                          				 'text'=>'1-3',
			                                          				 'bgcolor'=>"#86b9d8",
			                                          				 'fonts'=>'7',
			                                          				 'textcolor'=>'#FFFFFF',
			                                          				 'iframe'=>'25,17,20,10',
			                                          				 'm_top'=>'0.5')
			                                          				 ),
			             "Col_C"=>array("Title"=>"$oStockQty".'pcs',"Color"=>"$FORSHORT_COLOR","Margin"=>"0,-13,0,0","Align"=>"R")
			          );
			} 
			
			 if ($SumQty_12!=""){
				$dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"107","Timg"=>"i_zaik??u",
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"22"),
			             "Col_B"=>array("Title"=>"","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-13,0,0",
			                                       "DateIcon"=>array("Type"=>"10",
			                                          				 "Title"=>"3m",
			                                          				 'text'=>'>3',
			                                          				 'bgcolor'=>"#f5afac",
			                                          				 'fonts'=>'7',
			                                          				 'textcolor'=>'#FFFFFF',
			                                          				 'iframe'=>'25,17,20,10',
			                                          				 'm_top'=>'0.5')),
			             "Col_C"=>array("Title"=>"$SumQty_12".'pcs',"Color"=>"#FF0000","Margin"=>"0,-13,0,0","Align"=>"R")
			          );
			}	   					
           }
           $month_now = date("M");
           $month_now = strtoupper($month_now);
           $year_now = date("Y");
           
 if (in_array("220",$itemArray)){
                //备品转入
                $month=date("Y-m");
                $Result2200=mysql_fetch_array(mysql_query("SELECT  SUM(B.Qty) as Qty
							FROM $DataIn.ck7_bprk B 
							 WHERE  DATE_FORMAT(B.Date,'%Y-%m')='$month' ",$link_id));
                   $SumQty=number_format($Result2200["Qty"]); 	
				   //$SumTotal=number_format($Result2200["Amount"]);
                   $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"220",
			             "Timg"=>"i_bp",
			             "onTap"=>array("Title"=>"备品","Value"=>"1","Tag"=>"StuffExt","Args"=>""),
			             "RowSet"=>array("Separator"=>"0","Height"=>"38"),
			             
			             "Col_A"=>array("Title"=>"备       品","Align"=>"L","Color"=>"$TitleColor"),
			             "Col_B"=>array("Title"=>"","Color"=>"#0000FF","Align"=>"R","DateIcon"=> array("Type"=>"10",
			                                          				 "Title"=>"3m",
			                                          				 'text'=>"$month_now",
			                                          				 'bgcolor'=>"#86b9d8",
			                                          				 'fonts'=>'7',
			                                          				 'textcolor'=>'#FFFFFF',
			                                          				 'iframe'=>'25,17,20,10',
			                                          				 'm_top'=>'0.5'
			                                          				 )),
			             "Col_C"=>array("Title"=>"$SumQty".'pcs','Color'=>"","Align"=>"R")
			          );
			          

                   include "../../desk/subtask/subtask-220.php";
                   $SumQty=number_format($Result220["Qty"]); 	
				  // $SumTotal=number_format($Result220["Amount"]);
                   $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"220","Timg"=>"i_baofei??",
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"25"),
			             "Col_A"=>array("Title"=>" ","Align"=>"L"),
			    "Col_B"=>array("Title"=>"","Color"=>"#0000FF","Align"=>"R","Margin"=>"0,-10,0,0","DateIcon"=> array("Type"=>"10",
			                                          				 "Title"=>"3m",
			                                          				 'text'=>"$year_now",
			                                          				 'bgcolor'=>"#86b9d8",
			                                          				 'fonts'=>'7',
			                                          				 'textcolor'=>'#FFFFFF',
			                                          				 'iframe'=>'25,17,20,10',
			                                          				 'm_top'=>'0.5'
			                                          				 )),         "Col_C"=>array("Title"=>"$SumQty".'pcs',"Color"=>"","Align"=>"R","Margin"=>"0,-10,0,0")
			          );
           }

  if (in_array("110",$itemArray)){
                //配件报废
                $month=date("Y-m");
                $Result1100=mysql_fetch_array(mysql_query("SELECT  SUM(F.Qty) as Qty
							FROM $DataIn.ck8_bfsheet F
							 WHERE  DATE_FORMAT(F.Date,'%Y-%m')='$month' ",$link_id));
                   $SumQty=number_format($Result1100["Qty"]); 	
                   $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1018","Timg"=>"i_baofei",
			              "onTap"=>array("Title"=>"报废","Value"=>"1","Tag"=>"StuffExt","Args"=>""),
			             "RowSet"=>array("Separator"=>"0","Height"=>"38"),
			             "Col_A"=>array("Title"=>"报      废","Align"=>"L","Color"=>"$TitleColor"),
			             "Col_B"=>array("Title"=>"","Color"=>"#","Align"=>"R","DateIcon"=> array("Type"=>"10",
			                                          				 "Title"=>"3m",
			                                          				 'text'=>"$month_now",
			                                          				 'bgcolor'=>"#86b9d8",
			                                          				 'fonts'=>'7',
			                                          				 'textcolor'=>'#FFFFFF',
			                                          				 'iframe'=>'25,17,20,10',
			                                          				 'm_top'=>'0.5'
			                                          				 )),
			             "Col_C"=>array("Title"=>"$SumQty".'pcs',"Align"=>"R")
			          );

                   include "../../desk/subtask/subtask-110.php";
                   $SumQty=number_format($Result110["Qty"]); 	
				 //  $SumTotal=number_format($Result110["Amount"]);
                   $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1018",
			             "RowSet"=>array("Separator"=>"2","Height"=>"25"),
			             "Col_A"=>array("Title"=>" ","Align"=>"L"),
			             "Col_B"=>array("Title"=>"","Color"=>"#0000FF","Align"=>"R","Margin"=>"0,-10,0,0","DateIcon"=> array("Type"=>"10",
			                                          				 "Title"=>"3m",
			                                          				 'text'=>"$year_now",
			                                          				 'bgcolor'=>"#86b9d8",
			                                          				 'fonts'=>'7',
			                                          				 'textcolor'=>'#FFFFFF',
			                                          				 'iframe'=>'25,17,20,10',
			                                          				 'm_top'=>'0.5'
			                                          				 )),
			             "Col_C"=>array("Title"=>"$SumQty"."pcs","Align"=>"R","Margin"=>"0,-10,0,0")
			          );
           }

          if (count($dataArray)>0){
		       $NextPage="END";
		       $jsonArray[]=array( "Page"=>"$NextPage","GroupName"=>" ","GroupColor"=>"$FORSHORT_COLOR","bgColor"=>"$bgColor","Data"=>$dataArray);   
		  } 
          break;
 }

 
function mbstringtoarray($str,$charset) {
    $strlen=mb_strlen($str);
    while($strlen){
        $array[]=mb_substr($str,0,1,$charset);
        $str=mb_substr($str,1,$strlen,$charset);
        $strlen=mb_strlen($str);
    }
    return $array;
}          
  //   $jsonArray[]=array( "GroupName"=>"","Data"=>$dataArray); 
?>