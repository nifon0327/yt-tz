<?php 
//生产管理
   $ReadModuleTypeSign=2;
   include "user_access.php";  //用户权限
   $dataArray=array(); 
   $rowHeight=45;
 
 $bgColor="#E0EEF6";
 $TitleColor="#858C95";
  switch($NextPage){
	     case 1:
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
	           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"  3A","GroupColor"=>"$FORSHORT_COLOR","bgColor"=>"$bgColor","Data"=>$dataArray); 
	           $dataArray=array();
	           break;
	      }
	     case 2:   	
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
           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"  3B","GroupColor"=>"$FORSHORT_COLOR","bgColor"=>"$bgColor","Data"=>$dataArray); 
           $dataArray=array();
           break;
      }
     case 3:       
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
		           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"  1A","GroupColor"=>"$FORSHORT_COLOR","bgColor"=>"$bgColor","Data"=>$dataArray); 
		           $dataArray=array();
		           break;
		      }
     case 4:         
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
			             "Timg"=>"i_kzy",
			             "onTap"=>array("Title"=>"可占用","Value"=>"1","Tag"=>"Production2","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"可  占  用","Align"=>"L","Color"=>"$TitleColor"),
			             "Col_B"=>array("Title"=>"$overQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R","RLText"=>"($blCounts)","RLColor"=>"$TITLE_GRAYCOLOR")
			          );            

                    //待分配
                    // include "../desk/subtask/subtask-212.php";
					
					$SumTotalValue = $overQty = 0;
					$curDate = date("Y-m-d");
					$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$curDate',1) AS NextWeek",$link_id));
 					$curWeek=$dateResult["NextWeek"];
					$blCounts = 0;
					$mySql241= mysql_query( "SELECT A.*,PI.Leadtime,PIL.LeadTime as aLeadTime FROM
			(
				SELECT  S.CompanyId,S.Id,S.POrderId,S.ProductId,S.Qty,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty, SUM(L.Estate) as llEstate,Count(S.POrderId) as count
						FROM (
						    SELECT M.CompanyId,S.Id,S.POrderId,S.ProductId,S.Qty FROM   $DataIn.yw1_ordersheet S
						    LEFT JOIN  $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber
                            WHERE 1 AND S.scFrom>0 AND S.Estate=1 
                         )S  
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
						INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
						INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						INNER JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						LEFT JOIN (
									 SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(L.Estate) as Estate
									 FROM (SELECT S.POrderId FROM $DataIn.yw1_ordersheet S WHERE S.scFrom>0 AND S.Estate=1)S 
									 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
									 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
									 WHERE  1 GROUP BY L.StockId
								 ) L ON L.StockId=G.StockId
						WHERE 1 AND ST.mainType<2  GROUP BY S.POrderId 
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
						} else{  }
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
					
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"241",
			              "Timg"=>"i_dfp",
			             "onTap"=>array("Title"=>"待分配","Value"=>"1","Tag"=>"Production2","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待  分  配","Align"=>"L","Color"=>"$TitleColor"),
			              "Col_B"=>array("Title"=>"$overQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R","RLText"=>"($blCounts)","RLColor"=>"$TITLE_GRAYCOLOR")
			          );            
  
                    //待备料
           $SumTotalValue = $overQty = 0;
			$curDate = date("Y-m-d"); 
                  include "inware_item_sub_3.php";
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"242",
			              "Timg"=>"i_dblc",
			             "onTap"=>array("Title"=>"待备料","Value"=>"1","Tag"=>"Production2","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待  备  料","Align"=>"L","Color"=>"$TitleColor"),
			             "Col_B"=>array("Title"=>"$overQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R","RLText"=>"($blCounts)","RLColor"=>"$TITLE_GRAYCOLOR")
			          );            

                    //待备料
					 include "inware_item_sub_4.php";
					  $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"243",
			              "Timg"=>"i_wfbl",
			             "onTap"=>array("Title"=>"外发备料","Value"=>"1","Tag"=>"Production2","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"外发备料","Align"=>"L","Color"=>"$TitleColor"),
			             "Col_B"=>array("Title"=>"$overQty","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$SumTotalValue","Align"=>"R","RLText"=>"($blCounts)","RLColor"=>"$TITLE_GRAYCOLOR")
			          );   
			       
			       
			     if (versionToNumber($AppVersion)>300){
				          //待补货
				         $bhResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,COUNT(*) AS Nums 
						FROM $DataIn.ck13_replenish S 
						WHERE S.Estate=1 AND S.Lid=0",$link_id));	
						 $bhCounts=$bhResult["Nums"]==""?"0":$bhResult["Nums"];
						 $bhQty=number_format($bhResult["Qty"]);

					  $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"244",
			              "Timg"=>"i_bh",
			             "onTap"=>array("Title"=>"待补料","Value"=>"1","Tag"=>"StuffExt","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待  补  料","Align"=>"L","Color"=>"#FF0000"),
			             "Col_C"=>array("Title"=>"$bhQty","Align"=>"R","RLText"=>"($bhCounts)","RLColor"=>"$TITLE_GRAYCOLOR")
			          );   
                    
			         } 
					
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