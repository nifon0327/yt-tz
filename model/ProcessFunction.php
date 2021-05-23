<?php

function  getLaseProcess($sPOrderId,$StockId,$ProcessId,$DataIn,$link_id){
	
	$ProcessNameArray = array();
	$ProcessIdArray   = array();
	$gxTypeIdArray    = array();
	$SortIdArray      = array();
	$BeforeArray       = array(); 
	$ProcessResult=mysql_query("SELECT B.ProcessId ,PT.Color,PD.ProcessName,PD.gxTypeId,PT.SortId,B.BeforeProcessId 
		    FROM  $DataIn.yw1_scsheet  SC 
	        INNER JOIN $DataIn.cg1_processsheet B  ON B.StockId = SC.StockId
	        INNER JOIN $DataIn.process_data PD ON PD.ProcessId=B.ProcessId
	        INNER JOIN $DataIn.process_type PT ON PT.gxTypeId=PD.gxTypeId
			WHERE B.StockId='$StockId' AND SC.sPOrderId = '$sPOrderId' GROUP BY B.ProcessId ORDER BY PT.SortId,B.Id",$link_id);
	if($ProcessRow=mysql_fetch_array($ProcessResult)){
	   do{
	        $ProcessId          = $ProcessRow["ProcessId"];
	        $ProcessNameArray[$ProcessId] = $ProcessRow["ProcessName"];
	        $ProcessIdArray[]   = $ProcessRow["ProcessId"];
	        $gxTypeIdArray[]    = $ProcessRow["gxTypeId"];
	        $SortIdArray[]      = $ProcessRow["SortId"];  //前工序为0
	        $BeforeArray[]       = $ProcessRow["BeforeProcessId"]; //约束工序
	     }while($ProcessRow=mysql_fetch_array($ProcessResult));
	}
	
	$Counts=count($ProcessIdArray);
	for($k=0;$k<$Counts;$k++){
	  if($ProcessIdArray[$k]==$thisProcessId){
	        $tempk=$k-1;
	
	        if($gxTypeIdArray[$k] ==0){ //前工序 A B C D ...
		         $LastProcessName="<span class='redB'>前工序类型</span>";
		         $LastProcessId="";
		         $LastQty=$MaxQty;
		        
	        }else{ 
		        
		        if($BeforeArray[$k]!=''){ //有约束工序,多道工序，取约束工序
		        
		            if($gxTypeIdArray[$k]==1){  //第一个工序取约束工序的生产最小值
			            $BeforeProcessId = $BeforeArray[$k];
			            
		            }else{ //否则取约束工序和前面一个工序的生产最小值
			            $BeforeProcessId = $BeforeArray[$k].",".$ProcessIdArray[$tempk];
			            
		            }
		            //约束工序名称
	                $BeforeResult = mysql_query("SELECT ProcessId,ProcessName FROM $DataIn.process_data WHERE ProcessId IN ($BeforeProcessId) ORDER BY gxTypeId ",$link_id);
	                $scQtyArray = array();
	                while($BeforeRow = mysql_fetch_array($BeforeResult)){
	                    $BeforeProcessName = $BeforeRow["ProcessName"];
	                    $LastProcessName = $LastProcessName==""?$BeforeProcessName:$LastProcessName."<br>".$BeforeProcessName;
	                    
	                }
	                //约束工序最低生产值  
	               
	                $minRow = mysql_fetch_array(mysql_query("SELECT MIN(Qty) AS minQty FROM (
						        SELECT IFNULL(SUM(C.Qty),0) AS Qty FROM $DataIn.cg1_processsheet  P 
						        LEFT JOIN $DataIn.sc1_gxtj C ON P.ProcessId= C.ProcessId AND P.StockId = C.StockId
						        WHERE C.StockId='$StockId' AND P.ProcessId IN ($BeforeProcessId)
						        GROUP BY C.ProcessId
						        ) A ",$link_id));
					$LastQty = $minRow["minQty"]==""?0:$minRow["minQty"];    
		            
	  
		        }else{ //无约束工序，第一个工序取前工序的生产最小值
		          
			        if($gxTypeIdArray[$k]==1){
			        
				        $LastProcessId = "";
				        $LastProcessName="<span class='redB'>前工序类型</span>";
				        $minRow = mysql_fetch_array(mysql_query("SELECT MIN(Qty) AS minQty FROM (
						        SELECT IFNULL(SUM(C.Qty),0) AS Qty FROM $DataIn.cg1_processsheet  G 
						        LEFT JOIN $DataIn.process_data P ON P.ProcessId = G.ProcessId
						        LEFT JOIN $DataIn.sc1_gxtj C ON P.ProcessId= C.ProcessId AND G.StockId = C.StockId
						        WHERE C.StockId='$StockId'  AND C.sPOrderId='$sPOrderId' AND P.gxTypeId=0
						        GROUP BY C.ProcessId
						        ) A ",$link_id));
						$LastQty = $minRow["minQty"]==""?0:$minRow["minQty"];
				        
			        }else{   
				        $LastProcessId=$ProcessIdArray[$tempk];
		                $LastProcessName=$ProcessNameArray[$LastProcessId];  
	                    $LastQty=getProcessScQty($sPOrderId,$StockId,$LastProcessId,$DataIn,$link_id);
			        } 
		        }
	        }
	        
	        break;
	     }
	}
	
	return  array(""=>);
	
}

function getProcessScQty($sPOrderId,$StockId,$ProcessId,$DataIn,$link_id){
	
	$LastResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty 
		                FROM $DataIn.sc1_gxtj C 
	                    WHERE  C.StockId='$StockId' AND C.ProcessId='$ProcessId' AND C.sPOrderId='$sPOrderId'",$link_id));
	$LastQty=$LastResult["gxQty"]==""?0:$LastResult["gxQty"];
	return $LastQty;
}








?>